<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tr_renter extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'tr_renter';
    protected $primaryKey = 'id';
    // protected $casts = [
    //     'tanggal' => 'date:Y-m-d',
    //     'tgl_mulai' => 'date:Y-m-d',
    //     'tgl_selesai' => 'date:Y-m-d'
    // ];
    public function renter()
    {
        return $this->belongsTo(renter::class, 'id_renter');
    }
    public function room()
    {
        return $this->belongsTo(Rooms::class, 'room_id');
    }
    public function jurnal()
    {
        return $this->hasMany(fin_jurnal::class, 'doc_id', 'trans_id')->where('kode_akun', '4-10101');
    }
}
