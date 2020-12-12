<?php

namespace App\Admin\Controllers;

use App\Action\ExportAdMonitor;
use App\Action\ImportAdSchedule;
use App\Exports\AdMonitorExtra;
use App\Models\Ad;
use App\Models\App;
use App\Models\Campaign;
use App\Models\Cap;
use App\Models\Format;
use App\Models\Media;
use App\Models\MonitorType;
use App\Models\Region;
use App\Tool\Tool;
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

        $region_list = app()->make(Region::class)->getAllRegion(true);   //地区
        $app_list = app()->make(App::class)->getAllApp(true);     //app

        $grid->filter(function ($filter){
            $filter->disableIdFilter();

            // 添加新的字段过滤器（通过标题过滤）
            $filter->equal('id', 'AdID');
            $filter->like('title', '标题');
            $filter->in('formatid', '广告形式')->multipleSelect(Format::all()->pluck('title', 'formatid'));
            $filter->in('campaignid', '订单')->multipleSelect(Campaign::all()->pluck('title', 'campaignid'));
        });

        $grid->model()->orderBy('adid','desc');
        $grid->quickSearch('title');

        $grid->column('adid' ,'AdID')->sortable();
        $grid->column('title', '标题');
        $grid->column('campaign.title', '所属订单');
        $grid->column('re_conf', '属性')->display(function ($re_conf) use ($region_list, $app_list) {
            $regions = Q($re_conf, 'region_code') ? $re_conf['region_code'] : [];    //定向城市
            $apps = Q($re_conf, 'appid') ? $re_conf['appid'] : [];     //定向渠道
            $region_arr = [];
            $app_arr = [];
            foreach ($regions as $_r){
                if(isset($region_list[$_r])){
                    $region_arr[] = $region_list[$_r];
                }
            }
            foreach ($apps as $_a){
                if(isset($app_list[$_a])){
                    $app_arr[] = $app_list[$_a];
                }
            }
            $region_info = $region_arr ? implode(',', $region_arr) : '全国';
            $_str = "<span class='label label-warning'>{$region_info}</span>";
            if($app_arr){
                $app_info = implode(',', $app_arr);
                $_str .= "&nbsp;&nbsp;&nbsp; <span class='label label-success'>{$app_info}</span>";
            }
            return  $_str;
        });
        $grid->column('time_start', '开始时间');
        $grid->column('time_end', '结束时间');
        $grid->column('cap.re_conf', '频控')->display(function ($cap_detail) {
            $limit_arr = app()->make(Cap::class)->getCapDetail($cap_detail);
            $limit_info = $limit_arr ? implode(';', $limit_arr) : '';
            return  $limit_info ? "<span class='label label-warning'> $limit_info </span>" : '无';
        });
        $grid->column('format.title', '广告形式');
        $grid->column('media.title', '媒体');
//        $grid->column('monitorType.title', '监测方式');
//        $grid->column('basMonitorType.title', 'BAS监测');

        $grid->rows(function ($row){
            if($row->number%2 == 0){
                $row->style("background-color:#eee");
            }
        });

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        //批量导出监测代码
        $grid->batchActions(function ($batch) {
            $batch->add(new ExportAdMonitor());
        });

        $grid->actions(function ($actions) {
            // 去掉删除
            $actions->disableDelete();
            // 去掉查看
            $actions->disableView();
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

//        list($monitor_list, $bas_monitor_list) = app()->make(MonitorType::class)->getMonitorList(true);     //监测方式
        $cap_arr = app()->make(Cap::class)->getCapSelect();    //频控

        $form->text('title', '标题')->creationRules(['required', "unique:" . $ad->getTable()]);
        $form->select('campaignid', '所属订单')->options(Campaign::all()->pluck('title', 'campaignid'));
//        $form->datetimeRange('time_start', 'time_end', '广告周期');
        $form->datetime('time_start', '开始时间');
        $form->datetime('time_end', '结束时间');
        $form->table('cap_detail','频控', function ($form) use ($cap_arr) {
            $form->select('type', '')->options($cap_arr);
            $form->text('value', '')->rules('required');
        });

        $form->select('formatid', '广告形式')->options(Format::all()->pluck('title', 'formatid'));
        $form->select('mediaid', '媒体')->options(Media::all()->pluck('title', 'mediaid'));
//        $form->select('monitor_id', '监测方式')->options($monitor_list);
//        $form->select('bas_monitor_id', 'BAS监测')->options($bas_monitor_list);
        $form->multipleSelect('appids', '定向渠道')->options(App::all()->pluck('title', 'appid'));
        $form->multipleSelect('region_codes', '定向地区')->options(Region::all()->pluck('region_name','region_code'));
        $form->hidden('worktime', '排期')->attribute('id', 'worktime')->value("[]");
        $form->timeSheet('body');

        $this->formTools($form);
        $form->setAction('/admin/ad/save');

        return $content
            ->header('Create')
            ->description('新建广告')
            ->body($form);
    }


    /**
     * 编辑广告
    */
    public function edit($id, Content $content){
        try{
            if($this->request->method() == 'POST'){
                return $this->save($this->request);
            }
            if($id){
//                list($monitor_list, $bas_monitor_list) = app()->make(MonitorType::class)->getMonitorList(true);     //监测方式
                $cap_arr = app()->make(Cap::class)->getCapSelect();    //频控

                $form = new \Encore\Admin\Widgets\Form(Ad::findOrFail(intval($id)));

                $re_conf = isset($form->data()['re_conf']) ? $form->data()['re_conf'] : [];
                $region_arr = Q($re_conf, 'region_code') ? $re_conf['region_code'] : [];    //定向地区
                $appid_arr = Q($re_conf, 'appid') ? $re_conf['appid'] : [];    //定向渠道

                $form->hidden('adid', 'AdID');
                $form->hidden('capid', 'CapID');
                $form->hidden('campaignid', 'CampaignID');
                $form->text('title', '标题');
                $form->datetime('time_start', '开始时间');
                $form->datetime('time_end', '结束时间');

                $form->table('cap_detail','频控', function ($form) use ($cap_arr) {
                    $form->select('type', '')->options($cap_arr);
                    $form->text('value', '')->rules('required');
                });

                $form->select('formatid', '广告形式')->options(Format::all()->pluck('title', 'formatid'));
                $form->select('mediaid', '媒体')->options(Media::all()->pluck('title', 'mediaid'));
//                $form->select('monitor_id', '监测方式')->options($monitor_list);
//                $form->select('bas_monitor_id', 'BAS监测')->options($bas_monitor_list);
                $form->multipleSelect('appids', '定向渠道')->options(App::all()->pluck('title', 'appid'))->value($appid_arr);
                $form->multipleSelect('region_codes', '定向地区')->options(Region::all()->pluck('region_name', 'region_code'))->value($region_arr);

//                $worktime = Q($ad, 'worktime') ? json_decode($ad['worktime'], true) : "[]";
                $form->hidden('worktime', '排期')->attribute('id', 'worktime');
                $form->timeSheet('body');

                $this->formTools($form);
                $form->action('/admin/ad/save');

                return $content
                    ->header('Edit')
                    ->description('编辑广告')
                    ->body($form);
            }
        }catch (\Exception $e){
            logger($e);
        }
    }


    /**
     * 保存广告
     */
    public function save(Request $request){
        $param = $request->all();
        $entity = [
            'adid',
            "title",
            "time_start",
            "time_end",
            "campaignid",
            "capid",
            "formatid",
            "mediaid",
            "monitor_id",
            "bas_monitor_id",
            "appids",
            "region_codes",
            'worktime',
            'cap_detail'
        ];

        try{
            $ad = $this->validateByStructure($param, $entity);

            $adid = (isset($param['adid']) && !empty($param['adid'])) ? $param['adid'] : 0;
            $re_conf = [
                'region_code' => isset($param['region_codes']) ? array_filter($param['region_codes']) : [],
                'appid' => isset($param['appids']) ? array_filter($param['appids']) : [],
            ];

            $cap_detail = [];  //频控
            if(isset($param['cap_detail'])){
                $cap_arr = array_values($param['cap_detail']);
                foreach ($cap_arr as $_cap){
                    if(!Q($_cap, '_remove_')){
                        $cap_detail[] = ['type' => intval(Q($_cap, 'type')), 'value' => intval(Q($_cap, 'value'))];
                    }
                }
            }

            $ad['re_conf'] = json_encode($re_conf);
            $ad['cap_detail'] = json_encode($cap_detail);
            $worktime = isset($param['worktime']) ? $param['worktime'] : [];
            $ad['worktime'] = json_encode($worktime);

            unset($ad['appids']);
            unset($ad['region_codes']);
            unset($ad['adid']);

            $cap_re_conf = ['re_conf' => json_encode($cap_detail)];
            if(Q($param, 'capid')){   //编辑频控
                $capid = $param['capid'];
                app()->make(Cap::class)->updateByPrimary($param['capid'], $cap_re_conf);
            }else{   //新增频控配置
                $capid =  app()->make(Cap::class)->addOne($cap_re_conf);
            }

            $ad['capid'] = $capid;
            if($adid){
                app()->make(Ad::class)->updateByPrimary($adid, $ad);
            }else{
                app()->make(Ad::class)->addOne($ad);
            }
        }catch (\Exception $e){
            logger($e);
        }

        return redirect('/admin/ad/');
    }


    /**
     * 批量导出广告监测代码
    */
    public function exportBatchMonitor($adids = ''){
        $result = [];
        try{
            if($adids){
                $adid_arr = explode(',', $adids);
                $result = AdMonitorExtra::getMonitorExtraData($adid_arr);
            }
        }catch (\Exception $e){
            logger($e);
        }

        return Tool::exportExcel('广告监测代码', $result);
    }


    private function formTools($form = []){
        if($form){
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
                $footer->disableReset();
            });
        }
    }
}
