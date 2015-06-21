(function(define) {

    'use strict';

    define([], function() {

        var SendController = function($scope, $state, beers, storage) {

            $scope.beer = beers.byId($state.params.beerId);

            $scope.user = {
                name: '',
                email: '',
                phone: '',
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