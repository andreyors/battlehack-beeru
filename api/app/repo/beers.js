var Util = require('util');
var Repo = require('../repo');

/**
 * Beers repository
 * @constructor
 */
var Beers = function() {
    Repo.apply(this, arguments);
};
Util.inherits(Beers, Repo);

module.exports = Beers;
