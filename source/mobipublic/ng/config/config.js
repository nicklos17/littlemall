shopModuleConfig.route = [
    {
        'path' : '/',
        'templateUrl' : 'index/index.html',
        'controller' : 'indexCtrl'
    },
    {
        'path' : '/goods/:gid',
        'templateUrl' : 'goods/goods.html',
        'controller' : 'goodsCtrl'
    },
    {
        'path' : '/cart',
        'templateUrl' : 'cart/cart.html',
        'controller' : 'cartCtrl'
    },
    {
        'path' : '/confirm',
        'templateUrl' : 'confirm/confirm.html',
        'controller' : 'confirmCtrl'
    },
    {
        'path' : '/success',
        'templateUrl' : 'order/success.html',
        'controller' : 'orderSuccessCtrl'
    },
    {
        'path' : '/order',
        'templateUrl' : 'order/list.html',
        'controller' : 'orderListCtrl'
    },
    {
        'path' : '/detail/:sn',
        'templateUrl' : 'order/detail.html',
        'controller' : 'orderDetailCtrl'
    },
    {
        'path' : '/coupons',
        'templateUrl' : 'coupons/list.html',
        'controller' : 'couponsCtrl'
    },
    {
        'path' : '/addr',
        'templateUrl' : 'addr/list.html',
        'controller' : 'addrCtrl'
    },
    {
        'path' : '/addr/:addrId',
        'templateUrl' : 'addr/edit.html',
    },
    {
        'path' : '/addrAdd',
        'templateUrl' : 'addr/add.html',
    },
    {
        'path' : '/support/:sn',
        'templateUrl' : 'support/apply.html',
        'controller' : 'supportApplyCtrl'
    },
    {
        'path' : '/support',
        'templateUrl' : 'support/list.html',
        'controller' : 'supportCtrl'
    },
    {
        'path' : '/support/shippingSn/:sn',
        'templateUrl' : 'support/shippingSn.html',
        'controller' : 'supportShippingSnCtrl'
    },
    {
        'path' : '/supportDetail',
        'templateUrl' : 'support/detail.html',
        'controller' : 'supportDetailCtrl'
    },
    {
        'path' : '/code',
        'templateUrl' : 'code/code.html',
        'controller' : 'codeCtrl'
    },
    {
        'path' : '/404',
        'controller' : 'errorCtrl'
    },

];

shopModuleConfig.domain = 'http://test.m.mall.yunduo.com';

//售后类型 个项时间

shopModuleConfig.supportTime = {
    'back': 7*86400,
    'exchange': 30*86400,
    'supportTime': 183*86400
}

//物流类型
shopModuleConfig.express = {
    1: '申通快递',
    3: '圆通速递',
    5: '顺风速递',
    7: '顺风四日达',
    9: 'EMS'
}
