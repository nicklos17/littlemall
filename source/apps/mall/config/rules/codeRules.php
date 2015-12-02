<?php
include __DIR__.'/baseRules.php';

$rules['checkCode'] = array(
        '_request' => array('soap', 'secure'),
        '_method' => array(
             'post' => array('code','captcha'),
        ),
        'code'=>array(
            'required' => 1,
            'filters' => 'trim',
            'length' => 16,
            'msg' => '云码格式错误'
        )
);
return $rules;