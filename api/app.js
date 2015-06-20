var express = require('express');

var db = require('./app/db');
var router = require('./app/router');

var ENV = process.env.NODE_ENV || 'development';

db.init(ENV);

var app = express();
app.use('/api/beers/', router);

module.exports = app;
