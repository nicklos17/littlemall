<?php
include __DIR__.'/baseRules.php';
$rules['addCoupons'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('num', 'type','starTime','endTime','status')
    ),
    'num' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '请选择数量',
    ),
    'crid' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '请选择类型',
    ),
    'status' => array(
        'required' => 1,
        'range' => array(1, 3),
        'filters' => 'trim',
        'msg' => '请选择状态',
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

$rules['addCouponsRule'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('minAmount', 'maxAmount','amount','status','rangeType','name')
    ),
    'minAmount' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '请填写最小金额',
    ),
    'maxAmount' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '请填写最大金额',
    ),
    'amount' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '请填写抵消金额',
    ),
    'status' => array(
        'required' => 1,
        'range' => array(1, 3),
        'filters' => 'trim',
        'msg' => '请选择状态',
    ),
    'rangeType' => array(
        'required' => 1,
        'range' => array(1, 3, 5),
        'filters' => 'trim',
        'msg' => '请选择状态',
    ),
    'name' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '请选择类型',
    )
);

$rules['editeCouponsRulesStatus'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('status','id')
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

$rules['editeCouponsStatus'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('status','id')
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

$rules['listCouponsRule'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
       'get' => array('name', 'status')
    ),
    'name' => array(
        'required' => 0,
        'filters' => 'trim',
        'msg' => '请选择数量',
    ),
    'status' => array(
        'required' => 1,
        'range' => array(0,1, 3),
        'filters' => 'trim',
        'msg' => '请选择类型',
    ),
);

$rules['listCoupons'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
       'get' => array('sn', 'starTime','endTime','status')
    ),
    'sn' => array(
        'required' => 0,
        'filters' => 'trim',
        'msg' => '请选择数量',
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

return $rules;
