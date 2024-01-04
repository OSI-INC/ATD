<?php

include('../config.php');

use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Purchase;
use QuickBooksOnline\API\Data\IPPPurchase;
use QuickBooksOnline\API\QueryFilter\QueryMessage;
use QuickBooksOnline\API\ReportService\ReportService;
use QuickBooksOnline\API\ReportService\ReportName;

session_start();

function Report()
{
      // Create SDK instance
   $config2 = include('config2.php');
   $dataService = DataService::Configure(array(
     'auth_mode' => 'oauth2',
	'ClientID'=> $_SESSION['clientId'],
     'ClientSecret' => $_SESSION['clientS'],
     'RedirectURI' => $config2['oauth_redirect_uri'],
     'scope' => $config2['oauth_scope'],
     'baseUrl' => "production"
	));
	
	$accessToken = $_SESSION['sessionAccessToken'];
	$dataService->updateOAuth2Token($accessToken);
	$serviceContext = $dataService->getServiceContext();
	// Prep Data Services
	$reportService = new ReportService($serviceContext);
	if (!$reportService) {
	    exit("Problem while initializing ReportService.\n");
	}

	$reportService->setStartDate("2023-01-01");
	$reportService->setAccountingMethod("Accrual");
	$balanceSheet = $reportService->executeReport(ReportName::PROFITANDLOSSDETAIL);
	
	if (!$balanceSheet) {
	    exit("Entry did not work didnt work.\n");
	} else {
	    $reportName = strtolower($balanceSheet->Header->ReportName);
	    echo("ReportName: " . $reportName . "\n");
		echo '<pre>';
		print_r($balanceSheet);
		echo '</pre>';
	}


	
}

Report();
?>


