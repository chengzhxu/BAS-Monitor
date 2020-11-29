<?php


namespace App\Models;


use Illuminate\Support\Facades\DB;

class Campaign extends  BasModel
{
    protected $table = 'ads_campaign';

    protected $primaryKey = 'campaignid';

    protected $fillable = [
        'title'
    ];


    public function adCampaign()
    {
        return $this->hasMany('App\Models\AdCampaign', 'campaignid', 'campaignid');
    }


    public function getAllCampaign($is_trance = false){
        $sql = "select * from " . $this->getTable();
        $res = $this->fetchBySql($sql);

        if(!$is_trance){
            return $res;
        }

        $result = [];
        foreach ($res as $_r){
            $result[$_r['campaignid']] = $_r['title'];
        }

        return $result;
    }



    /**
     * 批量创建广告 - 文件上传
     * @param array $data     广告内容
     * @param array $asset_events    素材监测
     * @param int $campaignid 订单id
    */
    public function uploadSaveAd($data = [], $asset_events = [], $campaignid = 0){
        DB::beginTransaction();
        $errData = [];
        try {
            if(!$campaignid){
                return '订单传入失败';
            }else{
                $format_list = app()->make(Format::class)->getAllFormat();
                foreach ($data as $_k => $_row){
                    $ad_time_start = conDateTime(Q($_row, 'ad_time_start'));
                    $ad_time_end = conDateTime(Q($_row, 'ad_time_end'), false, 'Y-m-d 23:59:59');
                    if(!Q($_row, 'ad_title') || !$ad_time_start || !$ad_time_end){
                        $errData[] = '请确认第【' . ($_k + 1) . '】行的广告名称和时间是否填写完整';
                        continue;
                    }
                    $ad_regions = Q($_row, 'ad_region') ? explode(',', $_row['ad_region']) : [];
                    $region_codes = [];  //地区
                    foreach ($ad_regions as $ad_region){
                        $r_sql = "select * from t_region_code where region_name like '%" . $ad_region . "%'" ;
                        $regions = app()->make(Region::class)->fetchBySql($r_sql);
                        if(!$regions){
                            $errData[] = '第【' . ($_k + 1) . '】行的地域信息未找到';
                            continue;
                        }
                        if(count($regions) == 1){
                            $region_codes[] = $regions[0]['region_code'];
                        }else{
                            foreach ($regions as $_reg){
                                if($_reg['region_name'] == $ad_region){
                                    $region_codes[] = $_reg['$region_code'];
                                    break;
                                }
                            }
                        }
                    }
                    $formatid = 0;  //广告形式
                    if(is_numeric(Q($_row, 'format'))){
                        $formatid = intval(Q($_row, 'format'));
                    }else{
                        foreach ($format_list as $_f){
                            if($_f['title'] == Q($_row, 'format')){
                                $formatid = $_f['formatid'];
                                break;
                            }
                        }
                    }
                    $appids = Q($_row, 'appid') ? explode(',', $_row['appid']) : [];  //app

                    $re_conf = ['region_code' => $region_codes, 'appid' => $appids];

                    $ad = [
                        'title' => $_row['ad_title'],
                        'campaignid' => $campaignid,
                        'time_start' => $ad_time_start,
                        'time_end' => $ad_time_end,
                        'formatid' => $formatid,
                        'monitor_id' => intval(Q($_row, 'monitor_type')),
                        'bas_monitor_id' => intval(Q($_row, 'bas_monitor_type')) + 100,
                        're_conf' => json_encode($re_conf)
                    ];
                    if(!app()->make(Ad::class)->addOne($ad)){
                        $errData[] = '第【' . ($_k + 1) . '】行的广告创建失败，请检查';;
                    }
                }
            }
        }catch (\Exception $e){
            DB::rollBack();
            logger($e);
            $errData[] = $e->getMessage();
        }
        if(!$errData){
            DB::commit();
            return true;
        }else{
            return implode(';', $errData);
        }
    }
}
