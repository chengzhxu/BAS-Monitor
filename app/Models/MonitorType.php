<?php


namespace App\Models;


class MonitorType extends  BasModel
{
    protected $table = 'ads_monitor_type';

    protected $primaryKey = 'id';

    protected $fillable = [
        'title'
    ];

}
