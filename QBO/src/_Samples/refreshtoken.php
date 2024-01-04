<?php

include('../config.php');
use QuickBooksOnline\API\DataService\DataService;

session_start();

function refreshToken()
{

    // Create SDK instance
  $config2 = include('config2.php');
     /*
     * Retrieve the accessToken value from session variable
     */
    $accessToken = $_SESSION['sessionAccessToken'];
    $dataService = DataService::Configure(array(
        'auth_mode' => 'oauth2',
        'ClientID'=> $_SESSION['clientId'],
    'ClientSecret' => $_SESSION['clientS'],
        'RedirectURI' => $config2['oauth_redirect_uri'],
        'baseUrl' => $config2['baseUrl'],
        'refreshTokenKey' => $accessToken->getRefreshToken(),
        'QBORealmID' => "4620816365350555060",
    ));

    /*
     * Update the OAuth2Token of the dataService object
     */
    $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
    $refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();
    $dataService->updateOAuth2Token($refreshedAccessTokenObj);

    $_SESSION['sessionAccessToken'] = $refreshedAccessTokenObj;

    print_r($refreshedAccessTokenObj);
    return $refreshedAccessTokenObj;
}

$result = refreshToken();

?>