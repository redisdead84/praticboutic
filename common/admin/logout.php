<?php
   session_start();
   $boutic = $_SESSION['boutic'];
   session_destroy();
 	 header("LOCATION: index.php");
 	 exit();
?>