<?php

namespace Mall\Mall\Controllers;

use Phalcon\Mvc\Controller,
       Mall\Utils\RulesParse;

include __DIR__.'/../../utils/cas/CasClient.php';

class ControllerBase extends Controller
{
    protected $warnMsg = false;     // 校验错误提示信息
    protected $validFlag = true;    // 校验结果标识  true - 通过   false - 拒绝
    protected $_sanReq = array();   // 经处理过的参数
    protected $ctrlName;    // 当前访问控制器名
    protected $actName;    // 当前访问方法名

    public function init($obj)
    {
        $MallObj = new $obj();
        $MallObj->initMall($this->validFlag, $this->warnMsg, $this->_sanReq);
        return $MallObj;
    }

    public function beforeExecuteRoute($dispatcher)
    {
        $this->ctrlName = $this->dispatcher->getControllerName();
        $this->actName = $this->dispatcher->getActionName();

        $this->cas = new \CasClient($this->ctrlName, $this->actName, $this->request->get(), 'webpc');
        if($this->checkAuth())
        {
            $this->cas->auth();
        }
        if($casInfo = $this->cas->getData())
        {
            $userInfo = $this->cas->getRedisUserInfo($casInfo['uid']);
            $this->uid = $casInfo['uid'];
            $this->mobi = $userInfo['mobile'] ?: $casInfo['umobi'];

            if (!$this->uid || !$this->mobi)
                header("Location:/index/logout");

            $this->session->set('uid', $casInfo['uid']);
            $this->session->set('uinfo', array('name' => $userInfo['name'], 'mobile' => $this->mobi));
        }
        $ruleName = $this->ctrlName . 'Rules.php';
        $rules = @include __DIR__ . '/../config/rules/' . $ruleName;
        $actionRules = isset($rules[$this->actName]) ? $rules[$this->actName] : false;

        //获取已登陆用户的购物车商品数量
        if(!empty($this->uid) && !$this->session->has('n'))
            $this->session->set('n', (new \Mall\Mdu\CartModule())->getGoodsNum($this->uid));

        if (!$rules || !$actionRules)
            return true;

        $utils = new RulesParse($actionRules);
        $utils->parse();

        if($utils->warnMsg && is_array($utils->warnMsg))
        {
            $this->validFlag = false;
            foreach ($utils->warnMsg as $val)
            {
                $this->warnMsg = $val['msg'];
                break;
            }
        }
        else
            $this->_sanReq = $utils->_sanReq;

    }

    /**
     * 当前路径是否需要校验身份
     * @return [type] [description]
     */
    protected function checkAuth()
    {
        $auth = false;
        if (!empty($_SERVER['HTTP_REFERER']))
        {
            $refer = parse_url($_SERVER['HTTP_REFERER']);
            $casHost = parse_url($this->cas->cfg['host']);
            
            // 兼容商城添加购物车操作
            if ($refer['host'] == $casHost['path'] && $this->ctrlName == 'goods')
                return true;
        }

        $authConfig = $this->authConfig;
        foreach ($authConfig as $controller => $actions)
        {
            if ($this->ctrlName != $controller)
                continue;

            if (empty($actions))
            {
                $auth = true;
                break;
            }

            $auth = in_array($this->actName, $actions) ?  true : false;
        }

        return $auth;
    }

    protected function showMsg($url, $error, $title)
    {
        $this->view->setVars(
            array(
                'url' => $url,
                'error'=>$error,
                'title'=>$title,
            )
        );

        $this->view->pick('public/error');
        return;
    }
}