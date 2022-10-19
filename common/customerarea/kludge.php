<?php

  session_id("customerarea");
  session_start();
  session_destroy();
  session_id("boutic");
  session_start();
  session_destroy();
  

?>