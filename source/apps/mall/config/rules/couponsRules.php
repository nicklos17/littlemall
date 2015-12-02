<?php
include __DIR__.'/baseRules.php';

    $rules['checkCoupons'] = array(
        '_request' => array('soap', 'secure'),
        '_method' => array(
            'post' => array('sn'),
        ),
        'sn'=>array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^\w{12}$/',
            'msg' => '错误的sn码'
        )
    );
return $rules;