<?php


namespace App\Models;

class Ad extends  BasModel
{
    protected $table = 'ads_ad';

    protected $primaryKey = 'adid';

    protected $fillable = [
        'title',
    ];

    //json
    protected $casts = [
        're_conf' => 'json',
        'cap_detail' => 'json',
        'worktime' => 'json',
    ];


    public function adCampaign(){
        return $this->hasOne('App\Models\AdCampaign', 'adid', 'adid');
    }

    public function campaign(){
        return $this->hasOne(Campaign::class, 'campaignid', 'campaignid');
    }

    public function cap(){
        return $this->hasOne(Cap::class, 'capid', 'capid');
    }

    public function format(){
        return $this->hasOne('App\Models\Format', 'formatid', 'formatid');
    }

    public function monitorType(){
        return $this->hasOne('App\Models\MonitorType', 'id', 'monitor_id');
    }

    public function basMonitorType(){
        return $this->hasOne('App\Models\MonitorType', 'id', 'bas_monitor_id');
    }

}
