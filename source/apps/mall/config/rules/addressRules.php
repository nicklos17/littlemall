<?php

include __DIR__.'/baseRules.php';

$rules['add'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('pro', 'city', 'dis', 'detailAddr', 'zipCode', 'consignee', 'mobile', 'areaCode', 'telNum', 'ext', 'default'),
    ),
    'pro'=>array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '错误的省份id'
    ),
    'city'=>array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '错误的市id'
    ),
    'dis'=>array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '错误的县区id'
    ),
    'detailAddr' => array(
        'required' => 0,
        'filters' => 'trim',
        'length' => array(4, 240),
        'msg' => '请输入正确的街道地址'

    ),
    'zipCode' => array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(1, 6),
        'msg' => '请输入正确格式的邮政编码'
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
    'areaCode' => array(
        'required' => 0,
        'filters' => 'trim',
        'regex' => '/^[0\+]\d{2,3}$/',
        'msg' => '请输入正确的区号'
    ),
    'telNum' => array(
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
    )
);

$rules['edit'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('addrId', 'pro', 'city', 'dis', 'detailAddr', 'zipCode', 'consignee', 'mobile', 'areaCode', 'telNum', 'ext', 'default'),
    ),
    'addrId' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '错误的地址id'
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
    'detailAddr' => array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(4, 240),
        'msg' => '请输入正确的详细地址'

    ),
    'zipCode' => array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(1, 6),
        'msg' => '请输入正确格式的邮政编码'
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
    'areaCode' => array(
        'required' => 0,
        'filters' => 'trim',
        'regex' => '/^[0\+]\d{2,3}$/',
        'msg' => '请输入正确的区号'
    ),
    'telNum' => array(
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
    )
);

$rules['getAddr'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('aid')
    ),
    'aid'=>array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '错误的地址id'
    )
);

$rules['del'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('aid')
    ),
    'aid'=>array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '错误的地址id'
    )
);

$rules['setDefault'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('aid')
    ),
    'aid'=>array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '错误的地址id'
    )
);

return $rules;
