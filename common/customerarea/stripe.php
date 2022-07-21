<?php

  session_id("customerarea");
  session_start();
  
  require '../../vendor/autoload.php';
  include "../config/common_cfg.php";
  include "../param.php";
  
  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();

  // For sample support and debugging. Not required for production:
  \Stripe\Stripe::setAppInfo(
    "pratic-boutic/registration  ",
    "0.0.2",
    "https://praticboutic.fr"
  );

  $stripe = new \Stripe\StripeClient([
    'api_key' => $_ENV['STRIPE_SECRET_KEY'],
    'stripe_version' => '2020-08-27',
  ]);
  
  $account = $stripe->accounts->create([
    'type' => 'express',
    'country' => 'FR',
    'email' => $_SESSION['verify_email'],
    'capabilities' => [
      'card_payments' => ['requested' => true],
      'transfers' => ['requested' => true],
    ],
  ]);
  
  $_SESSION['STRIPE_ACCOUNT_ID'] = $account->id;
  
  if (isset($_SERVER['HTTPS']))
  {
    $protocole = 'https://';
  }
  else
  {
    $protocole = 'http://';
  }
  
  $server = $_SERVER['SERVER_NAME'];
  
  $accountlink = $stripe->accountLinks->create([
    'account' => $account->id,
    'refresh_url' => $protocole . $server . '/reauth',
    'return_url' => $protocole . $server . '/common/customerarea/moneyboutic.php',
    'type' => 'account_onboarding',
  ]);
  
  header('Location: ' . $accountlink->url);

?>

 