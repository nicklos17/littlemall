<?php
include __DIR__.'/baseRules.php';

$rules['editcate'] = array(
    '_request' => array('ajax'),
    '_method' => array(
     'post' => array('cid', 'name', 'gcat_id', 'desc', 'keyword', 'order', 'show')
    ),
    'cid'=>array(
        'required' => 1,
        'regex' => '/^[1-9][0-9]*$/',
        'filters' => 'trim',
        'msg' => '错误分类ID'
    ),
    'gcat_id'=>array(
        'required' => 1,
        'regex' => '/^[1-9][0-9]*$/',
        'filters' => 'trim',
        'msg' => '错误分类ID'
    ),
    'name'=>array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(1, 50),
        'msg' => '请输入正确的分类名'
    ),
    'keyword'=>array(
        'required' => 0,
        'length' => array(0, 255),
        'filters' => 'trim',
        'msg' => '请输入正确的关键字'
    ),
    'order'=>array(
        'required' => 0,
        'regex' => '/^\d*$/',
        'filters' => 'trim',
        'msg' => '错误的排序号'
    ),
    'show'=>array(
        'required' => 0,
        'filters' => 'trim',
    ),
        'desc'=>array(
            'required' => 0,
            'length' => array(0,255),
            'filters' => 'trim',
            'msg' => '错误的描述'
    ),
);

$rules['delcate'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('cid')
    ),
    'cid'=>array(
        'required' => 1,
        'regex' => '/^[1-9][0-9]*$/',
        'filters' => 'trim',
        'msg' => '错误分类ID'
    )
);

$rules['operatecate'] = array(
    '_request' => array('soap', 'secure'),
    '_method' => array(
        'get' => array('cid')
    ),
    'cid'=>array(
        'required' => 0,
        'regex' => '/^[1-9][0-9]*$/',
        'filters' => 'trim',
        'msg' => '错误商品ID'
    )
);

$rules['addcate'] = array(
    '_request' => array('ajax'),
    '_method' => array(
     'post' => array('name', 'gcat_id', 'desc', 'keyword', 'order', 'show')
    ),
    'gcat_id'=>array(
        'required' => 1,
        'regex' => '/^[1-9][0-9]*$/',
        'filters' => 'trim',
        'msg' => '错误分类ID'
    ),
    'name'=>array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(1, 50),
        'msg' => '请输入正确的分类名'
    ),
    'desc'=>array(
        'required' => 0,
        'filters' => 'trim',
        'length' => array(1, 255),
        'msg' => '请输入正确的描述'
    ),
    'keyword'=>array(
        'required' => 0,
        'filters' => 'trim',
        'length' => array(1, 255),
        'msg' => '请输入正确的关键词'
    ),
    'order'=>array(
        'required' => 1,
        'regex' => '/^\d*$/',
        'filters' => 'trim',
        'msg' => '错误排序数字'
    ),
    'show'=>array(
        'required' => 1,
        'regex' => '/^\d$/',
        'filters' => 'trim',
        'msg' => '选择是否显示'
    ),
);
return $rules;
