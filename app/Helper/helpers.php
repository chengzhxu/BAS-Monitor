<?php


if (! function_exists('Q')) {
    function Q(...$args)
    {
        $count = count($args);
        if($count > 1){
            $obj = null;
            if(isset($args[0]->{$args[1]})){
                $obj = $args[0]->{$args[1]};
            }elseif(isset($args[0][$args[1]])){
                $obj = $args[0][$args[1]];
            }
            if($count > 2){
                for($i = 2; $i < $count; $i++){
                    if(!isset($obj->{$args[$i]})){
                        if(isset($obj[$args[$i]])){
                            $obj = $obj[$args[$i]];
                        }else{
                            return null;
                        }
                    }else{
                        $obj = $obj->{$args[$i]};
                    }
                }
            }
            return $obj;
        }
        return $args[0] ?? null;
    }
}

if(!function_exists('conDateTime')){
    function conDateTime($date, $onlyDay = false, $format = ''){
        if(!$format){
            $format = 'Y-m-d H:i:s';
        }
        if($onlyDay){
            $format = 'Y-m-d';
        }
        return date($format, strtotime($date));
    }
}

if(!function_exists('array_combine_v')){
    function array_combine_v($arr1, $arr2){
        $new_arr = [];
        foreach ($arr1 as $k => $v){
            if(array_key_exists($v, $new_arr)){
                $xv = $new_arr[$v];
                if(is_array($xv)){
                    array_push($xv, $arr2[$k]);
                    $new_arr[$v] = $xv;
                    $arr2[$k] = $xv;
                }else{
                    $nv = [];
                    array_push($nv, $xv);
                    array_push($nv, $arr2[$k]);
                    $new_arr[$v] = $nv;
                    $arr2[$k] = $nv;
                }
            }else{
                $nv = [
                    $v => $arr2[$k]
                ];
                if($new_arr){
                    $new_arr = array_merge($new_arr, $nv);
                }else{
                    $new_arr = $nv;
                }

            }
        }
        $result = array_combine($arr1, $arr2);
        return $result;
    }
}


if(!function_exists('is_date')){
    function is_date($dateString){
        return strtotime( date('Y-m-d', strtotime($dateString)) ) === strtotime( $dateString );
    }
}



/**
 * @param string $file_name excel表的表名
 * @param array $data 要导出excel表的数据，接受一个二维数组
 * @param array $head excel表的表头，接受一个一维数组
 * @param array $user_style 列样式
 * @param string $sheet_name sheet名字
 * @throws \PhpOffice\PhpSpreadsheet\Exception
 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
 */
if(!function_exists('exportExcel')){
    function exportExcel($file_name = '', $data = [], $head = [],$format = "xlsx", $user_style = [], $sheet_name = '', $merge_col = []){
        set_time_limit(0);
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        if($sheet_name){
            if(strlen($sheet_name) > 30){
                $sheet_name = mb_strimwidth($sheet_name, 0, 28, "...");
            }
            $sheet->setTitle($sheet_name);
        }

//        $sheet->setTitle('表名');
        $letter = 'A';
        foreach($head as $values){
            $sheet->setCellValue($letter.'1', $values);
            ++$letter;
        }
        if($user_style){
            foreach ($user_style as $key => $val){
                if(Q($val, 'column') && Q($val, 'value')){
                    $sheet->getColumnDimension(Q($val, 'column'))->setWidth(Q($val, 'value'));
                }
            }
        }
        if(is_array($data)){
            foreach($data as $k=>$v){
                $letter = 'A';
                $k = $k+2;
                reset($head);
                foreach($head as $key=>$value){
                    $testKey = explode('.',$key);
                    if(count($testKey)>1){
                        $val = $v[$testKey[0]][$testKey[1]];
                    }else{
                        $v = array_values($v);

                        $val = isset($v[$key]) ? $v[$key] : '';
                    }
                    if($merge_col){
                        foreach($merge_col as $mk=>$mv) {
                            $sheet->setCellValue($letter.$k, $val);
                            $sheet->mergeCells($mk.':'.$mv);
                        }
                    }else{
                        $sheet->setCellValue($letter.$k, $val);
                    }
                    ++$letter;
                }
            }
        }
        ob_end_clean();
        if ($format == 'xls') {
            //输出Excel03版本
            header('Content-Type:application/vnd.ms-excel');
            $class = "\PhpOffice\PhpSpreadsheet\Writer\Xls";
        } elseif ($format == 'xlsx') {
            //输出07Excel版本
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $class = "\PhpOffice\PhpSpreadsheet\Writer\Xlsx";
        }
        //输出名称
        header('Content-Disposition:attachment;filename="'.mb_convert_encoding($file_name,"GB2312", "utf-8").'.'.$format.'"');
        //禁止缓存
        header('Cache-Control: max-age=0');
        $writer = new $class($spreadsheet);
//        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        $writer->save('php://output');
//
        //删除清空：
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }
}
