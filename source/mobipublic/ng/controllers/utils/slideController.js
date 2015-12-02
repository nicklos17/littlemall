(function(){
    //'use strict';
    var slideController = baseController.extend({
        init:function($scope, $interval){
            this._super($scope);
            $scope.options = {};
            $scope.options.list = [{
                src: '/img/ng/slider/index-slide-1.jpg',
                active: true
            },{
                src: '/img/ng/slider/index-slide-2.jpg',
                active: false
            },{
                src: '/img/ng/slider/index-slide-3.jpg',
                active: false
            }];
            $scope.imgClick = function(data){
                //console.log(data);
                console.log(data.img.src);
            };
            var at = 0, nt = 1;
            $scope.next = function(){
                $scope['options']['list'][at].active = false;
                $scope['options']['list'][nt].active = true;
                at = nt;
                var len = $scope['options']['list'].length;
                if(++nt == len){
                    nt = 0;
                }
            };
            $interval($scope.next, 2000);
        },
        defineListeners:function(){
            this._super();
        },
        destroy:function(){}
    });
    slideController.$inject = ['$scope', '$interval'];

    angular.module('controllers.utils.slide', []).controller('slideCtrl', slideController);
}());