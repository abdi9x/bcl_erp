<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fin_jurnal extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'fin_jurnal';
    protected $primaryKey = 'id';

    // public function renter_jurnal()
    // {
    //     return $this->belongsTo(tr_renter::class, 'doc_id');
    // }
}
