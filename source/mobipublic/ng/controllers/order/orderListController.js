(function(){
    'use strict';
    var orderListController = baseController.extend({
        init:function($scope, uriServices, $location, $route){
            this._super($scope);
            uriServices.getRequest(shopModuleConfig.domain + '/order/index')
            .then(function(data){
                $scope.more = true;
                $scope.orderList = data.list;
                if(data.last == 1)
                    $scope.more = false;
            }, function(data){
                $location.path('/');
            });

            $scope.getStatus = function(orderStatus, orderPayStatus, orderDeliveryStatus){
                var status;
                switch(orderStatus){
                  case 1:
                      if(orderPayStatus == 1)
                          status = '未付款';

                      if(orderPayStatus == 3)
                          status = '已付款';

                      if(orderDeliveryStatus == 3)
                          status = '已发货';
                      break;
                  case 3:
                      status = '已收货';
                      break;
                  case 5:
                      status = '已失效';
                      break;
                  case 7:
                      status = '已取消';
                      break;
                  case 11:
                      status = '已完成';
                      break;
                  }
                return status;
            };

             $scope.getActStatus = function(orderStatus, orderPayStatus, orderDeliveryStatus){
                var status = {};
                status.status = true;
                if(orderDeliveryStatus == 3 && orderPayStatus == 3){
                    status.actStatus = '确认收货';
                    status.color = 'blue';
                }

                else if(orderPayStatus == 1 && orderStatus == 1){
                    status.actStatus = '立即支付';
                    status.color = 'orange';
                }

                else if(orderStatus == 11){
                    status.actStatus = '申请售后';
                    status.color = 'blue';
                }

                else
                    status.status = false;

                return status;
            };

             $scope.act = function(orderStatus, orderPayStatus, orderDeliveryStatus, orderId, orderSn){

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
                    //return false;
                }

                else if(orderPayStatus == 1 && orderStatus == 1){
                    //保存订单号
                    uriServices.getRequest(shopModuleConfig.domain + '/order/payments?id=' + orderId)
                    .then(function(data){
                        $location.path('/success');
                        return fasle;
                    },function(data){
                    });
                }

                else if(orderStatus == 11){
                    $location.path('/support/' + orderSn);
                }

                else{
                    return false;
                }

            };

            $scope.page = 2;
            $scope.getMore = function(){
                uriServices.getRequest(shopModuleConfig.domain + '/order/index?page=' + $scope.page)
                .then(function(data){
                    if(data.last == 1)
                        $scope.more = false;
                    if(data.list.length == 0)
                        $scope.more = false;
                    else{
                        $scope.orderList = $scope.orderList.concat(data.list);
                        $scope.page ++;
                    }
                }, function(data){

                });
            };

            $scope.getOrderInfo = function(sn){
                $location.path('/detail/' + sn);
            };

        },
        defineListeners:function(){
            this._super();
        },
        destroy:function(){}
    });
    orderListController.$inject = ['$scope', 'uriServices', '$location', '$route'];

    angular.module('controllers.orderList', []).controller('orderListCtrl', orderListController);
}());
