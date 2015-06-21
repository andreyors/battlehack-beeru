"strict mode";

var DB = require('./db');

/**
 * Abstract Repository
 *
 * @param string table
 * @constructor
 */
var Repo = function(table) {
    this._table = table;
};

Repo.prototype = {

    /**
     * Get all entities from table
     *
     * @returns {Promise}
     */
    all: function(offset, limit) {
        var sql = 'SELECT * FROM `' + this._table + '`';

        if (offset && limit) {
            sql += ' LIMIT ' + this._escape(offset)
                + ', ' + this._escape(limit);
        }
        return this._query(sql);
    },

    /**
     * Get entity by given
     *
     * @param {int} id
     * @returns {Promise}
     */
    get: function(id) {
        id = this._escape(id);

        var sql = 'SELECT * FROM '
            + this._table
            + ' WHERE id = ' + id;

        return this._query(sql);
    },

    search: function(data) {
        var params = [];

        for (var key of data) {
            params.push(key + ' = ' + this._escape(data[key]));
        }

        var sql = 'SELECT * FROM '
            + this._table
            + ' WHERE ' + params.join(' AND ');

        return this._query(sql);
    },

    /**
     * Execute given SQL qury
     * @param {string} sql
     * @param {string} event
     * @returns {Promise}
     * @private
     */
    _query: function(sql) {
        return new Promise(function(resolve, reject) {

            DB.getConnection().then(function(connection) {
                connection.query(sql, function(err) {
                    connection.release();

                    if (err) {
                        throw err;
                    }

                    var data = [].splice.call(arguments, 1);

                    resolve.apply(data);
                });
            });

        });
    },

    _escape: function(value) {
        return this._connection.escape(value);
    },

    _compose: function(data) {
        var result =  '';

        for (var key of data) {
            result += key + '=' + this._escape(data[key]);
        }

        return result;
    }

};

module.exports = Repo;