<?php

namespace App\Model;

use App\Model\ProductVariant;

class Product extends Elegant
{
    public function setImagesAttribute($images)
    {
        $this->attributes['images'] = json_encode($images);
    }

    public function getImagesAttribute()
    {
        return json_decode($this->attributes['images']);
    }

    public function setOptionsAttribute($options)
    {
        $this->attributes['options'] = json_encode($options);
    }

    public function getOptionsAttribute()
    {
        return json_decode($this->attributes['options']);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
