<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BasModel extends Model
{
    protected $table = '';
    protected $primaryKey = '';


    /**
     * 新增数据
     * @param $data  entity
     * @return  int  id
    */
    public function addOne($data = []){
        try {
            if($data){
                $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s');
                return DB::table($this->getTable())->insertGetId($data);
            }
        }catch (\Exception $e){
            logger($e);
        }

        return 0;
    }


    public function updateByPrimary($id = 0, $data = []){
        try{
            if($id && $data){
                $data['updated_at'] = date('Y-m-d H:i:s');
                return DB::table($this->getTable())->where($this->primaryKey, $id)->update($data);
            }
        }catch (\Exception $e){
            logger($e);
        }

        return 0;
    }


    /**
     * 根据主键获取数据
     * @param $id
     * @return entity
    */
    public function fetchRowByPrimary($id = 0){
        $result = [];
        if($id && $this->primaryKey){
            $result = DB::table($this->getTable())->where($this->primaryKey, $id)->first();
        }

        return json_decode(json_encode($result),true);
    }


    /**
     * 根据主键删除数据
     * @param  $id
     * @return int
    */
    public function delById($id = 0){
        try {
            if($id){
                return DB::table($this->getTable())->where($this->primaryKey, $id)->delete();
            }
        }catch (\Exception $e){
            logger($e);
        }

        return 0;
    }


    /**
     * 根据条件获取相关数据
     * @param $where    条件
     * @param $column   返回列
     * @param $is_one   返回数量 是否一条
     * @return  array
    */
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


    /**
     * 根据 column 获取相关数据
     * @param $key   column
     * @param $val   值
     * @param $column   返回列
     * @param $is_one  是否返回一条
     * @return  array
    */
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


    /**
     * 根据 sql 获取相关数据
     * @param $sql
     * @return  array
    */
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
