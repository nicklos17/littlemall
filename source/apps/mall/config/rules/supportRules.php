<?php

include __DIR__.'/baseRules.php';

$rules['index'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'get' => array('page')
    ),
    'page'=>array(
        'required' => 0,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '页数不合法'
    )
);

$rules['apply'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'get' => array('orderSn')
    ),
    'orderSn'=>array(
        'required' => 1,
        'filters' => 'trim',
        'length' => 16,
        'msg' => '订单不存在'
    )
);

$rules['addSupport'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('goods_num', 'bord_reason', 'bord_msg', 'pro', 'city', 'dis', 'addr_detail', 'consignee', 'mobile', 'areaCode', 'telNum', 'ext', 'order_sn', 'bord_type'),
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
    'bord_msg' => array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(1, 255),
        'msg' => '请输入正确的情况描述'
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
        'length' => array(4, 240),
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
    'area_code' => array(
        'required' => 0,
        'filters' => 'trim',
        'regex' => '/^[0\+]\d{2,3}$/',
        'msg' => '请输入正确的区号'
    ),
    'tel_num' => array(
        'required' => 0,
        'filters' => 'trim',
        'regex' => '/^\d{7,8}$/',
        'msg' => '请输入正确的然电话号码'
    ),
    'ext' => array(
        'required' => 0,
        'filters' => 'trim',
        'regex' => '/^\d{1,}$/',
        'msg' => '请输入正确的分机号'
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
        'range' => array(1, 3, 5),
        'filters' => 'trim',
        'msg' => '申请类型选择有误'
    ),
    'pic' => array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(1, 20),
        'msg' => '图片地址错误'
    )
);

$rules['uploadImg'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'get' => array('fileInput', 'picPath')
    ),
    'fileInput' => array(
        'required' => 1,
        'regex' => '/^[1-9][0-9]*$/',
        'range' => array(1, 3),
        'filters' => 'trim',
        'msg' => '文件上传参数有误'
    ),
    'picPath' => array(
        'required' => 0,
        'filters' => 'trim',
        'length' => array(1, 20),
        'msg' => '图片路径错误'
    )
);

$rules['delImg'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('picPath')
    ),
    'picPath' => array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(1, 20),
        'msg' => '图片路径错误'
    )
);

$rules['express'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'get' => array('supportSn')
    ),
    'supportSn'=>array(
        'required' => 1,
        'filters' => 'trim',
        'length' => 16,
        'msg' => '售后单不存在'
    )
);

$rules['submitExpress'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('ship_comp', 'ship_sn')
    ),
    'ship_comp' => array(
        'required' => 1,
        'filters' => 'trim',
        'range' => array(1, 3, 5, 7, 9),
        'msg' => '快递公司错误'
    ),
    'ship_sn' => array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(5, 26),
        'regex' => '/^[a-zA-Z0-9]*$/',
        'msg' => '快递公司单号格式错误'
    )
);

$rules['progress'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'get' => array('supportSn')
    ),
    'supportSn'=>array(
        'required' => 1,
        'filters' => 'trim',
        'length' => 16,
        'msg' => '售后单不存在'
    )
);

return $rules;