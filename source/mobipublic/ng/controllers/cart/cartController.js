(function(){
    'use strict';
    var cartController = baseController.extend({
        init:function($scope, uriServices, $location){
            this._super($scope);
            uriServices.getRequest(shopModuleConfig.domain + '/cart/index/').then(function(data){
                if(data.length == 0){
                    alert('购物车为空,请先购买商品~');
                    $location.path('/');
                }
                $scope.cartList = data;
                $scope.totalNum = 0;
                $scope.checkNum = 0;
                $scope.totalPrice = 0;
                for(var i in data){
                    if(data[i].goods_status == 1 && !data[i].checked){
                        $scope.checkNum++;
                        $scope.totalNum++;
                        $scope.totalPrice = (parseFloat($scope.totalPrice) + parseFloat(data[i].goods_price) *100 * parseInt(data[i].car_good_num) / 100).toFixed(2);
                    }
                };
            }, function(data){
                $location.path('/');
            });

            $scope.checkData = function(idx){
                if($scope.cartList[idx].checked){
                    $scope.checkNum++;
                    $scope.totalPrice = (parseFloat($scope.totalPrice) + parseFloat($scope.cartList[idx].goods_price) * 100 * parseInt($scope.cartList[idx].car_good_num)/100).toFixed(2);
                }else{
                    $scope.checkNum--;
                    $scope.totalPrice = (parseFloat($scope.totalPrice) - parseFloat($scope.cartList[idx].goods_price) * 100 * parseInt($scope.cartList[idx].car_good_num)/100).toFixed(2);
                }
                $scope.cartList[idx].checked = !$scope.cartList[idx].checked;
            };

            $scope.delData = function(idx){
                uriServices.postRequest(shopModuleConfig.domain + '/cart/del',
                {'gid' : $scope.cartList[idx].goods_id, 'attrs-ids' : $scope.cartList[idx].attrs_ids})
                .then(function(data){
                    if(data.ret == 1){
                        if(!$scope.cartList[idx].checked){
                                $scope.checkNum --;
                                $scope.totalPrice = (parseFloat($scope.totalPrice) - parseFloat($scope.cartList[idx].goods_price) * 100 * parseInt($scope.cartList[idx].car_good_num)/100).toFixed(2);          
                        }
                        $scope.totalNum--;
                        $scope.cartList.splice(idx, 1);

                        if($scope.cartList.length == 0){
                            alert('请选择商品购买~');
                            $location.path('/');
                        }
                    }else{
                        alert('删除失败,请重试~');
                    }
                    return;
                }, function(data){
                    $location.path('/');
                });
            }

            $scope.chkAll = function(){
                if($scope.totalNum == $scope.checkNum)
                    return;
                var i = 0, len = $scope.cartList.length;
                for(i; i< len; i ++){
                    if($scope.cartList[i].goods_status == 1 && $scope.cartList[i].checked){
                        $scope.cartList[i].checked = false;
                        $scope.checkNum++;
                        $scope.totalPrice = (parseFloat($scope.totalPrice) + parseFloat($scope.cartList[i].goods_price) *100 * parseInt($scope.cartList[i].car_good_num) / 100).toFixed(2);
                    }
                }
            }

            $scope.buy = function(){
                if(!$scope.checkNum){
                    alert('请先选择购买的商品~');
                    return false;
                }
                var confirmData = {};
                var i = 0, len = $scope.cartList.length;
                for(i; i< len; i ++){
                    if($scope.cartList[i].goods_status == 1 && !$scope.cartList[i].checked){
                        confirmData[i] = {'a' : $scope.cartList[i].attrs_ids, 'g' : $scope.cartList[i].goods_id};
                    }
                }
                localStorage.setItem('confirmData', JSON.stringify(confirmData));
                $location.path('/confirm');
            }
        },
        defineListeners:function(){
            this._super();
        },
        destroy:function(){}
    });
    cartController.$inject = ['$scope', 'uriServices', '$location'];

    angular.module('controllers.cart', []).controller('cartCtrl', cartController);
}());
