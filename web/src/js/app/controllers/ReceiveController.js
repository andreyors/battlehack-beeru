(function(define) {

    'use strict';

    define([], function() {

        var ReceiveController = function($scope, $state, beers) {

            beers.byId($state.params.beerId).then(function(beer) {
                $scope.beer = beer;
            });

            $scope.receive = function() {

            };

        };

        return ['$scope', '$state', 'beers', ReceiveController];

    });

}(define));