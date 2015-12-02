(function(){
    'use strict';
    var supportController = baseController.extend({
        init:function($scope, uriServices, $location){
            this._super($scope);

            uriServices.getRequest(shopModuleConfig.domain + '/support/index?page=1')
            .then(function(data){
                if(data.ret == 0){
                    alert('错误的页码~');
                    $location.path('/support');
                    return false;
                }
                $scope.more = true;
                if(data.last == 1)
                    $scope.more = false;
                var len = data.supOrders.length, i = 0;
                $scope.list = [];
                for(i; i < len; i++){
                    $scope.list[i] = data.supOrders[i];
                    switch(data.supOrders[i].bord_status){
                        case 1:
                            $scope.list[i].bord_status = '未审核';
                            break;
                        case 3:
                            $scope.list[i].bord_status = '已审核';
                            break;
                        case 11:
                        case 15:
                            $scope.list[i].bord_status = '进行中';
                            break;
                        case 5:
                            $scope.list[i].bord_status = '审核未通过';
                            break;
                        case 7:
                            $scope.list[i].bord_status = '已解决';
                            break;
                        case 9:
                        case 13:
                            $scope.list[i].bord_status = '已关闭';
                            break;
                    }
                    //if(data.supOrders[i].bord_status == 3 && !data.supOrders[i].user_shipping_sn)
                     //  $scope.list[i].bord_shipping_sn = false;
                }

            },function(data){
            });

            $scope.page = 2;
            $scope.getMore = function(){
                uriServices.getRequest(shopModuleConfig.domain + '/support/index?page=' + $scope.page)
                .then(function(data){
                    if(data.last == 1)
                        $scope.more = false;
                    if(data.length == 0 || data.supOrders.length == 0)
                        $scope.more = false;
                    else{
                        var len = data.supOrders.length, i = 0, list = [];
                        for(i; i < len; i++){
                            list[i] = data.supOrders[i];
                            switch(data.supOrders[i].bord_status){
                                case 1:
                                    list[i].bord_status = '未审核';
                                    break;
                                case 3:
                                    list[i].bord_status = '已审核';
                                    break;
                                case 11:
                                case 15:
                                    list[i].bord_status = '进行中';
                                    break;
                                case 5:
                                    list[i].bord_status = '审核未通过';
                                    break;
                                case 7:
                                    list[i].bord_status = '已解决';
                                    break;
                                case 9:
                                case 13:
                                    list[i].bord_status = '已关闭';
                                    break;
                            }
                        }
                        $scope.list = $scope.list.concat(list);
                        $scope.page ++;
                        //if(data.supOrders[i].bord_status == 3 && !data.supOrders[i].user_shipping_sn)
                         //   list[i].bord_shipping_sn = false;
                    }
                }, function(data){
                });
            };

            $scope.addShippingSn = function(bordSn){
                $location.path('/support/shippingSn/' + bordSn);
            }
        },
        defineListeners:function(){
            this._super();
        },
        destroy:function(){}
    });
    supportController.$inject = ['$scope', 'uriServices', '$location'];

    angular.module('controllers.support', []).controller('supportCtrl', supportController);
}());
