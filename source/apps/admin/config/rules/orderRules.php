<?php
include __DIR__.'/baseRules.php';
$rules['detail'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'get' => array('orderId')
    ),

    'orderId' => array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '请选择数量',
    ),
);

$rules['editgoods'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('orderGoodsId', 'orderId', 'goodsId', 'colorId', 'sizeId', 'price', 'goodsNum')
    ),

    'orderGoodsId'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '订单商品错误'
    ),

    'orderId'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '订单ID错误'
    ),

    'goodsId'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '商品ID错误'
    ),

    'colorId'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '没有这种颜色'
    ),

    'sizeId'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '没有这种尺码'
    ),

    'price'=>array(
        'required' => 1,
        'regex' => '/^[0-9]+([.]{1}[0-9]+){0,1}$/',
        'filters' => 'trim',
        'msg' => '价格格式错误'
    ),

    'goodsNum'=>array(
        'required' => 1,
        'regex' => '/^[1-9]\d*$/',
        'filters' => 'trim',
        'msg' => '价格格式错误'
    )
);

$rules['editorder'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array(
            'orderId',
            'shippingType',
            'mobi',
            'province',
            'city',
            'district',
            'street',
            'addr',
            'consignee'
        ) 
    ),

    'orderId' => array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '订单id错误',
    ),

    'shippingType' => array(
        'required' => 1,
        'range' => array(1, 3),
        'filters' => 'trim',
        'msg' => '请选择数量',
    ),

    'mobi' => mobile(),

    'province' => array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '不是正确的地址',
    ),

    'city' => array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '不是正确的地址',
    ),

    'district' => array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '不是正确的地址',
    ),

    'street' => array(
        'required' => 0,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '不是正确的地址',
    ),
     
    'addr' => array(
        'required' => 1,
        'length' => array(0, 255),
        'filters' => 'trim',
        'msg' => '地址格式错误',
    ),

    'consignee' => array(
        'required' => 1,
        'length' => array(0, 60),
        'filters' => 'trim',
        'msg' => '收件人名字格式错误',
    ),

);

$rules['detail'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'get' => array('orderId')
    ),

    'orderId' => array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '订单id错误',
    ),
);

$rules['goodsattr'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('goodsId')
    ),

'goodsId' => array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '商品id错误',
    ),
);

$rules['getsizebycid'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('goodsId', 'colorId')
    ),

'goodsId' => array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '商品id错误',
    ),

'colorId' => array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '颜色id错误',
    ),
);

$rules['editstatus'] = array(
    '_request' => array('soap', 'secure', 'ajax'),
    '_method' => array(
        'post' => array('orderId', 'operate')
    ),

'orderId'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '订单ID错误'
    ),

'operate' => array(
        'required' => 1,
        'range' => array('invalid', 'applyBack', 'deliver', 'orderSuccess', 'orderClose'),
        'filters' => 'trim',
        'msg' => '操作错误',
    ),
);

$rules['batchoperate'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('orderIds', 'operate')
    ),
    'orderIds' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => ''
    ),

    'operate' => array(
        'required' => 1,
        'range' => array('batchInvalid', 'batchDeliver', 'batchApplyBack', 'batchOrderSuccess', 'batchOrderClose'),
        'filters' => 'trim',
        'msg' => '请检查操作的合法性',
    ),
);

$rules['getgoods'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('goodsName')
    ),
    'goodsName' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => ''
    ),
);

$rules['checkgoods'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('goodsId', 'colorId', 'sizeId')
    ),

'goodsId'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '商品ID错误'
    ),

'colorId'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '颜色ID错误'
    ),

'sizeId'=>array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '尺码ID错误'
    ),
);

$rules['create'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array(
            'goodsData',
            'consignee',
            'mobi',
            'province',
            'city',
            'district',
            'street',
            'addr',
            'shippingFee',
            'orderFee'
        ) 
    ),

    'goodsData' => array(
        'required' => 1,
        'msg' => '订单商品有误',
    ),

        'consignee' => array(
        'required' => 1,
        'length' => array(0, 60),
        'filters' => 'trim',
        'msg' => '收件人名字格式错误',
    ),

    'mobi' => mobile(),

    'province' => array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '不是正确的地址',
    ),

    'city' => array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '不是正确的地址',
    ),

    'district' => array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '不是正确的地址',
    ),

    'street' => array(
        'required' => 0,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '不是正确的地址',
    ),
     
    'addr' => array(
        'required' => 1,
        'length' => array(0, 255),
        'filters' => 'trim',
        'msg' => '地址格式错误',
    ),

    'shippingFee'=>array(
        'required' => 1,
        'regex' => '/^[0-9]+([.]{1}[0-9]+){0,1}$/',
        'filters' => 'trim',
        'msg' => '快递金额格式错误'
    ),

    'orderFee'=>array(
        'required' => 1,
        'regex' => '/^[0-9]+([.]{1}[0-9]+){0,1}$/',
        'filters' => 'trim',
        'msg' => '应付金额格式错误'
    ),

);

return $rules;