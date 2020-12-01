<?php


namespace App\Models;


class Asset extends  BasModel
{
    protected $table = 'ads_asset';

    protected $primaryKey = 'assetid';

    protected $fillable = ['title'];

    //json
    protected $casts = [
        'monitor' => 'json',
    ];


    public function getAllAsset($is_trance = false){
        $sql = "select * from " . $this->getTable();
        $res = $this->fetchBySql($sql);

        if(!$is_trance){
            return $res;
        }

        $result = [];
        foreach ($res as $_r){
            $result[$_r['assetid']] = $_r['title'];
        }

        return $result;
    }

}
