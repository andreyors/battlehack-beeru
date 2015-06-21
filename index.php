<?php

include 'conf/init.php';

$app = new \Slim\Slim(array(
    'debug' => true,
    'view' => new \Slim\Views\Twig,
    'templates.path' => VIEWSDIR,
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

$app->get('/pay/:token', function($token) use ($app, $db) {
  $payment = new Payment($db);
  $paymentData = $payment->getByToken($token);

  $customer_id = intval($paymentData['customer_id']);

  $customer = new Customer($db);
  $customerData = $customer->get($customer_id);

  $paypalCustomerId = $customer->getCustomerIdById($customer_id);

  if (empty($paypalCustomerId)) {
    $data = array(
      'firstName' => $customerData['first_name'],
      'lastName' => $customerData['last_name'],
      'email' => $customerData['email'],
      'phone' => $customerData['phone'],
    );

    $paypalCustomerId = $customer->createCustomer($data);
    $customer->update($customer_id, array('customer_id' => $paypalCustomerId));
  }

  $clientToken = false;
  if (!empty($paypalCustomerId)) {
    $clientToken = $customer->getTokenByCustomerId($paypalCustomerId);
  }

  $app->render('index/pay.twig', ['amount' => $paymentData['amount'], 'client_token' => $clientToken, 'token' => $token]);
});

$app->post('/pay/process', function() use ($app, $db) {
  $token = !empty($_POST['token']) ? $_POST['token'] : '';
  $amount = !empty($_POST['amount']) ? $_POST['amount'] : '';
  $nonce = !empty($_POST['payment_method_nonce']) ? $_POST['payment_method_nonce'] : '';

  $res = false;
  if (!empty($nonce) && !empty($amount)) {
    $payment = new Payment($db);
    $payment_id = $payment->getIdByToken($token);

    $res = $payment->createPayment($payment_id, $nonce, $amount);
  }

  if ($res) {
    header('Location: /paid', true, 302);
    die();
  } else {
    die(var_dump($res));
  }
});

$app->get('/paid', function() use($app, $db) {
  $app->render('index/paid.twig');
});

$app->post('/api/payment', 'API', function() use ($app, $db) {
  $rawData = file_get_contents("php://input");
  $json = json_decode($rawData, true);

  $customerData = !empty($json['customer']) ? $json['customer'] : array();
  $itemsData = !empty($json['items']) ? $json['items'] : array();

  $token = null;
  if (!empty($customerData)) {
    $customer = new Customer($db);
    $customer_id = $customer->getIdByValues($customerData);

    if (!$customer_id) {
      $customer_id = $customer->create($customerData);
    }

    $amount = 0;
    $payment_id = false;
    if ($customer_id) {
      $payment = new Payment($db);

      $amount = $payment->getAmount($itemsData);
      $payment_id = $payment->add($customer_id, $itemsData);
    }

    if ($payment_id) {
      $token = $payment->getTokenById($payment_id);
    }

    if ($token && !empty($customerData['phone']) && isValidPhone($customerData['phone'])) {
      $url = 'http://' . $_SERVER['HTTP_HOST'] . '/pay/' . $token;
      SMS($customerData['phone'], "Pay " . (!empty($amount) ? $amount . " EUR" : '') . " by " . URL($url));
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

$app->get('/api/mail', 'API', function() use ($app, $db) {
  $to = !empty($_GET['to']) ? $_GET['to'] : '';
  $subj = !empty($_GET['subj']) ? $_GET['subj'] : '';
  $from = !empty($_GET['from']) ? $_GET['from'] : '';
  $text = !empty($_GET['text']) ? $_GET['text'] : '';

  if (!empty($to) && !empty($from)) {
    QMAIL($from, $to, $subj, $text);
  }
});

$app->options('/api/token', function() use ($app, $db) {
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
  header('Access-Control-Max-Age: 1000');
  header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
});

$app->post('/api/token', 'API', function() use($app, $db) {
  $rawData = file_get_contents("php://input");
  $json = json_decode($rawData, true);

  $customer = new Customer($db);

  $data = array(
      'first_name' => (!empty($json['first_name']) ? $json['first_name'] : ''),
      'last_name' => (!empty($json['last_name']) ? $json['last_name'] : ''),
      'email' => (!empty($json['email']) ? $json['email'] : ''),
      'phone' => (!empty($json['phone']) ? $json['phone'] : ''),
  );

  $customer_id = $customer->getIdByValues($data);

  if (empty($customer_id)) {
    $customer_id = $customer->create($data);
  }

  $paypalCustomerId = $customer->getCustomerIdByValues($data);
  if (empty($paypalCustomerId)) {
    $data = array(
      'firstName' => (!empty($json['first_name']) ? $json['first_name'] : ''),
      'lastName' => (!empty($json['last_name']) ? $json['last_name'] : ''),
      'email' => (!empty($json['email']) ? $json['email'] : ''),
      'phone' => (!empty($json['phone']) ? $json['phone'] : ''),
    );
    $paypalCustomerId = $customer->createCustomer($data);
    $customer->update($customer_id, array('customer_id' => $paypalCustomerId));
  }

  $token = null;
  if ($paypalCustomerId) {
    $token = $customer->getTokenByCustomerId($paypalCustomerId);
  }

  $app->render(200, ['token' => $token]);
});

$app->options('/api/data', function() {
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
  header('Access-Control-Max-Age: 1000');
  header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
});

$app->post('/api/data', 'API', function() use($app, $db) {
  $rawData = file_get_contents("php://input");
  $json = json_decode($rawData, true);

  $email = !empty($json['email']) ? $json['email'] : '';
  $phone = !empty($json['phone']) ? $json['phone'] : '';
  $link = !empty($json['link']) ? $json['link'] : '';

  $result = 'failed';
  if (!empty($link)) {
    $short = URL($link);

    $text = "You've got a gift - Follow " . $short;

    if (!empty($email)) {
      QMAIL('delivery@beeru.com', $email, 'Bottle of Beer...', $text);
    }

    if (!empty($phone)) {
      SMS($phone, $text);
    }

    $result = 'sent';
  }

  $app->render(200, ['result' => $result]);
});

$app->run();