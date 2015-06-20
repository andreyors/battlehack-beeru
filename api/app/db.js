var mysql = require('mysql');
var configs = require('./configs/db.json');

var DB = function() {
    this._pool = null;
    this._repos = {};
};

DB.prototype.init = function(env) {
    this._pool = mysql.createPool(configs[env]);
};

DB.prototype.getConnection = function() {
    if (null === this._pool) {
        throw new Error('Database is not initialized');
    }

    var that = this;

    return new Promise(function(resolve) {
        that._pool.getConnection(function(err, connection) {
            if (err) {
                throw err;
            }
            resolve(connection);
        });
    });
};

DB.prototype.getRepository = function(name) {
    if (typeof this._repos[name] === 'undefined') {
        var Repo = require('./repo/' + name);
        this._repos[name] = new Repo(name);
    }

    return this._repos[name];
};

module.exports = new DB();