<?php
  session_id("customerarea");
  session_start();
  $_SESSION["active"] = 0;
  session_destroy();
 	header("LOCATION: index.php");
 	exit();
?>