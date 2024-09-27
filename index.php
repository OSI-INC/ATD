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

?>

<!DOCTYPE html>
<html>

<head>
	<title>ATD</title>
</head>

<body>

<center>
<h1>Accounting Transaction Download (ATD)</h1>
&copy; 2023-2024 Haley Hashemi, Open Source Instruments Inc.<br>
&copy; 2024 Kevan Hashemi, Open Source Instruments Inc.<br>
</center>

<?php

// A post method is one that sends information into the session.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	// Handle posting of tokens.
	if (isset($_POST['submit_tokens'])) {
		$clientId = $_POST['clientId'];
			if (empty($clientId)) {
			print "<b>ERROR:</b> clientId is empty.<br>";
		} else {
			$_SESSION['clientId'] = $clientId;
		}
		$clientS = $_POST['clientS'];
		if (empty($clientS)) {
			print "<b>ERROR:</b> clientS is empty.<br>";
		} else {
			$_SESSION['clientS'] = $clientS;
		}
	  
	// Handle posting of dates.  
	} elseif (isset($_POST['submit_dates'])) {
		$cash = $_POST['cash'];
		if (empty($cash)) {
			print "<b>ERROR:</b> No accounting method selected.<br>";
		} else {
			$_SESSION['cash'] = $cash;
		}
		$start = $_POST['start'];
		if (empty($start)) {
			print "<b>ERROR:</b> No Start Date selected.<br>";
		} else {
			$_SESSION['start'] = $start;
		}
		$end = $_POST['end'];
		if (empty($end)) {
			print "<b>ERROR:</b> No End Date selected.<br>";
		} else {
			$_SESSION['end'] = $end;
		}
	}
}

?>

<!-- Create a form with entry tables for the client ID and the client Secret.
The form method is "post", and the action is to submit the passwords to the page
itself -->

<br><br><br>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
	<table>
		<tr>
			<th>Identifier:</th>
			<td><input type="text" size="50" name="clientId"/></td>
		</tr>
		<tr>
			<th>Secret:</th>
			<td><input type="password" size="50" name="clientS"/></td>
		</tr>
		<tr>
			<th>Access:</th>
			<td><?php
			if (isset($accessTokenArray)) {
				echo "Granted";
			} else {
				echo "Not Yet Granted";
			} 
			?></td>
		</tr>
	</table>
	<input type="submit" 
	name="submit_tokens" 
	value="Submit Identifier and Secret" 
	required />
</form>

<form action="connect.php" method="get">
	<input type="submit" 
	name="connect" 
	value="Connect to Company Account" 
	required />
</form>


<!-- Create a form with entry tables for the report settings. The form
method is "post", and the action is to submit the passwords to the page
itself -->

<br>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
	<table>
	<tr>
		<th>Accounting:</th>
		<td>
			<input type="radio" 
			name="cash" 
			id="yescash" 
			value="true" 
			<?php if ($_SESSION['cash'] == 'true') echo 'checked'; ?> />Cash
			<input type="radio" name="cash" id="nocash" value="false" 
				<?php if ($_SESSION['cash'] != 'true') echo 'checked'; ?> />Accrual
		</td>
	</tr>
	<tr>
		<th>Start Date:</th>
		<td>
			<input type="text" name="start" value="<?php echo $_SESSION['start']; ?>" />
		</td>
	</tr>
		<th>End Date:</th>
		<td>
			<input type="text" name="end" value="<?php echo $_SESSION['end']; ?>" />
		</td>
	</tr>
	</table>
	<input type="submit" value="Submit Ledger Options" name="submit_dates" />
</form>

<!-- Each button below triggers the execution of a piece of PHP code. -->

<br>
<form action="ledger_read.php">
	<input type="submit" value="Read Ledger" name="ledger_read" />
</form>
<form action="ledger_write.php">
	<input type="submit" value="Write Ledger" name="ledger_write" />
</form>
<form action="class_list.php">
	<input type="submit" value="List Classes" name="class_list" />
</form>

</body>
</html>
