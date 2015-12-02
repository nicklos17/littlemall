(function(){
    'use strict';
    var addrController = baseController.extend({
        init:function($scope, uriServices, $location){
            this._super($scope);
            //获取地址
            uriServices.getRequest(shopModuleConfig.domain + '/order/addr')
            .then(function(data){
                $scope.addrList = data.addressList;
            }, function(data){
            });

            $scope.editAddr = function(addrId){
               $location.path('/addr/' + addrId);
            }
        },
        defineListeners:function(){
            this._super();
        },
        destroy:function(){}
    });
    addrController.$inject = ['$scope', 'uriServices', '$location'];

    angular.module('controllers.addr', []).controller('addrCtrl', addrController);
}());
