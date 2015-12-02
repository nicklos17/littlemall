(function(){
    'use strict'
    shopModule.config(
        ['$routeProvider', function($routeProvider){
            for (var i = 0, len = shopModuleConfig.route.length; i < len; i++){
                $routeProvider.when(shopModuleConfig.route[i].path,{
                        templateUrl :'/ng/views/' + shopModuleConfig.route[i].templateUrl,
                        controller: shopModuleConfig.route[i].controller
                    }
                )
            };
            $routeProvider.otherwise({redirectTo : '/404'})
        }]
    );
}());
