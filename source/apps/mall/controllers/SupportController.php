<?php

namespace Mall\Mall\Controllers;

use  Mall\Mdu\BackOrdersModule as Support,
     Mall\Mdu\OrderModule as Order,
     Mall\Utils\Pagination as Page,
     Mall\Utils\GoodsImgUpload as ImgUpload;

class SupportController extends ControllerBase
{

    const NUM_PER_PAGE = 3;//每页显示数
    const ORDER_STATUS_FINISHED = 11;//[订单状态:交易成功]
    const BORD_STATUS_PASS_AUDIT = 3; //[售后单状态:审核通过]
    const BORD_ACT_ROLE_FRONTEND = 1;//[操作售后的角色：前台]

    const MSG_ORDER_NONE = '10049'; //[错误：订单不存在或订单状态错误]
    const MSG_SHIP_RE_SUBMIT = '10055'; //[错误，该售后单已经提交了售后单号]
    const MSG_UPLOAD_INVALID = '10056'; //[错误：图片过大或格式错误]

    private $support;
    private $order;

    public function initialize()
    {
        $this->support = new Support();
        $this->order = new Order();
    }

    public function indexAction()
    {
        if(!$this->validFlag)
        {
            $page = new Page($this->support->getTotal(['u_id'=>$this->uid]), self::NUM_PER_PAGE, 1);
        }
        else
        {
            $page = new Page($this->support->getTotal(['u_id'=>$this->uid]), self::NUM_PER_PAGE, isset($this->_sanReq['page']) ? $this->_sanReq['page'] : 1);
        }
        $this->view->setVars(
            array(
                'supOrders' => $this->support->getBackOrdersByUser($this->uid, $page->firstRow >= 0 ? $page->firstRow : 0, $page->listRows),
                'page' => $page->createDotLinks()
            )
        );
    }

    public function applyAction()
    {
        if(!$this->validFlag)
        {
            $this->showMsg('/order', $this->sysconfig['flagMsg'][self::MSG_ORDER_NONE], '订单页');
        }
        else
        {
            if($orderInfo = $this->order->getSpecifyOrder($this->uid, $this->_sanReq['orderSn'], self::ORDER_STATUS_FINISHED))
            {
                $orderGoods = $this->order->getOrderGoods($orderInfo['order_id']);
                foreach($orderGoods as $key=>$goods)
                {
                    $orderGoods[$key]['attrArr'] = $this->support->getGoodsAttr($goods['attrs_info']);
                }
                if($orderInfo['order_tel']){
                    $telInfo = explode('-', $orderInfo['order_tel']);
                    $orderInfo['areaCode'] = empty($telInfo[0]) ?  '' : $telInfo[0];
                    $orderInfo['telNum'] = empty($telInfo[1]) ? '' : $telInfo[1];
                    $orderInfo['ext'] = empty($telInfo[2]) ?  '' : $telInfo[2];
                }
                $orderLogMdu = new \Mall\Mdu\OrderLogsModule();
                $this->view->setVars(array(
                    'orderSn' => $this->_sanReq['orderSn'],
                    'orderInfo' => $orderInfo,
                    'orderGoods' => $orderGoods,
                    //确认收货时间
                    'confirmTime' => $orderLogMdu->getOrderLogTime($orderInfo['order_id'], $this->sysconfig['orderActType']['received'])['ord_act_addtime']
                ));
            }
            else
            {
                $this->showMsg('/order', $this->sysconfig['flagMsg'][self::MSG_ORDER_NONE], '订单页');
            }
        }
    }

    /**
     * [ajax 添加售后单]
     */
    public function addSupportAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            if($picPath = $this->_sanReq['pic'])
            {
                @unlink($this->sysconfig['supportImgPath'] . '/' . $picPath . '/thumb_' . $this->uid . '.jpg');
                $this->_sanReq['pic'] = $this->sysconfig['staticServer'] . $this->sysconfig['supportImgAccess'] . '/' . $picPath . '/original_' . $this->uid . '.jpg';
            }
            $res = $this->support->addBackOrder($this->uid, $this->_sanReq, $this->mobi, self::BORD_ACT_ROLE_FRONTEND);

            if($res['ret'] == 1)
            {
                echo json_encode(array('ret' => 1));
            }
            else
            {
                echo json_encode(array('ret' => 0, 'msg' => $this->sysconfig['flagMsg'][$res['code']]));
            }
        }

        $this->view->disable();
        return;
    }

    /**
     * [ajax 上传图片]
     */
    public function uploadImgAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            if($this->_sanReq['fileInput'] == 1)
            {
                $inputName = 'img-evi';
            }
            else
            {
                $inputName = 'add-img-evi';
            }

            $upload = new ImgUpload($this->di);
            $pic = '';
            if(isset($_FILES[$inputName]) && $_FILES[$inputName]['tmp_name'])
            {
                $imgInfo = getimagesize($_FILES[$inputName]['tmp_name']);
                $pic = $upload->upload_file($_FILES[$inputName], $this->sysconfig['supportImgDir'],
                    'original_' . $this->uid, 'thumb_' . $this->uid,
                    $imgInfo[0], $imgInfo[1], 90, 90);
                if($upload->errmsg)
                {
                    echo json_encode(array(
                        'ret' => 0,
                        'msg' => $this->sysconfig['flagMsg'][self::MSG_UPLOAD_INVALID]
                    ));

                    $this->view->disable();
                    return;
                }
            }

            if($pic)
            {
                $dir = pathinfo($pic)['dirname'];
                if(!empty($this->_sanReq['picPath']))
                {
                    $pDir = explode('/', $this->_sanReq['picPath'])[0];
                    $this->delDir($this->sysconfig['supportImgPath'] . '/' . $pDir);
                }
                echo json_encode(array(
                    'ret' => 1,
                    'imgUrl' => $this->sysconfig['staticServer'] . $this->sysconfig['supportImgAccess'] . '/' . $pic,
                    'path' => $dir
                ));
            }
            else
                echo json_encode(array(
                    'ret' => 0,
                    'msg' => '图片上传失败'
                ));
        }

        $this->view->disable();
        return;
    }

    /**
     * [ajax 删除]
     */
    public function delImgAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            $pDir = explode('/', $this->_sanReq['picPath'])[0];
            if($this->delDir($this->sysconfig['supportImgPath'] . '/' . $pDir))
            {
                echo json_encode(array(
                    'ret' => 1
                ));
            }
            else
            {
                echo json_encode(array('ret' => 0, 'msg' => '删除图片失败,请稍后重试'));
            }
        }

        $this->view->disable();
        return;
    }

    public function expressAction()
    {
        if(!$this->validFlag)
        {
            $this->showMsg('/support', '该售后单不存在', '售后列表');
        }
        else
        {
            if($bordInfo = $this->support->getSpecifyBord($this->uid, $this->_sanReq['supportSn'], self::BORD_STATUS_PASS_AUDIT))
            {
                //判断该售后单是否已提交过快递单号
                if($bordInfo['user_shipping_sn'] != '')
                {
                    $this->showMsg('/support', $this->sysconfig['flagMsg'][self::MSG_SHIP_RE_SUBMIT], '售后列表');
                }
                else
                {
                    $this->session->set('supportSn', $this->_sanReq['supportSn']);
                }
            }
            else
            {
                $this->showMsg('/support', '售后订单状态错误', '售后列表');
            }
        }
    }

    /**
     * [ajax: 提交快递单号]
     */
    public function submitExpressAction()
    {
        if(!$this->validFlag)
        {
            echo json_encode(array('ret' => 0, 'msg' => $this->warnMsg));
        }
        else
        {
            $res = $this->support->addExpInfo($this->uid, $this->mobi, $this->session->get('supportSn'), $this->_sanReq['ship_comp'], $this->_sanReq['ship_sn']);
            if($res['ret'] == 1)
            {
                echo json_encode(array('ret' => 1));
            }
            else
            {
                echo json_encode(array('ret' => 0, 'msg' => $this->sysconfig['flagMsg'][$res['code']]));
            }
        }

        $this->view->disable();
        return;
    }

    /**
     * 售后进度
     */
    public function progressAction()
    {
        if(!$this->validFlag)
        {
            $this->showMsg('/support', '该售后单不存在', '售后列表');
        }
        else
        {
            if($support = $this->support->getUserOrderBySn($this->uid, $this->_sanReq['supportSn']))
            {
                $this->view->setVars(
                    array(
                        'supportInfo' => $support,
                        //获取售后操作日志
                        'supportLogs' => (new \Mall\Mdu\OrderLogsModule())->getBackLogsById($support['bord_id'])
                    )
                );
            }
            else
            {
                $this->showMsg('/support', '该售后单不存在', '售后列表');
            }
        }
    }

    /**
     * 删除文件夹,包括文件夹内的所有文件
     *
     * @param $dir
     * @return boolean
     */
    protected function delDir($dir)
    {
        $dh = opendir($dir);
        while($file = readdir($dh))
        {
            if($file != "." && $file != "..")
            {
                $fullPath = $dir . "/" . $file;
                if(!is_dir($fullPath))
                {
                    @unlink($fullPath);
                }
                else
                {
                    $this->delDir($fullPath);
                }
            }
        }
        closedir($dh);
        return rmdir($dir);
    }

    public function getSupCntAction()
    {
        header("Access-Control-Allow-Origin: http://my.yunduo.net");
        error_reporting(E_ALL || ~E_NOTICE);
        //$uid = $this->request->get('uid');
        $uid = isset($this->uid)? $this->uid: '';
        if ($uid) {
            echo  json_encode(array('ret' => 1, 'info' => $this->support->getSupCnt($uid)));
        } else {
            echo json_encode(array('ret' => 0, 'msg' => '获取售后订单数失败'));
        }
        $this->view->disable();
    }
}
