<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function Log()
    {
        return $this->belongsTo(Log::class);
    }

    public function Element()
    {
        return $this->belongsTo(Element::class);
    }
}
