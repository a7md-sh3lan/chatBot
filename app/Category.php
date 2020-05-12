<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function elements()
    {
        return $this->morphMany(Element::class, 'followable');
    }
}
