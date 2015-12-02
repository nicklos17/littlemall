<?php
include __DIR__.'/baseRules.php';

$rules['getOrders'] = array(
        '_request' => array('soap', 'secure'),
        '_method' => array(
             'post' => array('pageNum', 'pn', 'token'),
        ),
        'pageNum'=>array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*$/',
            'msg' => '不存在的商品'
        ),
        'pn'=>array(
            'required' => 0,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*$/',
            'msg' => '不存在的属性'
        ),
        'token'=>array(
            'required' => 0,
            'filters' => 'trim',
            'regex' => '/^\w{32}$/',
            'msg' => '不存在的属性'
        )
);

$rules['updateDelivery'] = array(
        '_request' => array('soap', 'secure'),
        '_method' => array(
             'post' => array('sn', 'invoice', 'token'),
        ),
        'sn'=>array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^\w{16}$/',
            'msg' => '错误的订单码号'
        ),
        'invoice'=>array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^\w{16}$/',
            'msg' => '错误的快递单号'
        ),
        'token'=>array(
            'required' => 0,
            'filters' => 'trim',
            'regex' => '/^\w{32}$/',
            'msg' => 'forbidden'
        )
);

return $rules;