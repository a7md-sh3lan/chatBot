<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = ['name', 'phone_number'];
    //
    public function logs()
    {
        return $this->hasMany(Log::class)->latest();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
