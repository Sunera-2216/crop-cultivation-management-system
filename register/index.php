<?php
	session_start();
	require_once('../include/connection.php');
	require_once('../include/function.php');
	
	$errors  = array();

	// Get Province list from database.
	$query = "SELECT * FROM province_details";
	$result_set = mysqli_query($connection, $query);

	$province_list = '';

	while ($result = mysqli_fetch_assoc($result_set)) {
		$province_list .= "<option value=\"{$result['province_code']}\">{$result['province_name']}</option>";
	}

	// Get district list according to selected province.
	if (isset($_GET['province_code'])) {
		$province_code = mysqli_real_escape_string($connection, $_GET['province_code']);

		$query = "SELECT * FROM district_details WHERE district_code IN (SELECT district_code FROM district_province_details WHERE province_code = '{$province_code}') ORDER BY district_name";

		$result_set = mysqli_query($connection, $query);

		$district_list = '<option>Select Your District</option>';

		while ($result = mysqli_fetch_assoc($result_set)) {
			$district_list .= "<option value=\"{$result['district_code']}\">{$result['district_name']}</option>";
		}

		echo $district_list;
	}

	// Get asc list according to selected district.
	if (isset($_GET['district_code'])) {
		$district_code = mysqli_real_escape_string($connection, $_GET['district_code']);

		$query = "SELECT * FROM asc_details WHERE asc_id IN (SELECT asc_id FROM asc_district_details WHERE district_code = '{$district_code}') ORDER BY asc_name";

		$result_set = mysqli_query($connection, $query);

		$asc_list = '';

		while ($result = mysqli_fetch_assoc($result_set)) {
			$asc_list .= "<option value=\"{$result['asc_id']}\">{$result['asc_name']}</option>";
		}

		echo $asc_list;
	}

	// Add new user to database.
	if (isset($_POST['submit'])) {
		$farmer_nic        = $_POST['farmer_nic'];
		$farmer_first_name = $_POST['farmer_first_name'];
		$farmer_last_name  = $_POST['farmer_last_name'];
		$farmer_address    = $_POST['farmer_address'];
		$farmer_contact_no = $_POST['farmer_contact_no'];
		$farmer_password   = $_POST['farmer_password'];
		$farmer_confirm_password = $_POST['farmer_confirm_password'];
		$asc_id 		   = $_POST['asc'];

		//checking required fields(empty fields)
		$req_fields = array('farmer_nic','farmer_first_name','farmer_last_name','farmer_address','farmer_contact_no','farmer_password');

		foreach ($req_fields as  $field) {
			if (empty(trim($_POST[$field]))) {
			$errors[] = 'Please enter your ' . $field;
			}
		}

		// Validating NIC and contact number
		if (strlen(trim($farmer_nic)) != 10 && strlen(trim($farmer_nic)) != 12) {
			$errors[] = 'Enter a valid NIC number';
		}
		if (strlen(trim($farmer_contact_no)) != 10) {
			$errors[] = 'Enter a valid contact number';
		}

		//checking max length
		$max_len_fields = array('farmer_nic' =>12,'farmer_first_name' =>20,'farmer_last_name' =>20,'farmer_address' =>100,'farmer_contact_no' =>10,'farmer_password' =>15);

		foreach ($max_len_fields as  $field => $max_len) {
			if (strlen(trim($_POST[$field])) > $max_len) {
				$errors[] = $field . 'must be less than ' . $max_len . 'characters.';
			}
		}

		if ($farmer_password != $farmer_confirm_password) {
			$errors[] = 'Passwords are not matching.';
		}
		
		if (empty($errors)) {
			//no errors found....adding new record to database
			$farmer_first_name = mysqli_real_escape_string($connection, $_POST['farmer_first_name']);
			$farmer_last_name  = mysqli_real_escape_string($connection, $_POST['farmer_last_name']);
			$farmer_nic        = mysqli_real_escape_string($connection, $_POST['farmer_nic']);
			$farmer_address    = mysqli_real_escape_string($connection, $_POST['farmer_address']);
			$farmer_contact_no = mysqli_real_escape_string($connection, $_POST['farmer_contact_no']);
			$farmer_password   = mysqli_real_escape_string($connection, $_POST['farmer_password']);
			$hashed_password   = sha1($farmer_password);


			$query = "INSERT INTO farmer_details (farmer_nic, farmer_first_name, farmer_last_name, farmer_address, farmer_contact_no, farmer_password) VALUES ('{$farmer_nic}', '{$farmer_first_name}','{$farmer_last_name}','{$farmer_address}', '{$farmer_contact_no}','{$hashed_password}')";

			$result = mysqli_query($connection, $query) or die(mysqli_error($connection));

			if ($result) {
				//query successfull
				$query2 = "INSERT INTO farmer_asc_details (asc_id,farmer_nic) VALUES ('{$asc_id}','{$farmer_nic}')";
				$result2 = mysqli_query($connection, $query2) or die(mysqli_error($connection));

				$_SESSION['farmer_nic']  = $farmer_nic;
				$_SESSION['first_name'] = $farmer_first_name;

				header('Location: ../user/home.php?user_added=true');
			} else {
				$errors[] = 'Failed to add query record.';
			}
		}
	}

?>

<!DOCTYPE html>

<html>

<head>
	<title>Register</title>

	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" referrerpolicy="no-referrer"></script>
</head>

<body>
	<header class="header">
	    <nav class="navbar navbar-expand-lg navbar-light">
	        <div class="container">
	            <a href="#" class="navbar-brand">
	                <img src="../assets/images/logo.png" alt="logo" width="100">
	            </a>
	        </div>
	    </nav>
	</header>

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

	<div class="container">
	    <div class="row align-items-center">
	        <div class="col-md-5">
	            <img src="img/background.png" alt="" class="img-fluid mb-3 d-none d-md-block">
	            <h1>Create an Account</h1>
	            <p class="font-italic text-muted mb-0">Register under your agrarian services center.</p>
	        </div>

	        <!-- Registeration Form -->
	        <div class="col-md-7 col-lg-6 ml-auto">
	        	<h2>Register</h2>
	            <form action="index.php" method="POST">
	                <div class="row">

	                	<!-- NIC -->
	                    <div class="input-group col-lg-12 mb-4">
	                        <div class="input-group-prepend">
	                            <span class="input-group-text bg-white px-4 border-md border-right-0">
	                                <i class="fa fa-user text-muted"></i>
	                            </span>
	                        </div>
	                        <input type="text" name="farmer_nic" placeholder="NIC (National Identity Card Number)" class="form-control bg-white border-left-0 border-md">
	                    </div>

	                    <!-- First Name -->
	                    <div class="input-group col-lg-6 mb-4">
	                        <div class="input-group-prepend">
	                            <span class="input-group-text bg-white px-4 border-md border-right-0">
	                                <i class="fa fa-user text-muted"></i>
	                            </span>
	                        </div>
	                        <input type="text" name="farmer_first_name" placeholder="First Name" class="form-control bg-white border-left-0 border-md">
	                    </div>

	                    <!-- Last Name -->
	                    <div class="input-group col-lg-6 mb-4">
	                        <div class="input-group-prepend">
	                            <span class="input-group-text bg-white px-4 border-md border-right-0">
	                                <i class="fa fa-user text-muted"></i>
	                            </span>
	                        </div>
	                        <input type="text" name="farmer_last_name" placeholder="Last Name" class="form-control bg-white border-left-0 border-md">
	                    </div>

	                    <!-- Email Address -->
	                    <div class="input-group col-lg-12 mb-4">
	                        <div class="input-group-prepend">
	                            <span class="input-group-text bg-white px-4 border-md border-right-0">
	                                <i class="fa fa-envelope text-muted"></i>
	                            </span>
	                        </div>
	                        <input type="text" name="farmer_address" placeholder="Address" class="form-control bg-white border-left-0 border-md">
	                    </div>

	                    <!-- Phone Number -->
	                    <div class="input-group col-lg-12 mb-4">
	                        <div class="input-group-prepend">
	                            <span class="input-group-text bg-white px-4 border-md border-right-0">
	                                <i class="fa fa-phone-square text-muted"></i>
	                            </span>
	                        </div>
	                        <input type="tel" name="farmer_contact_no" placeholder="Phone Number" class="form-control bg-white border-md border-left-0 pl-3">
	                    </div>

	                    <!-- Province -->
	                    <div class="input-group col-lg-12 mb-4">
	                        <div class="input-group-prepend">
	                            <span class="input-group-text bg-white px-4 border-md border-right-0">
	                                <i class="fa fa-address-card text-muted"></i>
	                            </span>
	                        </div>
	                        <select id="province" name="province" class="form-control bg-white border-md border-left-0 pl-3">
	                        	<option>Select Your Province</option>
	                        	<?php echo $province_list; ?>
	                        </select>
	                    </div>

	                    <!-- District -->
	                    <div class="input-group col-lg-12 mb-4">
	                        <div class="input-group-prepend">
	                            <span class="input-group-text bg-white px-4 border-md border-right-0">
	                                <i class="fa fa-address-card text-muted"></i>
	                            </span>
	                        </div>
	                        <select id="district" name="district" class="form-control bg-white border-md border-left-0 pl-3">
	                        	<option>Select Your District</option>
	                        </select>
	                    </div>

	                    <!-- ASC -->
	                    <div class="input-group col-lg-12 mb-4">
	                        <div class="input-group-prepend">
	                            <span class="input-group-text bg-white px-4 border-md border-right-0">
	                                <i class="fa fa-address-card text-muted"></i>
	                            </span>
	                        </div>
	                        <select id="asc" name="asc" class="form-control bg-white border-md border-left-0 pl-3">
	                        	<option>Select Your Agrarian Service Center</option>
	                        </select>
	                    </div>

	                    <!-- Password -->
	                    <div class="input-group col-lg-6 mb-4">
	                        <div class="input-group-prepend">
	                            <span class="input-group-text bg-white px-4 border-md border-right-0">
	                                <i class="fa fa-lock text-muted"></i>
	                            </span>
	                        </div>
	                        <input type="password" id="farmer_password" name="farmer_password" placeholder="Password" class="form-control bg-white border-left-0 border-md">
	                    </div>

	                    <!-- Password Confirmation -->
	                    <div class="input-group col-lg-6 mb-4">
	                        <div class="input-group-prepend">
	                            <span class="input-group-text bg-white px-4 border-md border-right-0">
	                                <i class="fa fa-lock text-muted"></i>
	                            </span>
	                        </div>
	                        <input type="password" id="farmer_confirm_password" name="farmer_confirm_password" placeholder="Confirm Password" class="form-control bg-white border-left-0 border-md">
	                        <label id="err" style="color: #800000;"></label>
	                    </div>

	                    <div class="col-lg-6 mb-4">
	                    	<input type="checkbox" id="showpass" onclick="togglePassword()">
	                    	<label>Show password</label>
	                    </div>

	                    <!-- Submit Button -->
	                    <div class="form-group col-lg-12 mx-auto mb-0">
	                        <button type="submit" name="submit" class="btn btn-primary btn-block py-2" style="background-color: #7AAD37; border-color: #7AAD37;">
	                            <span class="font-weight-bold">Create your account</span>
	                        </button>
	                    </div>

	                    <!-- Divider -->
	                    <div class="form-group col-lg-12 mx-auto d-flex align-items-center my-4">
	                        <div class="border-bottom w-100 ml-5"></div>
	                    </div>

	                    <!-- Already Registered -->
	                    <div class="text-center w-100">
	                        <p class="text-muted font-weight-bold">Already Registered?
	                        	<a href="../login/" class="text-primary ml-2" style="color: #7AAD37 !important;">Login</a>
	                        </p>
	                    </div>
	                </div>
	            </form>
	        </div>
	    </div>
	    <div class="bg-blue py-4">
            <div class="row px-3">
            	<small class="ml-4 ml-sm-5 mb-2">Copyright &copy; 2021. All rights reserved.</small>
        	</div>
    	</div>
	</div>


	<script type="text/javascript">
		// Get selected province and load relevant districts.
		$(document).ready(function() {
		    $("#province").on("change", function() {
		        var province_code = $("#province").val();

		        $.get("index.php?province_code=" + province_code, function(data, status) {
		        	$("#district").html(data);
		        });
		    });

		    // Get selected district and load relevant ASCs.
		    $("#district").on("change", function() {
		    	var district_code = $("#district").val();
		        
		        $.get("index.php?district_code=" + district_code, function(data, status) {
		        	$("select#asc").html(data);
		        });
	    	});

	    	// Check whether two passwords are same or not
	    	$("#farmer_confirm_password").keyup(function() {
	    		var password = $("#farmer_password").val();
	    		var password_confirm = $("#farmer_confirm_password").val();

	    		if (password != password_confirm) {
	    			console.log("Passwords are not matching.");
	    			$("#err").html("Passwords are not matching.");
	    		}else {
	    			$("#err").html("");
	    		}

	    		if (password_confirm == "") {
	    			$("#err").html("");
	    		}
	    	});
		});

		// Show password toggle
		function togglePassword() {
			var inputPass = document.getElementById("farmer_password");
			var confirmPass = document.getElementById("farmer_confirm_password");

			if (inputPass.type === "password" && confirmPass.type === "password") {
				inputPass.type = "text";
				confirmPass.type = "text";
			}else {
				inputPass.type = "password";
				confirmPass.type = "password";
			}
		}
	</script>

</body>
</html>

<?php mysqli_close($connection); ?>