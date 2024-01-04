
<?php

include('../config.php');
use QuickBooksOnline\API\DataService\DataService;



session_start();

function processCallbackCode()
{

    // Create SDK instance
   $config2 = include('config2.php');
    $dataService = DataService::Configure(array(
        'auth_mode' => 'oauth2',
        'ClientID'=> $_SESSION['clientId'],
    	'ClientSecret' => $_SESSION['clientS'],
        'RedirectURI' => $config2['oauth_redirect_uri'],
        'scope' => $config2['oauth_scope'],
        'baseUrl'=> $config2['baseUrl']
    ));
	
    $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

    /*
     * Update the OAuth2Token
    */
    $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($_GET['code'], $_GET['realmId']);
    $dataService->updateOAuth2Token($accessToken);


    /*
     * Setting the accessToken for session variable
     */
    $_SESSION['sessionAccessToken'] = $accessToken;
}


processCallbackCode();
header('location: http://localhost:3000');

?>
