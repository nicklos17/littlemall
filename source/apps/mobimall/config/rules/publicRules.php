<?php
include __DIR__.'/baseRules.php';

$rules['errorAjax'] = array(
        '_request' => array('soap', 'secure'),
        '_method' => array(
             'get' => array('v'),
        ),
    'v'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '错误的回调页面'
    ),
);

return $rules;
