<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OverdueRental extends Model
{
    protected $table = 'overdue_rentals';

    public $timestamps = false;
}
