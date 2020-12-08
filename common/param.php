<?php

  /*session_start();
  $customer = $_GET['customer'];

  include "../" . $customer . "/config/config.php";*/

  function GetValeurParam($nom,$conn,$customid,$valdef = "")
  {
    $query = 'SELECT paramid, nom, valeur FROM parametre WHERE nom = "' . $nom . '" AND customid = "' . $customid . '"';
    if ($result = $conn->query($query)) 
		{
			$row = $result->fetch_row();
    	if ($row != NULL) 
    	{
    		$valeur = $row[2];
    	}
 			else
			{
				$valeur = $valdef;
			}
		}
    return $valeur;
  }
  
?>
