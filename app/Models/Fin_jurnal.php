<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fin_jurnal extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'fin_jurnal';
}
