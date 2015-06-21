define([], function () {
    
    'use strict';
    
    return {

        constant: function(obj, name, value) {
            Object.defineProperty(obj, name, {
                value: value,
                writable: false,
                enumerable: false,
                configurable: false
            });
        },
        reset: function(base, obj) {
            if (typeof obj === 'undefined') {
                return;
            }
            for (var prop in obj) {
                if (base.hasOwnProperty(prop)) {
                    base[prop] = obj[prop];
                }
            }
        },
        extend: function(object, parent) {
            for (var prop in parent.prototype) {
                if (parent.prototype.hasOwnProperty(prop) && 
                    typeof object.prototype[prop] === 'undefined') {
                    object.prototype[prop] = parent.prototype[prop];
                }
            }
        }

    };

});
    
