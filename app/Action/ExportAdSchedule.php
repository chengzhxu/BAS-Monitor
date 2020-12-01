<?php


namespace App\Action;


use Admin\Model\AdModel;
use Admin\Model\StatAppAdScheduleModel;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class ExportAdSchedule extends RowAction
{

    public $name = '导出排期';

    public function handle(Model $model){
        $campaignid = Q($model, 'campaignid');
        return $this->response()->success('导出成功.')->download('/admin/campaign/export_schedule/' . $campaignid);
    }

}
