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
