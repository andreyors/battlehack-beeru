var router = require('express').Router();

/* GET index page. */
router.get('/', function(req, res) {
    res.send('User Info');
});

module.exports = router;