(function(define) {
    
    'use strict';
    
    define([], function() {

        var Time = function() {
            var getTime = function() {
                var date = new Date();
                var h = date.getHours();
                var m = date.getMinutes();
                var ampm = h < 12 ? 'AM' : 'PM';
                
                if (h >= 12) h = h - 12;
                if (m < 10) m = '0' + m;
                
                return h + ':' + m + ampm;
            };
            
            return {
                restrict: 'E',
                link: function(scope, el) {
                    el.text(getTime());
                    var timeout = 60 - new Date().getSeconds();
                    setTimeout(function() {
                        el.text(getTime());
                        setInterval(function() {
                            el.text(getTime());
                        }, 1000 * 60);
                    }, timeout * 1000);
                }
            };
        };
        
        return [Time];
        
    });
    
}(define));
