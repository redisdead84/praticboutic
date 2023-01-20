<?php

session_start();

header('Access-Control-Allow-Origin: *');
header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
header('Access-Control-Max-Age: 86400');

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
	
	$dossier = "";
  $fichier = isset($_FILES['file']) ? basename($_FILES['file']['name']) : '';
 	if(empty($fichier) == FALSE)
 	{  
    $dossier = '../../upload/';
    $taille_maxi = intval($maxfilesize);
    $mimetype = mime_content_type($_FILES['file']['tmp_name']);
    $taille = filesize($_FILES['file']['tmp_name']);
    $extensions = array('.png', '.gif', '.jpg', '.jpeg');
    $extension = strtolower(strrchr($_FILES['file']['name'], '.'));
    $mimes = array('image/gif','image/png','image/jpeg'); 
    //Début des vérifications de sécurité...
    if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
    {
      throw new Error('Vous devez uploader un fichier de type png, gif, jpg, jpeg...');
    }
    if(!in_array($mimetype, $mimes))
    {
    	throw new Error('type mime non reconnu');
    }
    if($taille>$taille_maxi)
    {
      throw new Error('Le fichier est trop gros...');
    } 
    if(empty($error) == TRUE) //S'il n'y a pas d'erreur, on upload
    {

      $fichier = uniqid('', true) . $extension;

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
