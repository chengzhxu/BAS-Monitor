<?php


namespace App\Tool;



use App\Exports\AdExport;
use Maatwebsite\Excel\Facades\Excel;

class Tool
{
    public function getExcelColumn($str_cn = ''){
        $col_key = 'none_key';
        $excel_columns = [
            ['name' => '广告名称', 'bas_key' => 'ad_title', 'example' => '广告名称', 'strict_nom_empty' => 1,'is_view' => 1, 'is_model' => 1],
            ['name' => '广告开始时间', 'bas_key' => 'ad_time_start', 'necessary' => 1, 'format' => 'yyyy-mm-dd hh:ii:ss 例如：2019-08-01 00:00:00', 'example' => '2019-08-01 00:00:00',
                'column_number_format' => 'yyyy-mm-dd hh:mm:ss', 'strict_nom_empty' => 1,'is_view' => 1, 'is_model' => 1],
            ['name' => '广告结束时间', 'bas_key' => 'ad_time_end', 'necessary' => 1, 'format' => 'yyyy-mm-dd hh:ii:ss 例如：2019-08-31 23:59:59', 'example' => '2019-08-31 23:59:59',
                'column_number_format' => 'yyyy-mm-dd hh:mm:ss', 'strict_nom_empty' => 1,'is_view' => 1, 'is_model' => 1],
            ['name' => '地区', 'bas_key' => 'ad_region', 'example' => '上海','format' => '若有多个地域名称或region_code，请半角逗号分割，例如：130000,130500,上海市','is_view' => 1, 'is_model' => 1],
            ['name' => '广告用途', 'bas_key' => 'format', 'necessary' => 1, 'format' => '可选参数:[开屏, 退出， 多贴， 暂停， 角标]', 'example' => '多贴', 'strict_nom_empty' => 1,'is_view' => 0, 'is_model' => 1],
            ['name' => '监测方式', 'bas_key' => 'monitor_type', 'necessary' => 1, 'format' => '可选参数:[1, 2, 4]；参数说明:1 =>常规投放；2 => 第三方PDB；3 => BAS-PDB；4 => 无第三方监测', 'example' => 1, 'strict_nom_empty' => 1,'is_view' => 1, 'is_model' => 1],
            ['name' => 'BAS监测', 'bas_key' => 'bas_monitor_type', 'necessary' => 1, 'format' => '可选参数:[0, 1, 2]；参数说明:0 =>无；1 =>BAS-常规监测；2 => BAS-PDB；', 'example' => 0, 'strict_nom_empty' => 1,'is_view' => 1, 'is_model' => 1],
            ['name' => 'APP', 'bas_key' => 'appid', 'necessary' => 1, 'example' => '', 'strict_nom_empty' => 1, 'format' => '若有多个APPID或名称，请半角逗号分割，例如：8001,8002,芒果TV','is_view' => 1, 'is_model' => 1],
            ['name' => '素材类型', 'bas_key' => 'resource_type', 'necessary' => 1,'strict_nom_empty' => 1, 'format' => '可选参数:[2, 4, 6 ] ; 参数说明:2 =>图片；4 => 视频； 6 => VAST', 'example' => '4', 'strict_nom_empty' => 1,'is_view' => 0, 'is_model' => 1],
            ['name' => '素材地址', 'bas_key' => 'asset_url', 'necessary' => 1, 'strict_nom_empty' => 1,'is_view' => 1, 'is_model' => 1],
            ['name' => '素材播放时长', 'bas_key' => 'seconds', 'necessary' => 1,'strict_nom_empty' => 1, 'format' => '单位秒', 'example' => '15','is_view' => 0, 'is_model' => 1],
            ['name' => '监测-start', 'bas_key' => 'start', 'allow_multi' => 1, 'format' => '若有多个相同事件监测，该字段可出现多次','is_view' => 0, 'is_model' => 1, 'is_event' => 1],
            ['name' => '监测-firstQuartile', 'bas_key' => 'firstQuartile', 'allow_multi' => 1, 'format' => '若有多个相同事件监测，该字段可出现多次','is_view' => 0, 'is_model' => 1, 'is_event' => 1],
            ['name' => '监测-midpoint', 'bas_key' => 'midpoint', 'allow_multi' => 1, 'format' => '若有多个相同事件监测，该字段可出现多次','is_view' => 0, 'is_model' => 1, 'is_event' => 1],
            ['name' => '监测-thirdQuartile', 'bas_key' => 'thirdQuartile', 'allow_multi' => 1, 'format' => '若有多个相同事件监测，该字段可出现多次','is_view' => 0, 'is_model' => 1, 'is_event' => 1],
            ['name' => '监测-complete', 'bas_key' => 'complete', 'allow_multi' => 1, 'format' => '若有多个相同事件监测，该字段可出现多次','is_view' => 0, 'is_model' => 1, 'is_event' => 1],
        ];
        if(trim($str_cn)){
            foreach ($excel_columns as $key => $val){
                if(Q($val, 'name') == trim($str_cn)){
                    return $val;
                }
            }
            return $col_key;
        }else{
            return $excel_columns;
        }
    }



    /**
     * 获取上传文件广告信息
     * @param campaign_id  系列id
     * @param file  上传文件
     */
    public function getMultiAdByFile($file){
        $errData = [];

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

        $spreadsheet = $reader->load($file['tmp_name']);

        $sheet = $spreadsheet->getSheet(0);     //获取第一个 sheet 的内容

        $res = array();
        $arr_key = [];
        $arr_title = [];
        $file_title = [];
        $suc_data = [];
        $tem_data = [];
        $us_inx = [];
        $ln = 0;
        $is_empty = [];
        $is_merge_arr = [];     //当前字段是否可以合并  如 频控
        $arr_name = [];
        $fix_col = [];
        $ast_events = [];      //素材 与 监测关系  (针对多素材，多监测)
        $event_arr = [];      //当前 文件包含的监测事件

        foreach ($sheet->getRowIterator(1) as $row) {
            $tmp = array();
            foreach ($row->getCellIterator() as $cell) {
                $tmp[] = $cell->getFormattedValue();
            }
            if($ln == 0){
                //根据title获取对应的key值
                $arr_title = $tmp;
                $file_title = $tmp;
                foreach ($tmp as $tk => $tv){
                    $tv = trim($tv);
                    if(!in_array($tv, $fix_col)){
                        $fix_col[] = $tv;
                    }
                    $columns = $this->getExcelColumn($tv);
                    if(Q($columns, 'strict_nom_empty') == 1 && !in_array($tv, $fix_col)){
                        array_push($is_empty, 1);
                    }else{
                        array_push($is_empty, 0);
                    }
                    if(Q($columns, 'is_event') == 1 && !in_array(Q($columns, 'bas_key'), $event_arr)){
                        $event_arr[] = Q($columns, 'bas_key');
                    }
                    if(Q($columns, 'is_merge') == 1){
                        array_push($is_merge_arr, 1);
                    }else{
                        array_push($is_merge_arr, 0);
                    }
                    array_push($arr_name, Q($columns, 'name'));
                    $tmp[$tk] = Q($columns, 'bas_key');
                    if(Q($columns, 'is_view') != 1){
                        unset($arr_title[$tk]);
                        array_push($us_inx, $tk);
                    }
                }
                $arr_title = array_values($arr_title);
                $arr_key = $tmp;
            }else{
//                    array_combine_v($arr_key, $tmp);
                foreach ($tmp as $tk => $tv){
                    $is_null = Q($is_empty, $tk) ? Q($is_empty, $tk) : 0;
                    $is_merge = Q($is_merge_arr, $tk) ? Q($is_merge_arr, $tk) : 0;
                    if((trim($tv) == '' || trim($tv) == null)) {
                        $row = $ln + 1;
                        $col = $tk + 1;

                        $n_tv = '';
                        if ($is_merge) {    //允许合并的列
                            $n_row = $row - 3;
                            $n_col = $col - 1;
                            $n_tv = isset($tem_data[$n_row][$n_col]) ? Q($tem_data, $n_row, $n_col) : '';     //获取当前列 上一行的值
                        }
                        if (!$n_tv && $is_null == 1) {
                            $col_title = Q($arr_name, $tk) ? $arr_name[$tk] : '第' . $col . '列';
                            array_push($errData, '第' . $row . '行，' . $col_title . '存在空值，请检查！');
                        } else {
                            $tmp[$tk] = $n_tv;
                        }
                    }
                }
                if(!$ast_events){
                    $event_inx = [];
                    foreach ($event_arr as $_er){
                        $event_inx[$_er] = 0;
                    }
                    $ai = -1;
                    foreach ($arr_key as $_ak => $_av){
                        if($_av == 'asset_url'){
                            $ai++;
                        }
                        if(in_array($_av, $event_arr)){
                            $event_i = intval(Q($event_inx, $_av));
                            $ast_events[$ai][$_av][] = $event_i;
                            $event_inx[$_av] = $event_i + 1;
                        }
                    }
                }

                $res[] = array_combine_v($arr_key, $tmp);

                $tem_data[] = $tmp;
                if($us_inx){
                    foreach ($us_inx as $ui){
                        unset($tmp[$ui]);
                    }
                }
                $tmp = array_values($tmp);
                $suc_data[] = $tmp;
            }
            $ln++;
        }

        return ['file_data' => $res, 'asset_events' => $ast_events, 'arr_title' => $arr_title, 'file_title' => $file_title, 'tem_data' => $tem_data, 'suc_data' => $suc_data, 'errData' => $errData];
    }




    public static function validateTrackEvent($data){
        return array_unique(array_values($data), SORT_REGULAR);
    }


    /**
     * 排期日期
    */
    public static function getScheduleTemplateByTime($start_time = '', $end_time = ''){
        $date_list = [];
        $val_list = [];
        if($start_time && $end_time){
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time);

            while ($start_time < $end_time){
                array_push($date_list, date('Y-m-d',$start_time));
                array_push($val_list, 0);
                $start_time += 1*24*60*60;
            }
        }

        return ['date_list' => $date_list, 'val_list' => $val_list];
    }


    /**
     * 导出 excel
    */
    public static function exportExcel($file_name = '', $data = []){
        if($data){
            $file_name = $file_name ? $file_name : '导出文件';
            return Excel::download(new AdExport($data),  $file_name .'.xlsx');
        }
    }
}
