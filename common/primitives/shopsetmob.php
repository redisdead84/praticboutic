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

    $json_str = file_get_contents('php://input');
    $input = json_decode($json_str);
    $output ="";
    
    $_SESSION['confboutic_chxmethode'] = $input->chxmethode;
    $_SESSION['confboutic_mntmincmd'] = $input->mntmincmd;
    $_SESSION['confboutic_validsms'] = $input->validsms;

    echo json_encode("OK");
  }
  catch (Error $e)
  {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
  }
?>
