(function(define) {

    'use strict';

    define([], function() {

        var Payment = function($http) {

            return {

                getToken: function(user) {
                    var url = APP.CONFIG.Main.paymentEndpoint + '/token';
                    return $http.post(url, user);
                },

                sendInfo: function(data) {
                    var url = APP.CONFIG.Main.paymentEndpoint + '/data';
                    return $http.post(url, data);
                }

            };

        };

        return ['$http', Payment];

    });

}(define));