<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    protected $fillable = ['body'];

    /**
     * Get the parent commentable model (Book or Reader).
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
