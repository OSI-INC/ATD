<?php

/*

class_list.php 

Print a list of classes to the browser.

Copyright (C) 2023-2024, Haley Hashemi, Open Source Instruments, Inc.
Copyright (C) 2024, Kevan Hashemi, Open Source Instruments, Inc.
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

// Connect to our session and compose a file name for the class report.
session_start();

?>

<!DOCTYPE html>
<html>

<head>
	<title>ATD</title>
</head>

<body>

<center>
<h1>Class List</h1>
</center>

<center><table border cellspacing=2>
<tr><th>Name</th><th>Identifier</th></tr>
<?php
	if (isset($_SESSION['ClassInfo'])) {
		foreach ($_SESSION['ClassInfo'] as $cInfo) {
			$temp = explode(':',$cInfo);
			echo '<tr><td>' . $temp[0] . '</td><td>' . $temp[1] . '</td></tr>';
		}
	} else {
		echo 'This session has not yet received a class list.';
	} 
?>
</table></center>

</body>
</html>
