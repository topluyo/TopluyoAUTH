<?php 

// Report all PHP errors
error_reporting(E_ALL);

// Display errors on the screen
ini_set('display_errors', 1);

// Optional: display startup errors too
ini_set('display_startup_errors', 1);

class TopluyoAUTH{
  public static $APP_ID;
  public static $APP_KEY;
  public static $REDIRECT_URI;
  public static function login(){
    @session_start();
    if($_GET['code'] && $_GET['state']){
      if($_SESSION["state"]!=$_GET["state"]){
        echo "State FAIL";
        die();
      }
      


      $token_url = "https://topluyo.com/!pass/token";

      $data = [
        "grant_type" => "authorization_code",
        "code" => $_GET['code'],
        "redirect_uri" => self::$REDIRECT_URI,
        "client_id" => self::$APP_ID,
        "client_secret" => self::$APP_KEY
      ];

      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $token_url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
          "Content-Type: application/x-www-form-urlencoded"
      ]);
      $response = curl_exec($ch);
      if (curl_errno($ch)) {
        echo "cURL Error: " . curl_error($ch);
        curl_close($ch);
        exit;
      }
      curl_close($ch);      
      $result = json_decode($response, true);
      return $result;
    }else{
      $_SESSION["state"] = bin2hex(random_bytes(16));
      $redirect = "https://topluyo.com/!pass/request?response_type=code&client_id=".self::$APP_ID."&redirect_uri=".urlencode(self::$REDIRECT_URI).
        "&scope=".urlencode("openid profile")."&state=".$_SESSION["state"];
      //echo $redirect;
      header("Location: ".$redirect);
      die();

    }
  }
}


TopluyoAUTH::$APP_ID = 11111111;
TopluyoAUTH::$APP_KEY = "XXXXXX";
TopluyoAUTH::$REDIRECT_URI = "https://app.kodluyo.com/login";



$response = TopluyoAUTH::login();
if($response["status"]=="success"){
  $_SESSION["user"] = $response["user"];
  //echo "TOPLUYO ile OTURUM AÇILDI =)";
  //print_r($response);
  header("Location: https://app.kodluyo.com/editor");
}else{
  echo $response["message"];
}
