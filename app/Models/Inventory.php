<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'inventories';

    public function room()
    {

        return $this->belongsTo(Rooms::class, 'assigned_to', 'id');
    }
}
