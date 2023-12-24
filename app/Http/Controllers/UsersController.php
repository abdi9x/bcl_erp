<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Traits\HasRoles;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        // return response()->json($users);
        return view('users.index')->with('users', $users);
    }
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                // 'img' => 'required',
                'phone' => 'required',
                'email' => 'required|unique:users',
                'password' => 'required',
            ]);
            if (!isset($request->img)) {
                $img = 'default.jpg';
            }
            $result = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'img' => $img,
                'password' => $request->password,
            ]);
            return back()->with('success', 'Data User Berhasil Ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->with('input', $request->all());
        }
    }
    public function destroy($id)
    {
        try {
            $current_user = auth()->user()->id;
            if ($current_user == $id) {
                return back()->with('error', 'Tidak Bisa Menghapus Akun Sendiri!');
            }
            $users = User::findOrFail($id);
            $users->delete();
            return back()->with('success', 'Data User Berhasil Dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function edit(Request $request)
    {
        $users = User::findOrFail($request->id);
        return response()->json($users);
    }

    public function update(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                // 'img' => 'required',
                'phone' => 'required',
                'email' => 'required',
                'password' => 'sometimes',
            ]);
            $users = User::findOrFail($request->id);
            $users->assignRole($request->role);
            $users->syncRoles($request->role);
            if (isset($request->password)) {
                $users->update([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'password' => $request->password,
                ]);
                return back()->with('success', 'Data User Berhasil Diubah!');
            } else {
                $users->update([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                ]);
                return back()->with('success', 'Data User Berhasil Diubah!');
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->with('input', $request->all());
        }
    }
}
