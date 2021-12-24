<?php
  session_id("customerarea");
  session_start();
  session_destroy();
 	header("LOCATION: https://pratic-boutic.fr");
 	exit();
?>