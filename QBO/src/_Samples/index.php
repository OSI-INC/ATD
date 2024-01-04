
<?php
include('../config.php');
use QuickBooksOnline\API\DataService\DataService;

session_start();

// Configure Data Service

//set the access token using the auth object
if (isset($_SESSION['sessionAccessToken'])) {

    $accessToken = $_SESSION['sessionAccessToken'];
    $accessTokenArray = array('token_type' => 'bearer',
        'access_token' => $accessToken->getAccessToken(),
        'refresh_token' => $accessToken->getRefreshToken(),
        'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt(),
        'expires_in' => $accessToken->getAccessTokenExpiresAt()
    );
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // collect value of input field
  $clientId = $_POST['clientId'];
  if (empty($clientId)) {
    print "clientId is empty";
  } else {
   $_SESSION['clientId'] = $clientId;
  }
  $clientS = $_POST['clientS'];
  if (empty($clientS)) {
    print "cclientS is empty";
  } else {
   $_SESSION['clientS'] = $clientS;
  }
}
?>

<!DOCTYPE html>
<html>
<head>

</head>
<body>
	
	<form method="post" action="<?php($_SERVER['PHP_SELF']);?>">
	 	Client ID <input type="password" name="clientId"/>
	 	Client S <input type="password" name="clientS"/>
	 	<input type="submit" value="Submit"/>
	</form>
	
    <p><a href="connectCompany.php">OAuth 2.0 Login</a></p>
    <p><a href="createcustomer.php">Create Customer</a></p>
    <p><a href="AccountCreate.php">Create Account</a></p>
	<p><a href="AccountRead.php">Read Account</a></p>
	<p><a href="AccountUpdate.php">Update Account</a></p>
	<p><a href="getInvoice.php">Get Invoices</a></p>
	<p><a href="BillRead.php">Get Bills</a></p>
	<p><a href="PaymentRead.php">Get Payments</a></p>
	<p><a href="BillPaymentRead.php">Get Bill Payments</a></p>
	<p><a href="Report.php"> Report</a></p>

    <p><strong>Access Token:</strong></p>
    <code>
        <?php
        $displayString = isset($accessTokenArray) ? $accessTokenArray : "No Access Token Generated Yet";
        echo json_encode($displayString); 
        ?>
    </code>



</body>
</html>
