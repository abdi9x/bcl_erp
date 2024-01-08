<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tb_extra_rent extends Model
{
    use HasFactory;
    protected $table = 'tb_extra_rent';
    protected $guarded = ['id'];

    public function jurnal()
    {
        return $this->hasMany(Fin_jurnal::class, 'doc_id', 'kode')
            ->where('identity', 'Tambahan Sewa')
            ->where('pos', 'K');
    }
}
