<?php

session_start();

require '../../vendor/autoload.php';

include "../config/common_cfg.php";
include "../param.php";

// Create connection
$conn = new mysqli($servername, $username, $password, $bdd);
// Check connection
if ($conn->connect_error) 
  die("Connection failed: " . $conn->connect_error); 

try {
	$output = "";
	
  $customer = $_POST['boutic'];
	$reqci = $conn->prepare('SELECT customid FROM customer WHERE customer = ?');
	$reqci->bind_param("s", $customer);
	$reqci->execute();
	$reqci->bind_result($customid);
	$resultatci = $reqci->fetch();
	$reqci->close();
	
	$dossier = "";
  $fichier = isset($_FILES['file']) ? basename($_FILES['file']['name']) : '';  
 	if(empty($fichier) == FALSE)
 	{  
    $dossier = '../../' . $customer . '/upload/';
    $taille_maxi = intval(GetValeurParam("Max_file_size", $conn, $customid, "5000000"));
    $taille = filesize($_FILES['file']['tmp_name']);
    $extensions = array('.png', '.gif', '.jpg', '.jpeg');
    $extension = strtolower(strrchr($_FILES['file']['name'], '.')); 
    //Début des vérifications de sécurité...
    if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
    {
      throw new Error('Vous devez uploader un fichier de type png, gif, jpg, jpeg...');
    }
    if($taille>$taille_maxi)
    {
      throw new Error('Le fichier est trop gros...');
    } 
    if(empty($error) == TRUE) //S'il n'y a pas d'erreur, on upload
    {
      //On formate le nom du fichier ici...
      $fichier = strtr($fichier, 
            'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 
            'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
      $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
      if(!(move_uploaded_file($_FILES['file']['tmp_name'], $dossier . $fichier))) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
      {
      	throw new Error('Echec de l\'upload !');
      }
      else {
      	$output = $fichier;	
    	}
  	}	
	}

  $conn->close();

	

  echo json_encode($output);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}
