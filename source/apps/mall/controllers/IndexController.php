<?php

namespace Mall\Mall\Controllers;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
            $this->view->setVars(array(
                'homeFlag' => 1
            ));
    }

    public function synlogoutAction()
    {
        $this->view->disable();
    }

    public function logoutAction()
    {
        $this->view->disable();
    }

    public function valiAction()
    {
        $this->cas->auth();
        $forward = $_GET['forward'] ?: '/';
        header('Location:'. $forward);
    }
}
