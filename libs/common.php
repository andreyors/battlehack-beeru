<?php

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

function SMS($phone, $text) {
  $client = new Services_Twilio(TWILIO_SID, TWILIO_TOKEN);

  try {
    $twilio = $client->account->messages->create(array(
        "From" => TWILIO_NUMBER,
        "To" => $phone,
        "Body" => $text,
    ));
  } catch (Services_Twilio_RestException $e) {
    echo $e->getMessage();
  }

  return $twilio ? $twilio->sid : false;
}

function URL($url) {
  $gApi = new GoogleShortener(GOOGLE_API_KEY);
  return $gApi->shorten($url);
}

function MAIL($from, $to, $subject, $text) {
  $url = 'https://api.sendgrid.com/api/mail.send.json';

  $params = array(
    'api_user' => SENDGRID_USER,
    'api_key' => SENDGRID_PASS,
    'from' => $from,
    'subject' => $subject,
    'text' => $text,
  );

  $to = !is_array($to) ? array($to) : $to;

  if (!empty($to)) {
    foreach($to as $v) {
      if (preg_match('#(.*)\s+\<(.*)\>+#', $v, $matches)) {
        var_dump($matches);
      }
    }
  }

  $postdata = http_build_query($params);
  $opts = array(
    'http' =>array(
      'method'  => 'POST',
      'header'  => 'Content-type: application/x-www-form-urlencoded',
      'content' => $postdata
    )
  );

  $context  = stream_context_create($opts);
  $result = file_get_contents($url, false, $context);

  return $result;
}

function isValidPhone($phone) {
  $phoneUtil = libphonenumber\PhoneNumberUtil::getInstance();
  try {
    $numberProto = $phoneUtil->parse($phone, "DE");
  } catch (\libphonenumber\NumberParseException $e) {
      echo $e->getMessage();
  }
  return $numberProto;
}
