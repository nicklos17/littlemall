<?php
include __DIR__.'/baseRules.php';
$rules['index'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
       'get' => array('code', 'type','starTime','endTime','status','gid')
    ),
    'code' => array(
        'required' => 0,
        'filters' => 'trim',
        'msg' => '请选择数量',
    ),
    'gid' => array(
        'required' => 0,
        'filters' => 'trim',
        'msg' => '请选择商品',
    ),
    'type' => array(
        'required' => 1,
        'range' => array(0,1, 3),
        'filters' => 'trim',
        'msg' => '请选择类型',
    ),
    'status' => array(
        'required' => 1,
        'range' => array(0,1, 3),
        'filters' => 'trim',
        'msg' => '请选择类型',
    ),
    'starTime' => array(
        'required' => 0,
        'filters' => 'trim',
        'regex' => '/^[0-9]{4}(\-|\/)[0-9]{1,2}(\-|\/)[0-9]{1,2}(|\s+[0-9]{1,2}(|:[0-9]{1,2}(|:[0-9]{1,2})))$/ ',
        'msg' => '请输入时间'
    ),
    'endTime' => array(
        'required' => 0,
        'filters' => 'trim',
        'regex' => '/^[0-9]{4}(\-|\/)[0-9]{1,2}(\-|\/)[0-9]{1,2}(|\s+[0-9]{1,2}(|:[0-9]{1,2}(|:[0-9]{1,2})))$/ ',
        'msg' => '请输入时间'
    ),
);

$rules['addcode'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('num', 'type','starTime','endTime')
    ),
    'num' => array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '请选择数量',
    ),
    'type' => array(
        'required' => 1,
        'range' => array(1, 3),
        'filters' => 'trim',
        'msg' => '请选择类型',
    ),
    'starTime' => array(
        'required' => '1',
        'filters' => 'trim',
        'regex' => '/^[0-9]{4}(\-|\/)[0-9]{1,2}(\-|\/)[0-9]{1,2}(|\s+[0-9]{1,2}(|:[0-9]{1,2}(|:[0-9]{1,2})))$/ ',
        'msg' => '请输入时间'
    ),
    'endTime' => array(
        'required' => '1',
        'filters' => 'trim',
        'regex' => '/^[0-9]{4}(\-|\/)[0-9]{1,2}(\-|\/)[0-9]{1,2}(|\s+[0-9]{1,2}(|:[0-9]{1,2}(|:[0-9]{1,2})))$/ ',
        'msg' => '请输入时间'
    ),
);

$rules['editeCode'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array( 'status','id')
    ),
    'id' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '请选择数量',
    ),
    'status' => array(
        'required' => 1,
        'range' => array(1,3),
        'filters' => 'trim',
        'msg' => '请选择类型',
    ),
);

return $rules;
