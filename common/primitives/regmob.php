<?php


  session_start();

  require '../../vendor/autoload.php';
  include "../config/common_cfg.php";
  include "../param.php";
  
  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();

  header('Access-Control-Allow-Origin: *');
  header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
  header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
  header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
  header('Access-Control-Max-Age: 86400');
  header('Content-Type: application/json');

  try
  {
    
    if (!isset($_SESSION))
    {
      throw new Error('Session expirée');
    }
    
    if (empty($_SESSION['verify_email']) == TRUE)
    {
      throw new Error('Courriel non vérifié');
    }
    
    //error_log($_SESSION['verify_email']);
    $json_str = file_get_contents('php://input');
    $input = json_decode($json_str);
    $output ="";

    $_SESSION['registration_pass'] = $input->pass;
    $_SESSION['registration_qualite'] = $input->qualite;
    $_SESSION['registration_nom'] = $input->nom;
    $_SESSION['registration_prenom'] = $input->prenom;
    $_SESSION['registration_adr1'] = $input->adr1;
    $_SESSION['registration_adr2'] = $input->adr2;
    $_SESSION['registration_cp'] = $input->cp;
    $_SESSION['registration_ville'] = $input->ville;
    $_SESSION['registration_tel'] = $input->tel;
    
    $stripe = new \Stripe\StripeClient([
      // TODO replace hardcoded apikey by env variable
        'api_key' => $_ENV['STRIPE_SECRET_KEY'],
        'stripe_version' => '2020-08-27',
      ]);
    
      $customer = $stripe->customers->create([
      'address' => ['city' => $input->ville,
                    'country' => 'FRANCE',
                    'line1' => $input->adr1,
                    'line2' => $input->adr2,
                    'postal_code' => $input->cp],
      'email' => $_SESSION['verify_email'],
      'name' => $input->nom,
      'phone' => $input->tel
    ]);

    $_SESSION['registration_stripe_customer_id'] = $customer->id;
    
    // error_log($_SESSION['verify_email']);
    echo json_encode("OK");
  }
  catch (Error $e)
  {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
  }
?>

