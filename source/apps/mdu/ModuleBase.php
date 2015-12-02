<?php

namespace Mall\Mdu;

class ModuleBase
{
    protected $di;

    protected function initModel($model)
    {
        $modObj = new $model();
        $this->di = $modObj->getDI();
        return $modObj;
    }
}
