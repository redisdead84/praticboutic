
<?php
  session_start();

  if (empty($_SESSION['bo_auth']) == TRUE)
  {
    header("LOCATION: index.php");
    exit();
  }

  if (strcmp($_SESSION['bo_auth'], 'oui') != 0)
  {
    header("LOCATION: index.php");
    exit();
  }

  include "../config/common_cfg.php";
  include "../param.php";

?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.01">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Annulation</title>

    <script src="https://js.stripe.com/v3/"></script>
    <script> var login = <?php echo '"' . $_SESSION['bo_email'] . '"'; ?>; </script>
    <script src="cancel.js" defer></script>
  </head>
  <body class="custombody">
    <main class="fcb">
      <h1>Annulation</h1>

      <input class="butc regbutton" type="button" onclick="javascript:revenir()" value="Revenir" />

      <button id="cancel-btn">Confirmer</button>

      <div id="messages"></div>
    </main>
    <script type="text/javascript" >
      function revenir()
      {
        window.location.href = './account.php';
      }
    </script>
  </body>
</html>