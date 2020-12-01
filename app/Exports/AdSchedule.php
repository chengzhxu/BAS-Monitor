<?php


namespace App\Exports;


use App\Models\Ad;
use App\Models\Campaign;
use App\Tool\Tool;

class AdSchedule
{

    /**
     * 导出订单下的广告排期
     * @param $campaignid   订单id
     * @return array  ad schedule
    */
    public static function getAdScheduleByCampaign($campaignid = 0){
        $result = [];
        try{
            if($campaignid){
                $ad_list = app()->make(Ad::class)->fetchRows(['campaignid' => $campaignid], ['adid', 'title', 'time_start', 'time_end', 'schedule'], false);
                $time_start_arr = array_column($ad_list, 'time_start');
                $time_end_arr = array_column($ad_list, 'time_end');
                sort($time_start_arr);
                rsort($time_end_arr);
                $time_start = array_shift($time_start_arr);
                $time_end = array_shift($time_end_arr);

                $column = self::getScheduleColumn();
                $cols = array_column($column, 'name');

                $date_template = Tool::getScheduleTemplateByTime($time_start, $time_end);
                $date_list = Q($date_template, 'date_list') ? $date_template['date_list'] : [];
                $val_list = Q($date_template, 'val_list') ? $date_template['val_list'] : [];
                $result[] = array_merge($cols, $date_list);

                $ad_day_start = '';
                $ad_day_end = '';
                foreach ($ad_list as $_ad){
                    $item = [Q($_ad, 'adid'), Q($_ad, 'title')];
                    if(!$ad_day_start || !$ad_day_end){
                        $ad_day_start = conDateTime(Q($_ad, 'time_start'), true);
                        $ad_day_end = conDateTime(Q($_ad, 'time_end'), true);
                    }
                    $schedule = Q($_ad, 'schedule') ? json_decode($_ad['schedule'], true) : [];
                    $date_item = self::getScheduleAmount($date_list, $val_list, $schedule);
                    $result[] = array_merge($item, $date_item);
                }
            }
        }catch (\Exception $e){
            logger($e);
        }

        return $result;
    }

    /**
     * 获取广告排期日期排量
     * @param  $date_list   排期时间
     * @param  $val_list    排期时间
     */
    private static function getScheduleAmount($date_list = [], $val_list = [], $schedule = []){
        if($date_list && $val_list){
            $schedules = Q($schedule, 'schedules') ? $schedule['schedules'] : [];

            foreach ($date_list as $key => $val){
                $dw_date = date('ymd', strtotime($val));
                $amount = 0;
                foreach ($schedules  as $_s){
                    if($val == Q($_s, 'dw_date') || $dw_date == Q($_s, 'dw_date')){
                        $amount = intval(Q($_s, 'amount'));
                        break;
                    }
                }
                $val_list[$key] = $amount ? $amount : '0';
            }
        }

        return $val_list;
    }



    /**
     * 排期模板格式 （模板二）
     */
    public static function getScheduleColumn($str_cn = ''){
        $col_key = 'none_key';
        $excel_columns = [
            ['name' => '广告ID', 'bas_key' => 'adid'],
            ['name' => '广告名称', 'bas_key' => 'ad_title'],
        ];
        if($str_cn){
            foreach ($excel_columns as $key => $val){
                if(Q($val, 'name') == $str_cn){
                    return $val;
                }
            }
            return $col_key;
        }else{
            return $excel_columns;
        }
    }
}
