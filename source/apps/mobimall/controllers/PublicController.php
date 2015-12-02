<?php

namespace Mall\MobiMall\Controllers;
class publicController extends ControllerBase
{
    /**
     * [errorAction 错误页面]
     * @return [type] [description]
     */
     public function errorAction()
    {
        if(empty($url))
            exit('forbidden access!');
    }

    /**
     * [errorAction ajax跳转错误页面]
     * @return [type] [description]
     */
     public function errorAjaxAction()
    {
        switch ($this->_sanReq['v'])
        {
            case '1':
                $ret = array('e' => '库存不足', 'u' => $_SERVER['HTTP_REFERER'], 't' => '返回上一页');
                break;

            case '2':
                $ret = array('e' => '库存不足', 'u' => '/cart', 't' => '返回购物车列表');
                break;

            case '3':
                $ret = array('e' => '购物车异常', 'u' => '/cart', 't' => '返回购物车列表');
                break;

            case '4':
                $ret = array('e' => '请先登陆', 'u' => $_SERVER['HTTP_REFERER'], 't' => '返回上一页');
                break;

            case '5':
                $ret = array('e' => '清除失败', 'u' => '/cart', 't' => '返回购物车列表');
                break;

            case '6':
                $ret = array('e' => '下单异常', 'u' => '/cart', 't' => '返回购物车列表');
                break;

            case '7':
                $ret = array('e' => '购物车超过50个商品', 'u' => $_SERVER['HTTP_REFERER'], 't' => '返回上一页');
                break;

            case '8':
                $ret = array('e' => '超过单件商品最大购买数', 'u' => $_SERVER['HTTP_REFERER'], 't' => '返回上一页');
                break;

            default:
                $ret = array('e' => '服务器异常', 'u' => '/index', 't' => '返回首页');
                break;
        }
        $this->view->setVars(array(
            'error' => $ret['e'],
            'url' => $ret['u'],
            'title' => $ret['t']
        ));
    }

    /**
     * [emailErrAction 邮件错误]
     * @return [type] [description]
     */
    public function emailErrAction()
    {

    }

    public function error404Action()
    {
       $this->view->disableLevel(\Phalcon\Mvc\View::LEVEL_MAIN_LAYOUT);
    }
}