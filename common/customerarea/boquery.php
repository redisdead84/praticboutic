<?php

session_start();

class Lien {
  public $nom;
  public $tblsrc;
  public function __construct($nom, $tblsrc) {
    $this->nom = $nom;
    $this->srctbl = $tblsrc;
  }
}


require '../../vendor/autoload.php';

include "../config/common_cfg.php";
include "../param.php";

// Create connection
$conn = new mysqli($servername, $username, $password, $bdd);
// Check connection
if ($conn->connect_error) 
  die("Connection failed: " . $conn->connect_error); 

 

header('Content-Type: application/json');

try {

  $json_str = file_get_contents('php://input');
  $input = json_decode($json_str);

	
	//$rcvnom = GetValeurParam("Receivernom_mail", $conn, $input->bouticid);

	//error_log($input->action);
	
	if (strcmp($input->table,"")!=0)
	{
		for($i=0; $i<count($input->tables); $i++)
		{
			if (strcmp($input->tables[$i]->nom, $input->table)==0)
				$numtable = $i;		
		}
	}	  
	
	if (strcmp($input->action,"elemtable") == 0)
  {
	  $colonnes ="";
	  $liens=array();

		for($i=0; $i<count($input->tables[$numtable]->champs); $i++) 
		{
			$str = $input->tables[$numtable]->champs[$i]->nom;
			$typ = $input->tables[$numtable]->champs[$i]->typ;
			//$posp = strpos($str, ".");
			if (strcmp($typ,"pk")==0)
				$colonnes = "count(" . $str . ")"; 		
		}
		
	  $query = 'SELECT ' . $colonnes . ' FROM `' . $input->tables[$numtable]->nom . '` T1';
	  $query = $query . ' WHERE T1.customid = ' . $input->bouticid; 
	  if (strcmp($input->selcol, "")!=0)
			$query = $query . ' AND T1.' . $input->selcol . ' = ' . $input->selid;
			
		//error_log($query);
		
		$arr=array();	
		
		if ($result = $conn->query($query)) 
		{
			if ($row = $result->fetch_row()) 
		  {	
				array_push($arr, html_entity_decode ($row[0]));
	    }						
		  $result->close();
	  }   
  }		
	
	if (strcmp($input->action,"vuetable") == 0)
  {
	  $colonnes ="";
	  $liens=array();
	  $orderby = array();
	  $pk="";

		for($i=0; $i<count($input->tables[$numtable]->champs); $i++) 
		{
			$str = $input->tables[$numtable]->champs[$i]->nom;
			$typ = $input->tables[$numtable]->champs[$i]->typ;
			$sens = $input->tables[$numtable]->champs[$i]->sens;
			$ordre = $input->tables[$numtable]->champs[$i]->ordre;
			//$posp = strpos($str, ".");
			if (strcmp($typ,"fk")!=0)
			{
				$str = "T1." . $str;
				if ($ordre>0)
				{
					if (strcmp($sens,"A") == 0)
						$tri = array("field"=> $str, "sens"=>"ASC");
					else if (strcmp($sens,"D") == 0)
						$tri = array("field"=> $str, "sens"=>"DESC");

					array_push($orderby, $tri);	
					//error_log(json_encode($orderby));
				}
				if (strcmp($typ,"pk")==0)
				{
					$pk = $input->tables[$numtable]->champs[$i]->nom;				
				}	
			}
		  else 
			{
				for($j=0; $j<count($input->liens); $j++) 
				{
					if ((strcmp($input->liens[$j]->srctbl, $input->tables[$numtable]->nom)==0) && (strcmp($input->liens[$j]->srcfld, $input->tables[$numtable]->champs[$i]->nom)==0))
					{
						$nomlien = $input->liens[$j]->nom;
						for ($k=0; $k<count($input->tables); $k++)
						{
							if (strcmp($input->tables[$k]->nom, $input->liens[$j]->dsttbl)==0)
								$fld = $input->tables[$k]->cs;
						}
						$tblsrclien = $input->liens[$j]->srctbl;
						$lelien = new Lien($nomlien, $tblsrclien);
					}
				}
				$find = FALSE;
				for ($j=0;$j<count($liens);$j++)
				{
					if((strcmp($nomlien,$liens[$j]->nom)==0)&&(strcmp($tblsrclien,$liens[$j]->srctbl)==0))
					{
						$find = TRUE;
						$tblindex = $j;
					}
				}
				if ($find == FALSE)
				{
					$liens[count($liens)] = $lelien;
					$tblindex = count($liens);
				}
				$str = "T" . strval($tblindex + 1) . "." . $fld;
				if ($ordre>0)
				{
					if (strcmp($sens,"A") == 0)
						$tri = array("field"=> $str, "sens"=>"ASC");
					else if (strcmp($sens,"D") == 0)
						$tri = array("field"=> $str, "sens"=>"DESC");

					array_push($orderby, $tri);	
				} 		
			}
	  	$colonnes = $colonnes . $str;
	  	if ($i != count($input->tables[$numtable]->champs)-1)
	  		$colonnes = $colonnes . ', ';
		}
		
	  $query = 'SELECT ' . $colonnes . ' FROM `' . $input->tables[$numtable]->nom . '` T1'; 
	  $addwhere ="";
	  
	  for ($i=0; $i<count($liens); $i++)
		{
			for ($j=0; $j<count($input->liens); $j++)
			{
				if (strcmp($input->liens[$j]->nom, $liens[$i]->nom)==0)
				{
					if(strcmp($input->liens[$j]->srctbl, $liens[$i]->srctbl)==0)
					{
						//$query = $query . ',`' . $input->liens[$j]->dsttbl . '` T' . strval($i + 2);
						if (strcmp($input->liens[$j]->join, "ij") == 0)
							$addwhere = $addwhere . ' INNER JOIN `' . $input->liens[$j]->dsttbl . '` T' . strval($i + 2) . ' ON T1.' . $input->liens[$j]->srcfld . '=T' . strval($i + 2) . '.' . $input->liens[$j]->dstfld;
						else if (strcmp($input->liens[$j]->join, "rj") == 0)
							$addwhere = $addwhere . ' RIGHT JOIN `' . $input->liens[$j]->dsttbl . '` T' . strval($i + 2) . ' ON T1.' . $input->liens[$j]->srcfld . '=T' . strval($i + 2) . '.' . $input->liens[$j]->dstfld;
						else if (strcmp($input->liens[$j]->join, "lj") == 0)
							$addwhere = $addwhere . ' LEFT JOIN `' . $input->liens[$j]->dsttbl . '` T' . strval($i + 2) . ' ON T1.' . $input->liens[$j]->srcfld . '=T' . strval($i + 2) . '.' . $input->liens[$j]->dstfld;
						
					}
				}
			}
		} 
		  
	  $query = $query . $addwhere;
	  
	  if (strcmp($addwhere, "")==0)
	    $query = $query . ' WHERE T1.customid = ' . $input->bouticid;
	  else
	    $query = $query . ' AND T1.customid = ' . $input->bouticid;
	    
	  if (strcmp($input->selcol, "")!=0)
	  	$query = $query . ' AND T1.' . $input->selcol . ' = ' . $input->selid;
	  	
	  $query = $query . ' ORDER BY ';

	  for ($i=0; $i<count($orderby); $i++)
	  	$query = $query . $orderby[$i]['field'] . ' ' . $orderby[$i]['sens'] . ', ';
	  
	  $query = $query . $pk . ' LIMIT ' . $input->limite . ' OFFSET '. $input->offset;
	  
	  //error_log($query);
		  
		$arr=array();	
		
		if ($result = $conn->query($query)) 
		{
			while ($row = $result->fetch_row()) 
		  {	
		  	$arm = array();
				for($i=0; $i<count($input->tables[$numtable]->champs); $i++) 
				{
					array_push($arm, $row[$i]);
				}
		  	
				array_push( $arr, $arm);
	
	    }						
		  $result->close();
	  }   

  }
    
  if (strcmp($input->action,"rempliroption") == 0)
  {
 		for ($i=0;$i<count($input->tables[$numtable]->champs);$i++)
			if (strcmp($input->tables[$numtable]->champs[$i]->typ, "pk")== 0)
				$clep = $input->tables[$numtable]->champs[$i]->nom; 		

  	$query = 'SELECT ' . $clep . ', ' . $input->colonne . ' FROM `' . $input->tables[$numtable]->nom . '`'; 
  	$query = $query . ' WHERE customid = ' . $input->bouticid;
  	
		if (strcmp($input->tables[$numtable]->nom, "statutcmd") == 0 ) 
	  	$query = $query . ' AND actif = 1';
  	
		$arr=array();	
		
		if ($result = $conn->query($query)) 
		{
			while ($row = $result->fetch_row()) 
		  {	
		  	$arm = array();
		  	for($j=0;$j<count($row);$j++)
					array_push($arm, $row[$j]);

		  	array_push($arr, $arm);

	    }						
		  $result->close();
	  }   

  }
  
  if (strcmp($input->action,"insertrow") == 0)
  {

  	$query = 'INSERT INTO `' . $input->tables[$numtable]->nom . '`(';
  	$query = $query . 'customid, ';
		for($i=0;$i<count($input->row);$i++) 
		{  	
			if (strcmp($input->row[$i]->type,"ref")==0)
			{
				$colonnes = "count(*)"; 						
			  $subquery = 'SELECT ' . $colonnes . ' FROM `' . $input->tables[$numtable]->nom . '` T1';
			  $subquery = $subquery . ' WHERE T1.customid = ' . $input->bouticid; 
				$subquery = $subquery . ' AND T1.' . $input->row[$i]->nom . ' = "' . $input->row[$i]->valeur . '"';
				//error_log($subquery);
				if ($result = $conn->query($subquery)) 
				{
					if ($row = $result->fetch_row()) 
				  {
						if (intval($row[0])>0)
							throw new Error("Impossible d'avoir plusieurs fois la valeur '" . $input->row[$i]->valeur . "' dans la colonne '" . $input->row[$i]->desc . "'");
			    }
				  $result->close();
			  }   
			}	  	
			if (strcmp($input->row[$i]->type,"email")==0)
			{
				$colonnes = "count(*)"; 						
			  $subquery = 'SELECT ' . $colonnes . ' FROM `' . $input->tables[$numtable]->nom . '` T1';
				$subquery = $subquery . ' WHERE T1.' . $input->row[$i]->nom . ' = "' . $input->row[$i]->valeur . '"';
				//error_log($subquery);
				if ($result = $conn->query($subquery)) 
				{
					if ($row = $result->fetch_row()) 
				  {
						if (intval($row[0])>0)
							throw new Error("Le courriel '" . $input->row[$i]->valeur . "' existe déjà dans la base de donnée");
			    }
				  $result->close();
			  }   
			}
			
			
	  	$query = $query . $input->row[$i]->nom;
	  	if ($i != count($input->row)-1)
	  		$query = $query . ', ';
  	}
  	$query = $query . ') VALUES (';
  	$query = $query . '"' . $input->bouticid . '", '; 
		for($i=0; $i<count($input->row); $i++) 
		{ 
			if (strcmp($input->row[$i]->type, "pass") != 0) 	
	  		$query = $query . '"' . $input->row[$i]->valeur . '"';
	  	else 
	  		$query = $query . '"' . password_hash($input->row[$i]->valeur, PASSWORD_DEFAULT) . '"';
	  	
	  	if ($i != count($input->row)-1)
	  		$query = $query . ', ';
  	}
  	$query = $query . ')';
  	  	
		$arr=array();

		if ($conn->query($query) === FALSE)
		{
			throw new Error($conn->error);
		}
		
  }
  
  if (strcmp($input->action,"getvalues") == 0)
  {
 		for ($i=0;$i<count($input->tables[$numtable]->champs);$i++)
			if (strcmp($input->tables[$numtable]->champs[$i]->typ, "pk")== 0)
				$clep = $input->tables[$numtable]->champs[$i]->nom; 		

  	$colonnes ="";
	  $liens=array();
		for($i=0; $i<count($input->tables[$numtable]->champs); $i++) 
		{
			$str = $input->tables[$numtable]->champs[$i]->nom;
			$colonnes = $colonnes . '`' . $str . '`';
			if ($i != count($input->tables[$numtable]->champs)-1)
	  		$colonnes = $colonnes . ', ';
		}
  	
  	$query = 'SELECT ' . $colonnes . ' FROM `' . $input->tables[$numtable]->nom . '`'; 
  	$query = $query . ' WHERE ' . $clep . '=' . $input->idtoup . ' AND customid = ' . $input->bouticid;
  	
  	//error_log($query);
  	
		$arr=array();	
		
		if ($result = $conn->query($query)) 
		{
			if ($row = $result->fetch_row()) 
		  {	
		  	for($i=0; $i<count($input->tables[$numtable]->champs); $i++)
		  	{
					array_push($arr, html_entity_decode ($row[$i]));
				}
	    }						
		  $result->close();
	  }   

  }
  
  if (strcmp($input->action,"updaterow") == 0)
  {
  	$query = 'UPDATE `' . $input->tables[$numtable]->nom . '` SET ';
		for($i=0;$i<count($input->row);$i++) 
		{
			if (strcmp($input->row[$i]->type,"ref")==0)
			{
				$colonnes = "count(*)"; 						
			  $subquery = 'SELECT ' . $colonnes . ' FROM `' . $input->tables[$numtable]->nom . '`';
			  $subquery = $subquery . ' WHERE customid = ' . $input->bouticid;
			  $subquery = $subquery . ' AND ' . $input->row[$i]->nom . ' = "' . $input->row[$i]->valeur . '"'; 
		  	$subquery = $subquery . ' AND ' . $input->colonne . '!=' . $input->idtoup;
				//error_log($subquery);
				if ($result = $conn->query($subquery)) 
				{
					if ($row = $result->fetch_row()) 
				  {
						if (intval($row[0])>0)
							throw new Error("Impossible d'avoir plusieurs fois la valeur '" . $input->row[$i]->valeur . "' dans la colonne '" . $input->row[$i]->desc . "'");
			    }
				  $result->close();
			  }   
			}	  	
			if (strcmp($input->row[$i]->type,"email")==0)
			{
				$colonnes = "count(*)"; 						
			  $subquery = 'SELECT ' . $colonnes . ' FROM `' . $input->tables[$numtable]->nom . '`';
			  $subquery = $subquery . ' WHERE ' . $input->row[$i]->nom . ' = "' . $input->row[$i]->valeur . '"';
		  	$subquery = $subquery . ' AND ' . $input->colonne . '!=' . $input->idtoup;
				//error_log($subquery);
				if ($result = $conn->query($subquery)) 
				{
					if ($row = $result->fetch_row()) 
				  {
						if (intval($row[0])>0)
							throw new Error("Le courriel '" . $input->row[$i]->valeur . "' existe déjà dans la base de donnée");
			    }
				  $result->close();
			  }   
			}  	
			$jump = 0;
	  	if (strcmp($input->row[$i]->type, "pass") == 0)
	  	{
	  		if (strcmp($input->row[$i]->valeur,"") != 0) 	
	  			$query = $query . $input->row[$i]->nom . ' = "' . password_hash($input->row[$i]->valeur, PASSWORD_DEFAULT) . '"';
	  		else 
					$jump = 1;
 			}
			else 
				$query = $query . $input->row[$i]->nom . ' = "' . $input->row[$i]->valeur . '"';
				
	  	if (($i != count($input->row)-1) && ($jump == 0))
		  		$query = $query . ', ';
		  		
  	}
  	
  	$query = $query . ' WHERE ' . $input->colonne . '=' . $input->idtoup . ' AND customid=' . $input->bouticid;
  	
		//error_log($query);	  	
  	
		$arr=array();

		if ($conn->query($query) === FALSE)
		{
			throw new Error($conn->error);
		}
		
  }

	if (strcmp($input->action,"colorrow") == 0)
  {
  	$query = 'SELECT statutcmd.couleur FROM commande ';
  	$query = $query . 'INNER JOIN statutcmd ON commande.statid = statutcmd.statid '; 
  	$query = $query . 'WHERE commande.customid = ' . $input->bouticid;
  	$query = $query . ' ORDER BY commande.cmdid';
  	$query = $query . ' LIMIT ' . $input->limite . ' OFFSET '. $input->offset;
  	
  	//error_log($query);
  	
		$arr=array();	
		
		if ($result = $conn->query($query)) 
		{
			while ($row = $result->fetch_row()) 
		  {	
		  	$arm = array();
				array_push($arm, $row[0]);
		  	array_push($arr, $arm);
	    }						
		  $result->close();
	  }   

  }
	
	if (strcmp($input->action,"getcomdata") == 0)
  {
  	$query = 'SELECT commande.telephone, statutcmd.message, commande.numref, commande.nom, commande.prenom, commande.adresse1, commande.adresse2, commande.codepostal, commande.ville,
  						commande.vente, commande.paiement, commande.sstotal, commande.fraislivraison, commande.total, commande.commentaire, statutcmd.etat, customer.nom  FROM commande ';
  	$query = $query . 'INNER JOIN statutcmd ON commande.statid = statutcmd.statid ';
  	$query = $query . 'INNER JOIN customer ON commande.customid = customer.customid '; 
  	$query = $query . 'WHERE commande.cmdid = ' . $input->cmdid . ' AND commande.customid = ' . $input->bouticid . ' AND statutcmd.customid = ' . $input->bouticid . ' AND customer.customid = ' . $input->bouticid;
  	$query = $query . ' ORDER BY commande.cmdid';
  	
  	//error_log($query);
  	
		$arr=array();	
		
		if ($result = $conn->query($query)) 
		{
			if ($row = $result->fetch_row()) 
		  {	
	  		$content = $row[1];  	
				$content = str_replace("%boutic%", $row[16], $content);
				$content = str_replace("%telephone%", $row[0], $content);		
				$content = str_replace("%numref%", $row[2], $content);  
				$content = str_replace("%nom%", $row[3], $content);  
				$content = str_replace("%prenom%", $row[4], $content);
				$content = str_replace("%adresse1%", $row[5], $content);		
				$content = str_replace("%adresse2%", $row[6], $content);
				$content = str_replace("%codepostal%", $row[7], $content);
				$content = str_replace("%ville%", $row[8], $content);
				$content = str_replace("%vente%", $row[9], $content);
				$content = str_replace("%paiement%", $row[10], $content);
				$content = str_replace("%sstotal%", number_format($row[11], 2, ',', ' '), $content);
				$content = str_replace("%fraislivraison%", number_format($row[12], 2, ',', ' '), $content);
				$content = str_replace("%total%", number_format($row[13], 2, ',', ' '), $content);
				$content = str_replace("%commentaire%", $row[14], $content);
				$content = str_replace("%etat%", $row[15], $content);
		  	$message = $content;
		  	
		  	//error_log($message);

				array_push($arr, $row[0], $message);
	    }						
		  $result->close();
	  }   
  }

	if (strcmp($input->action,"getparam") == 0)
  {
		$value = stripcslashes(GetValeurParam($input->param, $conn, $input->bouticid, ""));
					
		//error_log($value);
		
		$arr=array();	
		
		array_push($arr, $value);

  }		

	if (strcmp($input->action,"setparam") == 0)
  {
		$error = SetValeurParam($input->param, addslashes($input->valeur), $conn, $input->bouticid, "");
			
		//error_log($error);
		
  	$arr=array();	
		
		array_push($arr, $error);
		
  }	

	if (strcmp($input->action,"getCustomProp") == 0)
  {
  	$query = "SELECT " . stripcslashes($input->prop) . " FROM customer WHERE customid = " . $input->bouticid . " LIMIT 1";			
		
		//error_log($query);
		
		$arr=array();	
		
		if ($result = $conn->query($query)) 
		{
			if ($row = $result->fetch_row()) 
		  {	
				array_push($arr, $row[0]);
	    }						
		  $result->close();
	  }   

  }		

  if (strcmp($input->action,"setCustomProp") == 0)
  {
    $arr="";
    $query = "SELECT COUNT(*) FROM customer WHERE " . $input->prop . " = '" . addslashes($input->valeur) . "' LIMIT 1";
    //error_log($query);
    if ($result = $conn->query($query)) 
    {
      if ($row = $result->fetch_row())
      {
        if (($row[0]>=1) && (strcmp($input->typ, "url")==0))
        {
          $arr = "KO";
        }
        else
        {
          $query = "UPDATE customer SET " . $input->prop . " = '" . addslashes($input->valeur) . "' WHERE customid = " . $input->bouticid;
          //error_log($query);

          $arr=array();

          if ($conn->query($query) === FALSE)
          {
            throw new Error($conn->error);
          }
          else
            $arr = "OK";
        }
      }
      $result->close();
    }
  }
	if (strcmp($input->action,"getClientProp") == 0)
  {
  	$query = "SELECT client." . stripcslashes($input->prop) . " FROM customer, client WHERE customer.customid = " . $input->bouticid . " AND customer.cltid = client.cltid LIMIT 1";			
		
		//error_log($query);
		
		$arr=array();	
		
		if ($result = $conn->query($query)) 
		{
			if ($row = $result->fetch_row()) 
		  {	
				array_push($arr, $row[0]);
	    }						
		  $result->close();
	  }   

  }		

  if (strcmp($input->action,"setClientProp") == 0)
  {
    $arr="";
    
   	$query = "SELECT client.cltid FROM customer, client WHERE customer.customid = " . $input->bouticid . " AND customer.cltid = client.cltid LIMIT 1";

    //error_log($query);

    if ($result = $conn->query($query)) 
    {
      if ($row = $result->fetch_row()) 
      {	
        $cltid = $row[0];
      }
      $result->close();
    }
    if (strcmp($input->typ, "pass") == 0)
    {
      if (strcmp($input->valeur,"") != 0) 	
        $query = "UPDATE client SET " . $input->prop . " = '" . password_hash($input->valeur, PASSWORD_DEFAULT) . "' WHERE cltid = " . $cltid;
    }
    else
      $query = "UPDATE client SET " . $input->prop . " = '" . addslashes($input->valeur) . "' WHERE cltid = " . $cltid;
    //error_log($query);

    if ($conn->query($query) === FALSE)
    {
      throw new Error($conn->error);
    }
  }


  $conn->close();

  $output = $arr;

  //error_log(json_encode($output));	

  echo json_encode($output);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}
?>










