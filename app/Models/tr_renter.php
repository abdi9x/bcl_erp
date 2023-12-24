<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tr_renter extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'tr_renter';
    // protected $casts = [
    //     'tanggal' => 'date:Y-m-d',
    //     'tgl_mulai' => 'date:Y-m-d',
    //     'tgl_selesai' => 'date:Y-m-d'
    // ];
}
