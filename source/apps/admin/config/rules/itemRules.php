<?php
include __DIR__.'/baseRules.php';

$rules['index'] = array(
    '_request' => array(),
    '_method' => array(
        'get' => array('goods_name', 'goods_status', 'goods_sn')
    ),

    'goods_name' => array(
            'required' => 0,
            'filters' => 'trim',
            'length' => array(0, 120),
            'msg' => '请先填写商品名',
    ),
    'goods_status' => array(
        'required' => 0,
        'filters' => 'trim',
        'range' => array(1, 3),
        'msg' => '错误的商品状态',
    ),
    'goods_sn' => array(
        'required' => 0,
        'filters' => 'trim',
        'length' => array(0, 60),
        'msg' => '请输入唯一的商品sn码',
    ),
);

$rules['insertitem'] = array(
    '_request' => array(),
    '_method' => array(
       'post' => array('gcat_id', 'goods_name', 'goods_sn', 'goods_name_style', 'goods_clicks', 'goods_sales', 'goods_market', 'goods_price', 'goods_promote', 'goods_promote_start', 'goods_promote_end', 'goods_start', 'goods_end', 'goods_tags', 'goods_brief', 'editorValue', 'goods_status', 'goods_type', 'goods_order', 'goods_is_new', 'goods_is_promote', 'goods_is_hot', 'goods_is_warranty', 'goods_is_shipping', 'attr_ids', 'g_attr_barcode', 'g_attr_stocks', 'g_attr_enable', 'g_attrs_nums')
    ),
    'gcat_id' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '所属分类错误',
    ),
    'goods_name' => array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(0, 120),
        'msg' => '请先填写商品名',
    ),
    'goods_status' => array(
        'required' => 1,
        'filters' => 'trim',
        'range' => array(1,3),
        'msg' => '错误的商品状态',
    ),
    'goods_sn' => array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(0, 60),
        'msg' => '请输入唯一的商品sn码',
    ),
    'goods_clicks' => array(
        'required' => 0,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '请输入正确的商品点击数',
    ),
    'goods_sales' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '请输入正确的商品卖出数',
    ),
    'goods_market' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^(\d*)?\.\d{2}$/',
        'msg' => '请输入正确的商品的市场价',
    ),
    'goods_price' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^(\d*)?\.\d{2}$/',
        'msg' => '请输入正确的商品的本店价',
    ),
    'goods_is_new' => array(
        'required' => 1,
        'range' => array(1, 3),
        'filters' => 'trim',
        'msg' => '请选择类型',
    ),
    'goods_is_promote' => array(
        'required' => 1,
        'range' => array(1, 3),
        'filters' => 'trim',
        'msg' => '请选择类型',
    ),
    'goods_is_hot' => array(
        'required' => 1,
        'range' => array(1, 3),
        'filters' => 'trim',
        'msg' => '请选择类型',
    ),
    'goods_is_warranty' => array(
        'required' => 1,
        'range' => array(1, 3),
        'filters' => 'trim',
        'msg' => '请选择类型',
    ),
    'goods_is_shipping' => array(
        'required' => 1,
        'range' => array(1, 3),
        'filters' => 'trim',
        'msg' => '请选择类型',
    ),
    'attr_ids' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '错误的颜色/尺码',
    ),
    'g_attr_barcode' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '请输入条形码',
    ),
    'g_attr_stocks' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '请输入库存量',
    ),
    'g_attr_enable' => array(
        'required' => 0,
        'filters' => 'trim',
        //'range' => array(1, 3),
        'msg' => '请选择类型',
    ),
);

$rules['attrAlter'] = array(
    '_request' => array('ajax'),
    '_method' => array(
       'post' => array('goods_id', 'attr_ids', 'g_attr_barcode', 'g_attr_stocks', 'g_attr_enable', 'g_attrs_nums')
    ),
    'goods_id' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '商品编号错误',
    ),
    'attr_ids' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '错误的颜色/尺码',
    ),
    'g_attr_barcode' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '请输入条形码',
    ),
    'g_attr_stocks' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '请输入库存量',
    ),
    'g_attr_enable' => array(
        'required' => 0,
        'filters' => 'trim',
        'range' => array(1, 3),
        'msg' => '请选择类型',
    ),
    'g_attrs_nums' => array(
        'required' => 0,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '错误的数量',
    )
);


$rules['delitem'] = array(
    '_request' => array('ajax'),
    '_method' => array(
       'post' => array('goodsId')
    ),
    'goodsId' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '请选择正确的商品',
    )
);

$rules['edititem'] = array(
    '_request' => array(),
    '_method' => array(
        'get' => array('gid')
    ),

'gid' => array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '请选择正确的商品编辑',
    ),
);

$rules['updateitem'] = array(
    '_request' => array(),
    '_method' => array(
       'post' => array('gcat_id', 'goods_name', 'goods_sn', 'goods_name_style', 'goods_clicks', 'goods_sales', 'goods_market', 'goods_price', 'goods_promote', 'goods_promote_start', 'goods_promote_end', 'goods_start', 'goods_end', 'goods_tags', 'goods_brief', 'editorValue', 'goods_status', 'goods_type', 'goods_order', 'goods_is_new', 'goods_is_promote', 'goods_is_hot', 'goods_is_warranty', 'goods_is_shipping')
    ),
    'gcat_id' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '所属分类错误',
    ),
    'goods_name' => array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(0, 120),
        'msg' => '请先填写商品名',
    ),
    'goods_status' => array(
        'required' => 1,
        'filters' => 'trim',
        'range' => array(1,3),
        'msg' => '错误的商品状态',
    ),
    'goods_sn' => array(
        'required' => 1,
        'filters' => 'trim',
        'length' => array(0, 60),
        'msg' => '请输入唯一的商品sn码',
    ),
    'goods_clicks' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '请输入正确的商品点击数',
    ),
    'goods_sales' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '请输入正确的商品卖出数',
    ),
    'goods_promote' => array(
        'required' => 0,
        'filters' => 'trim',
        'regex' => '/^(\d*)?\.\d{2}$/',
        'msg' => '请输入正确的促销价格，保留2位小数',
    ),
    'goods_market' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^(\d*)?\.\d{2}$/',
        'msg' => '请输入正确的商品的市场价，保留2位小数',
    ),
    'goods_price' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^(\d*)?\.\d{2}$/',
        'msg' => '请输入正确的商品的本店价，保留2位小数',
    ),
    'goods_is_new' => array(
        'required' => 1,
        'range' => array(1, 3),
        'filters' => 'trim',
        'msg' => '请选择类型',
    ),
    'goods_is_promote' => array(
        'required' => 1,
        'range' => array(1, 3),
        'filters' => 'trim',
        'msg' => '请选择类型',
    ),
    'goods_is_hot' => array(
        'required' => 1,
        'range' => array(1, 3),
        'filters' => 'trim',
        'msg' => '请选择类型',
    ),
    'goods_is_warranty' => array(
        'required' => 1,
        'range' => array(1, 3),
        'filters' => 'trim',
        'msg' => '请选择类型',
    ),
    'goods_is_shipping' => array(
        'required' => 1,
        'range' => array(1, 3),
        'filters' => 'trim',
        'msg' => '请选择类型',
    ),
);

$rules['delgoodsimg'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('gid', 'img')
    ),

    'gid' => array(
            'required' => 1,
            'regex' => '/^\d+$/',
            'filters' => 'trim',
            'msg' => '错误的商品id',
        ),
    'img' => array(
            'required' => 1,
            'filters' => 'trim',
            'msg' => '错误的商品图片',
        ),
);

$rules['existGoodsName'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('name')
    ),
    'name' => array(
            'required' => 1,
            'filters' => 'trim',
            'msg' => '商品名必填',
        ),
);

$rules['existGoodsSn'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('sn')
    ),
    'sn' => array(
            'required' => 1,
            'filters' => 'trim',
            'msg' => '商品sn码必填',
        ),
);

$rules['existGoodsNameEdit'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('name', 'gid')
    ),
    'name' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '商品名必填',
        ),
    'gid' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '商品id不能为空',
        ),
);

$rules['existGoodsSnEdit'] = array(
    '_request' => array('ajax'),
    '_method' => array(
        'post' => array('sn', 'gid')
    ),
    'sn' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '商品sn码必填',
        ),
    'gid' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '商品id不能为空',
        ),
);

$rules['uploadAttrImg'] = array(
    '_method' => array(
        'get' => array('gid', 'aid')
    ),
    'gid' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '商品id不能为空',
        ),
    'aid' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '属性id不正确',
        ),
);
return $rules;
