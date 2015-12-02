(function(){
    'use strict';
    var orderSuccessController = baseController.extend({
        init:function($scope, uriServices, $location){
            this._super($scope);
            uriServices.getRequest(shopModuleConfig.domain + '/order/payments')
            .then(function(data){
                if(data.ret == 1){
                    $scope.orderInfo = data.order;
                }else
                    $location.path('/cart');
            }, function(data){
                $location.path('/cart');
            });

            $scope.choose = true;
            $scope.pay = function(){
                if($scope.choose){
                    //跳转支付宝
                    window.location = shopModuleConfig.domain + '/pay/alipay';
                }else{
                    //微信支付
                    window.location = shopModuleConfig.domain + '/pay/wxpay';
                }
            }
        },
        defineListeners:function(){
            this._super();
        },
        destroy:function(){}
    });
    orderSuccessController.$inject = ['$scope', 'uriServices', '$location'];

    angular.module('controllers.orderSuccess', []).controller('orderSuccessCtrl', orderSuccessController);
}());
