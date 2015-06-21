(function(define, angular) {
    
    'use strict';
    
    var dependencies = [
        'models/Beer'
    ];
    
    define(dependencies, function(Beer) {
        
        angular.module('app.models', [])
            .service('User', Beer);

    });
    
}(define, angular));
