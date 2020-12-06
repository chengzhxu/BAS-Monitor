<?php


namespace App\Extensions;


use Encore\Admin\Form\Field;

class TimeSheet extends Field
{
    protected $view = 'ad.time-sheet';

    protected static $css = [
        '/css/TimeSheet.css'
    ];

    protected static $js = [
        '/js/TimeSheet.js',
        '/js/work_time.js'
    ];

    public function render()
    {
        $this->script = <<<EOT

EOT;

        return parent::render(); // TODO: Change the autogenerated stub
    }
}