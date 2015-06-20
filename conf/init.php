<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

defined('PS') or define('PS', PATH_SEPARATOR);
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('WEBROOT') or define('WEBROOT', dirname(dirname(__FILE__)) . DS);

$paths = array(
  WEBROOT . 'models/',
  WEBROOT . 'helpers/',
  WEBROOT . 'libs/',
);

set_include_path(get_include_path() . PS . implode(PS, $paths));

spl_autoload_extensions(".php");
spl_autoload_register();

function API() {
  $app = \Slim\Slim::getInstance();
  $app->add(new \SlimJson\Middleware([
    'json.status' => true,
    'json.debug' => false,
    'json.override_error' => true,
    'json.override_notfound' => true
  ]));
}

function DB($db_dsn, $db_user, $db_pass) {
    try {
      $dbh = new PDO($db_dsn, $db_user, $db_pass);
      if ($dbh) {
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      }
    } catch (PDOException $e) {
      die(var_dump($e));
    }

    return $dbh;
}

require WEBROOT . 'vendor/autoload.php';
require WEBROOT . 'conf/config.php';
require WEBROOT . 'conf/config.twilio.php';
require WEBROOT . 'conf/config.braintree.php';
