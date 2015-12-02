<?php
include __DIR__.'/baseRules.php';

$rules['add'] = array(
        '_request' => array('soap', 'secure', 'ajax'),
        '_method' => array(
             'post' => array('gid', 'attrs-ids', 'num'),
        ),
        'gid'=>array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*$/',
            'msg' => '错误的商品'
        ),
        'attrs-ids' => array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*,[1-9][0-9]*$/',
            'msg' => '错误的商品属性'
        ),
        'num'=>array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*$/',
            'msg' => '数量填写错误'
        ),
);

$rules['del'] = array(
        '_request' => array('soap', 'secure', 'ajax'),
        '_method' => array(
             'post' => array('gid', 'attrs-ids'),
        ),
        'gid'=>array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*$/',
            'msg' => '错误的商品'
        ),
        'attrs-ids' => array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*,[1-9][0-9]*$/',
            'msg' => '错误的商品属性'
        )
);

$rules['incr'] = array(
        '_request' => array('soap', 'secure', 'ajax'),
        '_method' => array(
             'post' => array('gid', 'attrs-ids'),
        ),
        'gid'=>array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*$/',
            'msg' => '错误的商品'
        ),
        'attrs-ids' => array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*,[1-9][0-9]*$/',
            'msg' => '错误的商品属性'
        )
);

$rules['decr'] = array(
        '_request' => array('soap', 'secure', 'ajax'),
        '_method' => array(
             'post' => array('gid', 'attrs-ids'),
        ),
        'gid'=>array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*$/',
            'msg' => '错误的商品'
        ),
        'attrs-ids' => array(
            'required' => 1,
            'filters' => 'trim',
            'regex' => '/^[1-9][0-9]*,[1-9][0-9]*$/',
            'msg' => '错误的商品属性'
        )
);

$rules['batchdel'] = array(
        '_request' => array('soap', 'secure', 'ajax'),
        '_method' => array(
             'post' => array('gids', 'attrs-ids'),
        ),
        'gids'=>array(
            'required' => 1,
            'filters' => 'trim',
        ),
        'attrs-ids' => array(
            'required' => 1,
            'filters' => 'trim',
        )
);

return $rules;
