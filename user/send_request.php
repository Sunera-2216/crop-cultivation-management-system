<?php
	session_start();
	require_once('../include/connection.php');
	require_once('../include/function.php');

	//checking if a user is logged in
	if (!isset($_SESSION['farmer_nic'])) {
		header('Location: ../login/');
	}

	$logged_farmer_nic = $_SESSION['farmer_nic'];

	if (isset($_POST['removeBtn'])) {
		foreach ($_SESSION['selected_crops'] as $key => $value) {
			if ($value['crop_id'] == $_POST['removeBtn']) {
				unset($_SESSION['selected_crops'][$key]);
			}
		}
	}

	if (isset($_POST['sendRequest'])) {
		// Get ASC ID
		$query = "SELECT asc_id FROM farmer_asc_details WHERE farmer_nic = '{$logged_farmer_nic}'";
		$result = mysqli_query($connection, $query);
		$res = mysqli_fetch_assoc($result);
		$asc_id = $res['asc_id'];

		// Get user inputs
		$crop_id = $_POST['crop_id'];
		$crop_land_size = $_POST['crop_land_size'];

		for ($i = 0; $i < count($crop_id); $i++) {

			// Add data to request_details table
			$query = "INSERT INTO request_details (request_crop_id, request_farmer_nic, request_land_size, request_status) VALUES ('{$crop_id[$i]}', '{$logged_farmer_nic}', '{$crop_land_size[$i]}', -1)";
			$result = mysqli_query($connection, $query);
			
			if ($result) {
				// Get newly added request id
				$req = "SELECT request_id FROM request_details ORDER BY request_id DESC LIMIT 1";
				$rr = mysqli_query($connection, $req);
				$rrr = mysqli_fetch_assoc($rr);
				$req_id = $rrr['request_id'];

				// Add data to request_details table
				$query2 = "INSERT INTO asc_request_details (asc_id, request_id) VALUES ('{$asc_id}', '{$req_id}')";
				$result2 = mysqli_query($connection, $query2);

				if ($result2 && $i == count($crop_id) - 1) {
					echo "<script>
							alert('Request sent successfully.');
							window.location.href = 'home.php';
							</script>";
				}
			}
		
		}
	}

?>


<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Send Request</title>

	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" referrerpolicy="no-referrer"></script>

	<style type="text/css">
		.send-btn {
			background-color: #7AAD37;
			color: white;
		}

		.send-btn:hover {
			background-color: white;
			color: #7AAD37;
			border: 1px solid #7AAD37;
			transition: all 0.3s ease-in-out;
		}
	</style>
</head>

<body>
	<header>
		<div class="loggedin"><?php echo $_SESSION['first_name']; ?> | <a href="home.php">Home</a> | <a href="logout.php">Log Out</a></div>  
	</header>
	<div class="container px-1 px-md-5 px-lg-1 px-xl-5 py-5 mx-auto">
		<div class="row">
			<div class="col-md-12">
				<h2 style="text-align: center; margin-bottom: 50px;">Send Request</h2>
				<form action="send_request.php" method="POST" id="send_request_form">
					<table class="form-group table table-bordered">
						<tr style="margin-bottom: 20px;">
							<th class='col-md-2'>Crop Name</th>
							<th class='col-md-2'>Request Land Size (Acres)</th>
							<th class='col-md-2'>Remove Crop</th>
						</tr>

					<?php
						if (!empty($_SESSION['selected_crops'])) {
							foreach ($_SESSION['selected_crops'] as $key => $value) {
								$query = "SELECT crop_name FROM crop_details WHERE crop_id = '{$value['crop_id']}'";
								$result = mysqli_query($connection, $query);

								$crop_details = mysqli_fetch_assoc($result);

								echo "<div class='row'>";
								echo "<tr>";

								echo "<td><input type='hidden' value='{$value['crop_id']}' name='crop_id[]'>".$crop_details['crop_name']."</td>";
								echo "<td><input type='text' name='crop_land_size[]' class='form-control'></td>";
								echo "<td class='col-md-2'><button type='submit' name='removeBtn' value=".$value["crop_id"]." class='btn btn-danger'>Remove</button></td>";

								echo "</tr>";
								echo "</div>";
							}
						}
					?>
					
					</table>
					<div class="row">
						<div class="col-md-4"></div>
						<div class="col-md-4">
							<input type="submit" name="sendRequest" class="form-control send-btn" value="Send Request">
						</div>
						<div class="col-md-4"></div>
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
</html>