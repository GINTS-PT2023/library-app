<?php

namespace App\Models;

use Database\Factories\ReaderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Reader extends Model
{
    /** @use HasFactory<ReaderFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'rentals');
    }

    public function fine()
    {
        return $this->hasOne(ReaderFine::class, 'reader_id');
    }

    /**
     * Get all of the reader's comments.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
