
(function(define) {
    
    'use strict';
    
    define([], function() {

        return function() {
            
            var month = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            
            return {
                restrict: 'E',
                link: function($scope, el) {
                    var date = new Date();
                    var m = date.getMonth();
                    var d = date.getDay();
                    el.text(month[m] + ' ' + d);
                }
            };
        };
        
    });
    
}(define));
