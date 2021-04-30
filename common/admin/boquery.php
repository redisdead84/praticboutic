<?php

session_start();

  if (empty($_SESSION['boutic']) == TRUE)
 	  header("LOCATION: index.php");
  else	
	  $boutic = $_SESSION['boutic'];
	
  if (empty($_SESSION[$boutic . '_auth']) == TRUE)
  {
 	  header("LOCATION: index.php");
 	  exit();
  }	
  
  if (strcmp($_SESSION[$boutic . '_auth'],'oui') != 0)
  {
 	  header("LOCATION: index.php");
 	  exit();
  }
     
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

	$reqci = $conn->prepare('SELECT customid FROM customer WHERE customer = ?');
	$reqci->bind_param("s", $input->customer);
	$reqci->execute();
	$reqci->bind_result($customid);
	$resultatci = $reqci->fetch();
	$reqci->close();

	//error_log($input->action);
	
	for($i=0; $i<count($input->tables); $i++)
	{
		if (strcmp($input->tables[$i]->nom, $input->table)==0)
			$numtable = $i;		
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
	  $query = $query . ' WHERE T1.customid = ' . $customid; 
	  if (strcmp($input->selcol, "")!=0)
			$query = $query . ' AND T1.' . $input->selcol . ' = ' . $input->selid;
			
		for($i=0; $i<count($input->filtres); $i++)
		{
			if (strcmp($input->filtres[$i]->table, $input->tables[$numtable]->nom)==0)
			{
				$fchamp =	$input->filtres[$i]->champ;
				$fop	= $input->filtres[$i]->operateur;
				$fval = $input->filtres[$i]->valeur;
				$query = $query . ' AND T1.' . $fchamp . ' ' . $fop . ' ' . '"' . $fval . '"';
			}
		}
		
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
	    $query = $query . ' WHERE T1.customid = ' . $customid;
	  else
	    $query = $query . ' AND T1.customid = ' . $customid;
	    
	  if (strcmp($input->selcol, "")!=0)
	  	$query = $query . ' AND T1.' . $input->selcol . ' = ' . $input->selid;
	  	
		/*for($i=0; $i<count($input->filtres); $i++)
		{
			if (strcmp($input->filtres[$i]->table, $input->tables[$numtable]->nom)==0)
			{
				$fchamp =	$input->filtres[$i]->champ;
				$fop	= $input->filtres[$i]->operateur;
				$fval = $input->filtres[$i]->valeur;
				$query = $query . ' AND T1.' . $fchamp . ' ' . $fop . ' ' . '"' . $fval . '"';
			}
		}*/	  	
	  	
	  //$query = $query . $addwhere;
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
  	$query = $query . ' WHERE customid = ' . $customid;
  	
		$arr=array();	
		
		if ($result = $conn->query($query)) 
		{
			while ($row = $result->fetch_row()) 
		  {	
		  	$arm = array();
				array_push($arm, $row[0]);
		  	array_push($arm, $row[1]);
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
	  	$query = $query . $input->row[$i]->nom;
	  	if ($i != count($input->row)-1)
	  		$query = $query . ', ';
  	}
  	$query = $query . ') VALUES (';
  	$query = $query . '"' . $customid . '", '; 
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
		// remove following comments to enable writing in db
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
  	$query = $query . ' WHERE ' . $clep . '=' . $input->idtoup . ' AND customid = ' . $customid;
  	
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
			$jump = 0;
	  	if (strcmp($input->row[$i]->type, "pass") == 0)
	  		if (strcmp($input->row[$i]->valeur,"") != 0) 	
	  			$query = $query . $input->row[$i]->nom . ' = "' . password_hash($input->row[$i]->valeur, PASSWORD_DEFAULT) . '"';
	  		else 
					$jump = 1;
 
			else if (strcmp($input->row[$i]->type, "image") == 0)
			{
				if (strcmp($input->row[$i]->valeur,"") != 0)
					$query = $query . $input->row[$i]->nom . ' = "' . $input->row[$i]->valeur . '"';
				else 
					$jump = 1;
			}
			else 
				$query = $query . $input->row[$i]->nom . ' = "' . $input->row[$i]->valeur . '"';
				
	  	if (($i != count($input->row)-1) && ($jump == 0))
		  		$query = $query . ', ';
		  		
  	}
  	
  	$query = $query . ' WHERE ' . $input->colonne . '=' . $input->idtoup . ' AND customid=' . $customid;
  	
		//error_log($query);	  	
  	
		$arr=array();
		// remove following comments to enable writing in db
		if ($conn->query($query) === FALSE)
		{
			throw new Error($conn->error);
		}
		
  }

  if (strcmp($input->action,"getcs") == 0)
  {
 		for ($i=0;$i<count($input->tables[$numtable]->champs);$i++)
			if (strcmp($input->tables[$numtable]->champs[$i]->typ, "pk")== 0)
				$clep = $input->tables[$numtable]->champs[$i]->nom; 		

  	$cs = $input->tables[$numtable]->cs;

	  $liens=array();
  	
  	$query = 'SELECT ' . $cs . ' FROM `' . $input->tables[$numtable]->nom . '`'; 
  	$query = $query . ' WHERE ' . $clep . '=' . $input->idtoup . ' AND customid = ' . $customid;
  	
  	//error_log($query);
  	
		$arr=array();	
		
		if ($result = $conn->query($query)) 
		{
			if ($row = $result->fetch_row()) 
		  {	
		  	$arr = $row[0];
	    }						
		  $result->close();
	  }   

  }

	if (strcmp($input->action,"colorrow") == 0)
  {
  	$query = 'SELECT statutcmd.couleur FROM commande ';
  	$query = $query . 'INNER JOIN statutcmd ON commande.statid = statutcmd.statid '; 
  	$query = $query . 'WHERE commande.customid = ' . $customid;
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
  	$query = 'SELECT commande.telephone, statutcmd.message FROM commande ';
  	$query = $query . 'INNER JOIN statutcmd ON commande.statid = statutcmd.statid '; 
  	$query = $query . 'WHERE commande.cmdid = ' . $input->cmdid . ' AND commande.customid = ' . $customid;
  	$query = $query . ' ORDER BY commande.cmdid';
  	
  	//error_log($query);
  	
		$arr=array();	
		
		if ($result = $conn->query($query)) 
		{
			if ($row = $result->fetch_row()) 
		  {	
				array_push($arr, $row[0], $row[1]);
	    }						
		  $result->close();
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










