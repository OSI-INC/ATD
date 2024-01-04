
<?php 
/*
qbi_186.php is the URI redirect file used if you want to separate the server
from the client, hosted on the OSI page, which will redirect the
client server to the QBI server's callback procedure.

Copyright (C) 2023,  Haley Hashemi, Open Source Instruments, Inc.
Copyright(C) 2016,  Intuit, Inc.

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

session_start();

// Get the url String, this includes the access code
$a = $_SERVER['QUERY_STRING']; 
$b = 'http://192.168.1.186:3000/callback.php?';

// Set the redirect location to the index page with the access code
// included after the question mark
$c = $b.$a;

// Save the client server address and the QBI server address
$clientIP = $_SERVER['REMOTE_ADDR'];
$redirect = $_SERVER['PHP_SELF'];

// Save the unix time
$t = time();

// Create a new string for the data to be added
$data = $t . ' ' . $clientIP . ' ' . $redirect;
$fn = "log.txt";
$f = fopen($fn, "a");
fwrite($f, $data);
fclose($f);

// Redirect web server to the callback uri
header('location:'.$c);
?>
