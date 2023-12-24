<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class expense_receipt extends Model
{
    use HasFactory;
    protected $table = 'expense_receipt';
    protected $guarded = ['id'];

    public function jurnal()
    {
        return $this->belongsTo(Fin_jurnal::class, 'doc_id', 'trans_id');
    }
}
