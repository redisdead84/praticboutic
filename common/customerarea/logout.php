<?php
  session_start();
  $_SESSION["active"] = 0;
  header("LOCATION: index.php");
  exit();
?>