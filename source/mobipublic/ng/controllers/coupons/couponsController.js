(function(){
    'use strict';
    var couponsController = baseController.extend({
        init:function($scope, uriServices, $location){
            this._super($scope);
            uriServices.getRequest(shopModuleConfig.domain + '/coupons/index')
            .then(function(data){
                $scope.couponsList = [];
                var len = data.length, i = 0;
                for(i; i < len; i++){
                  $scope.couponsList[i] = data[i];
                  $scope.couponsList[i].cr_info = JSON.parse(data[i].cr_info);
                  //已使用
                  if(data[i].cp_used_time > 0){
                    $scope.couponsList[i].act = false;
                    $scope.couponsList[i].cp_status = 2;
                  }
                  //禁用
                  else if($scope.couponsList[i].cp_status == 3)
                    $scope.couponsList[i].act = false;
                  //过期
                  else if(data[i].cp_end_time * 1000 - new Date< 0){
                    $scope.couponsList[i].cp_status = 0;
                    $scope.couponsList[i].act = false;
                  }
                  //未到开启时间
                  else if(data[i].cp_start_time * 1000 - new Date> 0){
                    $scope.couponsList[i].cp_status = -1;
                    $scope.couponsList[i].act = false;
                  }
                  else
                    $scope.couponsList[i].act = true;
                };
            }, function(data){
                return false;
            });

            $scope.doAct = function(act){
              if(!act)
                return false;
              else
                $location.path('/');
            }
            $scope.page = 2;
            $scope.more = true;
            $scope.getMore = function(){
                uriServices.getRequest(shopModuleConfig.domain + '/coupons/index?page=' + $scope.page)
                .then(function(data){
                    if(data.length == 0)
                        $scope.more = false;
                    else{

                      var len = data.length, i = 0;

                      for(i; i < len; i++){
                        data[i].cr_info = JSON.parse(data[i].cr_info);
                        //已使用
                        if(data[i].cp_used_time > 0){
                          data[i].act = false;
                          data[i].cp_status = 2;
                        }
                        //禁用
                        else if(data[i].cp_status == 3)
                          data[i].act = false;
                        //过期
                        else if(data[i].cp_end_time * 1000 - new Date< 0){
                          data[i].cp_status = 0;
                          data[i].act = false;
                        }
                        //未到开启时间
                        else if(data[i].cp_start_time * 1000 - new Date> 0){
                          data[i].cp_status = -1;
                          data[i].act = false;
                        }
                        else
                          data[i].act = true;
                      };
                      $scope.couponsList = $scope.couponsList.concat(data);
                      $scope.page ++;
                    }
                }, function(data){

                });
            };

        },
        defineListeners:function(){
            this._super();
        },
        destroy:function(){}
    });
    couponsController.$inject = ['$scope', 'uriServices', '$location'];

    angular.module('controllers.coupons', []).controller('couponsCtrl', couponsController);
}());
