<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReaderFine extends Model
{
    protected $fillable = [
        'reader_id',
        'amount',
        'is_paid',
    ];

    public function reader()
    {
        return $this->belongsTo(Reader::class);
    }
}
