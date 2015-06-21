(function(require, APP) {
    
    'use strict';
    
    require.config({
        
        baseUrl: 'js/app',
        
        paths: {
            braintree: '/src/js/lib/braintree-web/dist/braintree',
            angular: '/src/js/lib//angular/angular',
            uiRouter: '/src/js/lib/angular-ui-router/release/angular-ui-router',
            ngAnimate: '/src/js/lib/angular-animate/angular-animate',
            ngAria: '/src/js/lib/angular-aria/angular-aria',
            ngMaterial: '/src/js/lib/angular-material/angular-material'
        },

        shim: {
            angular: {
                exports: 'angular'
            },
            uiRouter: {
                deps: ['angular']
            },
            ngAnimate: ['angular'],
            ngAria: ['angular'],
            ngMaterial: {
                deps: ['ngAnimate', 'ngAria']
            }
        },
        
        urlArgs: ''
        
    });
    
    APP.Globals = {};
    APP.CONFIG = require(['config']);
    
    // load Angular first
    require(['angular'], function(angular) {
        // bootstrap application
        require(['main'], function(app) {
            APP.Framework = app;
        });
        
    });
    
}(require, APP = {}));
