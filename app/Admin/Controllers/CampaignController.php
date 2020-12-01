<?php

namespace App\Admin\Controllers;

use App\Action\ExportAdSchedule;
use App\Action\ImportAdSchedule;
use App\Exports\AdSchedule;
use App\Models\Ad;
use App\Models\Campaign;
use App\Tool\Tool;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\MessageBag;

class CampaignController extends AdminAbstract
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Campaign';


    /**
     * 广告列表
     */
    public function listing(Content $content){
        $grid = new Grid(new Campaign());

        $grid->model()->orderBy('campaignid','desc');
        $grid->quickSearch('title');

        $grid->column('campaignid' ,'Id')->sortable();
        $grid->column('title', '标题');

        $grid->filter(function ($filter){
            $filter->disableIdFilter();

            // 添加新的字段过滤器（通过标题过滤）
            $filter->like('title', '标题');
        });


        //禁用行操作列
//        $grid->disableActions();

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });

//            $tools->append(
//                '<a href="/admin/campaign/import_schedule" class ="btn btn-sm btn-success"">导入</a>
//            ');
            $tools->append(new ImportAdSchedule());
        });

        $grid->actions(function ($actions) {
            // 去掉删除
            $actions->disableDelete();
            // 去掉查看
            $actions->disableView();

            $actions->add(new ExportAdSchedule());
        });

        return $content
            ->header('Campaign')
            ->description('订单列表')
            ->body($grid);
    }


    /**
     * 批量创建广告
    */
    public function create(Content $content){
        $ad = new Ad();
        $form = new Form($ad);

        $form->setAction('/admin/campaign/upload_ad');

        $form->text('campaign_title', '订单名称');
//        $form->multipleSelect('regions')->options(Region::all()->pluck('region_name', 'region_code'));
        $form->file('campaign_file', '文件');

        $form->tools(function (Form\Tools $tools) {
            // 去掉`删除`按钮
            $tools->disableDelete();
            // 去掉`查看`按钮
            $tools->disableView();
        });

        $form->footer(function ($footer) {
            // 去掉`查看`checkbox
            $footer->disableViewCheck();
            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();
            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();
        });

        return $content
            ->header('Create')
            ->description('批量创建广告')
            ->body($form);
    }

    /**
     * 批量创建订单-ad
    */
    public function upload_ad(Content $content){
        try {
            $file = $_FILES['campaign_file'];

            $err_msg = $this->validateUploadAd($this->request->all());
            $campaignid = 0;
            if(!$err_msg){
                $tool = new Tool();
                $file_result = $tool->getMultiAdByFile($file);
                $errData = Q($file_result, 'errData') ? $file_result['errData'] : [];
                if(!$errData){
                    $file_data = Q($file_result, 'file_data') ? $file_result['file_data'] : [];
                    $asset_events = Q($file_result, 'asset_events') ? $file_result['asset_events'] : [];
//                    $arr_title = Q($file_result, 'arr_title') ? $file_result['arr_title'] : [];
//                    $file_title = Q($file_result, 'file_title') ? $file_result['file_title'] : [];
//                    $suc_data = Q($file_result, 'suc_data') ? $file_result['suc_data'] : [];

                    $campaignid = app()->make(Campaign::class)->addOne(['title' => $this->request->get('campaign_title')]);
                    if($campaignid){
                        $result = app()->make(Campaign::class)->uploadSaveAd($file_data, $asset_events, $campaignid);
                        if($result !== true){
                            $err_msg = $result;
                        }
                    }else{
                        $err_msg = '创建订单失败';
                    }
                }else{
                    $err_msg = implode(';', $errData);
                }
            }
            if(!$err_msg){
                return redirect('/admin/campaign/');
            }

            app()->make(Campaign::class)->delById($campaignid);
            $error = new MessageBag([
                'title'   => '提示：',
                'message' => $err_msg,
            ]);
            return back()->with(compact('error'));
//            return $this->response()->warning($err_msg);
        }catch (\Exception $e){
            logger($e);
        }
    }


    private function validateUploadAd($data = []){
        if(!Q($data, 'campaign_title')){
            return '订单名称不能为空';
        }
        if(app()->make(Campaign::class)->fetchRowsByColumn('title', $data['campaign_title'])){
            return '名称为【' . $data['campaign_title'] . '】的订单已存在';
        }

        return '';
    }



    /**
     * 导出订单下的 ad 排期
     */
    public function export_schedule($campaignid){
        $result = [];
        $file_name = '广告排期';
        if($campaignid){
            $campaign = app()->make(Campaign::class)->fetchRowByPrimary($campaignid);
            $file_name = Q($campaign, 'title') . '-' . $file_name;
            $result = AdSchedule::getAdScheduleByCampaign($campaignid);
        }

        return Tool::exportExcel($file_name, $result);
    }

    /**
     * 导入广告排期文件
     */
    public function import_schedule(){
        dd(1122);
    }
}
