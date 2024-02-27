<?php

require '../../vendor/autoload.php';

require_once('../customerarea/qrcode/qrcode.class.php');

include "../config/common_cfg.php";
include "../param.php";

use Fpdf\Fpdf;

$json_str = file_get_contents('php://input');
$input = json_decode($json_str);

if (isset($_GET['sessionid']))
  session_id($_GET['sessionid']);
session_start();

if (!isset($_SESSION))
{
  exit();
}

if (empty($_SESSION['bo_auth']) == TRUE)
{
  exit();
}

if (strcmp($_SESSION['bo_auth'],'oui') != 0)
{
  exit();
}

if (empty($_SERVER['HTTPS']))
	$protocol = "http://";
else 
	$protocol = "https://";
	
$server = $_SERVER['SERVER_NAME'];

$conn = new mysqli($servername, $username, $password, $bdd);
if ($conn->connect_error) 
 	  die("Connection failed: " . $conn->connect_error);

$bouticid = intval($_GET['bouticid']); 	
$methv = intval($_GET['methv']);
$nbtable = intval($_GET['nbtable']);
$nbex = intval($_GET['nbex']);

$reqci = $conn->prepare('SELECT customer FROM customer WHERE customid = ?');
$reqci->bind_param("s", $bouticid);
$reqci->execute();
$reqci->bind_result($boutic);
$resultatci = $reqci->fetch();
$reqci->close();
 	


if ($methv == 3)
	$nbtable = 1;

$pdf = new FPDF();

$pdf->SetFont('Arial','B',10);

$num = 0;
$j = 0;
$notable = 0;
while ($num< $nbex * $nbtable )
{
	$pdf->AddPage();
	for($j=0; $j<5; $j++)
	{
		for($i=0; $i<4; $i++)
		{
		  $num++;
		  $notable++;
			
			if ($notable > $nbtable)
				$notable = 1;
			$pdf->SetXY(10 + 50*$i, 10 + 55*$j);
			$pdf->Cell( 10, 10, $boutic . "\n");
			$pdf->SetXY(10 + 50*$i, 10 + 55*$j);
			if ($methv == 2)
			{
			  $pdf->Cell( 10, 20, "Table " . strval($notable));
				$qrcode = new QRcode($rooturlfront . $boutic . '/2/' . strval($num), 'H'); // error level : L, M, Q, H
			}
			if ($methv == 3)
			{
				$pdf->Cell( 10, 20, "Qlick n Collect");
				$qrcode = new QRcode($rooturlfront . $boutic, 'H'); // error level : L, M, Q, H
			}
			
			
		  $qrcode->displayFPDF($pdf, 10 + 50*$i, 25 + 55*$j, 40);
		  if ($num >= $nbex * $nbtable)
		  	break;
		}
		if ($num >= $nbex * $nbtable)
			break;
	}
}

$pdf->Output();
?>

