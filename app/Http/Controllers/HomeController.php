<?php

namespace App\Http\Controllers;

use App\Models\Fin_jurnal;
use App\Models\Inventory;
use App\Models\Rooms;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data = Rooms::with('category')->with('renter')->get();
        $room_used = 0;
        foreach ($data as $value) {
            if ($value->renter != null) {
                $room_used++;
            }
        }
        $response = new \stdClass();
        $rooms = new \stdClass();
        $rooms->total = $data->count();
        $rooms->used = $room_used;
        $response->rooms = $rooms;

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
        $response->belum_lunas = $belum_lunas->count();

        $inventory = Inventory::leftjoin('rooms', 'rooms.id', '=', 'inventories.assigned_to')
            ->leftjoin('fin_jurnal', function ($join) {
                $join->on('fin_jurnal.kode_subledger', 'like', 'inventories.inv_number');
            })
            ->select('inventories.*', 'rooms.room_name', DB::raw('max(tanggal) as last_maintanance'))
            ->groupby('inventories.inv_number')
            ->get();

        $needed_maintanance = 0;
        foreach ($inventory as $data) {
            if ($data->last_maintanance != null && $data->maintanance_cycle != null) {
                if ($data->maintanance_cycle == 'Minggu') {
                    $next_maintanance = Carbon::parse($data->last_maintanance)->addWeeks($data->maintanance_period)->format('Y-m-d');
                    $remaining = Carbon::parse(Carbon::now())->diffInDays($next_maintanance);
                } else if ($data->maintanance_cycle == 'Bulan') {
                    $next_maintanance = Carbon::parse($data->last_maintanance)->addMonths($data->maintanance_period)->format('Y-m-d');
                    $remaining = Carbon::parse(Carbon::now())->diffInDays($next_maintanance);
                } else if ($data->maintanance_cycle == 'Tahun') {
                    $next_maintanance = Carbon::parse($data->last_maintanance)->addYears($data->maintanance_period)->format('Y-m-d');
                    $remaining =  Carbon::parse(Carbon::now())->diffInDays($next_maintanance);
                }
            } else {
                $next_maintanance = null;
            }
            if ($next_maintanance != null && $remaining <= 7) {
                $needed_maintanance++;
            }
        }
        $response->needed_maintanance = $needed_maintanance;


        $room_stat = Rooms::leftjoin('tr_renter', function ($join) {
            $join->on('tr_renter.room_id', '=', 'rooms.id');
            $join->where(DB::raw('year(tr_renter.tgl_mulai)'), '=', Carbon::now()->format('Y'));
        })->select('rooms.*', DB::raw('sum(tr_renter.harga) as total_value'))->groupby('rooms.id')->orderby('total_value', 'DESC')->get();
        $stat = new \stdClass();
        foreach ($room_stat->take(10) as $data) {
            $stat->room_name[] = $data->room_name;
            $stat->total_value[] = $data->total_value;
        }
        $response->room_stat = $stat;
        // return response()->json($response); 
        return view('home')->with('response', (object)$response);
    }
}
