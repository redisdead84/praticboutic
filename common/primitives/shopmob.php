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
    $_SESSION['initboutic_adresse1'] = $input->adresse1;
    $_SESSION['initboutic_adresse2'] = $input->adresse2;
    $_SESSION['initboutic_codepostal'] = $input->codepostal;
    $_SESSION['initboutic_ville'] = $input->ville;
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

    echo json_encode("OK");
  }
  catch (Error $e)
  {
    echo $e->getMessage();
  }
?>
