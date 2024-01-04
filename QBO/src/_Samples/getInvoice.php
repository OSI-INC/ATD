<?php
require_once(__DIR__ . '/vendor/autoload.php');
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Facades\Invoice;

session_start();

function getInvoice()
{
      // Create SDK instance
   $config = include('config.php');
   $dataService = DataService::Configure(array(
     'auth_mode' => 'oauth2',
     'ClientID'=> $_SESSION['clientId'],
    'ClientSecret' => $_SESSION['clientS'],
     'RedirectURI' => $config['oauth_redirect_uri'],
     'scope' => $config['oauth_scope'],
     'baseUrl' => "production"
  ));

       /*
        * Retrieve the accessToken value from session variable
        */
       $accessToken = $_SESSION['sessionAccessToken'];

       /*
        * Update the OAuth2Token of the dataService object
        */
       $dataService->updateOAuth2Token($accessToken);

       $invoicesArray = $dataService->Query("SELECT * FROM Invoice");
		
 
       $error = $dataService->getLastError();
       if ($error) {
        var_dump($error);
     } else {
		
        if (is_array($invoicesArray) && sizeof($invoicesArray) > 0) {
				echo '<pre>';
				print_r($invoicesArray);
				 echo '</pre>';
				
		
      
      }      
   }
}

getInvoice();
?>