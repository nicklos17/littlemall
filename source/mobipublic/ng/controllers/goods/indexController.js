(function(){
    'use strict';
    var goodsController = baseController.extend({
        init:function($scope, uriServices, $routeParams, $location){
            this._super($scope);
            var goodsId = $routeParams.gid;

            uriServices.getRequest(shopModuleConfig.domain + '/goods/index/' + goodsId).then(function(data){
                if(data.ret == 1){
                    $scope.goodsInfo = data;
                    $scope.showImg = data.gInfo.goods_img;
                }
                else
                    $location.path('/');
            }, function(data){
                $location.path('/goods/' + goodsId);
            });
            $scope.disCol = [];
            $scope.disSize = [];
            $scope.goodsNum = 1;

            $scope.filterSize = function(target){
                if($scope.sizeCur == target.gaAttr.attrs_id || $scope.disSize.indexOf(target.gaAttr.attrs_id) != -1)
                    return;
                $scope.sizeCur = target.gaAttr.attrs_id;
                if($scope.colCur && $scope.sizeCur){
                    $scope.showSizeCol = false;
                }
                uriServices.postRequest(shopModuleConfig.domain + '/goods/filterSize',
                {"gid" : goodsId, "filter-id" : target.gaAttr.attrs_id, "parent-other-id" : document.getElementById('pCol').value})
                .then(function(data){
                    $scope.disCol = data;
                    return false;
                }, function(data){
                    $location.path('/goods/' + goodsId);
                });
            }

            $scope.filterCol = function(target){
                if($scope.colCur == target.gaAttr.attrs_id || $scope.disCol.indexOf(target.gaAttr.attrs_id) != -1)
                    return;
                $scope.colCur = target.gaAttr.attrs_id;
                if($scope.colCur && $scope.sizeCur){
                    $scope.showSizeCol = false;
                }
                uriServices.postRequest(shopModuleConfig.domain + '/goods/filterCol',
                {"gid" : goodsId, "filter-id" : target.gaAttr.attrs_id, "parent-other-id" : document.getElementById('pSize').value})
                .then(function(data){
                    $scope.disSize = data;
                    $scope.showImg = target.gaAttr.attrs_img.replace('thumb', 'original');
                    return false;
                }, function(data){
                    $location.path('/goods/' + goodsId);
                });
            }

            $scope.numRdc = function(){
                if($scope.goodsNum > 1)
                    $scope.goodsNum--;
                return;
            }

            $scope.numAdd = function(){
                if($scope.goodsNum < 20)
                    $scope.goodsNum++;
                return;
            }

            $scope.addCart = function(){
                if($scope.goodsInfo.codeFlag != undefined && $scope.goodsInfo.codeFlag == 1){
                    alert('使用优惠券不支持加入购物车');
                    return false;
                }
                if(!$scope.colCur || !$scope.sizeCur){
                    $scope.showSizeCol = true;
                    return false;
                }
                if($scope.goodsNum < 1 || $scope.goodsNum > 20){
                    alert('请先选择正确的购买数量~');
                    return false;
                }
                uriServices.postRequest(shopModuleConfig.domain + '/cart/add',
                {'gid' : goodsId, 'attrs-ids' : $scope.colCur + ',' + $scope.sizeCur, 'num' : $scope.goodsNum})
                .then(function(data){
                    if(data.ret == 1)
                        $location.path('/cart');
                    else if(data.ret == 3)
                        alert('购物车不能超过50件商品~');
                    else if(data.ret == 5)
                        alert('单个商品最大购买量不能超过20~');
                    else
                        alert('商品库存不足~');
                    return;
                },function(data){
                    $location.path('/cart/');
                });
            }

            $scope.buy = function(){
                if(!$scope.colCur || !$scope.sizeCur){
                    $scope.showSizeCol = true;
                    return false;
                }
                localStorage.setItem('confirmData', JSON.stringify({'g' : goodsId, 'a' : $scope.colCur + ',' + $scope.sizeCur, 'n' : $scope.goodsNum}));
                $location.path('/confirm');
            }
        },
        defineListeners:function(){
            this._super();
        },
        destroy:function(){}
    });

    goodsController.$inject = ['$scope', 'uriServices', '$routeParams', '$location'];

    angular.module('controllers.goods', []).controller('goodsCtrl', goodsController);
}());
