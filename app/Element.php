<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Element extends Model
{
    //
    public function followable()
    {
        return $this->morphTo();
    }
}
