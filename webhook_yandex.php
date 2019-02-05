<?php
$requestBody = file_get_contents('php://input');

$json = json_decode($requestBody);
$text = strtolower($json->request->command);
$sessionnew = $json->session->new;
$message_id = $json->session->message_id;
$session_id = $json->session->session_id;
$user_id = $json->session->user_id;
$msg_hi = array("Привет.", "Привет!", "Приветствую.", "Приветствую!", "Приветик!", "Приветик.");
$msg_by = array("Пока!", "Всего доброго!", "До связи!", "До встречи!", "Если что, я - тут.");
$msg_accesDenied = array("Это закрытый навык.", "Мне нельзя общаться с чужими.", "Мне не разрешают общаться с чужими.");
$msg_hi = $msg_hi[shuffle($msg_hi)];
$msg_by = $msg_by[shuffle($msg_by)];
$msg_accesDenied = $msg_accesDenied[shuffle($msg_accesDenied)];

include_once("./config.php");
$_SERVER['PHP_AUTH_USER'] = EXT_ACCESS_USERNAME;
$_SERVER['PHP_AUTH_PW'] = EXT_ACCESS_PASSWORD;
include_once("./lib/loader.php");
$session = new session("prj");
$db = new mysql(DB_HOST, '', DB_USER, DB_PASSWORD, DB_NAME);
include_once(DIR_MODULES . "application.class.php");
include_once("./load_settings.php");
$keyword = gg('ThisComputer.keyword');
$yandexID = gg('ThisComputer.yandexID');

$speech = 'Принято: '.$text;
if ($text == 'пока') {$speech = $msg_by; goto answer2yandex;}
if ($text == $keyword) {
  say("Идентификатор пользователя изменён!\nБыл  ".$yandexID.",\nстал ".$user_id, -1);
  sg('ThisComputer.yandexID', $user_id);
  goto answer2yandex;
}

//отвечаем яндексу
if ($sessionnew == true) {
  if ($user_id != $yandexID) {$speech = $msg_hi." Простите, но это закрытый навык и он только для моей семьи."; goto answer2yandex;}
  $speech = $msg_hi." Чем могу помочь?";}
if ($user_id != $yandexID) {$speech = $msg_accesDenied; goto answer2yandex;} else {say(htmlspecialchars($text), 0, 1);}

answer2yandex:
$response = new \stdClass();
$response->response->text = $speech;
$response->response->tts = $speech;
$response->response->end_session = false;
$response->session->message_id = $message_id;
$response->session->session_id = $session_id;
$response->session->user_id = $user_id;
$response->version = '1.0';
echo json_encode($response);
?>
