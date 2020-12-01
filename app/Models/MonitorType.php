<?php


namespace App\Models;


class MonitorType extends  BasModel
{
    protected $table = 'ads_monitor_type';

    protected $primaryKey = 'id';

    protected $fillable = [
        'title'
    ];



    /**
     * 获取监测类型
     * @param $type   类型  是否BAS  0：否  1：是
     * @param $is_trance  是否转换key
    */
    public function getMonitorList($is_trance = false, $type = -1){
        $sql = "select * from " . $this->getTable();
        if($type > -1){
            $sql .= " where type = " . $type;
        }
        $res = $this->fetchBySql($sql);

        if(!$is_trance){
            return $res;
        }

        $result = [];
        $bas_result = [];
        foreach ($res as $_r){
            if($_r['type'] == 0){
                $result[$_r['id']] = $_r['title'];
            }else{
                $bas_result[$_r['id']] = $_r['title'];
            }
        }

        return array($result, $bas_result);
    }

}
