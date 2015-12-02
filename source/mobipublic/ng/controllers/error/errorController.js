(function(){
    'use strict';
    var errorController = baseController.extend({
        init:function($scope){
            this._super($scope);
            var _base = 'http://qzone.qq.com/gy/404/';
            document.write('<script type="text/javascript" src="' + _base + 'data.js" charset="utf-8"></script>script>');
            document.write('<script type="text/javascript" src="'+ _base + 'page.js" charset="utf-8"></script>script>');/*  |xGv00|bb048611f4d0bb34934fd40353d94edf */
        },
        defineListeners:function(){
            this._super();
        },
        destroy:function(){}
    });
    errorController.$inject = ['$scope'];
    angular.module('controllers.error', []).controller('errorCtrl', errorController);
}());
