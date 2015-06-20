var Util = require('util');
var Repo = require('../repo');

/**
 * Categories repository
 * @constructor
 */
var Categories = function() {
    Repo.apply(this, arguments);
};
Util.inherits(Categories, Repo);

module.exports = Categories;
