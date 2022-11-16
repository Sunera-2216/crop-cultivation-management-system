<?php
	session_start();
	require_once('../include/connection.php');
	require_once('../include/function.php');

	//check for form submission
	if (isset($_POST['submit'])) {

		$errors = array();

		//check if the username/password has been entered
		if (!isset($_POST['farmer_nic']) || strlen(trim($_POST['farmer_nic'])) < 1 ) {
			$errors[] = 'Username is Missing / Invalid';
		}

		if (!isset($_POST['farmer_password']) || strlen(trim($_POST['farmer_password'])) < 1 ) {
			$errors[] = 'Password is Missing / Invalid';
		}
		//check if there are any errors
		if (empty($errors)) {
			//save username and password into variables
			$farmer_nic      = mysqli_real_escape_string($connection, $_POST['farmer_nic']);
			$farmer_password = mysqli_real_escape_string($connection, $_POST['farmer_password']);
			$hashed_password = sha1($farmer_password);

			//prepare database query
			$query = "SELECT * FROM farmer_details WHERE farmer_nic = '{$farmer_nic}' AND farmer_password = '{$hashed_password}' LIMIT 1";

			$result_set = mysqli_query($connection, $query);

			//query successful
			verify_query ($result_set);
			
			//valid user found	
			if ($result_set) {
				if (mysqli_num_rows($result_set) == 1) {
					$farmer = mysqli_fetch_assoc($result_set);
					$_SESSION['farmer_nic']  = $farmer['farmer_nic'];
					$_SESSION['first_name'] = $farmer['farmer_first_name'];

					header('location: ../user/home.php');

				} else{
					$errors[] = 'Invalid Username / Password';
				}
			} else {
				$errors[] = 'Database query failed';
			}
		}

	}

 ?>


<!DOCTYPE html>

<html lang="en">
<head>
	<title>Login</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
</head>

<?php 
	if (!empty($errors)) {
		echo '<div class="errmsg">';
		echo "<b>There were error(s) on your form.</b><br>";
		foreach ($errors as $error) {
			$error = ucfirst(str_replace("_", " ", $error));
			echo $error . '<br>';
		}
		echo '</div>';
	}
?>

<body>
	<div class="container-fluid px-1 px-md-5 px-lg-1 px-xl-5 py-5 mx-auto">
	    <div class="card card0 border-0">
	        <div class="row d-flex">
	            <div class="col-lg-6">
	                <div class="pb-5">
	                    <div class="row">
	                    	<img src="../assets/images/logo.png" class="logo">
	                    </div>
	                    <div class="row px-3 justify-content-center mt-4 mb-5 border-line">
	                    	<img src="img/background.png" class="image">
	                    </div>
	                </div>
	            </div>
	            <div class="col-lg-6">
	                <div class="card card1 border-0 px-4 py-5">
	                    <div class="row mb-4 px-3">
	                        <h2 class="mb-0 mr-4 mt-2">Log in</h2>
	                    </div>
	                <form action="index.php" method="POST">    
	                    <div class="row px-3">
	                    	<label class="mb-1">
	                            <h6 class="mb-0 text-sm">NIC (National Identity Card Number)</h6>
	                        </label>
	                        <input class="mb-4" type="text" name="farmer_nic" placeholder="Enter a valid NIC number">
	                    </div>
	                    <div class="row px-3">
	                    	<label class="mb-1">
	                            <h6 class="mb-0 text-sm">Password</h6> 
	                        </label>
	                        <input type="password" name="farmer_password" placeholder="Enter password">
	                    </div>
	                    <!-- <div class="row px-3 mb-4">
	                        <div class="custom-control custom-checkbox custom-control-inline">
	                        	<input type="checkbox" name="remember_me_checkbox" class="custom-control-input">
	                        	<label for="remember_me_checkbox" class="custom-control-label text-sm">Remember me</label>
	                        </div>
	                        <a href="#" class="ml-auto mb-0 text-sm" style="color: #333;">Forgot Password?</a>
	                    </div> -->
	                    <div class="row mb-3 px-3" style="margin-top: 20px;">
	                    	<button type="submit" name="submit" class="btn btn-blue text-center">Login</button>
	                    </div>
	                </form>    
	                    <div class="row mb-4 px-3">
	                    	<small class="font-weight-bold">Don't have an account ?
	                    		<a href="../register/" style="color: #7AAD37;">Register</a>
	                    	</small>
	                    </div>
	                </div>
	            </div>
	        </div>
	        <div class="bg-blue py-4">
	            <div class="row px-3">
	            	<small class="ml-4 ml-sm-5 mb-2">Copyright &copy; 2021. All rights reserved.</small>
	        	</div>
	    	</div>
	    </div>
	</div>
</body>
</html>

 <?php mysqli_close($connection);// if connection open, have to close it ?>

