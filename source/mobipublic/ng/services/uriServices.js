(function(){
    'use strict';
    var uriService = Class.extend({
        getRequest: function($loadUrl){
            var deferred = this.$q.defer();
            this.$http({method: 'GET', url: $loadUrl})
            .success(function(data, status, headers, config){
                deferred.resolve(data);
            })
            .error(function(data, status, headers, config){
                deferred.reject(data);
            });
            return deferred.promise;
        },
        postRequest: function($loadUrl, $params){
            var deferred = this.$q.defer();
            this.$http({method: 'POST', url: $loadUrl, data: $params, headers: {'Content-Type': 'application/x-www-form-urlencoded'}}).
                success(function(data, status, headers, config){
                    deferred.resolve(data);
                }).
                error(function(data, status, headers, config){
                    deferred.reject(data);
                });
            return deferred.promise;
        }
    });

    var uriServiceProvider = Class.extend({
        instance: new uriService(),
        $get: ['$http', '$q', function($http, $q){
            this.instance.$http = $http;
            this.instance.$q = $q;
            return this.instance;
        }]
    });

    angular.module('services.uri', [])
        .provider('uriServices', uriServiceProvider);
}());