(function(define) {

    'use strict';

    define([], function() {

        var Categories = function(http) {

            return {

                all: function() {
                    var url = APP.CONFIG.Main.apiEndpoint + '/categories';
                    return http.get(url);
                }

            };

        };

        return ['$http', Categories];

    });

}(define));