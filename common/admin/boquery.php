<?php

session_start();

  if (empty($_SESSION['boutic']) == TRUE)
 	  exit();
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
	  $pk="";

		for($i=0; $i<count($input->tables[$numtable]->champs); $i++) 
		{
			$str = $input->tables[$numtable]->champs[$i]->nom;
			$typ = $input->tables[$numtable]->champs[$i]->typ;
			//$posp = strpos($str, ".");
			if (strcmp($typ,"fk")!=0)
			{
				$str = "T1." . $str;
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
			}
	  	$colonnes = $colonnes . $str ;
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
						$query = $query . ',`' . $input->liens[$j]->dsttbl . '` T' . strval($i + 2);
						$addwhere = $addwhere . ' AND T1.' . $input->liens[$j]->srcfld . '=T' . strval($i + 2) . '.' . $input->liens[$j]->dstfld;
					}
				}
			}
		} 
		  
	  $query = $query . ' WHERE T1.customid = ' . $customid;
	  $query = $query . $addwhere;
	  $query = $query . ' ORDER BY ' . $pk . ' LIMIT ' . $input->limite . ' OFFSET '. $input->offset;
	  
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
			$colonnes = $colonnes . $str ;
			if ($i != count($input->tables[$numtable]->champs)-1)
	  		$colonnes = $colonnes . ', ';
		}
  	
  	$query = 'SELECT ' . $colonnes . ' FROM `' . $input->tables[$numtable]->nom . '`'; 
  	$query = $query . ' WHERE ' . $clep . '=' . $input->idtoup . ' AND customid = ' . $customid;
  	
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
	  		$query = $query . $input->row[$i]->nom . ' = "' . password_hash($input->row[$i]->valeur, PASSWORD_DEFAULT) . '"'; 
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
  	
		$arr=array();
		// remove following comments to enable writing in db
		if ($conn->query($query) === FALSE)
		{
			throw new Error($conn->error);
		}
		
  }

  $conn->close();

	$output = $arr;
  echo json_encode($output);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}



?>










