<?php

namespace App\Http\Controllers;

use App\Models\tb_testimoni;
use Illuminate\Http\Request;

class tb_testimoniController extends Controller
{
    public function index()
    {
        $testimoni = tb_testimoni::all();
        return view('testimoni.index')->with('testimoni', $testimoni);
    }
}
