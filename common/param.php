<?php

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
  
  function SetValeurParam($nom, $valeur, $conn, $customid)
  {
  	$error = 0;
    $query = 'UPDATE parametre SET valeur = "' . $valeur .'" WHERE nom = "' . $nom . '" AND customid = "' . $customid . '"';
		if ($conn->query($query) === FALSE)
		{
			$error = -1;
		}
    return $error;
  }
?>
