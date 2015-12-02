<?php

namespace Mall\Admin\Controllers;

use \Mall\Utils\Umeditor as Uploader;

class UmeditController extends ControllerBase
{
    public function imageUpAction()
    {
        $sysConfig=$this->di->get('sysconfig');

        //上传配置
        $config = array(
            "savePath" => __DIR__.'/../../../'.$sysConfig['umeditor'].'/',//存储文件夹
            "maxSize" => 1000 ,//允许的文件最大尺寸，单位KB
            "allowFiles" => array(".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp")//允许的文件格式
        );

        $up = new Uploader("upfile" , $config);
        $info = $up->getFileInfo();
        $tmpUrl = explode('/', $info['url']);
        $info['url'] = '/'.array_slice($tmpUrl, -2, 1)[0].'/'.array_pop($tmpUrl);
        //返回数据
        echo json_encode($info);
        $this->view->disable();
        return;
    }

}
