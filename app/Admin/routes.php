<?php

use Illuminate\Routing\Router;

Admin::routes();

//Route::group([
//    'prefix'        => config('admin.route.prefix'),
//    'namespace'     => config('admin.route.namespace'),
//    'middleware'    => config('admin.route.middleware'),
//    'as'            => config('admin.route.`prefix`') . '.',
//], function (Router $router) {
//
//    $router->get('/', 'HomeController@index')->name('home');
//});


//BM
Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.`prefix`') . '.',
], function (Router $router) {
    //Home
    $router->get('/', 'HomeController@index')->name('home');

    //Ad
    $router->any('/ad', 'AdController@listing')->name('ad.listing');   //广告列表
    $router->get('/ad/create', 'AdController@create')->name('ad.create');   //新建广告
    $router->any('/ad/save', 'AdController@save')->name('ad.save');    //保存广告
    $router->any('/ad/{id}/edit', 'AdController@edit')->name('ad.edit');    //编辑广告
    $router->get('/ad/export_batch_monitor/{adids}', 'AdController@exportBatchMonitor')->name('ad.export_batch_monitor');    //编辑广告

    //Campaign
    $router->get('/campaign', 'CampaignController@listing')->name('campaign.listing');   //订单列表
    $router->get('/campaign/create', 'CampaignController@create')->name('campaign.create');   //新建订单
    $router->post('/campaign/upload_ad', 'CampaignController@upload_ad')->name('campaign.upload_ad');    //批量上传订单 - ad
    $router->get('/campaign/export_schedule/{campaignid}', 'CampaignController@exportSchedule')->name('campaign.export_schedule');   //导出指定订单下的广告排期
    $router->get('/campaign/import_schedule/', 'CampaignController@import_schedule')->name('campaign.import_schedule');   //导入广告排期文件
    $router->get('/campaign/export_batch_extra/{campaignids}', 'CampaignController@exportBatchExtra')->name('campaign.export_batch_extra');   //导入订单投放代码


    //媒体
    $router->get('/media', 'MediaController@listing')->name('media.listing');   //媒体列表
    $router->get('/media/create', 'MediaController@create')->name('media.create');   //新建媒体
    $router->post('/media/save', 'MediaController@save')->name('media.save');    //保存媒体
    $router->get('/media/{id}/edit', 'MediaController@edit')->name('media.edit');    //编辑媒体


});
