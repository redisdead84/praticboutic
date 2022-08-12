<?php
  session_id("customerarea");
  session_start();

  header('Access-Control-Allow-Origin: *');
  header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
  header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
  header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
  header('Access-Control-Max-Age: 86400');

  require '../../vendor/autoload.php';
  include "../config/common_cfg.php";
  include "../param.php";

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
  ) {
    
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
          printf('The CreateAssessment() call failed because the token was invalid for the following reason: ');
          printf(InvalidReason::name($response->getTokenProperties()->getInvalidReason()));
          throw new exception(InvalidReason::name($response->getTokenProperties()->getInvalidReason()));
        } 
        else 
        {
          return $response->getRiskAnalysis()->getScore();
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
        printf('CreateAssessment() call failed with the following error: ');
        printf($e);
        throw new Error($e);
    }
  }


  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->load();
  try
  {
    $json_str = file_get_contents('php://input');
    $input = json_decode($json_str);
    $output ="";
    
    putenv($_ENV['GOOGLE_APPLICATION_CREDENTIALS']);
    // TODO(Developer): Replace the following before running the sample
    $output = create_assessment($_ENV['RECAPTCHA_KEY'], $input->token, $_ENV['GOOGLE_PROJECT']);
    echo json_encode($output);
  }
  catch (Error $e)
  {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
  }

?>
