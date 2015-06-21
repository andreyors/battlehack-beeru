var app = require('express')();

var categories = require('./routes/categories'),
    styles = require('./routes/styles'),
    beers = require('./routes/beers'),
    payment = require('./routes/payment');

app.use('/categories', categories);
app.use('/styles', styles);
app.use('/', beers);

app.use('/payment', payment);

module.exports = app;