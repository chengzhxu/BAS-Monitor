<?php

namespace App\Admin\Controllers;

use App\Models\Ad;
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

        $grid->model()->orderBy('adid','desc');
        $grid->quickSearch('title');

        $grid->column('adid' ,'Ad Id')->sortable();
        $grid->column('title', '标题');
        $grid->column('time_start', '开始时间');
        $grid->column('time_end', '结束时间');

        $grid->filter(function ($filter){
            $filter->disableIdFilter();

            // 添加新的字段过滤器（通过标题过滤）
            $filter->like('title', '标题');
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
        $form->datetimeRange('time_start', 'time_end', 'Time Range');

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
