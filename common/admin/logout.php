<?php
   session_start();
   $customer = $_GET['customer'];
   session_destroy();
   header("location: ../../" . $customer . "/admin/index.php");
?>