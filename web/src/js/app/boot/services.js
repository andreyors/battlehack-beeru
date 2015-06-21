(function(define, angular, APP) {
    
    'use strict';
    
    var dependencies = [
        'services/Storage',
        'services/Payment',
        'services/api/Categories',
        'services/api/Styles',
        'services/api/Beers'
    ];
    
    define(dependencies, function(Storage, Payment, Categories, Styles, Beers) {
        
        angular.module('app.services', [])
            .constant('config', APP.CONFIG)
            .service('storage', Storage)
            .service('payment', Payment)
            .service('categories', Categories)
            .service('styles', Styles)
            .service('beers', Beers);

    });
    
}(define, angular, APP));