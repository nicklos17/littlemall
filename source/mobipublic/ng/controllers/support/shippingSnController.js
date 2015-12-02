(function(){
    'use strict';
    var shippingSnController = baseController.extend({
        init:function($scope, uriServices, $location, $routeParams){
            this._super($scope);

            var sn = $routeParams.sn;
            $scope.shipComps = {'5': '顺丰快递', '1': '申通快递', '7': '顺风四日达', '3': '圆通速递', '9': 'EMS'};
            $scope.shipComp = '1';

            uriServices.getRequest(shopModuleConfig.domain + '/support/express?supportSn=' + sn)
            .then(function(data){
                if(data.ret == 0){
                    alert(data.msg);
                    $location.path('/support');
                    return false;
                }
            },function(data){
                $location.path('/support');
                return false;
            });

            $scope.saveShippingSn = function(){
                if(!$scope.shipComp || !$scope.shippingSn){
                    alert('请选择快递公司和快递单号');
                    return false;
                }
                uriServices.postRequest(shopModuleConfig.domain + '/support/submitExpress',
                {'ship_comp' : $scope.shipComp, 'ship_sn' : $scope.shippingSn})
                .then(function(data){
                    if(data.ret == 1){
                        alert('提交快递单号成功');
                        $location.path('/support');
                    }else{
                        alert('失败,请重试~');
                    }
                    return;
                }, function(data){
                    alert('网络异常,请重试~');
                    $location.path('/support');
                });
            };
        },
        defineListeners:function(){
            this._super();
        },
        destroy:function(){}
    });
    shippingSnController.$inject = ['$scope', 'uriServices', '$location', '$routeParams'];

    angular.module('controllers.supportShippingSn', []).controller('supportShippingSnCtrl', shippingSnController);
}());
