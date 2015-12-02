<?php

namespace Mall\Utils\Pay;

class  SDKRuntimeException extends \Exception {
    public function errorMessage()
    {
        return $this->getMessage();
    }

}