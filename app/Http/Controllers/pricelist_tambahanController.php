<?php

namespace App\Http\Controllers;

use App\Models\extra_pricelist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class pricelist_tambahanController extends Controller
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
        try {
            DB::beginTransaction();
            $this->validate($request, [
                'nama'     => 'required|unique:extra_pricelist,nama',
                'harga'     => 'required|numeric',
                'jangka_waktu' => 'required|numeric',
                'jangka_sewa' => 'required',
            ]);
            $store = extra_pricelist::create([
                'nama'     => $request->nama,
                'qty'   => $request->jangka_waktu,
                'harga'     => $request->harga,
                'jangka_sewa'   => $request->jangka_sewa,
            ]);
            DB::commit();
            return back()->withSuccess('Berhasil menambahkan harga tambahan!');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(extra_pricelist $extra_pricelist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(extra_pricelist $extra_pricelist, Request $request)
    {
        $data = extra_pricelist::findorfail($request->id);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, extra_pricelist $extra_pricelist)
    {
        try {
            $this->validate($request, [
                'id' => 'required',
                'nama' => 'required',
                'jangka_waktu' => 'required',
                'jangka_sewa' => 'required',
                'harga' => 'required',
            ]);
            $pl = extra_pricelist::findorfail($request->id);
            $pl->update([
                'nama' => $request->nama,
                'jangka_waktu' => $request->jangka_waktu,
                'jangka_sewa' => $request->jangka_sewa,
                'harga' => $request->harga,
            ]);
            return back()->with(['success' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(extra_pricelist $extra_pricelist, Request $request)
    {
        try {
            $data = extra_pricelist::findorfail($request->id);
            $data->delete();
            return back()->with(['success' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
    }
}
