<?php


namespace App\Models;


class App extends  BasModel
{
    protected $table = 'ads_app';

    protected $primaryKey = 'appid';

    protected $fillable = ['title'];


    public function getAllApp($is_trance = false){
        $sql = "select * from " . $this->getTable();
        $res = $this->fetchBySql($sql);

        if(!$is_trance){
            return $res;
        }

        $result = [];
        foreach ($res as $_r){
            $result[$_r['appid']] = $_r['title'];
        }

        return $result;
    }

}
