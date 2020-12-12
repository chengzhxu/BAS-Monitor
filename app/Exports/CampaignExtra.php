<?php


namespace App\Exports;


use App\Models\Ad;
use App\Models\Campaign;

class CampaignExtra
{

    /**
     * 获取订单投放数据
     * @param   $campaignid_arr   订单 id 集合
     * @return   array
    */
    public static function getCampaignExtraData($campaignid_arr = []){
        $result = [];
        try{
            if($campaignid_arr){
                $config = config('monitor');
                $campaign_extra_arr = Q($config, 'campaign_extra');   //订单投放配置
                $put_url = Q($campaign_extra_arr, 'put_url');   //订单投放代码地址

                $campaignids = implode(',', $campaignid_arr);
                $campaign_arr = [];
                if($campaignids){
                    $sql = "select campaignid, title from ads_campaign where campaignid in ( " . $campaignids . " ) ";
                    $campaign_res = app()->make(Campaign::class)->fetchBySql($sql);
                    foreach ($campaign_res as $_c){
                        $campaign_arr[$_c['campaignid']] = Q($_c, 'title');
                    }
                }

                if($put_url){
                    $result[] = ['订单ID', '订单标题', '投放代码'];
                    foreach ($campaignid_arr as $_cid){
                        $campaign_title = isset($campaign_arr[$_cid]) ? $campaign_arr[$_cid] : '';
                        $_url = str_replace("{CAMPAIGN_ID}", $_cid, $put_url);
                        $result[] = [$_cid, $campaign_title, $_url];
                    }
                }
            }
        }catch (\Exception $e){
            logger($e);
        }

        return $result;
    }
}
