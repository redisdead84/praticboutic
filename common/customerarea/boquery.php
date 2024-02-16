<?php

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

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
     
header('Access-Control-Allow-Origin: *');
header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
header('Access-Control-Max-Age: 86400');
header('Content-Type: application/json');

try {

  $json_str = file_get_contents('php://input');
  $input = json_decode($json_str);
  $arr = "";
  
  if (isset($input->sessionid))
    session_id($input->sessionid);
  session_start();
	
	if (!isset($_SESSION))
  {
    throw new Error('Session expirée');
  }
	
	if (strcmp($input->action,"buildboutic") == 0)
  {
    if (empty($_SESSION['verify_email']) == TRUE)
    {
      throw new Error('Courriel non vérifié');
    }
  }
  else 
  {
    if (empty($_SESSION['bo_auth']) == TRUE)
    {
      throw new Error("Non authentifié");
    }
    if (strcmp($_SESSION['bo_auth'],'oui') != 0)
    {
      throw new Error("Non authentifié");
    }
  }
  
  $dbdfile = fopen('../dbd/model.json', 'r');
  $dbdjson = fread($dbdfile, filesize('../dbd/model.json'));
  fclose($dbdfile);
  
  $dbd = json_decode($dbdjson);
  
  $mdtables = $dbd->tables;
  $mdliens = $dbd->liens;
  
	if (strcmp($input->table,"")!=0)
	{
		for($i=0; $i<count($mdtables); $i++)
		{
			if (strcmp($mdtables[$i]->nom, $input->table)==0)
				$numtable = $i;		
		}
	}	  
	
	if (strcmp($input->action,"elemtable") == 0)
  {
    
	  $colonnes ="";
	  $liens=array();

		for($i=0; $i<count($mdtables[$numtable]->champs); $i++) 
		{
			$str = $mdtables[$numtable]->champs[$i]->nom;
			$typ = $mdtables[$numtable]->champs[$i]->typ;
			//$posp = strpos($str, ".");
			if (strcmp($typ,"pk")==0)
				$colonnes = "count(" . $str . ")"; 		
		}
		
	  $query = 'SELECT ' . $colonnes . ' FROM `' . $mdtables[$numtable]->nom . '` T1';
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

		for($i=0; $i<count($mdtables[$numtable]->champs); $i++) 
		{
			$str = $mdtables[$numtable]->champs[$i]->nom;
			$typ = $mdtables[$numtable]->champs[$i]->typ;
			$sens = $mdtables[$numtable]->champs[$i]->sens;
			$ordre = $mdtables[$numtable]->champs[$i]->ordre;
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
					$pk = $mdtables[$numtable]->champs[$i]->nom;				
				}	
			}
		  else 
			{
				for($j=0; $j<count($mdliens); $j++) 
				{
					if ((strcmp($mdliens[$j]->srctbl, $mdtables[$numtable]->nom)==0) && (strcmp($mdliens[$j]->srcfld, $mdtables[$numtable]->champs[$i]->nom)==0))
					{
						$nomlien = $mdliens[$j]->nom;
						for ($k=0; $k<count($mdtables); $k++)
						{
							if (strcmp($mdtables[$k]->nom, $mdliens[$j]->dsttbl)==0)
								$fld = $mdtables[$k]->cs;
						}
						$tblsrclien = $mdliens[$j]->srctbl;
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
	  	if ($i != count($mdtables[$numtable]->champs)-1)
	  		$colonnes = $colonnes . ', ';
		}
		
	  $query = 'SELECT ' . $colonnes . ' FROM `' . $mdtables[$numtable]->nom . '` T1'; 
	  $addwhere ="";
	  
	  for ($i=0; $i<count($liens); $i++)
		{
			for ($j=0; $j<count($mdliens); $j++)
			{
				if (strcmp($mdliens[$j]->nom, $liens[$i]->nom)==0)
				{
					if(strcmp($mdliens[$j]->srctbl, $liens[$i]->srctbl)==0)
					{
						//$query = $query . ',`' . $mdliens[$j]->dsttbl . '` T' . strval($i + 2);
						if (strcmp($mdliens[$j]->join, "ij") == 0)
							$addwhere = $addwhere . ' INNER JOIN `' . $mdliens[$j]->dsttbl . '` T' . strval($i + 2) . ' ON T1.' . $mdliens[$j]->srcfld . '=T' . strval($i + 2) . '.' . $mdliens[$j]->dstfld;
						else if (strcmp($mdliens[$j]->join, "rj") == 0)
							$addwhere = $addwhere . ' RIGHT JOIN `' . $mdliens[$j]->dsttbl . '` T' . strval($i + 2) . ' ON T1.' . $mdliens[$j]->srcfld . '=T' . strval($i + 2) . '.' . $mdliens[$j]->dstfld;
						else if (strcmp($mdliens[$j]->join, "lj") == 0)
							$addwhere = $addwhere . ' LEFT JOIN `' . $mdliens[$j]->dsttbl . '` T' . strval($i + 2) . ' ON T1.' . $mdliens[$j]->srcfld . '=T' . strval($i + 2) . '.' . $mdliens[$j]->dstfld;
						
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
				for($i=0; $i<count($mdtables[$numtable]->champs); $i++) 
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
    
 		for ($i=0;$i<count($mdtables[$numtable]->champs);$i++)
			if (strcmp($mdtables[$numtable]->champs[$i]->typ, "pk")== 0)
				$clep = $mdtables[$numtable]->champs[$i]->nom; 		

  	$query = 'SELECT ' . $clep . ', ' . $input->colonne . ' FROM `' . $mdtables[$numtable]->nom . '`'; 
  	$query = $query . ' WHERE customid = ' . $input->bouticid . ' OR ' . $clep . ' = 0';
  	
		if (strcmp($mdtables[$numtable]->nom, "statutcmd") == 0 ) 
	  	$query = $query . ' AND actif = 1';
  	//error_log($query);
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
  	$query = 'INSERT INTO `' . $mdtables[$numtable]->nom . '`(';
  	$query = $query . 'customid, ';
		for($i=0;$i<count($input->row);$i++) 
		{  	
			if (strcmp($input->row[$i]->type,"ref") == 0 || strcmp($input->row[$i]->type,"codepromo") == 0)
			{
				$colonnes = "count(*)"; 						
			  $subquery = 'SELECT ' . $colonnes . ' FROM `' . $mdtables[$numtable]->nom . '` T1';
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
			  $subquery = 'SELECT ' . $colonnes . ' FROM `' . $mdtables[$numtable]->nom . '` T1';
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

    //error_log($query);
		if ($conn->query($query) === FALSE)
		{
			throw new Error($conn->error);
		}
		
		array_push($arr, $conn->insert_id);
		
  }
  
  if (strcmp($input->action,"getvalues") == 0)
  {
    
 		for ($i=0;$i<count($mdtables[$numtable]->champs);$i++)
			if (strcmp($mdtables[$numtable]->champs[$i]->typ, "pk")== 0)
				$clep = $mdtables[$numtable]->champs[$i]->nom; 		

  	$colonnes ="";
	  $liens=array();
		for($i=0; $i<count($mdtables[$numtable]->champs); $i++) 
		{
			$str = $mdtables[$numtable]->champs[$i]->nom;
			$colonnes = $colonnes . '`' . $str . '`';
			if ($i != count($mdtables[$numtable]->champs)-1)
	  		$colonnes = $colonnes . ', ';
		}
  	
  	$query = 'SELECT ' . $colonnes . ' FROM `' . $mdtables[$numtable]->nom . '`'; 
  	$query = $query . ' WHERE ' . $clep . '=' . $input->idtoup . ' AND customid = ' . $input->bouticid;
  	
  	//error_log($query);
  	
		$arr=array();	
		
		if ($result = $conn->query($query)) 
		{
			if ($row = $result->fetch_row()) 
		  {	
		  	for($i=0; $i<count($mdtables[$numtable]->champs); $i++)
		  	{
					array_push($arr, html_entity_decode ($row[$i]));
				}
	    }						
		  $result->close();
	  }   

  }
  
  if (strcmp($input->action,"updaterow") == 0)
  {
  	$query = 'UPDATE `' . $mdtables[$numtable]->nom . '` SET ';
		for($i=0;$i<count($input->row);$i++) 
		{
			if (strcmp($input->row[$i]->type,"ref") == 0 || strcmp($input->row[$i]->type,"codepromo") == 0)
			{
				$colonnes = "count(*)"; 						
			  $subquery = 'SELECT ' . $colonnes . ' FROM `' . $mdtables[$numtable]->nom . '`';
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
			  $subquery = 'SELECT ' . $colonnes . ' FROM `' . $mdtables[$numtable]->nom . '`';
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
    $query = "SELECT COUNT(*) FROM customer WHERE " . $input->prop . " = '" . addslashes($input->valeur) . "' AND customid != '" . $input->bouticid . "' LIMIT 1";
    //error_log($query);
    if ($result = $conn->query($query)) 
    {
      if ($row = $result->fetch_row())
      {
        //error_log($row[0]);
        if (($row[0]>=1) && (strcmp($input->type, "url")==0))
        {
          $arr = "KO";
        }
        else
        {
          try
          {
            $query = "UPDATE customer SET " . $input->prop . " = '" . addslashes($input->valeur) . "' WHERE customid = " . $input->bouticid;
            //error_log($query);

            $arr=array();
            if ($conn->query($query) === TRUE)
            {
              $arr = "OK";
            }
          }
          catch(Exception $d)
          {
            $arr = "KO";
          }
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
    if (strcmp($input->prop, "pass") == 0)
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
  
  if (strcmp($input->action,"buildboutic") == 0)
  {
    
    $conn->autocommit(false);

    $arr ="";
    $raz = 0;

    $subquery = "SELECT count(*) FROM `client` WHERE email = '" . $_SESSION['verify_email'] . "'";
    //error_log($subquery);
    if ($result = $conn->query($subquery)) 
    {
      if ($row = $result->fetch_row()) 
      {
        if (intval($row[0])>0)
        {
          $raz = 1;
          throw new Error("Impossible d'avoir plusieurs fois le même courriel " . $_SESSION['verify_email']);
        }
      }
      $result->close();
    }

    $cpwd = password_hash($_SESSION['registration_pass'], PASSWORD_DEFAULT);
    $query = "INSERT INTO client(email, pass, qualite, nom, prenom, adr1, adr2, cp, ville, tel, stripe_customer_id, actif) VALUES ";
    $query = $query . "('" . $_SESSION['verify_email']  . "','" . $cpwd. "','" . $_SESSION['registration_qualite'] . "','" . addslashes($_SESSION['registration_nom']) . "','";
    $query = $query . addslashes($_SESSION['registration_prenom']) . "','" . addslashes($_SESSION['registration_adr1']) . "','" . addslashes($_SESSION['registration_adr2']) . "','" . addslashes($_SESSION['registration_cp']) . "','";
    $query = $query . addslashes($_SESSION['registration_ville']) . "','" . $_SESSION['registration_tel'] . "','" . $_SESSION['registration_stripe_customer_id'] . "','1')";
    
    //error_log($query);

    if ($conn->query($query) === FALSE)
    {
      throw new Error($conn->error);
    }
    
    $cltid = $conn->insert_id;
    
    if (empty($_SESSION['initboutic_aliasboutic'])==TRUE ) {
      throw new Error("Identifiant vide");
    }
    
    $notid = array('admin', 'common', 'upload', 'vendor');
    if(in_array($_SESSION['initboutic_aliasboutic'], $notid)) //Si l'extension n'est pas dans le tableau
    {
      throw new Error('Identifiant interdit');
    }
    
    $q = "INSERT INTO customer (cltid, customer, nom, logo, courriel) ";
    $q = $q . "VALUES ('" . $cltid . "', '" . $_SESSION['initboutic_aliasboutic'] . "', '" . addslashes($_SESSION['initboutic_nom']) . "', '";
    $q = $q . $_SESSION['initboutic_logo'] . "', '" . $_SESSION['initboutic_email'] . "')";
    //error_log($q);
    
    if ($conn->query($q) === FALSE) 
    {
      throw new Error("erreur lors de l insertion: " . $conn->connect_error);
    }
    
    $bouticid = $conn->insert_id;
    
    $query = "INSERT INTO abonnement(cltid, creationboutic, bouticid, stripe_subscription_id, actif) VALUES ";
    $query = $query . "('$cltid', '0', '$bouticid', '" . $_SESSION['creationabonnement_stripe_subscription_id'] . "', '1')";

    //error_log($query);

    if ($conn->query($query) === FALSE)
    {
      throw new Error($conn->error);
    }
    
    $aboid = $conn->insert_id;
    
    \Stripe\Stripe::setAppInfo(
      "pratic-boutic/registration  ",
      "0.0.2",
      "https://praticboutic.fr"
    );

    $stripe = new \Stripe\StripeClient([
    // TODO replace hardcoded apikey by env variable
      'api_key' => $_ENV['STRIPE_SECRET_KEY'],
      'stripe_version' => '2020-08-27',
    ]);

    $stripe->subscriptions->update(
      $_SESSION['creationabonnement_stripe_subscription_id'],
      ['metadata' => ['pbabonumref' => 'ABOPB' . str_pad($aboid, 10, "0", STR_PAD_LEFT)]]
    );
    
    $parametres = array (
      array("isHTML_mail", "1", "HTML activé pour l'envoi de mail"),
      array("Subject_mail","Commande PraticBoutic","Sujet du courriel pour l'envoi de mail"),
      array("VALIDATION_SMS", $_SESSION['confboutic_validsms'], "Commande validée par sms ?"),
      array("VerifCP", "0", "Activation de la verification des codes postaux"),
      array("Choix_Paiement", $_SESSION['confboutic_chxpaie'], "COMPTANT ou LIVRAISON ou TOUS"),
      array("MP_Comptant", "Par carte bancaire", "Texte du paiement comptant"),
      array("MP_Livraison", "Moyens conventionnels", "Texte du paiement à la livraison"),
      array("Choix_Method", $_SESSION['confboutic_chxmethode'], "TOUS ou EMPORTER ou LIVRER"),
      array("CM_Livrer", "Vente avec livraison", "Texte de la vente à la livraison"),
      array("CM_Emporter", "Vente avec passage à la caisse", "Texte de la vente à emporter"),
      array("MntCmdMini", $_SESSION['confboutic_mntmincmd'], "Montant commande minimal"),
      array("SIZE_IMG", "smallimg", "bigimg ou smallimg"),
      array("CMPT_CMD", "0", "Compteur des références des commandes"),
      array("MONEY_SYSTEM", "STRIPE MARKETPLACE", ""),
      array("STRIPE_ACCOUNT_ID", "", "ID Compte connecté Stripe"),
      array("NEW_ORDER", "0", "Nombre de nouvelle(s) commande(s)")
    );

    for($i=0; $i<count($parametres); $i++)
    {
      $q = ' INSERT INTO parametre (customid, nom, valeur, commentaire) ';
      $q = $q . 'VALUES ("' . $bouticid . '","' . $parametres[$i][0] . '","' . addslashes($parametres[$i][1]) . '","' . $parametres[$i][2] . '")';
      //error_log($q);
      if ($conn->query($q) === FALSE) 
      {
        throw new Error("Erreur lors de l'insertion d'un parametre : " . $conn->error);
      }
    }

    $statuts = array (
      array("Commande à faire", "#E2001A", "Bonjour, votre commande à été transmise. %boutic% vous remercie et vous tiendra informé de son avancé. ", 1, 1),
      array("En cours de préparation", "#EB690B","Votre commande est en cours de préparation. ", 0, 1),
      array("En cours de livraison", "#E2007A", "Votre commande est en cours de livraison, ", 0, 1),
      array("Commande à disposition", "#009EE0", "Votre commande est à disposition", 0, 1),
      array("Commande terminée", "#009036", "%boutic% vous remercie pour votre commande. À très bientôt. ", 0, 1),
      array("Commande anulée", "#1A171B", "Nous ne pouvons donner suite à votre commande. Pour plus d\'informations, merci de nous contacter. ", 0, 1),
    );

    for($i=0; $i<count($statuts); $i++)
    {
      $q = "INSERT INTO statutcmd (customid, etat, couleur, message, defaut, actif) ";
      $q = $q . "VALUES ('" . $bouticid . "','" . $statuts[$i][0] . "','" . $statuts[$i][1] . "','" . $statuts[$i][2] . "','" . $statuts[$i][3] . "','" . $statuts[$i][4] . "')";
      //error_log($q);
      if ($conn->query($q) === FALSE) 
      {
        throw new Error("Erreur lors de l'insertion d'un statut de commande : " . $conn->error);
      }
    }
    $conn->commit();
    $_SESSION['bo_stripe_customer_id'] = $_SESSION['registration_stripe_customer_id'];
    $_SESSION['bo_id'] = $bouticid;
    $_SESSION['bo_email'] = $_SESSION['verify_email'];
    $_SESSION['bo_auth'] = 'oui';
    $_SESSION['bo_init'] = 'oui';
    //error_log('ok');
  }

  if (strcmp($input->action,"radressboutic") == 0)
  {
    
    $arr ="";
    $raz = 0;

    $subquery = "SELECT count(*) FROM `client` WHERE email = '" . $_SESSION['bo_email'] . "'";
    //error_log($subquery);
    if ($result = $conn->query($subquery)) 
    {
      if ($row = $result->fetch_row()) 
      {
        if (intval($row[0])>1)
        {
          $raz = 1;
          throw new Error("Impossible d'avoir plusieurs fois le même courriel " . $_SESSION['bo_email']);
        }
      }
      $result->close();
    }
    
    $req = $conn->prepare('SELECT cltid FROM customer WHERE customid = ? ');
    $req->bind_param("s", $_SESSION['bo_id']);
    $req->execute();
    $req->bind_result($cltid);
    $resultat = $req->fetch();
    $req->close();
    $q2 = "UPDATE client SET email = '" . $input->email . "' WHERE cltid = $cltid";
    if ($r2 = $conn->query($q2)) 
    {
      if ($r2 === FALSE) 
      {
        throw new Error("Error: " . $q2 . "<br>" . $conn->error);
      }
      else
      {
        $_SESSION['bo_email'] = $input->email;
      }
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

