<?php

namespace Mall\MobiMall\Controllers;

class CommonController extends ControllerBase
{
    public function miniHeaderAction()
    {
        $this->view->pick('common/miniHeader');
        $this->view->disableLevel(\Phalcon\Mvc\View::LEVEL_MAIN_LAYOUT);
    }

    /**
     * mini头部
     * @return [type] [description]
     */
    public function miniAction()
    {
        $host = $this->di['sysconfig']['domain'];
$js =<<<JS
var span = document.getElementById('mini_head');
span.style.height="70px";
span.innerHTML='<iframe width="100%" scrolling="no" frameborder="0" height="70" src="{$host}/common/miniHeader"></iframe>';
JS;
echo $js;
        $this->view->disableLevel(\Phalcon\Mvc\View::LEVEL_MAIN_LAYOUT);
    }
}