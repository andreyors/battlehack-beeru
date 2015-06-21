var express = require('express');

var db = require('./app/db');
var router = require('./app/router');

var ENV = process.env.NODE_ENV || 'development';

db.init(ENV);

var app = express();

app.use(function(req, res, next) {
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    next();
});

app.use('/api/beers/', router);

module.exports = app;
