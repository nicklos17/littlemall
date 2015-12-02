<?php

namespace Mall\MobiMall\Controllers;

use  Mall\Mdu\CodesModule as Code;
use  Mall\Utils\RedisLib;

class CodeController extends ControllerBase
{

    private $code;
    public function initialize()
    {
        $this->code= new Code();
    }

    public function indexAction()
    {

    }

    public function checkCodeAction()
    {
        if ($this->request->isPost())
        {
            if (!$this->validFlag)
            {
                echo json_encode(array('ret' =>0, 'msg' => $this->warnMsg));
            }
            else
            {
                $res = $this->code->checkCode($this->uid, $this->_sanReq['code'], $this->_sanReq['captcha']);
                if(!is_array($res) && array_key_exists($res,$this->di['sysconfig']['flagMsg']))
                {
                    if($res == 10045)
                    {
                        echo json_encode(array(
                            'ret' => 0,
                            'msg'=> '需要验证码'
                        ));
                    }
                    else
                    {
                        echo json_encode(array(
                            'ret' => 0,
                            'msg'=>  $this->di['sysconfig']['flagMsg'][$res]
                        ));
                    }
                }
                else
                {
                    //$RedisLib = new \Mall\Utils\RedisLib($this->di);
                    //$redis = $RedisLib::getRedis($this->di);
                    //暂支持全免类型
                    if($res['yc_type'] == 3 && !$res['yc_used_time']) 
                        $this->session->set('codeInfo', $res);
                    else
                        $this->session->remove('codeInfo');

                    if(!empty($res['yc_good_ids']))
                    {
                        $ids = explode(',', $res['yc_good_ids']);
                        //$codeToken = \Mall\Utils\Inputs::createCode();
                        //$this->session->set('codeToken', $codeToken);
                        $url = '/goods/'.$ids[0];
                    }
                    else
                        $url = '/';

                    echo json_encode(
                        array(
                            'ret' => 1,
                            'url'=> $url
                        ));
                }
            }
        }

        $this->view->disable();
        return;
    }
}
