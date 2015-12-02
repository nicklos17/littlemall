(function(){
    'use strict';
    var ydIcoDirective = baseController.extend({
        init: function($scope, $elm, $attrs){
            this._attrs = $attrs;
            //this._elm = $elm;
            this._super($scope);

            $scope.ext = this._attrs.ext;
            $scope.type = this._attrs.type;
        },
        defineListeners: function(){
            this._super();
        },
        destroy: function(){}
    });

    angular.module('directives.ico', []).directive('ydIco', [function(){
        return{
            restrict: 'EA',
            scope: true,
            link: function($scope, $elm, $attrs){
                new ydIcoDirective($scope, $elm, $attrs);
            },
            replace: true,
            template: '<span class="ico-{{type}}">{{type}}</span>'
        };
    }]).directive('ydLink', [function(){
        return{
            restrict: 'EA',
            scope: true,
            link: function($scope, $elm, $attrs){
                new ydIcoDirective($scope, $elm, $attrs);
            },
            replace: true,
            template: '<a class="ico-{{type}}">{{ext}}</a>'
        };
    }]);
})();