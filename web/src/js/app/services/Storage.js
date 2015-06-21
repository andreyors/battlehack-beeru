
(function(define) {
    
    'use strict';
    
    define([], function() {
        
        var Storage = function() {

            var cookieStorage = {
                setItem: function(key, value) {
                    document.cookie = [key + '=' + value, null, 'path=/'].join('; ');
                },
                getItem: function(key) {
                    var valueMatch = new RegExp(key + "=([^;]+)").exec(document.cookie);
                    return valueMatch ? valueMatch[1] : null;
                },
                removeItem: function(key) {
                    // @todo: implement
                },
                clear: function() {
                    // @todo: implement
                }
            };

            function Storage(type) {
                switch (type) {
                    case 'local': 
                        this.storage = localStorage;
                        break;
                    case 'session':
                        this.storage = sessionStorage;
                        break;
                    case 'cookie':
                        this.storage = cookieStorage;
                        break;
                    case 'db':
                    default:
                        throw new Error('Storage type: ' + type + ' not implemented');
                }
            };
            Storage.prototype = {
                set: function(key, value) {
                    value = JSON.stringify(value);
                    this.storage.setItem(key, value);
                },
                get: function(key) {
                    var data = this.storage.getItem(key);
                    return JSON.parse(data);
                },
                has: function(key) {
                    return !!this.storage.getItem(key);
                },
                remove: function(key) {
                    this.storage.removeItem(key);
                },
                clear: function() {
                    this.storage.clear();
                }
            };

            return {
                Local: new Storage('local'),
                Session: new Storage('session'),
                Cookie: new Storage('cookie')
            };

        };

        return Storage;
        
    });
    
}(define));