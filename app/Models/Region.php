<?php


namespace App\Models;


class Region extends  BasModel
{
    protected $table = 't_region_code';

    protected $primaryKey = 'region_code';

    protected $fillable = [];


    public function getAllRegion($is_trance = false){
        $sql = "select * from " . $this->getTable();
        $res = $this->fetchBySql($sql);

        if(!$is_trance){
            return $res;
        }

        $result = [];
        foreach ($res as $_r){
            $result[$_r['region_code']] = $_r['region_name'];
        }

        return $result;
    }

}
