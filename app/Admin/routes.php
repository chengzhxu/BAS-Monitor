<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.`prefix`') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');


    $router->get('/campaign', 'CampaignController@grid');


});


//BM
Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.`prefix`') . '.',
], function (Router $router) {
    //Ad
    $router->get('/ad', 'AdController@listing')->name('ad.listing');
    $router->get('/ad/create', 'AdController@create')->name('ad.create');
    $router->any('/ad/save', 'AdController@save')->name('ad.save');

    //Campaign
    $router->get('/campaign', 'CampaignController@listing')->name('campaign.listing');
    $router->get('/campaign/create', 'CampaignController@create')->name('campaign.create');
    $router->post('/campaign/upload_ad', 'CampaignController@upload_ad')->name('campaign.upload_ad');


});
