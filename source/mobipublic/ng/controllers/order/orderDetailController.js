(function(){
    'use strict';
    var orderDetailController = baseController.extend({
        init:function($scope, uriServices, $location, $routeParams, $route){
            this._super($scope);
            var sn = $routeParams.sn;
            uriServices.getRequest(shopModuleConfig.domain + '/order/detail?sn=' + sn).then(function(data){
                if(data.ret == 0){
                  alert('错误的订单号~');
                  $location.path('/order');
                  return false;
                }
                $scope.orderGoods = [];
                $scope.orderDetail = data.orderInfo;
                $scope.orderDetail.order_shipping_type = shopModuleConfig.express[$scope.orderDetail.order_shipping_id];
                var len = data.orderGoods.length, i = 0;
                for(i; i < len; i++){
                  $scope.orderGoods[i] = data.orderGoods[i];
                  $scope.orderGoods[i].attrs_info = JSON.parse(data.orderGoods[i].attrs_info);
                };
            }, function(data){
                $location.path('/order');
            });

          $scope.act = function(orderPayStatus, orderDeliveryStatus, orderSn){

              if(orderDeliveryStatus == 3 && orderPayStatus == 3){
                  if(confirm('确认收货~')){
                      uriServices.postRequest(shopModuleConfig.domain + '/order/confirmOrder', {'sn' : orderSn})
                      .then(function(data){
                          if(data.ret == 1){
                              alert('成功确认~');
                              $route.reload();
                          }

                      }, function(data){
                          alert('确认失败,请重试~');
                      });
                  }
              }else
                return false;
          };

          $scope.actApply = function(orderStatus, orderSn){
            if(orderStatus != 11)
              return false;
            else
              $location.path('/support/' + orderSn);
          }

        },
        defineDetaileners:function(){
            this._super();
        },
        destroy:function(){}
    });
    orderDetailController.$inject = ['$scope', 'uriServices', '$location', '$routeParams', '$route'];

    angular.module('controllers.orderDetail', []).controller('orderDetailCtrl', orderDetailController);
}());
