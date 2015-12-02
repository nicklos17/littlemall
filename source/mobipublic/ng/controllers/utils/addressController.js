(function(){
    'use strict';
    var addressController = baseController.extend({
        init:function($scope, uriServices, $location, $routeParams){
            this._super($scope);

            $scope.citits = [{'city_id' : 0, 'city_name': '请先选择省份'}];
            $scope.districts = [{'dis_id' : 0, 'dis_name': '请先选择市区'}];
            $scope.addr = {};
            //是否为地址编辑
            var addrId = $routeParams.addrId;
            if(addrId){
                //获取地址信息
                uriServices.postRequest(shopModuleConfig.domain + '/address/getAddr',
                {'aid' : addrId}).then(function(data){
                    if(data.ret == 1){
                        var addrTel = data.data.u_addr_tel.split('-');
                        if(data.data.u_addr_default == 3)
                            $scope.addr.defaultAddr = true;
                        else
                            $scope.addr.defaultAddr = false;
                        $scope.addr.addrId = data.data.u_addr_id;
                        $scope.addr.consignee = data.data.u_addr_consignee;
                        $scope.addr.mobile = data.data.u_addr_mobile;
                        $scope.addr.areaCode = addrTel[0];
                        $scope.addr.telNum = addrTel[1];
                        $scope.addr.ext = addrTel[2];
                        $scope.addr.proId = data.data.pro_id;
                        $scope.addr.cityId = data.data.city_id;
                        $scope.addr.disId = data.data.dis_id;
                        $scope.addr.detailAddr = data.data.u_addr_info;
                        $scope.addr.zipCode = data.data.u_addr_zipcode;
                        //选择城市
                        $scope.choosePro($scope.addr.proId);
                        //选择地区
                         $scope.chooseCity($scope.addr.cityId);
                    }
                    else
                        $location.path('/addr');
                }, function(data){
                    $location.path('/addr');
                });
            }

            uriServices.postRequest(shopModuleConfig.domain + '/region/getProvinces',
            {}).then(function(data){
                $scope.provinces = data;
            }, function(data){
                $location.path('');
            });

            $scope.choosePro = function(proId){
                uriServices.postRequest(shopModuleConfig.domain + '/region/getCities',
                {'pro_id' : proId}).then(function(data){
                    $scope.citits = data;
                }, function(data){
                    $location.path('');
                });
            };

            $scope.chooseCity = function(cityId){
                uriServices.postRequest(shopModuleConfig.domain + '/region/getDistricts',
                {'city_id' : cityId}).then(function(data){
                    $scope.districts = data;
                }, function(data){
                    $location.path('');
                });
            };

            $scope.saveAddr = function(){
                if(!$scope.addr.detailAddr){
                    alert('请先填写详细地址');
                    return false;
                }
                uriServices.postRequest(shopModuleConfig.domain + '/address/add',
                {'consignee' : $scope.addr.consignee, 'areaCode' : $scope.addr.areaCode?$scope.addr.areaCode:'', 'pro' : $scope.addr.proId,
                'city' : $scope.addr.cityId, 'dis' : $scope.addr.disId, 'detailAddr' : $scope.addr.detailAddr?$scope.addr.detailAddr:'',
                'zipCode' : $scope.addr.zipCode, 'telNum' : $scope.addr.telNum?$scope.addr.telNum:'', 'ext' : $scope.addr.ext?$scope.addr.ext:'',
                'mobile' : $scope.addr.mobile})
                .then(function(data){
                    if(data.ret == 1){
                        alert('添加成功~');
                        //获取当前url 判断返回地址列表或者下单
                        if($location.url().indexOf('addrAdd') != -1)
                            $location.path('/addr');
                        else{
                            //刷新地址列表
                            uriServices.getRequest(shopModuleConfig.domain + '/order/confirm')
                            .then(function(data){
                                $scope.$parent.addrList = data.addressList;
                            }, function(data){
                                alert('网络延迟, 请刷新页面');
                            });
                            $scope.addAddr = false;
                        }
                    }else{
                        alert(data.msg);
                        return false;
                    }
                }, function(data){
                    $location.path('');
                });
            }

            $scope.editAddr = function(){
                uriServices.postRequest(shopModuleConfig.domain + '/address/edit',
                {'addrId' : addrId, 'consignee' : $scope.addr.consignee, 'areaCode' : $scope.addr.areaCode?$scope.addr.areaCode:'', 'pro' : $scope.addr.proId,
                'city' : $scope.addr.cityId, 'dis' : $scope.addr.disId, 'detailAddr' : $scope.addr.detailAddr?$scope.addr.detailAddr:'',
                'zipCode' : $scope.addr.zipCode, 'telNum' : $scope.addr.telNum?$scope.addr.telNum:'', 'ext' : $scope.addr.ext?$scope.addr.ext:'',
                'mobile' : $scope.addr.mobile})
                .then(function(data){
                    if(data.ret == 1){
                        alert('编辑成功~');
                        $location.path('/addr');
                    }else{
                        alert(data.msg);
                        return false;
                    }
                    $scope.addAddr = false;
                }, function(data){
                    $location.path('/addr');
                });
            }

            $scope.setDefaultAddr = function(addrId){
                var url;
                if($scope.addr.defaultAddr)
                    url = '/address/cancelDefault';
                else
                    url = '/address/setDefault';

                uriServices.postRequest(shopModuleConfig.domain + url,
                {'aid' : addrId})
                .then(function(data){
                    if(data.ret == 1){
                        $scope.addr.defaultAddr = !$scope.addr.defaultAddr;
                    }
                }, function(data){
                    $location.path('/addr');
                });

            }

        },
        defineListeners:function(){
            this._super();
        },
        destroy:function(){}
    });

    addressController.$inject = ['$scope', 'uriServices', '$location', '$routeParams'];

    angular.module('controllers.address', []).controller('addressCtrl', addressController);
}());
