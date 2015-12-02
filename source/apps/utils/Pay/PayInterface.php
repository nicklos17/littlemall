<?php

namespace Mall\Utils\Pay;

interface PayInterface
{
    public function buildRequest($req);

    public function notify($req);
}

