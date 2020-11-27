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
    $router->get('campaign/listing', 'CampaignController@grid');


});
