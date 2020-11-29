<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class BasModel extends Model
{
    protected $table = '';


    public function addOne($data = []){
        try {
            if($data){
                return DB::table($this->getTable())->insertGetId($data);
            }
        }catch (\Exception $e){
            logger($e);
        }

        return 0;
    }

    public function delById($id = 0){
        try {
            if($id){
                return DB::table($this->getTable())->delete($id);
            }
        }catch (\Exception $e){
            logger($e);
        }

        return 0;
    }

    public function fetchRows($where = [], $column = [], $is_one = true){
        $result = [];
        try {
            if(!$column){
                $column = ['*'];
            }
            $query = DB::table($this->getTable())->where($where);
            if($is_one){
                $result = $query->first($column);
            }else{
                $result = $query->get($column)->toArray();
            }
        }catch (\Exception $e){
            logger($e);
        }

        return json_decode(json_encode($result),true);
    }


    public function fetchRowsByColumn($key = '', $val = '', $column = [], $is_one = true){
        $result = [];
        try {
            if(!$column){
                $column = ['*'];
            }
            $query = DB::table($this->getTable());
            if($key && $val){
                $query = $query->where($key, $val);
            }
            if($is_one){
                $result = $query->first($column);
            }else{
                $result = $query->get($column)->toArray();
            }

        }catch (\Exception $e){
            logger($e);
        }

        return json_decode(json_encode($result),true);
    }


    public function fetchBySql($sql = ''){
        $result = [];
        try {
            if($sql){
                $result = DB::select($sql);
            }
        }catch (\Exception $e){
            logger($e);
        }

        return json_decode(json_encode($result),true);;
    }
}
