<?php

include __DIR__.'/baseRules.php';

$rules['index'] = array(
    '_method' => array(
        'get' => array('page'),
    ),
    'page' => array(
        'required' => 0,
        'regex' => '/^[1-9][0-9]*$/',
        'filters' => 'trim',
        'msg' => '页数错误'
    ),

);

$rules['deleteOrders'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('id_arr')
    ),
    'id_arr' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => ''
    ),
);

$rules['detail'] = array(
    '_method' => array(
        'get' => array('bord_id')
    ),
    'bord_id' => array(
        'required' => 1,
        'regex' => '/^[1-9][0-9]*$/',
        'filters' => 'trim',
        'msg' => ''
    ),
);

$rules['setStatus'] = array(
    '_method' => array(
        'post' => array('bord_id, status')
    ),
    'bord_id' => array(
        'required' => 1,
        'regex' => '/^[1-9][0-9]*$/',
        'filters' => 'trim',
        'msg' => ''
    ),
    'status' => array(
        'required' => 1,
        'regex' => '/^[1-9][0-9]*$/',
        'range' => array(1, 3, 5, 7, 9),
        'filters' => 'trim',
        'msg' => '状态值错误'
    ),
);

$rules['batchSetStatus'] = array(
    '_method' => array(
        'post' => array('id_arr, status')
    ),
    'id_arr' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => ''
    ),
    'status' => array(
        'required' => 1,
        'regex' => '/^[1-9][0-9]*$/',
        'range' => array(1, 3, 5, 7, 9),
        'filters' => 'trim',
        'msg' => '状态值错误'
    ),
);


$rules['setShip'] = array(
    '_method' => array(
        'post' => array('bord_id, shipping_sn, shipping_name')
    ),
    'bord_id' => array(
        'required' => 1,
        'regex' => '/^[1-9][0-9]*$/',
        'filters' => 'trim',
        'msg' => ''
    ),
    'shipping_sn' => array(
        'required' => 1,
        'length'   => array(1, 20),
        'filters' => 'trim',
        'msg' => '快递单号错误'
    ),
    'shipping_name' => array(
        'required' => 1,
        'length'   => array(1, 20),
        'filters' => 'trim',
        'msg' => '快递公司错误'
    ),
);

$rules['setType'] = array(
    '_method' => array(
        'post' => array('bord_id, bord_type')
    ),
    'bord_id' => array(
        'required' => 1,
        'regex' => '/^[1-9][0-9]*$/',
        'filters' => 'trim',
        'msg' => ''
    ),
    'bord_type' => array(
        'required' => 1,
        'regex' => '/^[1-9][0-9]*$/',
        'range' => array(1, 3, 5),
        'filters' => 'trim',
        'msg' => '状态值错误'
    ),
);

$rules['setActMoney'] = array(
    '_method' => array(
        'post' => array('bord_id, act_money, back_money')
    ),
    'bord_id' => array(
        'required' => 1,
        'regex' => '/^[1-9][0-9]*$/',
        'filters' => 'trim',
        'msg' => ''
    ),
    'back_money' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '申请退款金额格式错误'
    ),
    'act_money' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '实际退款格式错误'
    ),
);


$rules['syncOrderInfo'] = array(
    '_method' => array(
        'post' => array('order_sn')
    ),
    'order_sn' => array(
        'required' => 1,
        'filters' => 'trim',
        'length' => 16,
        'msg' => '订单不存在'
    ),
);

$rules['addSupport'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('goods_num', 'bord_reason', 'pro', 'city', 'dis', 'addr_detail', 'consignee', 'mobile', 'order_sn', 'bord_type', 'ship_comp', 'ship_sn'),
    ),
    'goods_num' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '申请数量有误'
    ),
    'bord_reason' => array(
        'required' => 1,
        'regex' => '/^[1-9][0-9]*$/',
        'range' => array(1, 2, 3, 4, 5, 6),
        'filters' => 'trim',
        'msg' => '申请原因选择有误'
    ),
    'pro' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '错误的省份id'
    ),
    'city' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '错误的市id'
    ),
    'dis' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '错误的县区id'
    ),
    'order_goods_id' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '错误的订单商品'
    ),
    'addr_detail' => array(
        'required' => 0,
        'filters' => 'trim',
        'length' => array(4, 120),
        'msg' => '请输入正确的详细地址'
    ),
    'consignee' => array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(1, 50),
        'msg' => '请输入正确格式的收货人姓名'
    ),
    'mobile' => array(
        'required' => 0,
        'length' => 11,
        'filters' => 'trim',
        'regex' => '/^1[3,4,5,7,8]+\\d{9}$/',
        'msg' => '请输入正确的手机号码'
    ),
    'order_sn' => array(
        'required' => 1,
        'filters' => 'trim',
        'length' => 16,
        'msg' => '订单号不存在'
    ),
    'bord_type' => array(
        'required' => 1,
        'regex' => '/^[1-9][0-9]*$/',
        'range' => array(1, 3, 7),
        'filters' => 'trim',
        'msg' => '申请类型选择有误'
    ),
    'ship_comp' => array(
        'required' => 0,
        'filters' => 'trim',
        'length' => array(2, 19),
        'msg' => '请输入正确的快递公司名称'
    ),
    'ship_sn' => array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(6, 30),
        'regex' => '/^[a-zA-Z0-9]*$/',
        'msg' => '快递单号格式错误'
    )
);

return $rules;