<?php

namespace App\Http\Controllers;

use App\Models\renter;
use App\Models\Rooms;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = DB::table('rooms')->leftjoin('room_category', 'rooms.room_category', '=', 'room_category.id_category')
            ->leftjoin('tr_renter', function ($join) {
                $join->on('rooms.id', '=', 'tr_renter.room_id')
                    ->where('tr_renter.tgl_mulai', '<=',  Carbon::now()->format('Y-m-d'))
                    ->where('tr_renter.tgl_selesai', '>=',  Carbon::now()->format('Y-m-d'));
            })->leftJoin('renter', 'tr_renter.id_renter', '=', 'renter.id')
            ->leftjoin('fin_jurnal', function ($join2) {
                $join2->on('tr_renter.trans_id', '=', 'fin_jurnal.doc_id')
                    ->where('fin_jurnal.identity', '=', 'Sewa Kamar');
            })
            ->select(
                'rooms.*',
                DB::raw('tr_renter.harga+(0-sum(fin_jurnal.kredit )) as kurang'),
                'renter.nama as nama',
                'tr_renter.trans_id',
                'tr_renter.id_renter',
                'tr_renter.room_id',
                'tr_renter.tgl_mulai',
                'tr_renter.tgl_selesai',
                'tr_renter.lama_sewa',
                'tr_renter.jangka_sewa',
                'tr_renter.free_sewa',
                'tr_renter.free_jangka',
                'room_category.category_name as category_name'
            )
            ->groupBy('rooms.room_name')
            ->get();
        // return dd($data);
        $category = DB::table('room_category')->get();
        // $rooms = Rooms::leftjoin('room_category', 'rooms.room_category', '=', 'room_category.id_category')
        //     ->select('rooms.*', 'room_category.category_name as category_name')->get();
        $rooms = Rooms::with('category')->get();
        $renter = renter::all();
        // return response()->json($rooms);
        return view('rooms.rooms')->with('data', $data)->with('category', $category)->with('renter', $renter)->with('base_room', $rooms);
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
        try {
            $this->validate($request, [
                'no_kamar'     => 'required|unique:rooms,room_name',
                'kategori'     => 'required|numeric'
            ]);
            $result = Rooms::create([
                'room_name'     => $request->no_kamar,
                'room_category'     => $request->kategori,
                'notes'   => $request->catatan
            ]);
            return redirect()->route('rooms')->with(['success' => 'Data Kamar berhasil ditambahkan!']);
        } catch (\Throwable $th) {
            return redirect()->route('rooms')->with(['error' => $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Rooms $rooms)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        try {
            $room = Rooms::find($request->id);
            return response()->json($room);
        } catch (\Throwable $th) {
            return redirect()->route('rooms')->with(['error' => 'Data tidak ditemukan!']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rooms $rooms)
    {
        try {
            $this->validate($request, [
                'no_kamar'     => 'required',
                'kategori'     => 'required|numeric'
            ]);
            $room = Rooms::find($request->id);
            $result = $room->update([
                'room_name'     => $request->no_kamar,
                'room_category'     => $request->kategori,
                'notes'   => $request->catatan
            ]);
            return redirect()->route('rooms')->with(['success' => 'Data Berhasil diubah!']);
        } catch (\Throwable $th) {
            return redirect()->route('rooms')->with(['error' => $th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rooms $rooms, Request $request)
    {
        try {
            $room = Rooms::find($request->id);
            $result = $room->delete();
            return redirect()->route('rooms')->with(['success' => 'Data berhasil dihapus!']);
        } catch (\Throwable $th) {
            return redirect()->route('rooms')->with(['error' => 'Data gagal dihapus!']);
        }
    }
}
