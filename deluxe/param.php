<?php

  include "config/config.php";

  function GetValeurParam($nom,$conn)
  {
    $query = 'SELECT paramid, nom, valeur FROM parametre WHERE nom = "' . $nom . '"';
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
