(function(define) {

    'use strict';

    define([], function() {

        var SendController = function($scope, $stateParams, beers) {

            $scope.beer = beers.byId($stateParams.beerId);

            $scope.user = {
                name: '',
                email: '',
                phone: '',
                address: ''
            };

            $scope.send = function() {

            };

        };

        return ['$scope', '$stateParams', 'beers', SendController];

    });

}(define));