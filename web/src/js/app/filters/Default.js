
(function(define) {

    'use strict';

    define([], function() {

        var Default = function($filter) {

            var numberFilter = $filter('number');

            return function(input, type) {

                var result = null;

                if (input !== undefined && !(input instanceof Number)) {
                    input = input.toNumber();
                }

                if (!isNaN(input)) {
                    var fraction = 2;
                    if (input % 100 < 1) {
                        fraction = 0;
                    }

                    result = numberFilter(input / 100, fraction);
                }

                return result;
            };
        };

        return ['$filter', Default];
    });

}(define));

