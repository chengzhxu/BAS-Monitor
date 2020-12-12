<?php


namespace App\Models;


class Media extends  BasModel
{
    protected $table = 'ads_media';

    protected $primaryKey = 'mediaid';

    protected $fillable = [
        'title'
    ];


    public function getAllMedia($is_trance = false){
        $sql = "select * from " . $this->getTable();
        $res = $this->fetchBySql($sql);

        if(!$is_trance){
            return $res;
        }

        $result = [];
        foreach ($res as $_r){
            $result[$_r['mediaid']] = $_r['title'];
        }

        return $result;
    }


    public static function getMediaType(){
        $config = config('monitor');
        $type_arr = Q($config, 'media_type');   //媒体属性

        $result = [];
        foreach ($type_arr as $_t){
            $result[$_t['type']] = $_t['text'];
        }

        return $result;
    }
}
