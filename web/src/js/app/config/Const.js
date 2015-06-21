
APP.CONFIG.Const = (function() {

    var ConstProto = {
        key: function(id) {
            for (var key in this) {
                if (this[key] === id) {
                    return key;
                }
            }
            return null;
        }
    };

    return {
    }

}());