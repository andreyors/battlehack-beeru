<?php

require WEBROOT . 'vendor/autoload.php';
require 'common.php';
require 'conf/config.php';

$app = new \Slim\Slim(array(
    'debug' => true,
    'view' => new \Slim\Views\Twig,
    'templates.path' => APP_ROOT . '/views',
));

$db = DB(DB_DSN, DB_USER, DB_PASS);
$view = $app->view;
$view->parserOptions = array(
    'debug' => true,
    'cache' => APP_ROOT . '/var/cache',
);

$app->get('/', function() use ($app, $db) {
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
    $token = !empty($_POST['token']) ? subtsr($_POST['token'], 0, 32) : false;
    
    $status = 'rejected';
    if (!empty($token)) {
        $status = 'paid';
    }
    
    $app->render(200, ['status' => $status]);
});

