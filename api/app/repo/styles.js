var Util = require('util');
var Repo = require('../repo');

/**
 * Styles repository
 * @constructor
 */
var Styles = function() {
    Repo.apply(this, arguments);
};
Util.inherits(Styles, Repo);

module.exports = Styles;
