<?php
/*
index.php is the "home page" for ATD where you can submit Client ID and
secret, connect to Quickbooks, set report settings and download
reports. 

Copyright (C) 2023,  Haley Hashemi, Open Source Instruments, Inc.
Copyright (C) 2016,  Intuit, Inc.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see
<https://www.gnu.org/licenses/>.
*/

// Including the autoloader config file in the directory level above
// that acts as an SQL database query function. Load service objects.
include('QBO/src/config.php');
use QuickBooksOnline\API\DataService\DataService;

session_start();

//Check to see if the session access token has been set and print token info if set
if (isset($_SESSION['sessionAccessToken'])) {

    $accessToken = $_SESSION['sessionAccessToken'];
    $accessTokenArray = array('token_type' => 'bearer',
        'access_token' => $accessToken->getAccessToken(),
        'refresh_token' => $accessToken->getRefreshToken(),
        'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt(),
        'expires_in' => $accessToken->getAccessTokenExpiresAt()
    );
}

// If the client ID and Secret have been submitted, save the values in
// the session variables. If the accounting method and date ranges
// have been submitted, save these values in session variables. These
// will be passed throughout the session between files. If they are
// empty, generate a notification.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	if (isset($_POST["submit1"])) {
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
	} elseif (isset($_POST['submit2'])) {
		$method = $_POST['choice'];
		if (empty($method)) {
			print "No method selected";
		} else {
			$_SESSION['method'] = $method;
		}
		$start = $_POST['start'];
		if (empty($start)) {
			print "No Start Date selected";
		} else {
			$_SESSION['start'] = $start;
		}
		$end = $_POST['end'];
		echo($end);
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

</head>
<body>

	<!--Create a form with entry tables for the client ID and the
	client Secret. The form method is "post", and the action is
	to submit the passwords to the page itself -->



	<form action= "<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		Client ID <input type="text" style = "width: 375px;" name="clientId"/>
		 Client S <input type="password" style = "width: 375px;"name="clientS"/>
	    <input type="submit" id="submit1" name="submit1" required>
	</form>
	 <p><a href="connectcompany.php">Connect App to Quickbooks</a></p>

	<style>
   		h2 {
    		font-size: 16px;
   			}
	</style>
	<style>
   		h1 {
    		font-size: 20px;
   			}
	</style>


	<!--Create a form with entry tables for the report settings. The
	form method is "post", and the action is to submit the
	passwords to the page itself -->

	<h1>Report Settings</h1>

	<form action= "<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		<h2>Select Accounting Method</h2>
		<label>
		    <input type="radio" name="choice" value="Cash" checked> Cash
		    <input type="radio" name="choice" value="Accrual"> Accrual
		 </select>
		</label>
		<h2>Date Range</h2> 
		From: <input type="text"  name="start" placeholder="YYYY-MM-DD" required>
		To: <input type="text" name="end" placeholder="YYYY-MM-DD" required>
		<input type="submit" id ="submit2" name= "submit2" value= "Save Report Settings">
</form>

	<!--Each button below triggers the execution of a piece of PHP code.-->

	</form>
	<p><a href="getclasses.php"> Generate Report </a></p>
	<p><a href="downloadclass.php"> Download Class Reports </a></p>
	<p><a href="downloadall.php"> Download Full Report</a></p>
 

    <p><strong>Access Token:</strong></p>
    <code>
        <?php
        $displayString = isset($accessTokenArray) ? $accessTokenArray : "No Access Token Generated Yet";
        echo json_encode($displayString); 
        ?>
    </code>



</body>
</html>
