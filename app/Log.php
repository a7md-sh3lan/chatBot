<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = ['guest_id', 'messages'];
    //
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
