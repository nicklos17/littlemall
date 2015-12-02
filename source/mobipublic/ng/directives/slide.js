(function(){
  'use strict';
  var ydIcoDirective = baseController.extend({
    init: function($scope, $elm, $attrs) {
      this._attrs = $attrs;
      //this._elm = $elm;
      this._super($scope);

      $scope.ext = this._attrs.ext;
      $scope.type = this._attrs.type;
    },
    defineListeners: function() {
      this._super();
    },
    destroy: function() {}
  });

  angular.module('directives.ydSlide', []).directive('ydSlide', [function() {
    return{
        restrict: 'EA',
        scope: true,
        replace: true,
        templateUrl: '/ng/views/utils/slide/slide.html'
    };
  }]);
})();
