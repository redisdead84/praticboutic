<?php
  session_start();

  include "config/custom_cfg.php";
  
  header('LOCATION: ../common/carte.php?method=' . $metdef . '&customer=' . $customer);

?>
