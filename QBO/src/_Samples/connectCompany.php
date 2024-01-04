<?php

include('../config.php');
use QuickBooksOnline\API\DataService\DataService;

$config2 = include('config2.php');

session_start();

$dataService = DataService::Configure(array(
    'auth_mode' => 'oauth2',
   	'ClientID'=> $_SESSION['clientId'],
    'ClientSecret' => $_SESSION['clientS'],
    'RedirectURI' => $config2['oauth_redirect_uri'],
    'scope' => $config2['oauth_scope'],
    'baseUrl' => "development"
));

$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
$authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();

// Redirect to the Authorization Page
header('location:'.$authUrl);
?>