(function(){
    'use strict';
    var indexController = baseController.extend({
        init:function($scope){
            this._super($scope);
        },
        defineListeners:function(){
            this._super();
        },
        destroy:function(){}
    });
    indexController.$inject = ['$scope'];
    angular.module('controllers.index', []).controller('indexCtrl', indexController);
}());
