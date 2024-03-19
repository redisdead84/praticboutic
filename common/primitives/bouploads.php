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
	$countfiles = count($_FILES['file']['name']);
	$arr = array();
	for($i=0;$i<$countfiles;$i++)
	{
	  //error_log($i);
    $fichier = isset($_FILES['file']) ? basename($_FILES['file']['name'][$i]) : '';
   	if(empty($fichier) == FALSE)
   	{  
      $dossier = '../../upload/';
      $taille_maxi = intval($maxfilesize);
      $mimetype = mime_content_type($_FILES['file']['tmp_name'][$i]);
      $taille = filesize($_FILES['file']['tmp_name'][$i]);
      $extensions = array('.png', '.gif', '.jpg', '.jpeg');
      $extension = strtolower(strrchr($_FILES['file']['name'][$i], '.'));
      $mimes = array('image/gif','image/png','image/jpeg'); 
      //Début des vérifications de sécurité...
      if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
      {
        throw new Error('Vous devez uploader des fichiers de type png, gif, jpg, jpeg...');
      }
      if(!in_array($mimetype, $mimes))
      {
      	throw new Error('Un des type mime d\'un fichier est non reconnu');
      }
      if($taille>$taille_maxi)
      {
        throw new Error('Un des fichiers est trop gros...');
      }
      $fichierext = uniqid('', true) . $extension;
      if(!(move_uploaded_file($_FILES['file']['tmp_name'][$i], $dossier . $fichierext))) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
      {
      	throw new Error('Echec d\'un upload de fichier !');
      }
      else 
      {
        array_push($arr, $fichierext);
    	}	
  	}
  }

  $conn->close();
  $output = $arr;
  echo json_encode($output);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}
