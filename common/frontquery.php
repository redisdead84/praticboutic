<?php

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

try 
{

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

  if (empty($_SESSION['customer']) == TRUE)
  {
    throw new Error('Pas de boutic associée');
  }
  
  if (strcmp($input->nom,"categories") == 0)
  {
    $query = 'SELECT catid, nom, visible FROM categorie WHERE customid = ' . $input->bouticid . ' OR catid = 0 ORDER BY catid';
    if ($result = $conn->query($query))
    {
      while ($row = $result->fetch_row())
      {
        array_push($arr, $row[0], $row[1], $row[2]);
      }
    }
  }
  
  if (strcmp($input->nom,"articles") == 0)
  {
    $query = 'SELECT artid, nom, prix, unite, description, image FROM article WHERE customid = ' . $input->bouticid . ' AND visible = 1 AND catid = ' . $input->catid . ' ORDER BY artid';
    if ($result = $conn->query($query))
    {
      while ($row = $result->fetch_row())
      {
        array_push($arr, $row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
      }
    }
  }
  
  if (strcmp($input->nom,"groupesoptions") == 0)
  {
    $query = 'SELECT groupeopt.grpoptid, groupeopt.nom, groupeopt.multiple FROM relgrpoptart, groupeopt WHERE relgrpoptart.customid = ' . $input->bouticid . ' AND groupeopt.customid = ' . $input->bouticid . ' AND relgrpoptart.visible = 1 AND groupeopt.visible = 1 AND artid = ' . $input->artid . ' AND relgrpoptart.grpoptid = groupeopt.grpoptid ORDER BY groupeopt.grpoptid';
    if ($result = $conn->query($query))
    {
      while ($row = $result->fetch_row())
      {
        array_push($arr, $row[0], $row[1], $row[2]);
      }
    }
  
  }
  
  if (strcmp($input->nom,"options") == 0)
  {
    $query = 'SELECT optid, nom, surcout FROM `option` WHERE customid = ' . $customid . ' AND visible = 1 AND grpoptid = ' . $input->grpoptid . ' ORDER BY optid';
    if ($result = $conn->query($query))
    {
      while ($row = $result->fetch_row())
      {
        array_push($arr, $row[0], $row[1], $row[2]);
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
  