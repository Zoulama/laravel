<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    // Que la date de création
    // migrations
    public $timestamps = true;
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject', 'email', 'description'
    ];
}
