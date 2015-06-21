(function(define, angular) {

    'use strict';

    var dependencies = [
        'controllers/MainController',
        'controllers/BeerYouController',
        'controllers/BeerMeController',
        'controllers/SendController'
    ];

    define(dependencies, function() {

        angular.module('app.controllers', [])
            .controller('MainController', require('controllers/MainController'))
            .controller('BeerYouController', require('controllers/BeerYouController'))
            .controller('BeerMeController', require('controllers/BeerMeController'))
            .controller('SendController', require('controllers/SendController'));

    });

}(define, angular));
