<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    //

    protected $fillable = [
        'name',
        'description',
        'image',
        'address',
        'phone',
        'province_id',
        'city_id',
        'district_id',
        'subdistrict_id',
        'province_name',
        'regency_name',
        'subdistrict_name',
    ];
}
