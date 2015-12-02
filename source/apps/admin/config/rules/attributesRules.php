<?php
include __DIR__.'/baseRules.php';

$rules['editattr'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('aid', 'name', 'status', 'rank')
    ),
    'aid'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '错误分类ID'
    ),
    'status'=>array(
        'required' => 1,
        'filters' => 'trim',
        'valueis' => 1,
        'msg' => '请选择正确的状态'
    ),
    'name'=>array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(1, 50),
        'msg' => '请输入正确的分类名'
    ),
    'rank'=>array(
        'default' => 0,
        'required' => 0,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '错误的排序'
    )
);

$rules['delattr'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('aid')
    ),
    'aid'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '错误分类ID'
    )
);

$rules['addattr'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('aid', 'name', 'rank')
    ),
    'aid'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '错误分类ID'
    ),
    'name'=>array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(1, 50),
        'msg' => '请输入正确的分类名'
    ),
    'rank'=>array(
        'default' => 0,
        'required' => 0,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '错误的排序'
    )
);
return $rules;
