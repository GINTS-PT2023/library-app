<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'published_year',
        'total_copies',
        'available_copies',
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function readers()
    {
        return $this->belongsToMany(Reader::class, 'rentals');
    }
}
