<?php


namespace App\Action;


use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class ExportCampaignExtra extends BatchAction
{

    public $name = '导出订单投放代码';

    public function handle(Collection $collection){
        try {
            $campaignid_arr = [];
            foreach ($collection as $_c){
                if(Q($_c, 'campaignid')){
                    $campaignid_arr[] = $_c['campaignid'];
                }
            }
            if(!$campaignid_arr){
                return $this->response()->error('导出失败：请选择要导出的订单!');
            }

            return $this->response()->success('导出成功.')->download('/admin/campaign/export_batch_extra/' . implode(',', $campaignid_arr));
        } catch (\Exception $e) {
            return $this->response()->error('导出失败：'.$e->getMessage());
        }
    }
}
