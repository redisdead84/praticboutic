<?php
  session_id("customerarea");
  session_start();

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
