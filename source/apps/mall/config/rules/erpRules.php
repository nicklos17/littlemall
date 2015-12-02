<?php
include __DIR__.'/baseRules.php';

$rules['getOrders'] = array(
        '_request' => array('soap', 'secure'),
        '_method' => array(
             'post' => array('pageNum', 'pn', 'token', 'startTime', 'endTime'),
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
            'required' => 1,
            'filters' => 'trim',
            // 'regex' => '/^\w{32}$/',
            'valueis' => '37693cfc748049e45d87b8c7d8b9aacd',
            'msg' => '认证失败'
        ),
        'startTime' => array(
            'required' => 1,
            'msg' => '缺少参数st'
        ),
        'endTime' => array(
            'required' => 1,
            'msg' => '缺少参数et'
        ),
);

$rules['updateDelivery'] = array(
        '_request' => array('soap', 'secure'),
        '_method' => array(
             'post' => array('sn', 'shippingId' , 'shippingSn', 'token'),
        ),
        'sn'=>array(
            'required' => 1,
            'filters' => 'trim',
            // 'regex' => '/^\w{16}$/',
            'msg' => '错误的订单码号'
        ),
        'shippingId'=>array(
            'required' => 1,
            'filters' => 'trim',
            // 'regex' => '/^\w{16}$/',
            'msg' => '错误的快递id'
        ),
        'shippingSn'=>array(
            'required' => 1,
            'filters' => 'trim',
            // 'regex' => '/^\w{16}$/',
            'msg' => '错误的快递单号'
        ),
        'token'=>array(
            'required' => 0,
            'filters' => 'trim',
            'valueis' => '37693cfc748049e45d87b8c7d8b9aacd',
            'msg' => 'forbidden'
        )
);

return $rules;