<?php

namespace Mall\MobiMall\Controllers;

class IndexController extends ControllerBase
{

    public function synlogoutAction()
    {
        $this->view->disable();
    }

    /**
     * 请求验证用户状态
     * @return [type] [description]
     */ 
    public function authAction()
    {
        $url = parse_url($_GET['url']);
        $path = strpos($url['path'], "/ng/views/") === 0 ? str_replace(array('/ng/views/', '.html'), array('', ''), $url['path']) : $url['path'];
        list($ctrlName, $actName) = explode('/', ltrim($path, '/'));
        $this->session->set('furl', $_GET['furl']);     // 保存前端html url地址
        if ($this->checkAuth($ctrlName, $actName) && !$this->casInfo)
            echo 0;
        else
            echo 1;
        $this->view->disable();
    }

    public function valiAction()
    {
        $forward = $this->session->get('furl') ?: '/ng/#';
        $this->cas->auth();
        header('Location:'. $forward);
    }
    
    public function indexAction()
    {
        header('Location:/ng/#');
    }

    public function logoutAction()
    {
        $this->view->disable();
    }
}
