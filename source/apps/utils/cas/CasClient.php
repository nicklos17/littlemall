<?php
// 加载phpcas类文件
include 'phpCAS/CAS.php';
include 'utils/Encrypt.php';
include 'utils/Curl.php';

class CasClient extends \Phalcon\Mvc\Application
{
    public $cfg;
    protected $ctrl;    //　控制器
    protected $act;     // 方法
    protected $req;  // 接收到的参数
    protected $casInfo; // cas server端获取的数据

    public function __construct($controller, $action, $request, $cfgType = '')
    {
        switch ($cfgType) {
            case 'adm':
                $this->cfg = include 'config/admConfig.php';
                break;
            case 'mobi':
                $this->cfg = include 'config/mobiConfig.php';
                break;
            case 'webpc':
            default:
                $this->cfg = include 'config/webpcConfig.php';
                break;
        }
        $this->ctrl = $controller;
        $this->act = $action;
        $this->req = $request;
        $this->isAdmin = $cfgType == 'adm' ? true : false;

        // 初始化cas
        $rs = phpCAS::client(CAS_VERSION_1_0, $this->cfg['host'], $this->cfg['port'], 'index/');
        phpCAS::setNoCasServerValidation();
    }

    public function auth($isRedirect = true)
    {
        // 判断是否为退出操作
        if ($this->ctrl == 'index' && $this->act == 'logout')
        {
            $this->_logout();
        }
        elseif($this->ctrl == 'index' && $this->act == 'synlogout')
        {
            $this->_synlogout();
        }
        else
        {
            if ($isRedirect) {
                phpCAS::setServerLoginURL($this->loginUrl());
                phpCAS::forceAuthentication();
                $this->casInfo = $this->_getUser();
                
                if ($backurl = $this->request->getQuery('backurl'))
                {
                    $backurl = 'http://' . $this->cfg['host'] . ':' . $this->cfg['port'] . '/index/sessid?backurl=' . urlencode($backurl) . '&st=' . session_id();
                    $this->response->redirect($backurl);
                    return;
                }

                if ($this->isAdmin && !$this->_checkPerm())
                {
                    exit('无权限访问');
                }
            }
        }
    }

    public function getData()
    {
        return phpCAS::isAuthenticated() ? $this->_getUser() : array();
    }

    /**
     * 记录日志
     * @param  string $content [description]
     * @return [type]          [description]
     */
    public function log($controller, $action, $content = '')
    {
        $data = array(
            'adminId' => $this->casInfo['uid'],
            'siteId' => $this->cfg['siteid'],
            'controller' => $controller,
            'action' => $action,
            'content' => $content,
        );

        return Curl::send($this->cfg['logUrl'], $data, 'post');
    }

    public function _getUser()
    {
        // 解密cas server传来的原始数据
        $encKey = $this->cfg['encKey'];
        if ($encVal = Encrypt::auth(phpCAS::getUser(), $encKey, 'DECODE'))
        {
            $encVal = json_decode($encVal, true);
            if ($this->isAdmin)
            {
                // 获取redis权限
                $redis = new \Redis();
                $redis->connect($this->cfg['redis']['host'], $this->cfg['redis']['port']);
                $redis->select($this->cfg['redis']['dbname']);
                $res = unserialize($redis->get('group' . $encVal['ugroup'] . '_' . $this->cfg['siteid']));
                $encVal['permMenu'] = unserialize($redis->get('group' . $encVal['ugroup'] . '_' . $this->cfg['siteid']));
            }
        }
        return $encVal ?: false;
    }

    public function loginUrl()
    {
        $backurl = $this->cfg['domain'] . $_SERVER['REQUEST_URI'];
        return $this->cfg['loginUrl'] . '?siteid=' . $this->cfg['siteid'] . '&backurl=' . urlencode($backurl);
    }

    private function _logout()
    {
        $logoutOpt = array(
            'url' => $this->cfg['domain']
        );

        phpCAS::setServerLogoutURL($this->cfg['logoutUrl']);
        phpCAS::logout($logoutOpt);
    }

    private function _synlogout()
    {
        phpCAS::handleLogoutRequests(false);
    }

    /**
     * 校验是否有权限访问
     */
    protected function _checkPerm()
    {
        if (isset($this->casInfo['usuper']) && $this->casInfo['usuper'])
        {
            // 超级管理员
            return true;
        }
        $allowPerm = $this->_allowPerm();
        if (isset($allowPerm[$this->ctrl]) && in_array($this->act, $allowPerm[$this->ctrl]))
        {
            return true;
        }

        // ajax
        $request = new \Phalcon\Http\Request();
        if ($request->isAjax())
        {
            return true;
        }

        if($permMenu = $this->casInfo['permMenu'])
        {
            foreach ($permMenu as $menu)
            {
                if ($menu['m_controller'] == $this->ctrl && $menu['m_action'] == $this->act)
                    {
                        return true;
                    }
            }
        }
        return false;
    }

    /**
     * 默认允许访问白名单
     */
    private function _allowPerm()
    {
        return array(
            'index' => array('index', 'logout')
        );
    }

    public function __destruct()
    {
        $this->view->disable();
    }

    public function getRedisUserInfo($uid)
    {
        $redis = new \Redis();
        $redis->connect($this->cfg['redis']['host'], $this->cfg['redis']['port']);
        $redis->select($this->cfg['redis']['dbname']);
        $key = 'ucenter_cas:' . $uid;
        // return unserialize($redis->get($key));
        return $redis->exists($key) ? json_decode(unserialize($redis->get($key)), true) : false;
    }
}