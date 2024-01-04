<?php
/*
refreshtoken.php generates a refresh token if the access token has expired.

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
use QuickBooksOnline\API\DataService\DataService;

function refreshToken()
{
	// Load QBI config values and access token
	$qbiconfig = include('qbiconfig.php');
	$accessToken = $_SESSION['sessionAccessToken'];

	// Create data service object with credentials
	$dataService = DataService::Configure(array(
		'auth_mode' => 'oauth2',
		'ClientID'=> $_SESSION['clientId'],
		'ClientSecret' => $_SESSION['clientS'],
		'RedirectURI' => $qbiconfig['oauth_redirect_uri'],
		'baseUrl' => $qbiconfig['baseUrl'],
		'refreshTokenKey' => $accessToken->getRefreshToken(),
));

   	// Update log in object with the data service
    $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
    $refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();
    $dataService->updateOAuth2Token($refreshedAccessTokenObj);

	// Refresh session accesst token
    $_SESSION['sessionAccessToken'] = $refreshedAccessTokenObj;
    print_r($refreshedAccessTokenObj);
    return $refreshedAccessTokenObj;
}

$result = refreshToken();

?>