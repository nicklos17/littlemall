(function(){
    'use strict';
    window.shopModule = angular.module(
        'shopManager',
        ['ngRoute', 'shopManager.controllers', 'shopManager.directives', 'shopManager.services'],
        function(){}
    ).factory('httpInterceptor', function($q){
        return{
            request: function(config){
                if(config.url.indexOf('/ng/views/') == -1 || config.url == '/ng/views/code/code.html'){
                    var backurl = 'http://test.m.mall.yunduo.com/index/vali';
                    try
                    {
                        var xmlHttp = new XMLHttpRequest();
                        xmlHttp.open('GET', '/index/auth?url=' + config.url + '&furl=' + encodeURIComponent(window.location.href), false);
                        xmlHttp.send(null);
                        if(xmlHttp.responseText == 0){
                            window.location.href = 'http://test.my.yunduo.com/login?siteid=5&backurl='+encodeURIComponent(backurl);
                            return false;
                        }
                    }
                    catch(e)
                    {
                        window.location.href = 'http://test.my.yunduo.com/login?siteid=5&backurl='+encodeURIComponent(backurl);
                        return false;
                    }
                }

                return config || $q.when(config);
            }
        };
  }).config(function($httpProvider){
        $httpProvider.interceptors.push('httpInterceptor');
        $httpProvider.defaults.transformRequest = function(request){
         if(typeof(request)!='object')
         {
                return request;
         }
         var str = [];
         for(var k in request){
            if(k.charAt(0)=='$'){
                    delete request[k];
                     continue;
            }
           var v='object'==typeof(request[k])?JSON.stringify(request[k]):request[k];
           str.push(encodeURIComponent(k) + "=" + encodeURIComponent(v));
         }
         return str.join("&");
       };
       $httpProvider.defaults.timeout=10000;
       $httpProvider.defaults.headers.post = {
            'Content-Type': 'application/x-www-form-urlencoded'
       };
    })
}())

