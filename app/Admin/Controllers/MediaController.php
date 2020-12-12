<?php


namespace App\Admin\Controllers;


use App\Action\ExportAdSchedule;
use App\Models\Media;
use App\Models\MyPic;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends  AdminAbstract
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Media';

    //媒体列表
    public function listing(Content $content){
        $grid = new Grid(new Media());

        $grid->model()->orderBy('mediaid','desc');
        $grid->quickSearch('title');

        $grid->column('mediaid' ,'Id')->sortable();
        $grid->column('title', '标题');

        $grid->column('license', '执照')
            ->image('/', 100, 100);

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        $grid->actions(function ($actions) {
            // 去掉删除
            $actions->disableDelete();
            // 去掉查看
            $actions->disableView();

            $actions->add(new ExportAdSchedule());
        });

        return $content
            ->header('Media')
            ->description('媒体列表')
            ->body($grid);
    }


    /**
     * 新建媒体
     */
    public function create(Content $content){
        $media = new Media();
        $form = new Form($media);

        $media_type_arr = Media::getMediaType();   //属性

        $form->text('title', '名称')->creationRules(['required', "unique:" . $media->getTable()]);
        $form->select('type', '属性')->options($media_type_arr);
        $form->image('license', '执照');


        $this->formTools($form);
        $form->setAction('/admin/media/save');

        return $content
            ->header('Create')
            ->description('新建媒体')
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
                $media = app()->make(Media::class)->fetchRowByPrimary($id);
                $media_type_arr = Media::getMediaType();   //属性
                $form = new Form(Media::findOrFail(intval($id)));

                $license_url = str_replace("/uploads","", Q($media, 'license'));   //暂时解决方案

                $form->hidden('id', 'MediaID')->value(Q($media, 'mediaid'));
                $form->text('title', '名称')->value(Q($media, 'title'));
                $form->select('type', '属性')->options($media_type_arr)->value(Q($media, 'type'));
                $form->image('license', '执照')->value($license_url);

                $this->formTools($form);
                $form->setAction('/admin/media/save');

                return $content
                    ->header('Media')
                    ->description('编辑媒体')
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
            'id',
            "title",
            "type",
        ];

        try{
            $media = $this->validateByStructure($param, $entity);
            $license = $request->file('license');
            $fileName = $this->upload($license);   //上传执照
            if ($fileName){
                $media['license'] = $fileName;
            }else{
                return '上传失败';
            }
            $mediaid = (isset($param['id']) && !empty($param['id'])) ? $param['id'] : 0;
            unset($media['mediaid']);

            if($mediaid){
                app()->make(Media::class)->updateByPrimary($mediaid, $media);
            }else{
                app()->make(Media::class)->addOne($media);
            }
        }catch (\Exception $e){
            logger($e);
        }

        return redirect('/admin/media/');
    }


    /**
     * 上传媒体执照
     */
    private function upload($file, $disk='public') {
        // 1.是否上传成功
        if (!$file->isValid()) {
            return false;
        }

        // 2.是否符合文件类型 getClientOriginalExtension 获得文件后缀名
        $fileExtension = $file->getClientOriginalExtension();
        if(! in_array($fileExtension, ['png', 'jpg', 'gif', 'jpeg', 'mp4', 'avi', '3gp', 'rmvb'])) {
            return false;
        }

        // 3.判断大小是否符合 2M
        $tmpFile = $file->getRealPath();
        if (filesize($tmpFile) >= 2048000) {
            return false;
        }

        // 4.是否是通过http请求表单提交的文件
        if (! is_uploaded_file($tmpFile)) {
            return false;
        }

        // 5. 生成一个随机文件名
        $fileName = md5(time()) .mt_rand(0,9999).'.'. $fileExtension;
        if (Storage::disk($disk)->put($fileName, file_get_contents($tmpFile)) ){
            return Storage::disk($disk)->url($fileName);
        }
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
