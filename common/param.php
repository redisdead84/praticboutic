<?php

  /*session_start();
  $customer = $_GET['customer'];

  include "../" . $customer . "/config/config.php";*/

  function GetValeurParam($nom,$conn,$customid)
  {
    $query = 'SELECT paramid, nom, valeur FROM parametre WHERE nom = "' . $nom . '" AND customid = "' . $customid . '"';
    if ($result = $conn->query($query)) 
		{
    	if ($row = $result->fetch_row()) 
    	{
    		$valeur = $row[2];
    	}
		}
    return $valeur;
  }
  
?>
