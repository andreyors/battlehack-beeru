<?php

include 'conf/init.php';

$app = new \Slim\Slim(array(
    'debug' => true,
    'view' => new \Slim\Views\Twig,
    'templates.path' => ROOTDIR . 'views',
));

$db = DB(DB_DSN, DB_USER, DB_PASS);

$view = $app->view;
$view->parserOptions = array(
    'debug' => true,
    'cache' => CACHEDIR,
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

  $customerData = !empty($json['customer']) ? $json['customer'] : array();
  $itemsData = !empty($json['items']) ? $json['items'] : array();

  $token = false;
  if (!empty($customerData)) {
    $customer = new Customer($db);
    $customer_id = $customer->getCustomerIdByValues($customerData);

    if (!$customer_id) {
      $customer_id = $customer->create($customerData);
    }

    $payment_id = false;
    if ($customer_id) {
      $payment = new Payment($db);
      $payment_id = $payment->add($customer_id, $itemsData);
    }

    if ($payment_id) {
      $token = $payment->getTokenById($payment_id);
    }
  }

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
    $rawData = file_get_contents("php://input");
    $json = json_decode($rawData, true);

    $token = !empty($json['token']) ? substr($json['token'], 0, 40) : false;

    $payment = new Payment($db);

    $status = $payment->getStatusByToken($token);
    $transaction_id = $payment->getTransactionIdByToken($token);

    $app->render(200, ['status' => $status, 'transaction_id' => $transaction_id]);
});

$app->get('/api/payment/status', function() {
    $data = array(
      'token' => sha1('token')
    );
    die(json_encode($data));
});

$app->get('/api/sms', 'API', function() use ($app) {

  $phone = !empty($_GET['phone']) ? $_GET['phone'] : '';
  $message = !empty($_GET['message']) ? substr($_GET['message'], 0, 140) : '';

  $result = SMS($phone, $text);

  $app->render(200, ['result' => $result]);
});

$app->get('/api/url', 'API', function() use ($app) {
  $result = false;

  $url = !empty($_GET['url']) ? $_GET['url'] : '';

  if (!empty($url)) {

    $result = array(
      "longUrl" => $url,
      "url" => URL($url),
    );
  }

  $app->render(200, ['result' => $result]);
});

$app->run();