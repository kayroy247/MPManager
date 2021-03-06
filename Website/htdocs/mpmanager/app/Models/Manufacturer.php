<?php

namespace App\Models;

use App\Models\Address\Address;

class Manufacturer extends BaseModel
{
    protected $hidden = ['api_name'];
    protected $guarded = [];
    public function address()
    {
        return $this->morphMany(Address::class, 'owner');
    }
}
