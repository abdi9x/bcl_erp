<?php

namespace App\Http\Controllers;

use App\Http\Controllers\FinJurnalController as ControllersFinJurnalController;
use App\Models\Fin_jurnal;
use App\Models\Pricelist;
use App\Models\renter;
use App\Models\tr_renter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class tr_renterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(tr_renter $tr_renter)
    {
        //
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
    public function destroy(tr_renter $tr_renter)
    {
        //
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
            return redirect()->route('rooms')->with(['success' => 'Kamar berhasil disewa']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('rooms')->with(['error' => $th->getMessage()]);
        }
    }
    public function get_no_trans()
    {
        $data = DB::select("SELECT
        CONCAT(
            'BL-',
            DATE_FORMAT( STR_TO_DATE(now(), '%Y-%m-%d' ), '%m%y' ),
            '',
        LPAD( count(*) + 1, 4, '0' )) AS no_trans 
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
}