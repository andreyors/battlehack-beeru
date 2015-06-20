<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('WEBROOT') or define('WEBROOT', dirname(__FILE__) . DS);

require WEBROOT . 'vendor/autoload.php';
require WEBROOT . 'conf/config.php';

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

$app = new \Slim\Slim(array(
    'debug' => true,
    'view' => new \Slim\Views\Twig,
    'templates.path' => WEBROOT . 'views',
));

die(var_dump($app));

$db = DB(DB_DSN, DB_USER, DB_PASS);

$view = $app->view;
$view->parserOptions = array(
    'debug' => true,
    'cache' => WEBROOT . 'var/cache',
);

$app->get('/', function() use ($app, $db) {
  die('S');
  $sql = "SELECT NOW()";
  $res = $db->query($sql);
  
  $cnt = $res ? $res->fetchColumn() : 0;
  
  die(var_dump($cnt));
  
  $app->render('index/index.twig');
});

$app->get('/api/payment', 'API', function() use ($app, $db) {
  $sql = "SELECT NOW()";
  $res = $db->query($sql);
  
  $cnt = $res ? $res->fetchColumn() : 0;
  
  $token = md5('hello');
  
  $app->render(200, ['token' => $token]);
});


$app->get('/api/payment/status', 'API', function() use ($app, $db) {
    $token = !empty($_GET['token']) ? subtsr($_GET['token'], 0, 32) : false;
    
    $status = 'rejected';
    if (!empty($token)) {
        $status = 'paid';
    }
    
    $app->render(200, ['status' => $status]);
});

