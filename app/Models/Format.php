<?php


namespace App\Models;


class Format extends  BasModel
{
    protected $table = 'ads_format';

    protected $primaryKey = 'formatid';

    protected $fillable = [
        'title'
    ];


    public function getAllFormat($is_trance = false){
        $sql = "select * from " . $this->getTable();
        $res = $this->fetchBySql($sql);

        if(!$is_trance){
            return $res;
        }

        $result = [];
        foreach ($res as $_r){
            $result[$_r['formatid']] = $_r['title'];
        }

        return $result;
    }

}
