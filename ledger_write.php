<?php

/*

ledger_write.php 

Returns the general ledger and writes to a text file on the browser machine's
hard drive.

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
$filename = $_SESSION['start'].'_'.$_SESSION['end'].'_All.txt';

// Set the content type and disposition to indicate that the data being sent to
// the browser is a binary file which will be automatically downloaded. Set the
// file name. Output the contents of the report to the file to download.
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$filename);
print_r($_SESSION['GeneralLedger']);

?>
