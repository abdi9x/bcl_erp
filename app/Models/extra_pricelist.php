<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class extra_pricelist extends Model
{
    use HasFactory;
    protected $table = 'extra_pricelist';
    protected $guarded = ['id'];
}
