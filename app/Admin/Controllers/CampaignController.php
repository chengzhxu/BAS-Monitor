<?php

namespace App\Admin\Controllers;

use App\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CampaignController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Campaign';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid(Content $content)
    {
        $grid = new Grid(new User());

        $grid->model()->orderBy('id','desc');
        $grid->quickSearch('name');
        $grid->column('id', 'ID')->sortable();
        $grid->column('name', '名称');

        $grid->column('url', '图片')
            ->image('/', 100, 100)
            ->modal('IMG', function ($model) {
                return '<img src="'.$model['url'].'" style="width:100%;height:100%;">';
            });

        $grid->column('created_at', '创建时间');

        $grid->paginate(20);

        $grid->actions(function ($actions) {
//            $actions->disableEdit();
        });

        return $content
            ->header('MyPic')
            ->description('图片列表')
            ->body($grid);
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('email_verified_at', __('Email verified at'));
        $show->field('password', __('Password'));
        $show->field('remember_token', __('Remember token'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User());

        $form->text('name', __('Name'));
        $form->email('email', __('Email'));
        $form->datetime('email_verified_at', __('Email verified at'))->default(date('Y-m-d H:i:s'));
        $form->password('password', __('Password'));
        $form->text('remember_token', __('Remember token'));

        return $form;
    }
}
