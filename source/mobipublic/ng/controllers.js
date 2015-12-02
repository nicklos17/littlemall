(function(){
    'use strict';
    angular.module(
        'shopManager.controllers',
        ['controllers.index', 'controllers.goods', 'controllers.cart', 'controllers.confirm',
        'controllers.address', 'controllers.orderSuccess', 'controllers.orderList', 'controllers.orderDetail',
        'controllers.coupons', 'controllers.addr', 'controllers.supportApply', 'controllers.support',
        'controllers.supportShippingSn', 'controllers.error', 'controllers.code']
    );
})();
