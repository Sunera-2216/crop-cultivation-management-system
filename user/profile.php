<?php
	session_start();
	require_once('../include/connection.php');
	require_once('../include/function.php');

	//checking if a user is logged in
	if (!isset($_SESSION['farmer_nic'])) {
		header('Location: ../login/');
	}

	$logged_farmer_nic = $_SESSION['farmer_nic'];

	// Get farmer details by NIC
	$query = "SELECT * FROM farmer_details WHERE farmer_nic = '{$logged_farmer_nic}'";
	$result = mysqli_query($connection, $query);

	if (mysqli_num_rows($result) > 0) {
		$farmer_details = mysqli_fetch_array($result);
	}else {
			echo "Cannot found data. Please try again.";
	}

	// Get ASC name
	$query = "SELECT asc_id FROM farmer_asc_details WHERE farmer_nic = '{$logged_farmer_nic}'";
	$result = mysqli_query($connection, $query);
	$res = mysqli_fetch_array($result);

	$asc_id = $res['asc_id'];

	$query = "SELECT asc_name FROM asc_details WHERE asc_id = '{$asc_id}'";
	$result = mysqli_query($connection, $query);
	$res_asc = mysqli_fetch_array($result);

	// Get district
	$query = "SELECT district_code FROM asc_district_details WHERE asc_id = '{$asc_id}'";
	$result = mysqli_query($connection, $query);
	$res = mysqli_fetch_array($result);

	$district_code = $res['district_code'];

	$query = "SELECT district_name FROM district_details WHERE district_code = '{$district_code}'";
	$result = mysqli_query($connection, $query);
	$res_district = mysqli_fetch_array($result);

	// Get province
	$query = "SELECT province_code FROM district_province_details WHERE district_code = '{$district_code}'";
	$result = mysqli_query($connection, $query);
	$res = mysqli_fetch_array($result);

	$province_code = $res['province_code'];

	$query = "SELECT province_name FROM province_details WHERE province_code = '{$province_code}'";
	$result = mysqli_query($connection, $query);
	$res_province = mysqli_fetch_array($result);

?>


<!DOCTYPE html>

<html>

<head>
	<meta charset="utf-8">
	<title>Profile</title>

	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>

	<header>
		<div class="loggedin"><?php echo $_SESSION['first_name']; ?> | <a href="home.php">Home</a> | <a href="logout.php">Log Out</a></div>  
	</header>

	<div class="container">
		<div class="row">
			<div class="col-md-12 text-center" style="margin: 20px;">
				<h2>Profile</h2>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-6">
				<h3 style="border-bottom: 1px solid #888;">Personal Details</h3>
				<div class="row">
					<div class="col-md-12" style="margin-bottom: 10px;">
						<h6>NIC (National Identity Card)</h6>
						<span><?php echo $farmer_details['farmer_nic']; ?></span>
					</div>
				</div>
				<div class="row" style="margin-bottom: 20px;margin-top: 10px;">
					<div class="col-md-4">
						<h6>First Name</h6>
						<span><?php echo $farmer_details['farmer_first_name']; ?>
					</div>
					<div class="col-md-6">
						<h6>Last Name</h6>
						<span><?php echo $farmer_details['farmer_last_name']; ?>
					</div>
				</div>
				<div class="row" style="margin-bottom: 20px;">
					<div class="col-md-4">
						<h6>Address</h6>
						<span><?php echo $farmer_details['farmer_address']; ?></span>
					</div>
					<div class="col-md-6">
						<h6>Contact Number</h6>
						<span><?php echo $farmer_details['farmer_contact_no']; ?></span>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<h3 style="border-bottom: 1px solid #888;">Agricultural Services Center Details</h3>
				<div class="row" style="margin-bottom: 10px;">
					<div class="col-md-3">
						<h6>Province</h6>
						<span><?php echo $res_province['province_name']; ?></span>
					</div>
					<div class="col-md-3">
						<h6>District</h6>
						<span><?php echo $res_district['district_name']; ?></span>
					</div>
					<div class="col-md-6">
						<h6>Agricultural Service Center</h6>
						<span><?php echo $res_asc['asc_name']; ?></span>
					</div>
				</div>
			</div>
		</div>

		<div class="row" style="margin-top: 30px;">
			<div class="col-md-12">
				<h3 style="border-bottom: 1px solid #888;">Crop Details</h3>
				<div class="table-responsive">
					<table class="table table-bordered">
						<th>Crop name</th>
						<th>Land size</th>
						<th>Request status</th>

						<?php
							$out = '';

							$r = mysqli_query($connection, "SELECT * FROM request_details WHERE request_farmer_nic = '{$logged_farmer_nic}'");

							while ($fa = mysqli_fetch_array($r)) {
								$query_crop = "SELECT crop_name FROM crop_details WHERE crop_id = '{$fa['request_crop_id']}'";
								$result_crop = mysqli_query($connection, $query_crop);
								$crop_details = mysqli_fetch_assoc($result_crop);

								$rs = '';

								if ($fa['request_status'] == -1) {
									$rs = 'Pending';
								}else if ($fa['request_status'] == 1) {
									$rs = 'Approved';
								}else {
									$rs = 'Rejected';
								}

								$out .= '<tr>
											<td>'.$crop_details['crop_name'].'</td>
											<td>'.$fa['request_land_size'].' acres</td>
											<td>'.$rs.'</td>
										</tr>';
							}

							echo $out;
						?>
					</table>
				</div>
			</div>
		</div>
	
</body>
</html>