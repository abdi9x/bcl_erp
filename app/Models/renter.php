<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class renter extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama',
        'alamat',
        'phone',
        'phone2',
        'identitas',
        'no_identitas',
        'kendaraan',
        'nopol',
        'birthday',
    ];
    protected $table = 'renter';
    protected $primaryKey = 'id';

    public function tr_renter()
    {
        return $this->hasMany(tr_renter::class, 'id_renter');
    }
    public function document()
    {
        return $this->hasMany(renter_document::class, 'id_renter');
    }

    public function current_room()
    {
        return $this->hasOne(tr_renter::class, 'id_renter')->leftjoin('rooms', 'tr_renter.room_id', '=', 'rooms.id')->where('tgl_mulai', '<=', Carbon::now())->where('tgl_selesai', '>=', Carbon::now());
    }


}
