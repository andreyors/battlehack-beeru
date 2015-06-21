(function(define) {

    'use strict';

    define([], function() {

        var Styles = function(http) {

            return {

                all: function() {
                    var url = APP.CONFIG.Main.apiEndpoint + '/styles';
                    return http.get(url);
                }

            };

        };

        return ['$http', Styles];

    });

}(define));