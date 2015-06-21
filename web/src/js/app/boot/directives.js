(function(define, angular) {
    
    'use strict';
    
    var dependencies = [
        'directives/time',
        'directives/date',
    ];
    
    define(dependencies, function(Time, Date) {
        angular.module('app.directives', [])
            .directive('time', Time)
            .directive('date', Date);
    });
    
}(define, angular));
