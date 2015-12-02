<?php

include __DIR__.'/baseRules.php';

$rules['index'] = array(
    '_method' => array(
        'get' => array('page', 'pro_id', 'city_id', 'dis_id', 'shipping_pay'),
    ),
    'page' => array(
        'required' => 0,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '页数错误'
    ),
    'pro_id' => array(
        'required' => 0,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '省份ID'
    ),
    'city_id' => array(
        'required' => 0,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '市ID'
    ),
    'dis_id' => array(
        'required' => 0,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '县区ID'
    ),
    'shipping_pay' => array(
        'required' => 0,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '快递费用格式错误'
    )
);

$rules['addRule'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('pro_id', 'city_id', 'dis_id', 'fee')
    ),
    'pro_id'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '省份ID'
    ),
    'city_id'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '市ID'
    ),
    'dis_id'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '县区ID'
    ),
    'fee'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '快递费用格式错误'
    )
);

$rules['editRule'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('pro_id', 'city_id', 'dis_id', 'fee')
    ),
    'pro_id' => array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '省份ID'
    ),
    'city_id' => array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '市ID'
    ),
    'dis_id' => array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '县区ID'
    ),
    'fee'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '快递费用格式错误'
    )
);

$rules['deleteRules'] = array(
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

return $rules;