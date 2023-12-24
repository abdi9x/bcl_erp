<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rooms extends Model
{
    use HasFactory;
    // protected $primaryKey = 'id';
    protected $fillable = [
        'room_name',
        'room_category',
        'notes',
    ];
    // protected $cast = [
    //     'tanggal' => 'date:Y-m-d',
    //     'tgl_mulai' => 'date:Y-m-d',
    //     'tgl_selesai' => 'date:Y-m-d'
    // ];
    protected $table = 'rooms';
    protected $primaryKey = 'id';
}
