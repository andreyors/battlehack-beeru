(function(define) {

    'use strict';

    define([], function() {

        var Beers = function($http, $q) {

            var _beers = [];

            var _load = function() {
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
            };

            return {

                all: function() {
                    return _load();

                },

                byId: function(id) {
                    var result = $q.defer();

                    _load().then(function(data) {
                        for (var i = 0; i < data.length; i++) {
                            if (id == data[i].id) {
                                result.resolve(data[i]);
                            }
                        }

                        result.reject();
                    });

                    return result.promise;
                }

            };

        };

        return ['$http', '$q', Beers];

    });

}(define));