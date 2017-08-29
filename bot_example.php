<?php

define('BOT_TOKEN', 'your_token');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

function reply($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }

  $parameters["method"] = $method;

  header("Content-Type: application/json");
  echo json_encode($parameters);
  return true;
}

function processMessage($message) {
  $chat_id = $message['chat']['id'];
  $text = $message['text'];

  if (strpos($text, "/start") === 0) {
    reply("sendMessage", array('chat_id' => $chat_id, "text" => 'Hello'));
  } else if (strpos($text, "/stop") === 0) {
      // stop now
  } else {
    reply("sendMessage", array('chat_id' => $chat_id,  "text" => 'Hi'));
  }   
}

function logging($message) {
  $id = $message['from']['id'];
  $username = $message['from']['username'];
  $date = $message['date'];
  $text = $message['text'];

  $logFile = fopen("telegramBot.log", "a");
  //fwrite($logFile, json_encode($message, JSON_PRETTY_PRINT)."\n");
  fwrite($logFile, gmdate("Y/m/d H:i:s", $date)." ".$id." ".$username." ".$text."\n");
  fclose($logFile);
}

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
  // receive wrong update, must not happen
  exit;
}

if (isset($update["message"])) {
  processMessage($update["message"]);
  logging($update["message"]);
}
