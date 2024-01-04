<?php
/*
getreport.php retreives the general ledger report from quickbooks for all
transactions and class-specific transactions.

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

// Including the autoloader config file in the directory level above
// that acts as an SQL database query function.
// Loading all QBO service objects needed to generate the report
include('QBO/src/config.php');
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Purchase;
use QuickBooksOnline\API\Data\IPPPurchase;
use QuickBooksOnline\API\QueryFilter\QueryMessage;
use QuickBooksOnline\API\ReportService\ReportService;
use QuickBooksOnline\API\ReportService\ReportName;

function Report()
{
	// Load contents of API config
	$qbiconfig = include('qbiconfig.php');
	$dataService = DataService::Configure(array(
		'auth_mode' => 'oauth2',
		'ClientID'=> $_SESSION['clientId'],
		'ClientSecret' => $_SESSION['clientS'],
		'RedirectURI' => $qbiconfig['oauth_redirect_uri'],
		'scope' => $qbiconfig['oauth_scope'],
		'baseUrl' => "production"
	));

	// To get a general report with class IDs, we must call the report
	// on each class individually. The general ledger does not return
	// the class associated with each transaction. This
	// class-specific report returns all transactions for each class
	// for an allotted time period. We call the IDlist array created
	// from the class query function which has been saved as a
	// session variable
	$idList = $_SESSION['Ids'];
	$start = $_SESSION['start'];
	$end = $_SESSION['end'];
	$method = $_SESSION['method'];

	// Update the session access token, and update the oath2 token if
	// necessary. 
	$accessToken = $_SESSION['sessionAccessToken'];
	$dataService->updateOAuth2Token($accessToken);
	$serviceContext = $dataService->getServiceContext();

	// Using the updates data service values, prep the report service.
	$reportService = new ReportService($serviceContext);
	if (!$reportService) {
	    exit("Problem while initializing ReportService.\n");
	}

	// Set the start date and accounting method for the report service
	// that has been initialized. The balance sheet is set equal to
	// the results of the report, which is denoted by the string
	// following the Report Name. The report service variable
	// contains all of the information and keys/tokens required for
	// executing report, and we provide all of the necessary report
	// service parameters to the execute report function. We call the
	// execute report function, which uses the aforementioned string
	// to determine what sort of report will be executed. For every
	// ID, call the general ledger report for that specific class.
	$merge = array();
	foreach ($idList as $id) {
		$reportService->setStartDate($start);
		$reportService->setEndDate($end);
		$reportService->setAccountingMethod($method);
		$reportService->setClassId($id);

		// Quickbooks has a list of viable reports stored as classes. Below,
		// use the report name to access the static properties of the
		// corresponding report class.
		$classreport = $reportService->executeReport(ReportName::GENERALLEDGER);
			
		// Print the results of the report to the screen / web server page.
		if (!$classreport) {
		    exit("Class Entry did not work didnt work.\n");
		} else {
			array_push($merge, $classreport);			
		}

	}

	$_SESSION['classreport'] = $merge;

	// Execute the general ledger report again, this time for no
	// specific class. This will retrieve all transactions for an
	// allotted time period, but it will not return any class IDs for
	// transactions. This will include transactions that, for
	// whatever reason, do not have a class assigned.
	$serviceContext = $dataService->getServiceContext();
	$reportService = new ReportService($serviceContext);
	if (!$reportService) {
	    exit("Problem while initializing ReportService.\n");
	}
	$reportService->setStartDate($start);
	$reportService->setEndDate($end);
	$reportService->setAccountingMethod($method);
	$GLreport = $reportService->executeReport(ReportName::GENERALLEDGER);
	
	// Print the results of the report to the screen.
	if (!$GLreport) {
	    exit("General Entry did not work didnt work.\n");
	} else {
	    $_SESSION['GLreport'] = $GLreport;
	}

	// Redirect the web server to the index page set in qbiconfig
	// using the header function
	$homeURI = $qbiconfig['homeURI'];
	header("location: " . $homeURI);
}
Report();

?>


