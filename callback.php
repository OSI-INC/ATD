<?php

/*
Callback.php exchanges the authorization code and realmID for an
access token from quickbooks, and redirects the web server to main
index page.

Copyright (C) 2023,  Haley Hashemi, Open Source Instruments, Inc.
Copyright (C) 2016,  Intuit, Inc.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/
session_start();

// Including the autoloader config file in the directory level above
// that acts as an SQL database query function.
include('QBO/src/config.php');

// This code is using the Data Service object
use QuickBooksOnline\API\DataService\DataService;

function processCallbackCode()
{

	// Loading the contents of the qbi config file to create a data
	// service object
    $atdconfig = include('atdconfig.php');
    $dataService = DataService::Configure(array(
        'auth_mode' => 'oauth2',
        'ClientID'=> $_SESSION['clientId'],
    	'ClientSecret' => $_SESSION['clientS'],
        'RedirectURI' => $atdconfig['oauth_redirect_uri'],
        'scope' => $atdconfig['oauth_scope'],
        'baseUrl'=> $atdconfig['baseUrl']
    ));
	
    $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

    // Update the tokens 
    $accessToken = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($_GET['code'], $_GET['realmId']);
    $dataService->updateOAuth2Token($accessToken);
 	$_SESSION['sessionAccessToken'] = $accessToken;

	// Redirects us to the inital index.html page, make sure the IP address
	// below is the same ip address as the computer as the one hosting the
	// php server.
	$homeURI = $atdconfig['homeURI'];
	header('location:'.$homeURI);

}

processCallbackCode();

?>
