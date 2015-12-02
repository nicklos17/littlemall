<?php
include __DIR__.'/baseRules.php';

$rules['buynow'] = array(
        '_request' => array('soap', 'secure', 'ajax'),
        '_method' => array(
             'post' => array('g', 'a', 'n'),//g为商品id,a为组合属性id,n为商品数量
        ),
        'g'=>array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*$/',
            'msg' => '不存在的商品'
        ),
        'a' => array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*,[1-9][0-9]*$/',
            'msg' => '错误的商品属性'
        ),
        'n'=>array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*$/',
            'msg' => '错误的商品数量'
        ),

);

$rules['buycart'] = array(
        '_request' => array('soap', 'secure', 'ajax'),
        '_method' => array(
             'post' => array('select'),
        ),
        'select'=>array(
            'required' => 1,
            'filters' => 'trim'
        )
);

$rules['getShipPay'] = array(
    '_request' => array('soap', 'secure'),
    '_method' => array(
        'post' => array('proId'),
    ),
    'proId'=>array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^[1-9][0-9]*$/',
        'msg' => '错误的省份id'
    )
);

$rules['go'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('is_inv', 'inv_type', 'inv_title', 'add_id', 'memo', 'select'),
    ),
    'is_inv'=>array(
        'required' => 1,
        'filters' => 'trim',
        'range' => array(1,3),
        'msg' => '请选择是否需要发票'
    ),
    'inv_type'=>array(
        'required' => 1,
        'filters' => 'trim',
        'range' => array(1,3),
        'msg' => '错误的发票类型'
    ),
    'inv_title'=>array(
        'required' => 0,
        'filters' => 'trim',
        'length' => array(0, 400),
        'msg' => '错误的发票抬头'
    ),
    'add_id'=>array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^[1-9][0-9]*$/',
        'msg' => '错误的地址'
    ),
    'select'=>array(
        'required' => 1,
        'filters' => 'trim'
    ),
    'memo'=>array(
        'required' => 0,
        'filters' => 'trim',
        'length' => array(0, 500),
        'msg' => '备注太长'
    )
);

$rules['confirmOrder'] = array(
        '_request' => array('soap', 'secure'),
        '_method' => array(
             'post' => array('sn'),
        ),

);

$rules['getExpress'] = array(
        '_request' => array('soap', 'secure'),
        '_method' => array(
             'post' => array('type','code'),
        ),
);

$rules['payments'] = array(
        '_request' => array('soap', 'secure'),
        '_method' => array(
             'get' => array('id'),
        ),
        'id'=>array(
            'required' => 0,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*$/',
            'msg' => '错误的订单号'
        )
);

return $rules;