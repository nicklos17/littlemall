<?php

namespace Mall\MobiMall\Controllers;

use  Mall\Mdu\CartModule as Cart;

class CartController extends ControllerBase
{
    const NEED_LOGIN = 3;//用户需要登陆

    private $cart;

    public function initialize()
    {
        $this->cart = new Cart;
    }

    public function indexAction()
    {
        if(empty($this->uid))
            echo json_encode(array('ret' =>self::NEED_LOGIN));
        else
            echo json_encode($this->cart->getAttrsNames($this->cart->getCartData($this->uid)));

        $this->view->disable();
        return;
    }

    public function addAction()
    {
        if($this->request->isPost())
        {
            if (!$this->validFlag)
            {
                echo json_encode(array('ret' => 0));
                $this->view->disable();
                return;
            }

            //判断是否超过50个商品
            if($this->cart->getGoodsNum($this->uid) >= 50)
            {
                echo json_encode(array('ret' => 3));
                $this->view->disable();
                return;
            }

            echo json_encode(array('ret' => $this->cart->addCart($this->uid, $this->_sanReq['gid'], $this->_sanReq['attrs-ids'],
                    $this->_sanReq['num'])));

            $this->view->disable();
            return;
        }
    }

    public function delAction()
    {
        if (!$this->validFlag)
        {
            echo json_encode(array('ret' => 0));
            $this->view->disable();
            return;
        }

        if($this->request->isPost())
        {
            if($this->cart->delCart($this->uid, $this->_sanReq['gid'], $this->_sanReq['attrs-ids']) == 1)
                echo json_encode(array('ret' => 1));
            else
                echo json_encode(array('ret' =>0));
            $this->view->disable();
            return;
        }
    }

    // public function expiredAction()
    // {
    //     if($this->request->isPost())
    //     {
    //         if($this->cart->expiredDel($this->uid))
    //             echo json_encode(array('ret' => 1));
    //         else
    //             echo json_encode(array('ret' =>0));

    //         $this->view->disable();
    //         return;
    //     }
    // }

    // public function incrAction()
    // {
    //     if (!$this->validFlag)
    //     {
    //         echo json_encode(array('ret' => 0));
    //         $this->view->disable();
    //         return;
    //     }

    //     if($this->request->isPost())
    //     {
    //         echo json_encode(array('ret' => $this->cart->increase($this->uid, $this->_sanReq['gid'],
    //             $this->_sanReq['attrs-ids'])));

    //         $this->view->disable();
    //         return;
    //     }
    // }

    // public function decrAction()
    // {
    //     if (!$this->validFlag)
    //     {
    //         echo json_encode(array('ret' => 0));
    //         $this->view->disable();
    //         return;
    //     }

    //     if($this->request->isPost())
    //     {
    //         if($this->cart->decrease($this->uid, $this->_sanReq['gid'], $this->_sanReq['attrs-ids']) == 1)
    //             echo json_encode(array('ret' => 1));
    //         else
    //             echo json_encode(array('ret' =>0));
    //         $this->view->disable();
    //         return;
    //     }
    // }

    // public function batchDelAction()
    // {
    //     if($this->request->isPost())
    //     {
    //         if(!is_array($this->_sanReq['gids']) || !is_array($this->_sanReq['attrs-ids']))
    //         {
    //             echo json_encode(array('ret' => 0));
    //             $this->view->disable();
    //             return;
    //         }
    //         if($this->cart->batchDelCart($this->uid, $this->_sanReq['gids'], $this->_sanReq['attrs-ids']) == 1)
    //             echo json_encode(array('ret' => 1));
    //         else
    //             echo json_encode(array('ret' =>0));
    //         $this->view->disable();
    //         return;
    //     }
    // }
}
