<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

error_reporting(E_ALL);

ini_set('date.timezone', 'Europe/Berlin');
ini_set('display_errors', true);

defined('PS') or define('PS', PATH_SEPARATOR);
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

defined('ROOTDIR') or define('ROOTDIR', dirname(dirname(__FILE__)) . DS);
defined('VIEWSDIR') or define('VIEWSDIR', ROOTDIR . 'views' . DS);
defined('VARDIR') or define('VARDIR', ROOTDIR . 'var' . DS);
defined('CACHEDIR') or define('CACHEDIR', VARDIR . 'cache' . DS);
defined('CONFDIR') or define('CONFDIR', ROOTDIR . 'conf' . DS);

defined('MODELSDIR') or define('MODELSDIR', ROOTDIR . 'models' . DS);
defined('HELPERSDIR') or define('HELPERSDIR', ROOTDIR . 'helpers' . DS);
defined('LIBSDIR') or define('LIBSDIR', ROOTDIR . 'libs' . DS);

$paths = array(
  MODELSDIR,
  HELPERSDIR,
  LIBSDIR,
);
set_include_path(get_include_path() . PS . implode(PS, $paths));

spl_autoload_extensions(".php");
spl_autoload_register();

// Vendor autoload
require ROOTDIR . 'vendor/autoload.php';

// Shared libs
require LIBSDIR . 'common.php';

// Config
require CONFDIR . 'config.php';
require CONFDIR . 'config.twilio.php';
require CONFDIR . 'config.braintree.php';
require CONFDIR . 'config.sendgrid.php';