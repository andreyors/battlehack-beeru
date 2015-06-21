define([], function() {
  
    'use strict';
    
    var Mediator = (function () {

        var events = {};
        var eventsOnce = {};

        function trigger(events, params) {
            var event;
            for (var i = 0, len = events.length; i < len; i++) {
                event = events[i];
                event.callback.apply(event.context, params);
            }
        }

        return {

            on: function(name, callback, context) {
                (events[name] = events[name] || []).push({
                    callback: callback,
                    context: context
                });
                return this;
            },

            once: function(event, callback, context) {
                (eventsOnce[event] = eventsOnce[event] || []).push({
                    callback: callback,
                    context: context
                });
                return this;
            },

            off: function(name, callback, context) {
                if (!events[name]) {
                    return this;
                }

                if (callback) {
                    var _events = events[name],
                        event;

                    for (var i = 0, len = _events.length; i < len; i++) {
                        event = _events[i];
                        if (event.callback === callback) {
                            if (context) {
                                if (event.context === context) {
                                    _events.splice(i, 1);
                                }
                            } else {
                                _events.splice(i, 1);
                            }

                        }
                    }
                } else {
                    delete events[name];
                }

                return this;
            },

            emit: function(name) {

                var params = [].slice.call(arguments, 1);

                // regular event callbacks
                if (name in events) {
                    trigger(events[name], params);
                }

                // once event callbacks
                if (name in eventsOnce) {
                    trigger(eventsOnce[name], params);
                    delete eventsOnce[name];
                }
            }
        };
        
    }());

    return Mediator;

});
    
