<?php

include __DIR__.'/baseRules.php';

$rules['getCities'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('pro_id')
    ),
    'pro_id'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '省份ID'
    ),
);

$rules['getDistricts'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('city_id')
    ),
    'city_id'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '市ID'
    ),
);

$rules['getStreets'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('dis_id')
    ),
    'dis_id'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '县区ID'
    ),
);

$rules['editRegion'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('type', 'name', 'region_id')
    ),
    'region_id'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '县区ID'
    ),
    'type'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '类型ID'
    ),
    'name'=>array(
        'required' => 1,
        'filters'  => 'trim',
        'length'   => array(1, 30),
        'msg'      => '请输入正确的地名'
    ),
    'code'=>array(
        'required' => 0,
        'filters'  => 'trim',
        'length'   => array(1, 30),
        'msg'      => '代码格式错误'
    )
);

$rules['addRegion'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('type', 'name', 'pro_id', 'city_id', 'dis_id')
    ),
    'type'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '类型ID'
    ),
    'name'=>array(
        'required' => 1,
        'filters'  => 'trim',
        'length'   => array(1, 30),
        'msg'      => '请输入正确的地名'
    ),
    'code'=>array(
        'required' => 0,
        'filters'  => 'trim',
        'length'   => array(1, 30),
        'msg'      => '代码格式错误'
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
    )
);

$rules['deleteRegion'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('type', 'name', 'region_id')
    ),
    'type'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '类型ID'
    ),
    'reg_id_arr'=>array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '地区ID'
    ),
);

return $rules;