<?php


namespace App\Action;


use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class ExportAdMonitor extends BatchAction
{
    //导出 Ad 监测代码


    public $name = '导出监测代码';

    public function handle(Collection $collection){
        try {
            $adid_arr = [];
            foreach ($collection as $_c){
                if(Q($_c, 'adid')){
                    $adid_arr[] = $_c['adid'];
                }
            }
            if(!$adid_arr){
                return $this->response()->error('导出失败：请选择要导出的数据!');
            }

            return $this->response()->success('导出成功.')->download('/admin/ad/export_batch_monitor/' . implode(',', $adid_arr));
        } catch (\Exception $e) {
            return $this->response()->error('导出失败：'.$e->getMessage());
        }
    }

}
