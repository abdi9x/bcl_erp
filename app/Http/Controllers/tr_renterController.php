<?php

namespace App\Http\Controllers;

use App\Http\Controllers\FinJurnalController as ControllersFinJurnalController;
use App\Models\extra_pricelist;
use App\Models\Fin_jurnal;
use App\Models\Pricelist;
use App\Models\renter;
use App\Models\Rooms;
use App\Models\tb_extra_rent;
use App\Models\tr_renter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class tr_renterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (isset($request->filter)) {
            $start = explode('s/d', $request->filter)[0];
            $end = explode('s/d', $request->filter)[1];
        } else {
            $start = date('Y-m-d', strtotime('first day of january this year'));
            $end = date('Y-m-d', strtotime('last day of december this year'));
        }
        $data = tr_renter::with('renter')->with('room')
            ->with('tambahan')
            ->whereBetween('tanggal', [$start, $end])->get();
        // return response()->json($data);
        $category = DB::table('room_category')->get();
        // $rooms = Rooms::with('category')->get();
        $rooms = Rooms::with('category')->with('renter')->get();
        // return response()->json($rooms);
        $renter = renter::all();
        $belum_lunas = Fin_jurnal::leftjoin('tr_renter', 'tr_renter.trans_id', '=', 'fin_jurnal.doc_id')
            ->leftjoin('renter', 'renter.id', '=', 'tr_renter.id_renter')
            ->select(
                'fin_jurnal.*',
                'renter.nama',
                'renter.id',
                'tr_renter.harga',
                DB::raw('sum( kredit ) AS dibayar'),
                DB::raw('tr_renter.harga - sum( kredit ) AS kurang')
            )->where('fin_jurnal.identity', 'regexp', 'pemasukan|sewa kamar')

            ->groupby('fin_jurnal.doc_id')
            ->havingRaw('(tr_renter.harga - sum(kredit)) > 0')
            ->orderby('fin_jurnal.tanggal', 'DESC')
            ->get();
        $belum_lunas_extra = tb_extra_rent::withsum('jurnal as total_kredit', 'kredit')
            ->groupby('id')
            ->get()
            ->filter(function ($item) {
                return ($item->harga * $item->lama_sewa * $item->qty) - $item->total_kredit > 0;
            });

        foreach ($belum_lunas_extra as $val) {
            $detail = tr_renter::where('trans_id', $val->parent_trans)->with('renter')->first();
            $val->renter = $detail->renter;
        }            // return response()->json($belum_lunas_extra);
        $extra_pricelist = extra_pricelist::all();
        // return response()->json($extra_pricelist);
        return view('transaksi.index', compact(
            'belum_lunas',
            'belum_lunas_extra',
            'data',
            'category',
            'rooms',
            'renter',
            'start',
            'end',
            'extra_pricelist'
        ));
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
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     */
    public function show(tr_renter $tr_renter, Request $request)
    {
        $transaksi = tr_renter::with('renter')
            ->with('room')
            ->with('jurnal', function ($query) {
                $query->leftjoin('users', 'users.id', '=', 'fin_jurnal.user_id');
                $query->orderby('fin_jurnal.tanggal', 'ASC');
            })->with('tambahan')
            ->where('trans_id', '=', $request->id)->first();
        return response()->json($transaksi);
    }

    public function cetak(tr_renter $tr_renter, Request $request)
    {
        $transaksi = tr_renter::with('renter')
            ->with('room', function ($query) {
                $query->with('category');
            })
            ->with('jurnal', function ($query) {
                $query->leftjoin('users', 'users.id', '=', 'fin_jurnal.user_id');
                $query->orderby('fin_jurnal.tanggal', 'ASC');
            })
            ->with('tambahan')
            ->where('trans_id', '=', $request->id)->first();

        return view('transaksi.cetak', compact('transaksi'));
        // return response()->json($transaksi);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(tr_renter $tr_renter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, tr_renter $tr_renter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(tr_renter $tr_renter, Request $request)
    {

        try {
            DB::beginTransaction();
            $transaksi = tr_renter::where('trans_id', $request->id)->get();
            $extra = tb_extra_rent::where('parent_trans', $request->id)->get();
            $jurnal = Fin_jurnal::where('doc_id', $request->id)->get();
            foreach ($transaksi as $key => $value) {
                $value->delete();
            }
            foreach ($jurnal as $key => $value) {
                $value->delete();
            }
            foreach ($extra as $key => $value) {
                $value->delete();
            }
            DB::commit();
            return back()->with(['success' => 'Transaksi berhasil dihapus']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with(['error' => $th->getMessage()]);
        }
    }

    public function refund(tr_renter $tr_renter, Request $request)
    {
        DB::beginTransaction();
        try {
            $transaksi = tr_renter::where('trans_id', $request->kode_trans)->first();
            if ($transaksi->harga < $request->nominal_refund) {
                return back()->with(['error' => 'Nominal refund melebihi harga sewa']);
            }
            $transaksi->update([
                'tgl_selesai' => $request->tgl_refund
            ]);
            $renter = renter::findorfail($transaksi->id_renter);

            $no_exp = app(ControllersFinJurnalController::class)->get_no_exp();
            $no_jurnal = app(ControllersFinJurnalController::class)->get_no_jurnal();
            $data = Fin_jurnal::create([
                'no_jurnal' => $no_jurnal,
                'tanggal' => $request->tgl_refund,
                'kode_akun' => '1-10101',
                'debet' => 0,
                'kredit' => $request->nominal_refund,
                'kode_subledger' => null,
                'catatan' => 'Refund Sewa Kamar kepada ' . $renter->nama . ', dengan alasan: ' . $request->alasan,
                'index_kas' => 0,
                'doc_id' => $no_exp,
                'identity' => 'Refund',
                'pos' => 'K',
                'user_id' => auth()->user()->id,
                'csrf' => time()
            ]);
            $data = Fin_jurnal::create([
                'no_jurnal' => $no_jurnal,
                'tanggal' => $request->tgl_refund,
                'kode_akun' => '5-10102',
                'debet' => $request->nominal_refund,
                'kredit' => 0,
                'kode_subledger' => null,
                'catatan' => 'Refund Sewa Kamar kepada ' . $renter->nama . ', dengan alasan: ' . $request->alasan,
                'index_kas' => 0,
                'doc_id' => $no_exp,
                'identity' => 'Refund',
                'pos' => 'D',
                'user_id' => auth()->user()->id,
                'csrf' => time()
            ]);
            DB::commit();
            return back()->with(['success' => 'Refund berhasil dilakukan']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with(['error' => $th->getMessage()]);
        }
    }

    public function reschedule(Request $request)
    {

        try {
            $transaksi = tr_renter::where('trans_id', $request->trans_id)->first();
            switch ($transaksi->jangka_sewa) {
                case 'Hari':
                    $jangka_sewa = 'days';
                    break;
                case 'Minggu':
                    $jangka_sewa = 'weeks';
                    break;
                case 'Bulan':
                    $jangka_sewa = 'months';
                    break;
                case 'Tahun':
                    $jangka_sewa = 'years';
                    break;
                default:
                    $jangka_sewa = 'days';
                    break;
            }
            $tgl_selesai = date('Y-m-d', strtotime("+$transaksi->jangka_waktu $jangka_sewa", strtotime($request->tgl_rencana_masuk)));
            $transaksi->update([
                'tgl_mulai' => $request->tgl_rencana_masuk,
                'tgl_selesai' => $tgl_selesai
            ]);
            return back()->with(['success' => 'Tanggal masuk berhasil diubah']);
        } catch (\Throwable $th) {
            return back()->with(['error' => $th->getMessage()]);
        }
    }
    public function sewa(tr_renter $tr_renter, Request $request)
    {
        $renter = renter::findorfail($request->renter);
        // return response()->json($request);
        $no_trans = $this->get_no_trans();
        DB::beginTransaction();
        try {
            $this->validate($request, [
                'renter' => 'required',
                'kamar' => 'required',
                'renter' => 'required',
                'pricelist' => 'required',
                'tgl_masuk' => 'required',
                'catatan' => 'sometimes',
                'tgl_bayar' => 'sometimes',
                'nominal' => 'required|numeric|min:1',
            ]);
            $pl = pricelist::findorfail($request->pricelist);
            switch ($pl->jangka_sewa) {
                case 'Hari':
                    $jangka_sewa = 'days';
                    break;
                case 'Minggu':
                    $jangka_sewa = 'weeks';
                    break;
                case 'Bulan':
                    $jangka_sewa = 'months';
                    break;
                case 'Tahun':
                    $jangka_sewa = 'years';
                    break;
                default:
                    $jangka_sewa = 'days';
                    break;
            }
            $bonus_sewa = 'days';
            if ($pl->bonus_waktu > 0) {
                switch ($pl->bonus_sewa) {
                    case 'Hari':
                        $bonus_sewa = 'days';
                        break;
                    case 'Minggu':
                        $bonus_sewa = 'weeks';
                        break;
                    case 'Bulan':
                        $bonus_sewa = 'months';
                        break;
                    case 'Tahun':
                        $bonus_sewa = 'years';
                        break;
                    default:
                        $bonus_sewa = 'days';
                        break;
                }
            }
            $periode_normal = date('Y-m-d', strtotime("+$pl->jangka_waktu $jangka_sewa", strtotime($request->tgl_masuk)));
            $periode_bonus = date('Y-m-d', strtotime("+$pl->bonus_waktu $bonus_sewa", strtotime($periode_normal)));
            $Kamar = Rooms::findorfail($request->kamar);
            tr_renter::create([
                'trans_id' => $no_trans,
                'identity' => 'Baru',
                'id_renter' => $request->renter,
                'tanggal' => date('Y-m-d'),
                'tgl_mulai' => $request->tgl_masuk,
                'tgl_selesai' =>  $periode_bonus,
                'room_id' => $request->kamar,
                'lama_sewa' => $pl->jangka_waktu,
                'jangka_sewa' => $pl->jangka_sewa,
                'harga' => $pl->price,
                'free_sewa' => $pl->bonus_waktu,
                'free_jangka' => $pl->bonus_sewa,
                'catatan' => $request->catatan
            ]);
            $no_jurnal = app(ControllersFinJurnalController::class)->get_no_jurnal();
            Fin_jurnal::create([
                'no_jurnal' => $no_jurnal,
                'tanggal' => $request->tgl_bayar,
                'kode_akun' => '4-10101',
                'debet' => 0,
                'kredit' => $request->nominal,
                'kode_subledger' => $request->renter,
                'catatan' => 'Pendapatan Sewa Kamar dari ' . $renter->nama,
                'index_kas' => 0,
                'doc_id' => $no_trans,
                'identity' => 'Sewa Kamar',
                'pos' => 'K',
                'user_id' => auth()->user()->id,
                'csrf' => time()
            ]);
            Fin_jurnal::create([
                'no_jurnal' => $no_jurnal,
                'tanggal' => $request->tgl_bayar,
                'kode_akun' => '1-10101',
                'debet' => $request->nominal,
                'kredit' => 0,
                'kode_subledger' => null,
                'catatan' => 'Pendapatan Sewa Kamar dari ' . $renter->nama,
                'index_kas' => 0,
                'doc_id' => $no_trans,
                'identity' => 'Sewa Kamar',
                'pos' => 'D',
                'user_id' => auth()->user()->id,
                'csrf' => time()
            ]);
            DB::commit();
            // return response()->json($request);
            return back()->with(['success' => 'Kamar berhasil disewa']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with(['error' => $th->getMessage()]);
        }
    }
    public function get_no_trans()
    {
        $data = DB::select("SELECT
        CONCAT(
            'BL-',
            DATE_FORMAT( STR_TO_DATE(now(), '%Y-%m-%d' ), '%m%y' ),
            '',
        LPAD( ifnull(max(RIGHT(trans_id,4)),0) + 1, 4, '0' )) AS no_trans 
        -- LPAD( count(*) + 1, 4, '0' )) AS no_trans
        FROM
        tr_renter 
        WHERE
        MONTH ( tanggal )= MONTH (
        STR_TO_DATE(now(), '%Y-%m-%d' )) 
        AND YEAR ( tanggal )= YEAR (
        STR_TO_DATE(now(), '%Y-%m-%d' ))");
        $result = $data[0];
        return $result->no_trans;
    }

    public function ranking_penyewa()
    {
        $data = tr_renter::with('renter')->select(DB::raw('*, sum(
            DATEDIFF( tr_renter.tgl_selesai, tr_renter.tgl_mulai )) AS total_lama_sewa '))
            ->groupby('id_renter')
            ->orderby('total_lama_sewa', 'DESC')
            ->get();
        return response()->json($data);
    }
}
