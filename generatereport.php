<?php 

/*

getclasses.php 

Retrieves a list of all classes for the specified company and then executes the
report code.

Copyright (C) 2023-2024, Haley Hashemi, Open Source Instruments, Inc.
Copyright (C) 2016, Intuit, Inc.

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.

*/

// Include the autoloader that acts as an SQL database query function. 
include('QBO/src/config.php'); 

// Load QBO service objects.
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService; 
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer; 
use QuickBooksOnline\API\QueryFilter\QueryMessage; 
use QuickBooksOnline\API\ReportService\ReportService; 
use QuickBooksOnline\API\ReportService\ReportName;

// Connect to our sesstion.
session_start();

// Load the contents of the ATD configuration file to create a service object.
$atdconfig = include('atdconfig.php');
$dataService = DataService::Configure(array(
	'auth_mode' => 'oauth2',
	'ClientID'=> $_SESSION['clientId'],
	'ClientSecret' => $_SESSION['clientS'],
	'RedirectURI' => $atdconfig['oauth_redirect_uri'],
	'scope' => $atdconfig['oauth_scope'],
	'baseUrl' => 'production'
));

// Retrieve or update the session access token, and update the oath2 token
// if necessary.
$accessToken = $_SESSION['sessionAccessToken'];
$dataService->updateOAuth2Token($accessToken);

// Create an array that will contain the results of the query, the query
// parameters are set by a string that is passed to the Query function.
// Update the data service with the query. In this case, we are querying all
// classes.
$class = $dataService->Query("SELECT * FROM Class"); 
$error = $dataService->getLastError(); 
if ($error) {var_dump($error);}      	

// Convert class object into string then back into an array that is
// delimited by new line characters
$classstring = serialize($class);

// Set out pattern for the regular expression search. The two patterns
// listed below look for the ID number, and the class name. We use
// parentheses to extract the matches for each pattern. Declare two arrays
// that wil store our IDs and names, and declare and array that will match
// each ID to its name.
$idpattern = '/Id.[^"]*?"(\d+)"/'; 
$namepattern = '/"Name".[^"]*?"([a-zA-Z0-9]+)"/'; 
$idmatchlist = array(); 
$namematchlist = array();
$matchlist = array();

// For every element in the class array, use regular expression matching to
// check for our pattern defined above. Pass the regexp command the pattern
// and the element to extract. If there is a match, the contents of the
// element to extract will be saved in the match variable. The first search
// extracts only the ID numbers, the second search extracts the class names.
preg_match_all($idpattern, $classstring, $idmatch);

// Save extracted values to a new list
$idmatchlist = $idmatch [1]; 

preg_match_all($namepattern, $classstring, $namematch);

// Save extracted values to a new list
$namematchlist = $namematch[1];

// For every ID extracted, select the corresponding name and combine the two
// into a new element into a new array.
for ($i = 0; $i < count($idmatchlist); $i++) {
	array_push($matchlist, $namematchlist[$i] . ' : ' . $idmatchlist[$i]);
}

// Print the ID-Name list to the screen. Save the ID array in a session
// variable, which will be called when we generate our report.	 
$_SESSION['ClassIds'] = $idmatchlist;
$_SESSION['ClassInfo'] = $matchlist;

// To get a general report with class IDs, we must call the report on each class
// individually. The general ledger does not return the class associated with
// each transaction. This class-specific report returns all transactions for
// each class for an allotted time period. We call the IDlist array created from
// the class query function which has been saved as a session variable
$idList = $_SESSION['ClassIds'];
$start = $_SESSION['start'];
$end = $_SESSION['end'];
$method = $_SESSION['method'];

// Update the session access token, and update the oath2 token if necessary. 
$accessToken = $_SESSION['sessionAccessToken'];
$dataService->updateOAuth2Token($accessToken);
$serviceContext = $dataService->getServiceContext();

// Using the updates data service values, prep the report service.
$reportService = new ReportService($serviceContext);
if (!$reportService) {
	exit("Problem while initializing ReportService.\n");
}

// Set the start date and accounting method for the report service that has been
// initialized. The balance sheet is set equal to the results of the report,
// which is denoted by the string following the Report Name. The report service
// variable contains all of the information and keys/tokens required for
// executing report, and we provide all of the necessary report service
// parameters to the execute report function. We call the execute report
// function, which uses the aforementioned string to determine what sort of
// report will be executed. For every ID, call the general ledger report for
// that specific class.
$_SESSION['ClassLedger'] = array();
foreach ($idList as $id) {
	$reportService->setStartDate($start);
	$reportService->setEndDate($end);
	$reportService->setAccountingMethod($method);
	$reportService->setClassId($id);

	// Quickbooks has a list of viable reports stored as classes. Below, use the
	// report name to access the static properties of the corresponding report
	// class.
	$classreport = $reportService->executeReport(ReportName::GENERALLEDGER);
		
	// Print the results of the report to the screen / web server page.
	if (!$classreport) {
		exit("Failed to download ledger for class \"$id\".\n");
	} else {
		array_push($_SESSION['ClassLedger'],$classreport);			
	}
}

// Execute the general ledger report, this time for no specific class.
// This will retrieve all transactions for an allotted time period, but it will
// not return any class IDs for transactions. This will include transactions
// that, for whatever reason, do not have a class assigned.
$serviceContext = $dataService->getServiceContext();
$reportService = new ReportService($serviceContext);
if (!$reportService) {
	exit("Failed to initialize reporting services.\n");
}
$reportService->setStartDate($start);
$reportService->setEndDate($end);
$reportService->setAccountingMethod($method);
$report = $reportService->executeReport(ReportName::GENERALLEDGER);

// Save the general ledger report in a session variable.
if (!$report) {
	exit("Failed to retrieve general ledger.\n");
} else {
	$_SESSION['GeneralLedger'] = $report;
}

// Redirect the web server to the ATD main page.
$homeURI = $atdconfig['homeURI'];
header("location: " . $homeURI);

?>
