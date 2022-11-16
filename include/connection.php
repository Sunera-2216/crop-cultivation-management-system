<?php 

	$dbhost = 'localhost';
	$dbusername = 'root';
	$dbpassword = '';
	$dbname = 'project - i';

	$connection = mysqli_connect($dbhost, $dbusername, $dbpassword, $dbname);

	if (mysqli_connect_errno()) {
		die('Database connection failed ' . mysqli_connect_error());
	}

 ?>