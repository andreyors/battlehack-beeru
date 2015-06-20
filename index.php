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

$db = DB(DB_DSN, DB_USER, DB_PASS);
$view = $app->view;
$view->parserOptions = array(
    'debug' => true,
    'cache' => WEBROOT . 'var/cache',
);

$app->get('/', function() use ($app, $db) {
  $sql = "SELECT NOW()";
  $res = $db->query($sql);
  
  $cnt = $res ? $res->fetchColumn() : 0;

  $app->render('index/index.twig');
});

$app->post('/api/payment', 'API', function() use ($app, $db) {
  $rawData = file_get_contents("php://input");
  $json = json_decode($rawData, true);
  
  $customer = !empty($json['customer']) ? $json['customer'] : array();
  $items = !empty($json['items']) ? $json['items'] : array();
  
  $sql = "SELECT NOW()";
  $res = $db->query($sql);
  
  $cnt = $res ? $res->fetchColumn() : 0;
  
  $token = md5('token');
  
  $app->render(200, ['token' => $token]);
});

$app->get('/api/payment', function() {
    $data = array(
      'customer' => array(
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@gmail.com',
        'phone' => '+491751261257',
        ),
        'items' => array(
          array(
            'title' => 'Sony PlayStation 4',
            'price' => '399',
            'currency' => 'EUR',
          ),
          array(
            'title' => 'Witcher 3',
            'price' => '69',
            'currency' => 'EUR',
          ),
        ),
      );
      
      die(json_encode($data));
});


$app->post('/api/payment/status', 'API', function() use ($app, $db) {
    $token = !empty($_POST['token']) ? subtsr($_POST['token'], 0, 32) : false;
    
    $status = 'rejected';
    if (!empty($token)) {
        $status = 'paid';
    }
    
    $app->render(200, ['status' => $status]);
});

$app->get('/api/payment/status', function() {
  $data = array(
    'token' => md5('token')
    );
    
    die(json_encode($data));
});

$app->get('/api/sms', 'API', function() {
    // TODO
});

$app->get('/api/short', 'API', function() use ($app) {
  $url = !empty($_GET['url']) ? $_GET['url'] : '';

  if (!empty($url)) {
    
    include WEBROOT . "libs/GoogleShortener.php";
  
    $gApi = new GoogleShortener(GOOGLE_API_KEY);
    $short = $gApi->shorten($url);
    
    $result = array(
      "longUrl" => $url,
      "url" => $short,
    );
    
    die(json_encode($result));
  }
});

$app->run();