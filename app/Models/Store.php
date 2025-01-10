<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'banner',
        'address',
        'whatsapp',
        'province_id',
        'regency_id',
        'subdistrict_id',
        'province_name',
        'regency_name',
        'subdistrict_name',
        'email_notification'
    ];
}
