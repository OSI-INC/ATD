<?php 
/*
	atd_local.php is a generic redirect to file "callback.php" on the client's
	local host. In the code below, we assume the local host is accepting
	connections on port 3000. The redirect extracts the original URL query
	string and attaches this to the local host address to create a complet
	localhost URL with query string that will be loaded by the client's browser,
	thus accessing a local file on the client and passing the query string into
	the file.

	Copyright (C) 2023, Haley Hashemi, Open Source Instruments, Inc. 
	Copyright (C) 2025, Kevan Hashemi, Open Source Instruments, Inc.

	This program is free software: you can redistribute it and/or modify it
	under the terms of the GNU General Public License as published by the Free
	Software Foundation, either version 3 of the License, or (at your option)
	any later version.

	This program is distributed in the hope that it will be useful, but WITHOUT
	ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
	FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
	more details.

	You should have received a copy of the GNU General Public License along with
	this program.  If not, see <https://www.gnu.org/licenses/>.
*/
 
session_start();

//Get the url String, this includes the access code
$a = $_SERVER['QUERY_STRING']; 
$b = 'http://localhost:3000/callback.php?';

// Set the redirect location to the index page with the access code
// included after the question mark
$c = $b.$a;

// Redirect web server to the callback uri
header('location:'.$c);

?>
