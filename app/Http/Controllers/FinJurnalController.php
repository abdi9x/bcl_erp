<?php

namespace App\Http\Controllers;

use App\Models\expense_receipt;
use App\Models\Fin_jurnal;
use App\Models\Inventory;
use App\Models\renter;
use App\Models\tb_extra_rent;
use App\Models\tr_renter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image as Image;

class FinJurnalController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function get_no_jurnal()
    {
        $data = DB::select("SELECT CONCAT(DATE_FORMAT(NOW(), '%y' ),LPAD(ifnull(max(SUBSTR(no_jurnal,3)),0)+1,7,0)) as no_jurnal from fin_jurnal");
        $result = $data[0];
        return $result->no_jurnal;
    }

    public function get_no_exp()
    {
        $data = DB::select("SELECT
        CONCAT(
            'EX',
            DATE_FORMAT( NOW(), '%m%y' ),
        LPAD( ifnull( max( SUBSTR( doc_id, 7 )), 0 )+ 1, 7, 0 )) AS no_exp 
        FROM
        fin_jurnal 
        WHERE
        doc_id LIKE 'EX%'");
        $result = $data[0];
        return $result->no_exp;
    }

    public function index(Request $request, Fin_jurnal $fin_jurnal)
    {
        if (isset($request->filter)) {
            $start = explode('s/d', $request->filter)[0];
            $end = explode('s/d', $request->filter)[1];
        } else {
            $start = date('Y-m-d', strtotime('first day of this month'));
            $end = date('Y-m-d', strtotime('last day of this month'));
        }
        $data = Fin_jurnal::whereBetween('tanggal', [$start, $end])
            ->where('kode_akun', '1-10101')->where('pos', 'D')
            ->orderby('tanggal', 'DESC')
            ->get();
        foreach ($data as $value) {
            if ($value->kode_subledger != null) {
                $inventory = Inventory::where('assigned_to', $value->kode_subledger)->get();
                $value->inventories = $inventory;
            }
        }

        $belum_lunas = Fin_jurnal::leftjoin('tr_renter', 'tr_renter.trans_id', '=', 'fin_jurnal.doc_id')
            ->leftjoin('renter', 'renter.id', '=', 'tr_renter.id_renter')
            ->select(
                'fin_jurnal.*',
                'renter.nama',
                'renter.id',
                'tr_renter.harga',
                DB::raw('ifnull(sum( kredit ),0) AS dibayar'),
                DB::raw('ifnull(tr_renter.harga - sum( kredit ),0) AS kurang')
            )->where('fin_jurnal.identity', 'regexp', 'pemasukan|sewa kamar')
            ->groupby('fin_jurnal.doc_id')
            ->havingRaw('(tr_renter.harga - sum(kredit)) > 0')
            ->orderby('fin_jurnal.tanggal', 'DESC')
            ->get();
        $belum_lunas_extra = tb_extra_rent::withsum('jurnal as total_kredit', 'kredit')
            ->get()
            ->filter(function ($item) {
                // $detail = tr_renter::where('trans_id', $item->parent_trans)->with('renter')->first();
                return ($item->harga * $item->lama_sewa * $item->qty) - $item->total_kredit > 0;
            });
        foreach ($belum_lunas_extra as $val) {
            $detail = tr_renter::where('trans_id', $val->parent_trans)->with('renter')->first();
            $val->renter = $detail->renter;
        }
        // return response()->json($belum_lunas_extra);
        return view('finance.income')
            ->with('data', $data)
            ->with('start', $start)
            ->with('end', $end)
            ->with('belum_lunas', $belum_lunas)
            ->with('belum_lunas_extra', $belum_lunas_extra);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, tr_renter $tr_renter)
    {
        $no_jurnal = $this->get_no_jurnal();
        $extra_rent = tb_extra_rent::where('kode', $request->transaksi)->first();
        $tr_renter = tr_renter::where('trans_id', '=', $extra_rent->parent_trans)->first();
        $renter = renter::findorfail($tr_renter->id_renter);
        // $extra_rent = tb_extra_rent::where('trans_id_renter', $request->transaksi)->first();
        if ($request->section == 'Tambahan Sewa') {
            $catatan = 'Pembayaran Tambahan Sewa ' . $extra_rent->qty . ' ' . $extra_rent->nama . ' selama ' . $extra_rent->lama_sewa . ' ' . $extra_rent->jangka_sewa . ' Oleh ' . $renter->nama . '. dengan catatan: ' . $request->keterangan;
        } else {
            $catatan = 'Pembayaran Sewa Kamar dari ' . $renter->nama . ' dengan catatan: ' . $request->keterangan;
        }
        DB::beginTransaction();
        try {
            Fin_jurnal::create([
                'no_jurnal' => $no_jurnal,
                'tanggal' => $request->tgl_transaksi,
                'kode_akun' => '4-10101',
                'debet' => 0,
                'kredit' => $request->nominal,
                'kode_subledger' => $request->renter,
                'catatan' => $catatan,
                'index_kas' => 0,
                'doc_id' => $request->transaksi,
                'identity' => $request->section,
                'pos' => 'K',
                'user_id' => auth()->user()->id,
                'csrf' => time()
            ]);
            Fin_jurnal::create([
                'no_jurnal' => $no_jurnal,
                'tanggal' => $request->tgl_transaksi,
                'kode_akun' => '1-10101',
                'debet' => $request->nominal,
                'kredit' => 0,
                'kode_subledger' => null,
                'catatan' => $catatan,
                'index_kas' => 0,
                'doc_id' => $request->transaksi,
                'identity' => $request->section,
                'pos' => 'D',
                'user_id' => auth()->user()->id,
                'csrf' => time()
            ]);
            DB::commit();
            return back()->with('success', 'Pembayaran Berhasil');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function expense_show(Fin_jurnal $fin_jurnal, Request $request)
    {
        $data = Fin_jurnal::with('user')->with('receipt')
            ->where('doc_id', $request->id)->where('kode_akun', 'regexp', '5-10101|5-10102')
            ->where('pos', 'D')->get();
        foreach ($data as $value) {
            switch ($value->kode_akun) {
                case '5-10101': {
                        $tipe_pengeluaran = "Perbaikan/perawatan inventaris kamar atau bangunan";
                        break;
                    };
                case '5-10102': {
                        $tipe_pengeluaran = "Biaya Operasional/lain-lain";
                        break;
                    };
            }
            $value->tipe_pengeluaran = $tipe_pengeluaran;
            $raw_inv = explode(',', $value->kode_subledger);
            $arr_inv = [];
            foreach ($raw_inv as $v_inv) {
                array_push($arr_inv, Inventory::where('inv_number', $v_inv)->first());
            }
            $value->arr_inventory = $arr_inv;
        }

        return response()->json($data);
    }

    public function income_delete(Request $request, Fin_jurnal $fin_jurnal)
    {
        try {
            $data = Fin_jurnal::where('no_jurnal', $request->id)->first();
            $jurnals = Fin_jurnal::where('doc_id', $data->doc_id)->get();
            if (count($jurnals) > 2) {
                Fin_jurnal::where('no_jurnal', $request->id)->delete();
                return back()->with('success', 'Pemasukan Berhasil Dihapus');
            } else {
                return back()->with('error', 'DP tidak dapat dihapus, Batalkan transksi sewa kamar untuk menghapus transaksi');
            }
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Fin_jurnal $fin_jurnal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fin_jurnal $fin_jurnal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fin_jurnal $fin_jurnal)
    {
        //
    }

    public function expense(Request $request)
    {
        if (isset($request->filter)) {
            $start = explode('s/d', $request->filter)[0];
            $end = explode('s/d', $request->filter)[1];
        } else {
            $start = date('Y-m-d', strtotime('first day of this month'));
            $end = date('Y-m-d', strtotime('last day of this month'));
        }
        $inventory = Inventory::leftjoin('rooms', 'rooms.id', '=', 'inventories.assigned_to')
            ->select('inventories.*', 'rooms.room_name as nama_kamar')
            ->get();
        $data = Fin_jurnal::whereBetween('tanggal', [$start, $end])
            ->where('kode_akun', 'regexp', '5-10101|5-10102')->where('pos', 'D')
            ->orderby('tanggal', 'DESC')
            ->get();
        // return response()->json($inventory);
        foreach ($data as $value) {
            switch ($value->kode_akun) {
                case '5-10101': {
                        $tipe_pengeluaran = "Perbaikan/perawatan inventaris kamar atau bangunan";
                        break;
                    };
                case '5-10102': {
                        $tipe_pengeluaran = "Biaya Operasional/lain-lain";
                        break;
                    };
            }
            $value->tipe_pengeluaran = $tipe_pengeluaran;

            $raw_inv = explode(',', $value->kode_subledger);
            $arr_inv = [];
            foreach ($raw_inv as $v_inv) {
                array_push($arr_inv, Inventory::where('inv_number', $v_inv)->first());
            }
            $value->arr_inventory = $arr_inv;
        }
        // return response()->json($data);
        return view('finance.expense')->with('data', $data)->with('start', $start)->with('end', $end)->with('inventory', $inventory);
    }
    public function expense_delete(Request $request)
    {
        try {
            $delete = Fin_jurnal::where('doc_id', $request->id)->delete();
            $receipt = expense_receipt::where('trans_id', $request->id)->get();
            foreach ($receipt as $value) {
                $path = public_path('assets/images/receipt/' . $value->img);
                if (file_exists($path)) {
                    unlink($path);
                }
                expense_receipt::find($value->id)->delete();
            }
            return back()->with('success', 'Pengeluaran Berhasil Dihapus');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
        return response()->json($request->all());
    }
    function generateRandomString($length = 20)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyz', ceil($length / strlen($x)))), 1, $length) . time();
    }
    public function store_expense(Request $request)
    {
        $width = 600;
        $height = 600;

        // return response()->json($request);
        // dd($data);
        DB::beginTransaction();
        try {
            $no_exp = $this->get_no_exp();
            $no_jurnal = $this->get_no_jurnal();
            for ($i = 0; $i < count($request->akun); $i++) {
                $subledger = null;
                if (isset($request->akun_subledger) && count($request->akun_subledger) > 0) {
                    foreach ($request->akun_subledger as $key => $value) {
                        if ($key == $i) {
                            if (count($value) > 0) {
                                $subledger = implode(',', $value);
                            }
                        }
                    }
                }
                $data = Fin_jurnal::create([
                    'no_jurnal' => $no_jurnal,
                    'tanggal' => $request->tgl_transaksi,
                    'kode_akun' => '1-10101',
                    'debet' => 0,
                    'kredit' => $request->jumlah[$i],
                    'kode_subledger' => null,
                    'catatan' => $request->deskripsi[$i],
                    'index_kas' => 0,
                    'doc_id' => $no_exp,
                    'identity' => 'Pengeluaran',
                    'pos' => 'K',
                    'user_id' => auth()->user()->id,
                    'csrf' => time()
                ]);
                $data = Fin_jurnal::create([
                    'no_jurnal' => $no_jurnal,
                    'tanggal' => $request->tgl_transaksi,
                    'kode_akun' => $request->akun[$i],
                    'debet' => $request->jumlah[$i],
                    'kredit' => 0,
                    'kode_subledger' => $subledger,
                    'catatan' => $request->deskripsi[$i],
                    'index_kas' => 0,
                    'doc_id' => $no_exp,
                    'identity' => 'Pengeluaran',
                    'pos' => 'D',
                    'user_id' => auth()->user()->id,
                    'csrf' => time()
                ]);
            }
            if (isset($request->receipt)) {
                foreach ($request->receipt as $key => $value) {
                    $image = $request->file('receipt')[$key];
                    $filename = 'bclReceipt_' . Carbon::now()->format('Y-m-d') . '_' . $this->generateRandomString(8) . '.' . $image->getClientOriginalExtension();
                    $path = public_path('assets/images/receipt/' . $filename);
                    $img = Image::make($request->file('receipt')[$key]);
                    $img->height() > $height ? $height = $height : $height =  $img->height();
                    $img->width() > $width ? $width = $width : $width =  $img->width();
                    $img->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $img->save($path);
                    expense_receipt::create([
                        'trans_id' => $no_exp,
                        'img' => $filename
                    ]);
                }
            }
            DB::commit();
            return back()->with('success', 'Pengeluaran Berhasil Dibuat');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    // public function get_expense(Request $request)
    // {
    //     $data = Fin_jurnal::where('doc_id', $request->no_exp)->get();
    //     foreach ($data as $value) {
    //         $raw_inv = explode(',', $value->kode_subledger);
    //         $arr_inv = [];
    //         foreach ($raw_inv as $v_inv) {
    //             array_push($arr_inv, Inventory::where('inv_number', $v_inv)->first());
    //         }
    //         $value->arr_inventory = $arr_inv;
    //     }
    //     return response()->json($data);
    // }

    public function update_expense(Request $request)
    {
        $data = Fin_jurnal::where('doc_id', $request->no_exp)->get();
        foreach ($data as $key => $value) {
            $value->tanggal = $request->tgl_transaksi;
            $value->kode_akun = $request->akun;
            $value->kredit = $request->nominal;
            $value->kode_subledger = $request->kamar;
            $value->catatan = $request->keterangan;
            $value->save();
        }

        return response()->json($request);
    }
}
