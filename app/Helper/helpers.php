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
