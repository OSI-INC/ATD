<?php

/*
atdconfig.php saves configuration settings in an array.

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

return array(
	//	Auth and token URLS are the URLs suggested by quickbooks
	//	whatever method you are using to connect and access
	//	tokens. This method currently uses oath2.
    'authorizationRequestUrl' => 'https://appcenter.intuit.com/connect/oauth2',
    'tokenEndPointUrl' => 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer',
	// Set the scope to accounting
    'oauth_scope' => 'com.intuit.quickbooks.accounting',
	// The redirect should match the redirect uri in your production
	// settings. Use a redirect uri that is a web server executable
	// php script. 
    'oauth_redirect_uri' => 'https://opensourceinstruments.com/HTML/Redirect/atd_local.php',
	// The baseURL is production or development depending on your app
	// settings.
	'baseUrl' => "production", 'homeURI' => 'http://localhost:3000' )
?>
