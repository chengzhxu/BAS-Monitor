<?php


namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Ad extends  Authenticatable
{
    protected $table = 'ads_ad';

    protected $fillable = [
        'title'
    ];

}
