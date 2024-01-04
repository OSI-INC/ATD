<?php 
/*
connectcompany.php brings the user to the quickbooks log in page if the Client
ID and Client secret for the app have been set. Quickbooks sends back
the authorization code in the url string.

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

include('QBO/src/config.php');
use QuickBooksOnline\API\DataService\DataService;

// ATD config values will be loaded
$atdconfig = include('atdconfig.php');

// Create the service object with credentials
$dataService = DataService::Configure(array(
    'auth_mode' => 'oauth2',
   	'ClientID'=> $_SESSION['clientId'],
    'ClientSecret' => $_SESSION['clientS'],
    'RedirectURI' => $atdconfig['oauth_redirect_uri'],
    'scope' => $atdconfig['oauth_scope'],
    'baseUrl' => "production"
));

// Update log in object with the data service
$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
$authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();

// php header command brings the web server to the quickbooks log in page
header('location:'.$authUrl);
?>
