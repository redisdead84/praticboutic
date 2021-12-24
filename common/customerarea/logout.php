<?php
  session_id("customerarea");
  session_start();
  session_destroy();
 	header("LOCATION: index.php");
 	exit();
?>