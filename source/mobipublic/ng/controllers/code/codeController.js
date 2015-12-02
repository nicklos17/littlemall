(function(){
    'use strict';
    var codeController = baseController.extend({
        init:function($scope, uriServices, $location){
            this._super($scope);
            $scope.codeImg = shopModuleConfig.domain + '/captcha/getCode';

            $scope.changeCode = function(){
               $scope.codeImg = shopModuleConfig.domain + '/captcha/getCode?tm=' + Math.random();
            };

            $scope.codeUse= function(){
                if(!$scope.code){
                    alert('请先输入16位云码');
                    return false;
                }
                if(!$scope.captcha){
                    alert('请先输入验证码');
                    return false;
                }
                uriServices.postRequest(shopModuleConfig.domain + '/code/checkCode',
                {'captcha' : $scope.captcha, 'code' : $scope.code})
                .then(function(data){
                    if(data.ret == 1){
                        $location.path(data.url);
                    }else{
                        alert(data.msg);
                        return false;
                    }
                },function(data){
                    return false;
                })
            };
        },
        defineListeners:function(){
            this._super();
        },
        destroy:function(){}
    })

    codeController.$inject = ['$scope', 'uriServices', '$location'];
    angular.module('controllers.code', []).controller('codeCtrl', codeController);
}());
