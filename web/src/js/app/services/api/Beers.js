(function(define) {

    'use strict';

    define([], function() {

        var Beers = function($http, $q) {

            var _beers = [];

            return {

                all: function() {

                    var result = $q.defer();

                    if (!_beers.length) {
                        var url = APP.CONFIG.Main.apiEndpoint + '/';
                        $http.get(url).then(function(response) {
                            _beers = response.data;
                            result.resolve(_beers);
                        });
                    } else {
                        result.resolve(_beers);
                    }

                    return result.promise;

                },

                byId: function(id) {
                    for (var i = 0; i < _beers.length; i++) {
                        if (id == _beers[i].id) {
                            return _beers[i];
                        }
                    }

                    return null;
                }

            };

        };

        return ['$http', '$q', Beers];

    });

}(define));