<?php

namespace App\Admin\Controllers;

use App\Models\Ad;
use App\Models\Campaign;
use App\Models\Format;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

class AdController extends AdminAbstract
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Users';


    /**
     * 广告列表
    */
    public function listing(Content $content){
        $grid = new Grid(new Ad());

        //广告形式过滤
        $format_list = app()->make(Format::class)->getAllFormat(true);
        $campaign_list = app()->make(Campaign::class)->getAllCampaign(true);

        $grid->filter(function ($filter) use ($format_list, $campaign_list){
            $filter->disableIdFilter();

            // 添加新的字段过滤器（通过标题过滤）
            $filter->equal('id', 'AdID');
            $filter->like('title', '标题');
            $filter->in('formatid', '广告形式')->multipleSelect($format_list);
            $filter->in('campaignid', '订单')->multipleSelect($campaign_list);
        });

        $grid->model()->orderBy('adid','desc');
        $grid->quickSearch('title');

        $grid->column('adid' ,'AdID')->sortable();
        $grid->column('title', '标题');
        $grid->column('campaign.title', '所属订单');
        $grid->column('time_start', '开始时间');
        $grid->column('time_end', '结束时间');
        $grid->column('format.title', '广告形式');
        $grid->column('monitorType.title', '监测方式');
        $grid->column('basMonitorType.title', 'BAS监测');

//        $grid->tools(function ($tools){
//            $tools->append(
//                '<a href="excel" class ="btn btn-sm btn-success"">导入</a>
//            ');
//        });

        $grid->column('Region')->display(function () {
            return '我是地区';
        });

        $a = 'ABC';

        $grid->column('re_conf', '定向')->display(function ($re_conf) use ($a) {
            $rf = json_encode($re_conf);
            return "<span class='label label-warning'>{$a}</span>" . "-xx";
        });

        $grid->rows(function ($row){
            if($row->number%2 == 0){
                $row->style("background-color:#eee");
            }
        });


        return $content
            ->header('Ad')
            ->description('广告列表')
            ->body($grid);
    }


    /**
     * 创建广告
     */
    public function create(Content $content){
        $ad = new Ad();
        $form = new Form($ad);

        $form->setAction('/admin/ad/save');

        $form->text('title', '标题')->creationRules(['required', "unique:" . $ad->getTable()]);
        $form->datetimeRange('time_start', 'time_end', '广告周期');

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
            ->description('新建广告')
            ->body($form);
    }


    /**
     * 保存广告
     */
    public function save(Request $request){
        $param = $request->all();
        $entity = [
            "title",
            "time_start",
            "time_end"
        ];

        try{
            $ad = new Ad();
            $ad = $this->validateByStructure($param, $entity, $ad);
            $ad->save();
        }catch (\Exception $e){
            logger($e);
        }

        return redirect('/admin/ad/');
    }
}
