<?php


namespace App\Admin\Controllers;


use Encore\Admin\Controllers\AdminController;
use Illuminate\Http\Request;


class AdminAbstract extends AdminController
{
    protected $request;


    public function __construct(Request $request){
        $this->request = $request;
    }



    protected function validateByStructure($request = [], $e = [], $model = null){
        foreach ($e as $_k => $key) {
            if (isset($request[$key])) {
                if (is_string($request[$key])) {
                    $model[$key] = trim($request[$key]);
                } else {
                    $model[$key] = $request[$key];
                }
            }
        }

        return $model;
    }
}
