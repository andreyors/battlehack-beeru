(function(define) {

    'use strict';

    define([], function() {

        var SendController = function($scope, $state, beers, storage) {

            beers.byId($state.params.beerId).then(function(beer) {
                $scope.beer = beer;
            });

            $scope.user = {
                name: 'Test Name',
                email: 'blublublu@mailinator.com',
                phone: '+4915164597723',
                address: ''
            };

            $scope.send = function() {
                storage.Session.set('user', $scope.user);
                storage.Session.set('beer', $scope.beer);
                $state.go('pay');
            };

        };

        return ['$scope', '$state', 'beers', 'storage', SendController];

    });

}(define));