<?php

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
    $postdata = file_get_contents("php://input");
    if (isset($postdata))
    {
      $request = json_decode($postdata);
    }
    
    if (isset($input->sessionid))
      session_id($input->sessionid);
    session_start();

    $authofile = fopen('../mobileapp/authorisation.json', 'r');
    $authojson = fread($authofile, filesize('../mobileapp/authorisation.json'));
    fclose($authofile);
    $autho = json_decode($authojson);

    $output = $autho;

    echo json_encode($output);
  } 
  catch (Error $e) 
  {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
  }
?>