<?php


namespace App\Action;


use App\Exports\AdSchedule;
use App\Models\Ad;
use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportAdSchedule extends Action
{
    public $name = '导入排期';

    protected $selector = '.import-schedule';

    public function handle(Request $request){
        try {
            $file = $request->file('file');
            if(!$file){
                return $this->response()->warning('排期文件上传失败');
            }
            //$file = $_FILES['file'];
            $file_result = self::importSchedule($file);    //获取排期文件内容

            $errData = Q($file_result, 'errData') ? $file_result['errData'] : [];
            if($errData){
                return $this->response()->warning('导入失败：' . implode(';', $errData))->timeout(7000);
            }
            $file_data = Q($file_result, 'file_data') ? $file_result['file_data'] : [];
            $result = self::writeScheduleToAd($file_data);
            if($result === true){    //导入成功
                return $this->response()->success('导入成功');
            }else{
                return $this->response()->warning('导入失败：' . implode(';', $result))->timeout(7000);
            }
        } catch (\Exception $e) {
            return $this->response()->error('导入失败：'.$e->getMessage());
        }
    }

    //表单
    public function form(){
        $this->file('file', '请选择排期文件');
    }

    public function html(){
        //按钮的样式，你可以自定义，包括按钮名称
        return <<<HTML
            <a class="btn btn-sm btn-success import-schedule">$this->name</a>
HTML;
    }



    /**
     * 解析排期文件数据
     * @param $file   排期文件内容
     * @return array
    */
    private static function importSchedule($file = null){
        $res = [];
        $errData = [];
        $tem_data = [];
        $arr_title = [];
        if($file){
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file);
            $sheet = $spreadsheet->getActiveSheet();

            $arr_key = [];
            $ln = 0;
            $arr_name = [];

            foreach ($sheet->getRowIterator(1) as $row) {
                $tmp = array();
                foreach ($row->getCellIterator() as $cell) {
                    $tmp[] = $cell->getFormattedValue();
                }
                if($ln == 0){
                    //根据title获取对应的key值
                    $arr_title = $tmp;
                    foreach ($tmp as $tk => $tv){
                        if(is_date($tv)){
                            $tmp[$tk] = $tv;
                        }else{
                            $columns = AdSchedule::getScheduleColumn($tv);
                            $arr_name[] = Q($columns, 'name');
                            $tmp[$tk] = Q($columns, 'bas_key');
                        }
                    }
                    $arr_key = $tmp;
                }else{
                    foreach ($tmp as $tk => $tv){
                        if(strlen(trim($tv)) == 0 && isset($arr_name[$tk]) && $arr_name[$tk]){
                            $row = $ln + 1;
                            $col = $tk + 1;
                            $col_title = Q($arr_name, $tk) ? $arr_name[$tk] : '第' . $col . '列';
                            $errData[] = '第' . $row . '行，' . $col_title . '存在空值，请检查！';
                        }
                    }
                    $res[] = array_combine_v($arr_key, $tmp);

                    $tmp = array_values($tmp);
                    $tem_data[] = $tmp;
                }
                $ln++;
            }
        }

        return ['file_data' => $res, 'file_title' => $arr_title, 'tem_data' => $tem_data, 'errData' => $errData];
    }


    /**
     * 写入排期文件数据到 ad
     * @param $data    排期文件数据
     * @return bool
    */
    private static function writeScheduleToAd($data = []){
        DB::beginTransaction();
        $errData = [];
        try{
            $ad_res = [];
            $adid_arr = array_column($data, 'adid');
            if($adid_arr){
                $ad_list = Ad::all(['adid', 'time_start', 'time_end', 'schedule'])->whereIn('adid', $adid_arr)->all();
                foreach ($ad_list as $_ad){
                    $ad_res[$_ad['adid']] = $_ad;
                }
            }

            foreach ($data as $item){
                $adid = Q($item, 'adid');
                $keys = array_keys($item);
                if($adid){
                    $ad = isset($ad_res[$adid]) ? $ad_res[$adid] : [];
                    $ad_schedule = isset($ad['schedule']) ? $ad['schedule'] : [];
                    $schedules = [];
                    $date_sum = [];
                    foreach ($keys as $key){
                        if(is_date($key)){
                            $amount = str_replace(',', '', trim(Q($item, $key)));    //替换逗号，去除千分位
                            if(!is_numeric($amount) || floatval($amount) < 0){
                                $errData[] = '文件中ID为【' . $adid . '】的广告排期数量填写错误(必须为大于0的数值)';
                            }else{
                                if(is_numeric($amount) && floatval($amount) > 0){
                                    $dw_date = conDateTime($key, true);
                                    $check_schedule = self::checkSchedules($schedules, $dw_date);
                                    if($check_schedule === true){
                                        if(strtotime($dw_date) < strtotime(Q($ad, 'time_start')) || strtotime($dw_date) > strtotime(Q($ad, 'time_end'))){
                                            $errData[] = '文件中ID为【' . $adid . '】的广告排期日期【'. $dw_date . '】不在广告投放时间段内！';
                                        }else{
                                            $num = number_format($amount, 3, '.', '');
                                            $schedules[] = [
                                                'appid' => "",
                                                'amount' => $num,
                                                'dw_date' => $dw_date,
                                                'regioncode' => ""
                                            ];
                                            $date_sum[$dw_date] = $num;
                                        }
                                    }else{
                                        $errData[] = '文件中ID为【' . $adid . '】的广告存在多个日期为【'. $dw_date . '】的排期！';
                                    }
                                }
                            }
                        }
                    }
                    if(!$errData){
                        $ad_schedule['schedules'] = $schedules;
                        $ad_schedule['date_sum'] = $date_sum;
                        if(!app()->make(Ad::class)->updateByPrimary($adid, ['schedule' => json_encode($ad_schedule)])){
                            $errData[] = '文件中ID为【' . $adid . '】的广告更新排期失败！';
                            continue;
                        }
                    }
                }
            }
        }catch (\Exception $e){
            logger($e);
            DB::rollBack();
            return [$e->getMessage()];
        }
        if(!$errData){
            DB::commit();
            return true;
        }

        DB::rollBack();
        return $errData;
    }

    private static function checkSchedules($schedules, $dw_date){
        if($schedules && $dw_date){
            foreach ($schedules as $key => $schedule){
                if(Q($schedule, 'dw_date') == $dw_date){
                    return ['inx' => $key, 'amount' => Q($schedule, 'amount') ? $schedule['amount'] : 0];
                }
            }
        }

        return true;
    }
}
