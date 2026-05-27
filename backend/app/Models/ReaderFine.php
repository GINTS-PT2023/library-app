<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReaderFine extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reader_fines';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'reader_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
}
