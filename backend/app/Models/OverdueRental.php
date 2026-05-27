<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OverdueRental extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'overdue_rentals';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'rented_at' => 'datetime',
        'due_at' => 'datetime',
    ];
}
