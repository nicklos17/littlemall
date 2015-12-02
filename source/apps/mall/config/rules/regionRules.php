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

return $rules;
