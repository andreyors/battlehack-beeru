<?php

require WEBROOT . 'vendor/autoload.php';

app = new \Slim\Slim(array(
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
$app->get('/my', function() use ($app, $db) {
  $sql = "SELECT COUNT(1) cnt FROM (SELECT a1.ts, a1.z, MAX(a2.z) highest FROM gc_events a1 LEFT JOIN gc_events a2 ON a2.ts BETWEEN DATE_SUB(a1.ts, INTERVAL 2 SECOND) AND DATE_ADD(a1.ts, INTERVAL 2 SECOND) GROUP BY a1.ts, a1.z HAVING a1.z = highest ORDER BY a1.ts) max";
  $res = $db->query($sql);
  
  $cnt = $res ? $res->fetchColumn() : 0;
  
  $cnt = $cnt > 30 ? 30 : $cnt;
  
  $app->render('index/my.twig', array('cnt' => $cnt));
}); 
$app->get('/workout', function() use ($app, $db) {
  $res = $db->query("SELECT val_int FROM gc_switches WHERE name = 'like'");
  $like = $res->fetchColumn();
  
  $sql = "SELECT COUNT(1) cnt FROM (SELECT a1.ts, a1.z, MAX(a2.z) highest FROM gc_events a1 LEFT JOIN gc_events a2 ON a2.ts BETWEEN DATE_SUB(a1.ts, INTERVAL 2 SECOND) AND DATE_ADD(a1.ts, INTERVAL 2 SECOND) GROUP BY a1.ts, a1.z HAVING a1.z = highest ORDER BY a1.ts) max";
  $res = $db->query($sql);
  
  $cnt = $res ? $res->fetchColumn() : 0;
  
  $cnt = $cnt > 30 ? 30 : $cnt;
  
  $app->render('index/workout.twig', array('like' => intval($like), 'cnt' => $cnt ));
});