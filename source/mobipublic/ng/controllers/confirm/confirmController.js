(function(){
    'use strict';
    var confirmController = baseController.extend({
        init:function($scope, uriServices, $location){
            this._super($scope);
            $scope.addAddr = false;
            $scope.confirmData = JSON.parse(localStorage.getItem('confirmData'));
            $scope.inv_type = '3';
           // $scope.cpsEnable = 0;
           // $scope.cpsType = 0;
           // $scope.cpsVal = '';
           //商品总价
            $scope.totalFee = '0.00';
            //优惠金额
            $scope.cpsPay = '0.00';
            //实付金额
            $scope.payFee = '0.00';
            //配送费用
            $scope.shipPay = '0.00';

            if(!$scope.confirmData){
                //alert('请先选择购买的商品~');
                $location.path('/cart');
                return;
            }

            if(typeof $scope.confirmData.n !== 'undefined'){
                uriServices.postRequest(shopModuleConfig.domain + '/order/buynow',
                {'g':$scope.confirmData.g, 'a':$scope.confirmData.a, 'n':$scope.confirmData.n})
                .then(function(data){
                    if(data.ret == 1){
                        data.g.car_good_num = $scope.confirmData.n;
                        $scope.goods = {0 : data.g};
                        $scope.goodsLen = 1;
                        $scope.codeFlag = data.g.codeFlag;
                        $scope.totalFee= (parseFloat(data.g.goods_price) *100 * $scope.confirmData.n / 100).toFixed(2);
                        if( $scope.codeFlag == 1){
                            $scope.payFee = '0.00';
                            $scope.cpsPay = $scope.totalFee;
                        }
                    }else if(data.ret == 3){
                        alert('商品库存不足,请重新购买');
                        $location.path('/goods/' + $scope.confirmData.g);
                        return false;
                    }else{
                        alert('请先选择购买的商品~');
                        $location.path('/');
                        return false;
                    }

                }, function(data){
                    $location.path('/cart');
                });
            }else{
                uriServices.postRequest(shopModuleConfig.domain + '/order/buycart',
                {'select' : $scope.confirmData}).then(function(data){
                    if(data.ret == 1){
                        $scope.goods = data.data;
                        $scope.goodsLen = $scope.goods.length;
                        for(var i in data.data){
                            $scope.totalFee= ((parseFloat($scope.totalFee) * 100 + parseFloat(data.data[i].goods_price) * 100 * parseInt(data.data[i].car_good_num))/100).toFixed(2);
                        };
                    }else if(data.ret == 3){
                        alert('您购物车某商品库存不足~');
                        $location.path('/cart');
                        return false;
                    }else
                        $location.path('/cart');
                }, function(data){
                    $location.path('/cart');
                });
            }

            uriServices.getRequest(shopModuleConfig.domain + '/order/confirm')
            .then(function(data){
                //默认选中地址
                if(data.addressList){
                    $scope.addrChoose =data.addressList[0].u_addr_id;
                    $scope.addressChoose(data.addressList[0].u_addr_id, data.addressList[0].pro_id);
                }else{
                    $scope.payFee = $scope.totalFee - $scope.cpsPay;
                }
                $scope.addrList = data.addressList;
                //$scope.couponsList = [];
                //var len = data.coupons.length, i = 0;
                //for(i; i < len; i++){
                //  $scope.couponsList[i] = data.coupons[i];
                //  $scope.couponsList[i].cr_info = JSON.parse(data.coupons[i].cr_info);
                //}
            }, function(data){
                $location.path('/cart');
            });

            $scope.addressChoose = function(addrId, proId){
                $scope.addrChoose = addrId;
                //getShipPay
                uriServices.postRequest(shopModuleConfig.domain + '/order/getShipPay', {'proId' : proId})
                .then(function(data){
                    if(data.ret == 1){
                        $scope.shipPay = parseFloat(data.data).toFixed(2);
                        //$scope.totalFee = (($scope.payFee * 100 - $scope.cpsPay * 100 + $scope.shipPay * 100)/100).toFixed(2);
                        $scope.payFee = (($scope.totalFee * 100 + $scope.shipPay * 100 - $scope.cpsPay * 100)/100).toFixed(2);
                    }
                    else{
                        alert('异常,请重试~');
                        return false;
                    }
                },function(data){
                });
            };

            //优惠券兑换码
            // $scope.promotionCodeCheck = function(){
            //     if(!$scope.promotionCode){
            //         alert('请输入优惠券兑换码~');
            //         return false;
            //     }
            //     uriServices.postRequest(shopModuleConfig.domain + '/coupons/checkCoupons',
            //     {'sn' : $scope.promotionCode}).then(function(data){
            //         if(data.ret != 1){
            //             alert('请输入正确的优惠券兑换码~');
            //             $scope.promotionCode = '';
            //             return false;
            //         }else{
            //             $scope.cpsEnable = 1;
            //             $scope.cpsType = 3;
            //             $scope.cpsVal = $scope.promotionCode;
            //             //优惠券优惠
            //             $scope.cpsPay = parseFloat(msg.amount).toFixed(2);
            //             $scope.totalFee = (($scope.payFee * 100 - $scope.cpsPay * 100 + $scope.shipPay * 100)/100).toFixed(2);
            //             $scope.totalFee = $scope.totalFee > 0 ? $scope.totalFee : '0.00';

            //         }
            //     }, function(data){
            //         alert('请重试~');
            //     });
            // };

            //优惠券优惠选项
           // $scope.chooseCps = function(cpId){
                //满多少减多少 还未处理 是否需要根据 对固定的商品 或者商品分类进行处理
                // if(!$scope.promotionCode){
                //     alert('请输入优惠券兑换码~');
                //     return false;
                // }
                // uriServices.postRequest(shopModuleConfig.domain + '/coupons/checkCoupons',
                // {'sn' : $scope.promotionCode}).then(function(data){
                //     if(data.ret != 1){
                //         alert('请输入正确的优惠券兑换码~');
                //         $scope.promotionCode = '';
                //         return false;
                //     }else{
                //         $scope.cpsEnable = 1;
                //         $scope.cpsType = 3;
                //         $scope.cpsVal = $scope.promotionCode;
                //         //优惠券优惠
                //         $scope.cpsPay = parseFloat(msg.amount).toFixed(2);
                //         $scope.totalFee = (($scope.payFee * 100 - $scope.cpsPay * 100 + $scope.shipPay * 100)/100).toFixed(2);
                //         $scope.totalFee = $scope.totalFee > 0 ? $scope.totalFee : '0.00';

                //     }
                // }, function(data){
                //     alert('请重试~');
                // });
           // };

            $scope.invType = function(inv_type){
                $scope.inv_type = inv_type;
            }

            $scope.goOrder = function(){
                //$scope.addrChoose 地址ID
                //$scope.shipPay 运费
                //invoice 是否需要发票
                //inv_type 发票类型
                //invTitle 发票抬头
                //memo留言
                //cps_enable 是否启用优惠券 0 ,1 $scope.cpsEnable
                //cps_type 优惠券类型 0,1,3  $scope.cpsType
                //兑换码 或者优惠券id cps_val $scope.cpsVal
                //是否发票
                if($scope.invoice && !$scope.inv_type){
                    alert('请填写发票抬头~');
                    return false;
                }
                if(!$scope.addrChoose){
                    alert('请选择地址~');
                    return false;
                }
                uriServices.postRequest(shopModuleConfig.domain + '/order/go',
                {
                    is_inv : $scope.invoice ? 1 : 3,
                    inv_type : $scope.inv_type,
                    inv_title : $scope.invTitle ? $scope.invTitle : '',
                    //cps_enable : $scope.cpsEnable,
                   // cps_val : $scope.cpsVal,
                    //cps_type : $scope.cpsType,
                    add_id : $scope.addrChoose,
                    memo : $scope.memo ? $scope.memo : '',
                    select:JSON.stringify($scope.confirmData)
                })
                .then(function(data){
                    if(data.ret == 1){
                        $scope.confirmData = null;
                        $scope.goods = null;
                        localStorage.removeItem('confirmData');
                        if($scope.codeFlag != undefined && $scope.codeFlag == 1)
                            $location.path('/order');
                        else
                            $location.path('/success');
                    }else if(data.ret == 3){
                        alert('您购物车某商品库存不足~');
                        $location.path('/cart');
                    }else{
                        alert(data.msg);
                        $location.path('/cart');
                    }

                }, function(data){
                    $location.path('/cart');
                });
            }

        },
        defineListeners:function(){
            this._super();
        },
        destroy:function(){}
    });

    confirmController.$inject = ['$scope', 'uriServices', '$location'];

    angular.module('controllers.confirm', []).controller('confirmCtrl', confirmController);
}());
