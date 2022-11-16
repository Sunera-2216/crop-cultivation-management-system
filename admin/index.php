<?php
	session_start();
	require_once('../include/connection.php');
	require_once('../include/function.php');

	//check for form submission
	if (isset($_POST['submit'])) {

		$errors = array();

		//check if the username/password has been entered
		if (!isset($_POST['das_username']) || strlen(trim($_POST['das_username'])) < 1 ) {
			$errors[] = 'Username is Missing / Invalid';
		}

		if (!isset($_POST['das_password']) || strlen(trim($_POST['das_password'])) < 1 ) {
			$errors[] = 'Password is Missing / Invalid';
		}
		//check if there are any errors
		if (empty($errors)) {
			//save username and password into variables
			$user_role = mysqli_real_escape_string($connection, $_POST['user_role']);
			$das_username = mysqli_real_escape_string($connection, $_POST['das_username']);
			$das_password = mysqli_real_escape_string($connection, $_POST['das_password']);
			$hashed_password = sha1($das_password);

			//prepare database query
			if ($user_role == "das") {
				$query = "SELECT * FROM das_details WHERE das_username = '{$das_username}' AND das_password = '{$hashed_password}' LIMIT 1";
				$result_set = mysqli_query($connection, $query);
			}else {
				$query = "SELECT * FROM asc_details WHERE asc_email = '{$das_username}' AND asc_password = '{$hashed_password}' LIMIT 1";
				$result_set = mysqli_query($connection, $query);
			}

			//query successful
			verify_query ($result_set);
			
			//valid user found	
			if ($result_set) {
				if (mysqli_num_rows($result_set) == 1) {
					$das = mysqli_fetch_assoc($result_set);
					$_SESSION['das_username'] = $das_username;

					if ($user_role == "das") {
						header('location: admin_dashboard.php');
					}else {
						$_SESSION['asc_id'] = $das['asc_id'];
						header('location: mini_admin_dashboard.php');
					}
					

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

<html>

<head>
	<meta charset="utf-8">
	<title>Login</title>

	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<!--
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	-->
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

	<div class="wrapper">
	  <div id="formContent">
	    <!-- Tabs Titles -->

	    <!-- Icon -->
	    <div style="margin: 25px;">
	      	<h3>ADMIN LOGIN</h3>
	    </div>

	    <!-- Login Form -->
	    <form action="index.php" method="POST">
	    	<select name="user_role" class="form-control" style="width: 380px; margin-left: 35px; margin-bottom: 10px;">
	    		<option value="das">Department of Agrarian Services</option>
	    		<option value="asc">Agrarian Services Center</option>
	    	</select>
	      	<input type="text" name="das_username" placeholder="Username">
	      	<input type="password" name="das_password" placeholder="Password">
	      	<input type="submit" name="submit"  value="Log In">
	    </form>

	    <!-- Remind Passowrd -->
	    <div id="formFooter">
	      	<a href="#" style="color: #7AAD37;">Forgot Password?</a>
	    </div>

	  </div>
	</div>

</body>
</html>