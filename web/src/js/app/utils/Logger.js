
(function(define) {
    
    'use strict';
    
    define([], function() {
        
        var COLORS = {
            error: '#ef3a38',
            warn: '#f16724',
            info: '#4062ae',
            log: '#000066',
            debug: '#00736a'
        };
        
        var Logger = function($log) {
            
            var prepareLogFn = function(logFn, color) {

                var enhancedLogFn = function () {
                    var args = Array.prototype.slice.call(arguments);

                    args[0] = '%c' + args[0];
                    args.splice(1, 0, 'color:' + color);

                    logFn.apply(null, args);
                };

                return enhancedLogFn;
            };
            
            $log.log = prepareLogFn($log.log, COLORS.log);
            $log.warn = prepareLogFn($log.warn, COLORS.warn);
            $log.info = prepareLogFn($log.info, COLORS.info);
            $log.log = prepareLogFn($log.log, COLORS.log);
            $log.debug = prepareLogFn($log.log, COLORS.debug);
        
        };
        
        var LogDecorator = function($provide) {
            $provide.decorator('$log', ['$delegate', function($delegate) {
                Logger($delegate);
                return $delegate;
            }]);
        };

        return ['$provide', LogDecorator];
        
    });
    
}(define));