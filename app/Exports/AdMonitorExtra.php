<?php


namespace App\Exports;


use Admin\Model\AdGroupModel;
use App\Models\Ad;

class AdMonitorExtra
{


    /**
     * 获取广告监测代码数据
     * @param  $adid_arr   广告 id  集合
    */
    public static function getMonitorExtraData($adid_arr = []){
        $result = [];
        try{
            if($adid_arr){
                $config = config('monitor');
                $domain_arr = Q($config, 'monitor_domain');   //域名
                $track_event_arr = Q($config, 'track_event');   //监测事件
                $domain = $domain_arr ? $domain_arr[0] : '';

                $adids = implode(',', $adid_arr);
                $ad_arr = [];
                if($adids){
                    $sql = "select adid, title from ads_ad where adid in ( " . $adids . " ) ";
                    $ad_res = app()->make(Ad::class)->fetchBySql($sql);
                    foreach ($ad_res as $_ad){
                        $ad_arr[$_ad['adid']] = Q($_ad, 'title');
                    }
                }

                if($domain && $ad_arr){
                    $head = ['广告ID', '广告标题', '投放代码'];
                    foreach ($adid_arr as $_k => $adid){
                        $ad_title = isset($ad_arr[$adid]) ? $ad_arr[$adid] : '';
                        $monitor_arr = [];
                        $url = 'https://dsp.'.$domain.'/mgtv/vastd?adid='.$adid.'&os=__OS__&ip=__IP__&m7=__MAC__&t=__TS__&mr=__MACR__&uuid=__UUID__&br=__BRANCH__&mn=__MN__&pos=__POS__&chn=__CHAN__&mac_md5_1=__MAC1__&mac_md5_2=__MAC2__&mac_md5_3=__MAC3__&mac_md5_4=__MAC4__&mac_md5_5=__MAC5__&app_version=__APP__&gppid=__GPPID__&cookie=${COOKIE}&version=__VERSION__&hiesid=__HIESID__&iesid=__IESID__&device_type=__DEVICETYPE__&ts_s=__GCORRELATOR__&ipdx=__IPDX__&ctref=__CTREF__&ua=__UA__&dra=__DRA__&sys=__SYSTEM__';
                        foreach ($track_event_arr as $_t){
                            if($_k == 0){
                                $head[] = Q($_t, 'name');
                            }
                            $monitor_arr[] = 'https://monitor.'.$domain.'/monitor/a.gif?et='.Q($_t, 'value').'&p=8002&a='.$adid.'&s=0&mac=__MAC__&n=__IP__&o=__OS__&t=__TS__&ni=__IESID__&nan=__APP__&ng=__CTREF__&macr=__MACR__&nd=__DRA__&dp=__BRANCH__&mn=__MN__&vr=__VERSION__&sys=__SYSTEM__&hdt=__DEVICETYPE__&ipdx=__IPDX__&uuid=__UUID__&uid=__UID__&hies=__HIESID__&pos=__POS__&adsid=${ADSPACE_ID}&camid=${CAMPAIGN_ID}&stid=${STRATEGY_ID}&creid=${CREATIVE_ID}&conid=${CONTENT_ID}&cook=${COOKIE}&chan=__CHAN__&url=__URL__&gppid=__GPPID__&adsts=__GCORRELATOR__&m6o=__M6O__&ua=__UA__&mck=__MCOOKIE__,h';
                        }
                        $row = [$adid, $ad_title, $url];
                        $row = array_merge($row, $monitor_arr);
                        if($_k == 0){
                            $result[] = $head;
                        }
                        $result[] = $row;
                    }

                }
            }

        }catch (\Exception $e){
            logger($e);
        }

        return $result;
    }
}
