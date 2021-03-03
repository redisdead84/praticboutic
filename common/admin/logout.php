<?php
   session_start();
   $boutic = $_SESSION['boutic'];
   session_destroy();
   header("location: ../../" . $boutic . "/admin/index.php");
?>