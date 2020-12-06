<?php


namespace App\Models;


use Admin\Model\AssetModel;
use App\Tool\Tool;
use Illuminate\Support\Facades\DB;
use Zeed\Config\Config;

class Cap extends  BasModel
{
    protected $table = 'ads_cap';

    protected $primaryKey = 'capid';

    protected $fillable = [
        're_conf'
    ];

    protected $casts = [
        're_conf' => 'json',
    ];


    /**
     * 解析频控信息
     * @param array $cap_arr 频控信息
    */
    public function getCapDetail($cap_arr = []){
        $limit_arr = [];
        if($cap_arr){
            $config = config('monitor');
            $limit_detail = Q($config, 'limit_detail');

            foreach ($cap_arr as $ck => $cv){
                foreach ($limit_detail as $key => $val){
                    if(Q($cv, 'type') == Q($val, 'value')){
                        $limit_arr[] = Q($val, 'name') . ':' . Q($cv, 'value');
                    }
                }
            }
        }

        return $limit_arr;
    }


    /**
     * 获取频控选择
    */
    public function getCapSelect(){
        $cap_arr = [];
        $config = config('monitor');
        $limit_detail = Q($config, 'limit_detail');
        foreach ($limit_detail as $key => $val){
            $cap_arr[$val['value']] = $val['name'];
        }

        return $cap_arr;
    }

}
