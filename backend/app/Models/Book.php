<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'published_year',
        'total_copies',
        'available_copies',
        'status',
        'copied_from_id',
        'copied_at',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Global scope to only show active books by default
        static::addGlobalScope('onlyActive', function (Builder $builder) {
             $builder->where('status', 'active');
        });
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function readers()
    {
        return $this->belongsToMany(Reader::class, 'rentals');
    }

    /**
     * Get all of the book's comments.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Local scope to eager load comments.
     */
    public function scopeWithComments($query)
    {
        return $query->with('comments');
    }

    /**
     * Custom scope to filter out deleted records (manual implementation of soft delete filter)
     */
    public function scopeOnlyActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Local scope for archived books
     */
    public function scopeArchived($query)
    {
        return $query->withoutGlobalScope('onlyActive')->where('status', 'archived');
    }
}
