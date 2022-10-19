<?php

  session_start();

  require '../vendor/autoload.php';
  include "config/common_cfg.php";
  include "param.php";

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();
  
  if (empty($_SESSION['customer']) != 0)
	{
    header('LOCATION: 404.html');
    exit();
	}
	
	$valrecap = isset($_POST['gRecaptchaResponse']) ? $_POST['gRecaptchaResponse'] : '';
	
  $customer = $_SESSION['customer'];
  $method = intval($_SESSION['method']);
  $table = intval($_SESSION['table']);
  
  if (empty($_SESSION[$customer . '_mail']) == TRUE)
  {
    header('LOCATION: index.php?customer=' . $customer . '');
    exit();
  }
  
  if (strcmp($_SESSION[$customer . '_mail'],'oui') == 0)
  {
    header('LOCATION: index.php?customer=' . $customer . '');
    exit();
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Prise de commande</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" media="screen" href="css/style.css?v=<?php echo $ver_com_css;?>" />
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/style.css?v=<?php echo $ver_com_css;?>">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body ondragstart="return false;" ondrop="return false;">
    <div id="header">
		  <a href="https://pratic-boutic.fr"><img id="mainlogo" src="img/logo-pratic-boutic.png"></a>
		</div>
    <div id='mainmenu' class="modal-content-mainmenu elemcb" style="display: block;">
		  <div class="modal-header-cb">
        <h5 class="modal-title-cb">INFORMATION</h5>
      </div>
      <div class="modal-body-cb">
<?php
  
  use Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient;
  use Google\Cloud\RecaptchaEnterprise\V1\Event;
  use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
  use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;
    
   /**
  * Create an assessment to analyze the risk of a UI action.
  * @param string $siteKey The key ID for the reCAPTCHA key (See https://cloud.google.com/recaptcha-enterprise/docs/create-key)
  * @param string $token The user's response token for which you want to receive a reCAPTCHA score. (See https://cloud.google.com/recaptcha-enterprise/docs/create-assessment#retrieve_token)
  * @param string $project Your Google Cloud project ID
  */
  function create_assessment(
     string $siteKey,
     string $token,
     string $project
  ): void {
    
    $client = new RecaptchaEnterpriseServiceClient();
    $projectName = $client->projectName($project);

    $event = (new Event())
        ->setSiteKey($siteKey)
        ->setToken($token);
 
    $assessment = (new Assessment())
        ->setEvent($event);
 
    try {
        $response = $client->createAssessment(
            $projectName,
            $assessment
        );

        // You can use the score only if the assessment is valid,
        // In case of failures like re-submitting the same token, getValid() will return false
        if ($response->getTokenProperties()->getValid() == false) {
            //printf('The CreateAssessment() call failed because the token was invalid for the following reason: ');
            //printf(InvalidReason::name($response->getTokenProperties()->getInvalidReason()));
            echo('Problème avec le détecteur de script automatisé. ');
            echo(InvalidReason::name($response->getTokenProperties()->getInvalidReason()));
        } 
        else 
        {
          header('LOCATION: getinfo.php');
          //printf('The score for the protection action is:');
          //printf($response->getRiskAnalysis()->getScore());

          // Optional: You can use the following methods to get more data about the token
          // Action name provided at token generation.
          // printf($response->getTokenProperties()->getAction() . PHP_EOL);
          // The timestamp corresponding to the generation of the token.
          // printf($response->getTokenProperties()->getCreateTime()->getSeconds() . PHP_EOL);
          // The hostname of the page on which the token was generated.
          // printf($response->getTokenProperties()->getHostname() . PHP_EOL);
        }
    } catch (exception $e) {
        //printf('CreateAssessment() call failed with the following error: ');
        //printf($e);
        echo('Problème avec le détecteur de script automatisé. ');
        echo($e);
    }
  }

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();
   
  putenv($_ENV['GOOGLE_APPLICATION_CREDENTIALS']);
  create_assessment($_ENV['RECAPTCHA_KEY'], $valrecap, $_ENV['GOOGLE_PROJECT']);

?>
      </div>
      <div class="modal-footer-cb">
        <a href="carte.php"><button class="soloindic" type="button" value="Valider">Retour</button></a>
      </div>
    </div>
  </body>
</html>