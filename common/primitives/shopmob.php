<?php
  session_id("customerarea");
  session_start();
  
  header('Access-Control-Allow-Origin: *');
  header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
  header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
  header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
  header('Access-Control-Max-Age: 86400');
  
  require_once '../../vendor/autoload.php';
  include "../config/common_cfg.php";
  include "../param.php";

  try
  {
    if (empty($_SESSION['verify_email']) == TRUE)
    {
      throw new Error('Courriel non vérifié');
    }
    
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $bdd);
    // Check connection
    if ($conn->connect_error)
    {
      throw new Error("Connection failed: " . $conn->connect_error);
    }

    $json_str = file_get_contents('php://input');
    $input = json_decode($json_str);
    $output ="";

    $sql = "SELECT count(*) FROM customer cu WHERE cu.customer = '" . $input->aliasboutic . "' LIMIT 1";

    // error_log($sql);
    $result = $conn->query($sql);

    // output data of each row
    if($row = $result->fetch_row())
    {
      if ($row[0] > 0)
      {
        throw new Error('Alias de boutic déjà utilisé');
      }
    }

    $result->close();
    $conn->close();

    $_SESSION['initboutic_aliasboutic'] = $input->aliasboutic;
    $_SESSION['initboutic_nom'] = $input->nom;
    $_SESSION['initboutic_logo'] = $input->logo;
    $_SESSION['initboutic_email'] = $input->email;

    if (empty($_SESSION['initboutic_aliasboutic'])) {
      throw new Error("Identifiant vide");
    }
    
    $notid = array('admin', 'common', 'route', 'upload', 'vendor');
    if(in_array($_SESSION['initboutic_aliasboutic'], $notid)) //Si l'extension n'est pas dans le tableau
    {
      throw new Error('Identifiant interdit');
    }

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
      'refresh_url' => $protocole . $server . '/common/404.php',
      'return_url' => 'http://localhost/shopsettings',
      'type' => 'account_onboarding',
    ]);

    echo json_encode($accountlink->url);
  }
  catch (Error $e)
  {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
  }
?>
