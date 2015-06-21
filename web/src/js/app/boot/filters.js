(function(define, angular) {
    
    'use strict';
    
    var dependencies = [
        'filters/Default'
    ];
    
    define(dependencies, function(Default) {
        
        angular.module('app.filters', [])
            .filter('default', Default);
    
    });
    
}(define, angular));
