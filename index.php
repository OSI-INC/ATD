<?php

/*

index.php 

The ATD home page. We load this page from a browser, and through it we control
the ATD process.

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

// Including the autoloader config file in the directory level above
// that acts as an SQL database query function. Load service objects.
include('QBO/src/config.php');
use QuickBooksOnline\API\DataService\DataService;

// Start or resume a session with a browser. The browser will have
// passed a token with its request, and we will use this token to
// look up the session.
session_start();

// Configure sesssion variables if they are not set.
if (!isset($_SESSION['start'])) {$_SESSION['start'] = "YYYY-MM-DD";}
if (!isset($_SESSION['end'])) { $_SESSION['end'] = "YYYY-MM-DD";}
if (!isset($_SESSION['cash'])) { $_SESSION['cash'] = "true";}

// Check to see if the session access token has been set and print token info if
// set. 
if (isset($_SESSION['sessionAccessToken'])) {
    $accessToken = $_SESSION['sessionAccessToken'];
    $accessTokenArray = array('token_type' => 'bearer',
        'access_token' => $accessToken->getAccessToken(),
        'refresh_token' => $accessToken->getRefreshToken(),
        'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt(),
        'access expires_in' => $accessToken->getAccessTokenExpiresAt()
    );
}

// A post method is one that sends information into the session.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	// Handle posting of tokens.
	if (isset($_POST["submit_tokens"])) {
		$clientId = $_POST['clientId'];
			if (empty($clientId)) {
			print "clientId is empty";
		} else {
			$_SESSION['clientId'] = $clientId;
		}
		$clientS = $_POST['clientS'];
		if (empty($clientS)) {
			print "clientS is empty";
		} else {
			$_SESSION['clientS'] = $clientS;
		}
	  
	// Handle posting of dates.  
	} elseif (isset($_POST['submit_dates'])) {
		$cash = $_POST['cash'];
		if (empty($cash)) {
			print "No accounting method selected";
		} else {
			$_SESSION['cash'] = $cash;
		}
		$start = $_POST['start'];
		if (empty($start)) {
			print "No Start Date selected";
		} else {
			$_SESSION['start'] = $start;
		}
		$end = $_POST['end'];
		if (empty($end)) {
			print "No End Date selected";
		} else {
			$_SESSION['end'] = $end;
		}
	}
}

?>

<!DOCTYPE html>
<html>

<head>
	<title>Accounting Transaction Download (ATD)</title>
</head>

<body>

<center>
<h1>Accounting Transaction Download (ATD)</h1>
&copy; 2023-2024 Haley Hashemi, Open Source Instruments Inc.<br>
&copy; 2024 Kevan Hashemi, Open Source Instruments Inc.<br>
</center>

<!-- Create a form with entry tables for the client ID and the client Secret.
The form method is "post", and the action is to submit the passwords to the page
itself -->

<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
	<p>Identifier<input type="text" style="width:375px;" name="clientId"/></p>

	<p>Secret<input type="password" style="width:375px;" name="clientS"/></p>

	<p><input type="submit" name="submit_tokens" required/></p>
</form>

<p><a href="connectcompany.php">Connect to Company</a></p>

<!-- Create a form with entry tables for the report settings. The form
method is "post", and the action is to submit the passwords to the page
itself -->

<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
	<b>Method:</b> <input type="radio" name="cash" id="yescash" value="true" 
		<?php if ($_SESSION['cash'] == 'true') echo 'checked'; ?>
	/>Cash
	<input type="radio" name="cash" id="nocash" value="false" 
		<?php if ($_SESSION['cash'] != 'true') echo 'checked'; ?>
	/>Accrual
	<b>From:</b> <input type="text" name="start" value="<?php echo $_SESSION['start']; ?>" />
	<b>To:</b> <input type="text" name="end" value="<?php echo $_SESSION['end']; ?>" />
	<input type="submit" name="submit_dates"/>
</form>

<!-- Each button below triggers the execution of a piece of PHP code. -->

<p><a href="generatereport.php">Generate Report</a></p>
<p><a href="downloadclasses.php">Download Classes</a></p>
<p><a href="downloadledger.php">Download Ledger</a></p>

<p><b>Access Token:</b>
<?php
	if (isset($accessTokenArray)) {
		echo "Granted";
	} else {
		echo "None Yet";
	} 
?>
</p>

</body>
</html>
