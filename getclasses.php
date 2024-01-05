<?php 
/*
getclasses.php retrieves a list of all classes for the specified company
and then executes the report code.

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

include('QBO/src/config.php'); 
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService; 
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer; 
use QuickBooksOnline\API\QueryFilter\QueryMessage; 
use QuickBooksOnline\API\ReportService\ReportService; 
use QuickBooksOnline\API\ReportService\ReportName;

session_start();

// Including the autoloader config file in the directory level above
// that acts as an SQL database query function. Load service objects.



function getclass(){

   $atdconfig = include('atdconfig.php');

	//Loading the contents of the atdconfig file to create a service
	//object. We will set the uri and scope to have the same value
	//that they do in the config2 file. We also set the client ID and
	//secret to their session variable values. The baseUrl is
	//production if you are in the production setting.

	
   $dataService = DataService::Configure(array(
		'auth_mode' => 'oauth2',
		'ClientID'=> $_SESSION['clientId'],
		'ClientSecret' => $_SESSION['clientS'],
		'RedirectURI' => $atdconfig['oauth_redirect_uri'],
		'scope' => $atdconfig['oauth_scope'],
		'baseUrl' => 'production'
	));

    // 	Retrieve or update the session access token, and update the oath2
    // 	token if necessary.
	$accessToken = $_SESSION['sessionAccessToken'];
	$dataService->updateOAuth2Token($accessToken);
	
	

	// Create an array that will contain the results of the query, the
	// query parameters are set by a string that is passed to the
	// Query function. Update the data service with the query. In
	// this case, we are querying all classes.
  	$class = $dataService->Query("SELECT * FROM Class"); 
	$error = $dataService->getLastError(); 
	if ($error) { 
		var_dump($error);
    }      	
 
	// Convert class object into string then back into an array that
	// is delimited by new line characters
	$classstring = serialize($class);

	//Set out pattern for the regular expression search. The two
	//patterns listed below look for the ID number, and the class
	//name. We use parentheses to extract the matches for each
	//pattern. Declare two arrays that wil store our IDs and names,
	//and declare and array that will match each ID to its name.
	$idpattern = '/Id.[^"]*?"(\d+)"/'; 
	$namepattern = '/"Name".[^"]*?"([a-zA-Z0-9]+)"/'; 
	$idmatchlist = array(); 
	$namematchlist = array();
	$matchlist = array();
	
	// For every element in the class array, use regular expression
	// matching to check for our pattern defined above. Pass the
	// regexp command the pattern and the element to extract. If
	// there is a match, the contents of the element to extract will
	// be saved in the match variable. The first search extracts only
	// the ID numbers, the second search extracts the class names.
	preg_match_all($idpattern, $classstring, $idmatch);

	// Save extracted values to a new list
	$idmatchlist =$idmatch [1]; 

	preg_match_all($namepattern, $classstring, $namematch);
	
	// Save extracted values to a new list
	$namematchlist = $namematch[1];

	// For every ID extracted, select the corresponding name and
	// combine the two into a new element into a new array.
	for ($i = 0; $i < count($idmatchlist); $i++) {
		array_push($matchlist, $namematchlist[$i] . ' : ' . $idmatchlist[$i]);
	}

	// Print the ID-Name list to the screen. Save the ID array in a
	// session variable, which will be called when we generate our
	// report.	 
	$_SESSION['Ids'] = $idmatchlist;
	$_SESSION['ClassInfo'] = $matchlist;
	// Execute the report file
	include('getreport.php');
}
getclass();

?>
