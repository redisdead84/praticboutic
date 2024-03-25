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

  if (strcmp($input->action,"listcustomer") == 0)
  {
    $arr = array();
    
    $query = "SELECT customer.customid, customer.customer, customer.nom, customer.logo, client.stripe_customer_id FROM customer, client WHERE customer.actif = 1 AND customer.cltid = client.cltid";

    if ($result = $conn->query($query)) 
    {
      while ($row = $result->fetch_row())
      {
        $arm = array();
        array_push( $arm, $row[0]);
        array_push( $arm, $row[1]);
        array_push( $arm, $row[2]);
        array_push( $arm, $row[3]);
        array_push( $arm, $row[4]);
        array_push( $arr, $arm);
      } 

      $result->close();
    }
  }

  $conn->close();

  $output = $arr;
  
  echo json_encode($output);
} 
catch (Error $e) 
{
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}
?>
