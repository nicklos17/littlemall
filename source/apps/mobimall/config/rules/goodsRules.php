<?php
include __DIR__.'/baseRules.php';

// $rules['index'] = array(
//         '_request' => array('soap', 'secure'),
//         '_method' => array(
//              'get' => array('id'),
//         ),
//         'id'=>array(
//             'required' => 1,
//             'filters' => 'trim',
//             'regex' => '/^[1-9][0-9]*$/',
//             'msg' => '不存在的商品'
//         )
// );

//过滤提供商品id,过滤的属性id,另外类属性的父id(用于遍历组合)
$rules['filterCol'] = array(
        '_request' => array('soap', 'secure', 'ajax'),
        '_method' => array(
             'post' => array('gid', 'filter-id', 'parent-other-id'),
        ),
        'gid'=>array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*$/',
            'msg' => '不存在的商品'
        ),
        'filter-id'=>array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*$/',
            'msg' => '不存在的属性'
        ),
        'parent-other-id'=>array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*$/',
            'msg' => '不存在的属性'
        )
);

$rules['filterSize'] = array(
        '_request' => array('soap', 'secure', 'ajax'),
        '_method' => array(
             'post' => array('gid', 'filter-id', 'parent-other-id'),
        ),
        'gid'=>array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*$/',
            'msg' => '不存在的商品'
        ),
        'filter-id'=>array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*$/',
            'msg' => '不存在的属性'
        ),
        'parent-other-id'=>array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*$/',
            'msg' => '不存在的属性'
        )
);

return $rules;