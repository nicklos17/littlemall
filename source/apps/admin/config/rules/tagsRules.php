<?php
include __DIR__.'/baseRules.php';

$rules['edittags'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('tid', 'name')
    ),
    'tid'=>array(
        'required' => 1,
        'regex' => '/^[1-9][0-9]*$/',
        'filters' => 'trim',
        'msg' => '错误标签ID'
    ),
    'name'=>array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(1, 30),
        'msg' => '请输入正确的标签名'
    )
);

$rules['deltags'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('tid')
    ),
    'tid'=>array(
        'required' => 1,
        'regex' => '/^[1-9][0-9]*$/',
        'filters' => 'trim',
        'msg' => '错误标签ID'
    )
);

return $rules;
