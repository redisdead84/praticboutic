<?php
  session_id("customerarea");
  session_start();
  
  require '../../vendor/autoload.php';
  include "../config/common_cfg.php";
  include "../param.php";

  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();

?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Public+Sans' rel='stylesheet'>
    <link rel="stylesheet" href="css/back.css?v=1.12">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body ondragstart="return false;" ondrop="return false;">
    <div id="screen">
      <img id='bandeauh' src='img/bandeau_haut.png' onclick="quitterbuildboutic()"/>
      <div id="workspace" class="spacemodal">
        <img id='illus2' src='img/illustration_2.png' class="elemcb" />
        <div class="modal-content-mainmenu elemcb">
          <div class="modal-header-cb">
            <h5 class="modal-title-cb">INFORMATION</h5>
          </div>
          <div class="modal-body-cb">
            <?php
              
              $_SESSION['email'] = isset($_POST['email']) ? $_POST ['email'] : '';
            	$valrecap = isset($_POST['gRecaptchaResponse']) ? $_POST['gRecaptchaResponse'] : '';
            
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
                      header('LOCATION: chkmail.php');
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
            
              putenv($_ENV['GOOGLE_APPLICATION_CREDENTIALS']);
              // TODO(Developer): Replace the following before running the sample
              create_assessment($_ENV['RECAPTCHA_KEY'], $valrecap, $_ENV['GOOGLE_PROJECT']);
            ?>
          </div>
          <div class="modal-footer-cb">
            <a href="index.php"><button class="btn btn-primary btn-block" type="button" value="Valider">Retour</button></a>
   		    </div>
  		  </div>
      </div>
      <img id='bandeaub' src='img/bandeau_bas.png' onclick="quitterbuildboutic()"/>
    </div>
  </body>
  <script type="text/javascript" >
    function quitterbuildboutic()
    {
      if (confirm("Voulez-vous quitter ?") == true)
      {
        document.getElementById("loadid").style.display = "block";
        document.getElementById("mainmenu").style.display = "none";
        document.getElementById("illus2").style.display = "none";
        window.location.href ='exit.php';
      }
    }
  </script>
</html>