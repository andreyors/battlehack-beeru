<?php

require WEBROOT . 'vendor/autoload.php';
require 'common.php';

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
  
  $app->render(200, ['progress' => ceil($cnt * 100 / 30), 'cnt' => $cnt]);
});


$app->get('/api/payment/status', 'API', function() use ($app, $db) {
  $sql = "SELECT NOW()";
  $res = $db->query($sql);
  
  $cnt = $res ? $res->fetchColumn() : 0;
  
  $app->render(200, ['progress' => ceil($cnt * 100 / 30), 'cnt' => $cnt]);
});

