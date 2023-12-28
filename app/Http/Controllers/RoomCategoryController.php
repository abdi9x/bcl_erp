<?php

namespace App\Http\Controllers;

use App\Models\room_category;
use App\Models\Room_Category_image;
use App\Models\Rooms;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoomCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $images = Room_Category_image::with('category')->get();
        $categories = room_category::with('images')->get();
        $rooms = Rooms::with('category')->get();
        // return response()->json($images);
        return view('category.index', compact('categories', 'images', 'rooms'));
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
                'nama_kategori' => 'required|unique:room_category,category_name',
            ]);
            $data = new room_category;
            $data->category_name = $request->nama_kategori;
            $data->notes = $request->notes;
            $data->slug = Str::slug($request->nama_kategori);
            $data->save();
            return back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return back()->with('error', 'Data gagal ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(room_category $room_category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(room_category $room_category, Request $request)
    {
        $data = room_category::with('images')->find($request->id);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, room_category $room_category)
    {
        try {
            $data = room_category::find($request->id);
            $data->category_name = $request->nama_kategori;
            $data->notes = $request->notes;
            $data->slug = Str::slug($request->nama_kategori);
            $data->save();
            return back()->with('success', 'Data berhasil diubah');
        } catch (\Throwable $th) {
            return back()->with('error', 'Data gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $data = room_category::find($request->id);
            $data->delete();
            return back()->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            return back()->with('error', 'Data gagal dihapus');
        }
    }
}
