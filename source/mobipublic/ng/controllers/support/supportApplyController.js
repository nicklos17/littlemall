(function(){
    'use strict';
    var supportApplyController = baseController.extend({
        init:function($scope, uriServices, $location, $routeParams){
            this._super($scope);
            $scope.sn = $routeParams.sn;
            uriServices.getRequest(shopModuleConfig.domain + '/support/apply?orderSn=' + $scope.sn)
            .then(function(data){
                if(data.ret == 0){
                    alert('错误的订单号~');
                    $location.path('/order');
                    return false;
                }
                $scope.goods = [];
                for(var g in data.orderGoods){
                    $scope.goods[g] = data.orderGoods[g];
                    if(data.orderGoods[g].ord_goods_back == 3)
                        $scope.goods[g].dis = true;
                    else
                        $scope.goods[g].dis = false;
                }
                $scope.orderInfo = data.orderInfo;
                $scope.orderInfo.goods_num = 1;

                //售后原因
                $scope.bordReason = ['请选择申请原因', '尺码原因', '快递原因', '鞋体原因', '智能模块原因', '发错货', '其他'];
                $scope.bordReasonChoose = '0';

                //售货类型 根据时间判断
                $scope.bordModelChoose = '0';
                $scope.bordModels = {};
                $scope.bordModels[0] = {'tit':'请先选择售后类型'};
                if(data.nowTime - data.confirmTime <= shopModuleConfig.supportTime.back)
                $scope.bordModels[1] = {'tit':'退货'};
                //换货
                if(data.nowTime - data.confirmTime <= shopModuleConfig.supportTime.exchange)
                    $scope.bordModels[3] = {'tit':'换货'};
                //换模块
                if(data.nowTime - data.confirmTime <= shopModuleConfig.supportTime.supportTime)
                    $scope.bordModels[5] = {'tit':'智能模块更换'};

                //获取地址信息
                uriServices.postRequest(shopModuleConfig.domain + '/region/getProvinces',
                {}).then(function(data){
                    $scope.provinces = data;
                    //选择城市
                    $scope.choosePro($scope.orderInfo.order_province);
                    //选择地区
                     $scope.chooseCity($scope.orderInfo.order_city);
                }, function(data){
                });
            }, function(data){
                alert('错误的订单号~');
                $location.path('/order');
                return false;
            });

            $scope.chooseBordGoods = function(gid, status){
                if(status == true){
                    return false;
                }
                else
                    $scope.chooseBordGoodsId = gid;
            }

            $scope.confirmBord = function(){
                if($scope.bordModelChoose == 0){
                    alert('请选择售后类型');
                    return false;
                }

                if($scope.bordReasonChoose == 0){
                    alert('请选择申请原因');
                    return false;
                }

                if(!$scope.chooseBordGoodsId){
                    alert('请选择需要售后的商品');
                    return false;
                }

                uriServices.postRequest(shopModuleConfig.domain + '/support/addSupport',
                {'bord_type': $scope.bordModelChoose, 'order_goods_id': $scope.chooseBordGoodsId, 'goods_num': $scope.orderInfo.goods_num,
                'bord_reason': $scope.bordReasonChoose, 'bord_msg': $scope.bordMsg, 'pro': $scope.orderInfo.order_province,
                'city': $scope.orderInfo.order_city, 'dis': $scope.orderInfo.order_district, 'addr_detail': $scope.orderInfo.order_addr,
                'consignee': $scope.orderInfo.order_consignee, 'mobile': $scope.orderInfo.order_mobile, 'area_code': $scope.orderInfo.areaCode?$scope.orderInfo.areaCode:'',
                'tel_num': $scope.orderInfo.telNum?$scope.orderInfo.telNum:'', 'ext': $scope.orderInfo.ext?$scope.orderInfo.ext:'', 'order_sn': $scope.sn}).then(function(data){
                    if(data.ret == 0){
                        alert(data.msg);
                        return false;
                    }else{
                        alert('申请成功,请耐心等待客服处理~');
                        $location.path('/support');
                        return;
                    }
                }, function(data){
                    alert('申请失败, 请重试~');
                    return false;
                });
            };

            $scope.choosePro = function(proId){
                uriServices.postRequest(shopModuleConfig.domain + '/region/getCities',
                {'pro_id' : proId}).then(function(data){
                    $scope.citits = data;
                }, function(data){
                    $location.path('/order');
                });
            };

            $scope.chooseCity = function(cityId){
                uriServices.postRequest(shopModuleConfig.domain + '/region/getDistricts',
                {'city_id' : cityId}).then(function(data){
                    $scope.districts = data;
                }, function(data){
                });
            };
        },
        defineListeners:function(){
            this._super();
        },
        destroy:function(){}
    });
    supportApplyController.$inject = ['$scope', 'uriServices', '$location', '$routeParams'];

    angular.module('controllers.supportApply', []).controller('supportApplyCtrl', supportApplyController);
}());
