(function(define) {

    'use strict';

    define(['braintree'], function(braintree) {

        var PayController = function($scope, $state, payment, storage) {

            $scope.user = storage.Session.get('user');
            $scope.beer = storage.Session.get('beer');

            payment.getToken({
                first_name: 'Loh',
                last_name: 'Pidr',
                phone: '+4917612345678',
                email: 'loh.pidr@mail.com'
            }).then(function(response) {
                braintree.setup(response.data.token, 'dropin', {
                    container: 'payment-form',
                    onReady: function() {
                        $scope.ready = true;
                        $scope.$digest();
                    },
                    onPaymentMethodReceived: function(data) {
                        payment.sendInfo({
                            phone: $scope.user.phone,
                            email: $scope.user.email,
                            //link: $state.href('receive', {beerId: $scope.beer.id}, {absolute: true})
                            link: 'http://localhost:8000/src/index.html#/receive/' + $scope.beer.id
                        });
                        storage.Session.clear();
                        $state.go('.success');
                    }
                });
            });

        };

        return ['$scope', '$state', 'payment', 'storage', PayController];

    });

}(define));