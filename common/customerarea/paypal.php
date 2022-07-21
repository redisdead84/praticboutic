<?php

  session_id("boutic");
  session_start();
  
  require '../vendor/autoload.php';
  include "config/common_cfg.php";
  include "param.php";
  
  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();
    $postdata = array(
      'grant_type' => 'authorization_code',
      'code' => $_GET['code'],
      )
    );
    
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/oauth2/token");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic {AQTFxFjirZ4jnrHFeik5AQFuFJuSvhPe0n274XMjK1ogWD1W7HOsyZWy_rKrN4NJY7jHZYHWKp0MeBtO:EIhaNzY6OtojFEBi8etn-wK751Oa9FOQatCPKsm2YG5kQbrQCXBAYdHjlIQGtkPNLo2qd-nwUJM8e76q}');
  $response = curl_exec($ch);
  curl_close($ch);

?>