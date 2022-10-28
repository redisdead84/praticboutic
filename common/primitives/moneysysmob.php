<?php

    require_once '../../vendor/autoload.php';
    include "../config/common_cfg.php";
    include "../param.php";

    try
    {

        $json_str = file_get_contents('php://input');
        $input = json_decode($json_str);
        $output ="";

        if (isset($input->sessionid))
          session_id($input->sessionid);
        session_start();
        
        if (empty($_SESSION['verify_email']) == TRUE)
        {
          throw new Error('Courriel non vérifié');
        }

        
        $_SESSION['moneysys_moneysys'] = $input->moneysys;
        $_SESSION['moneysys_caisse'] = $input->caisse;
        $_SESSION['moneysys_stripepubkey'] = $input->stripepubkey;
        $_SESSION['moneysys_stripeseckey'] = $input->stripeseckey;
        $_SESSION['moneysys_paypalid'] = $input->paypalid;

        echo json_encode("OK");
    }
    catch (Error $e)
    {
    echo $e->getMessage();
    }
?>

