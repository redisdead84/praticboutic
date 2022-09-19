<?php
  session_id("customerarea");
  session_start();

  header('Access-Control-Allow-Origin: *');
  header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
  header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
  header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
  header('Access-Control-Max-Age: 86400');

  require '../../vendor/autoload.php';
  include "../config/common_cfg.php";
  include "../param.php";

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();
  try
  {
    
    if (empty($_SESSION['bo_auth']) == TRUE)
    {
      throw new Error("Non authentifié");
    }
  
    if (strcmp($_SESSION['bo_auth'],'oui') != 0)
    {
      throw new Error("Non authentifié");
    }
    
    echo json_encode($_ENV['STRIPE_PUBLISHABLE_KEY']);
  }
  catch (Error $e)
  {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
  }

?>
