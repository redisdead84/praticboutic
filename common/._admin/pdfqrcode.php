<?php

require '../../vendor/autoload.php';

require_once('qrcode/qrcode.class.php');

use Fpdf\Fpdf;

session_start();

if (empty($_SERVER['HTTPS']))
	$protocol = "http://";
else 
	$protocol = "https://";
	
$server = $_SERVER['SERVER_NAME'];
$boutic = $_SESSION['boutic'];
$methv = intval($_POST['methv']);
$nbtable = intval($_POST['nbtable']);
$nbex = intval($_POST['nbex']);

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
			$pdf->SetXY(10 + 50*$i, 10 + 50*$j);
			if ($methv == 2)
			{
				$pdf->Cell( 10, 10, $boutic . " table " . strval($notable));
				$qrcode = new QRcode($protocol . $server . '/common/carte.php?method=2&table=' . strval($num) . '&customer=' . $boutic, 'H'); // error level : L, M, Q, H
			}
			if ($methv == 3)
			{
				$pdf->Cell( 10, 10, $boutic . " Qlick'n'Collect ");
				$qrcode = new QRcode($protocol . $server . '/common/carte.php?method=3&table=0&customer=' . $boutic, 'H'); // error level : L, M, Q, H
			}
			
			
		  $qrcode->displayFPDF($pdf, 10 + 50*$i, 20 + 50*$j, 40);
		  if ($num >= $nbex * $nbtable)
		  	break;
		}
		if ($num >= $nbex * $nbtable)
			break;
	}
}

$pdf->Output();
?>
