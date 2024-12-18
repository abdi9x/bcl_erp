<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class renter_document extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_renter',
        'document_type',
        'img'
    ];
    protected $table = 'renter_document';
    public function renter()
    {
        return $this->belongsTo(renter::class, 'id_renter');
    }
}
