<?php


namespace App\Models;


class AdCampaign extends  BasModel
{
    protected $table = 'ads_ad_campaign';

    protected $primaryKey = ['adid', 'campaignid'];
    public $incrementing = false;

    protected $fillable = [];


    public function ad()
    {
        return $this->hasOne('App\Models\Ad', 'adid', 'adid');
    }


    public function campaign()
    {
        return $this->hasOne('App\Models\Campaign', 'campaignid', 'campaignid');
    }

}
