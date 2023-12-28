<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room_Category_image extends Model
{
    use HasFactory;
    protected $table = 'room_category_image';
    protected $fillable = [
        'room_category_id',
        'image',
    ];
    public function category()
    {
        return $this->belongsTo(room_category::class, 'room_category_id', 'id_category');
    }
}
