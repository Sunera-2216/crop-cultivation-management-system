<?php
	session_start();
	require_once('../include/connection.php');
	require_once('../include/function.php');

	//checking if a user is logged in
	if (!isset($_SESSION['farmer_nic'])) {
		header('Location: ../login/');
	}

	$query = "SELECT * FROM crop_details WHERE is_deleted = 0 LIMIT 10";

	$result = mysqli_query($connection, $query);

	$output = '';

	$tod = date("Y-m-d");

	if (mysqli_num_rows($result) > 0) {
		while ($crop = mysqli_fetch_array($result)) {
			if ($crop['crop_cultivation_end_date'] >=  $tod) {
				$output .= '
						<table class=table table-bordered>
						<tr>
							<td><img src="../admin/images/'.$crop['image'].'" style=\'height:100px; width:100px;\'></td>
							<td style="width: 15%">'.$crop['crop_name'].'</td>
							<td style="width: 15%">'.$crop['crop_land_size'].' acres</td>
							<td style="width: 20%">'.$crop['crop_cultivation_start_date'].'</td>
				            <td style="width: 20%">'.$crop['crop_cultivation_end_date'].'</td>
							<td style="width: 20%"><button type="submit" name="selectBtn" value="'.$crop['crop_id'].'" class="btn btn-success">Select</button></td>
				         </tr>
				         </table>';
			}
		}

		$_SESSION['output'] = $output;

	}else {
		echo "No Result Found";
	}

	// Get selected crops
	if (isset($_POST['selectBtn'])) {
		if (isset($_SESSION['selected_crops'])) {
			$crop_array_id = array_column($_SESSION['selected_crops'], "crop_id");

			if (!in_array($_POST['selectBtn'], $crop_array_id)) {
				$count = count($_SESSION['selected_crops']);

				$crop_array = array('crop_id' => $_POST['selectBtn']);

				$_SESSION['selected_crops'][$count] = $crop_array;

				//echo "<script>alert('Crop added.')</script>";

			}else {
				echo "<script>alert('Crop already selected.')</script>";
			}

		}else {
			$crop_array = array('crop_id' => $_POST['selectBtn']);

			$_SESSION['selected_crops'][0] = $crop_array;
		}
	}

	// Get data to pie chart
	$dataPoints = array();

	$pieres = mysqli_query($connection, "SELECT request_crop_id, SUM(request_land_size) as size FROM request_details WHERE request_status = 1 GROUP BY request_crop_id");

	$i = 0;
	while ($data = mysqli_fetch_array($pieres)) {
		$cr = mysqli_query($connection, "SELECT crop_name FROM crop_details WHERE crop_id = '{$data['request_crop_id']}'");
		$crop = mysqli_fetch_assoc($cr);

		$arr = array("label" => $crop['crop_name'], "y" => $data['size']);
		$dataPoints[$i] = $arr;

		$i++;
	}

	// Get data to bar chart
	$required = array();
	$cultivated = array();

	$barres = mysqli_query($connection, "SELECT crop_name, crop_land_size FROM crop_details");

	$j = 0;
	while ($d = mysqli_fetch_array($barres)) {
		$arr1 = array("label" => $d['crop_name'], "y" => $d['crop_land_size']);
		$required[$j] = $arr1;
		$j++;
	}

	$barres1 = mysqli_query($connection, "SELECT request_crop_id, SUM(request_land_size) as size FROM request_details WHERE request_status = 1 GROUP BY request_crop_id");

	$k = 0;
	while ($data = mysqli_fetch_array($barres1)) {
		$cr = mysqli_query($connection, "SELECT crop_name FROM crop_details WHERE crop_id = '{$data['request_crop_id']}'");
		$crop = mysqli_fetch_assoc($cr);

		$arr2 = array("label" => $crop['crop_name'], "y" => $data['size']);
		$cultivated[$k] = $arr2;

		$k++;
	}

?>

<!DOCTYPE html>

<html>

<head>
	<meta charset="utf-8">
	<title>Home</title>
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" referrerpolicy="no-referrer"></script>
</head>

<body>

	<header>
		<div class="logo">
			<img src="../assets/images/logo.png" width="80px">
			<div style="float: right;">
				<h4>Selected crop count: 
					<?php
						if (isset($_SESSION['selected_crops'])) {
							echo count($_SESSION['selected_crops']);
						}else {
							echo '0';
						}
					?>
				</h4>
			</div>
		</div>
		<div class="loggedin">Welcome <?php echo $_SESSION['first_name']; ?>! | <a href="send_request.php">Send Request</a> | <a href="profile.php">Profile</a> | <a href="logout.php">Log Out</a></div>  
	</header>

	<div class="container-fluid px-1 px-md-5 px-lg-1 px-xl-5 py-5 mx-auto">
		<div class="row">
			<div class="col-md-6" style="border-right: 1px solid #AAA;">
				<h3>Statistical data</h3>
				<div id="chartContainer" style="height: 370px; width: 100%; margin-top: 50px;"></div>
				<div id="barChartContainer1" style="height: 370px; width: 100%; margin-top: 120px; margin-right: 50px;"></div>
				<!-- <div id="barChartContainer2" style="height: 370px; width: 100%; margin-top: 120px; margin-right: 50px;"></div> -->
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<form action="" method="GET">
						<h3 class="input-group-addon">Crops list</h3>
						<input type="text" name="search" id="search" placeholder="Search crop" class="form-control">
					</form>
				</div>
				<div class="masterlist">
					<div id="result" class="table-responsive">
						<table class="table">
							<tr>
								<th>Image</th>
								<th>Crop Name</th>
								<th>Land Size</th>
								<th>Start Date to Apply</th>
								<th>Last Date to Apply</th>
								<th></th>
							</tr>
							<form method="POST" action="home.php" id="sub-form">
								<?php echo $output; ?>
							</form>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		// Crop search
		$(document).ready(function() {
			$("#search").keyup(function() {
				var keyword = $("#search").val();

				$.get("get-data.php?search=" + keyword, function(data, status) {
					$("#result").html(data);
				});
			});

			// Pie chart
			var chart = new CanvasJS.Chart("chartContainer", {
				animationEnabled: true,
				title: {
					text: "Extent of land cultivated by crop"
				},
				subtitles: [{
					text: "Uva Province"
				}],
				data: [{
					type: "pie",
					yValueFormatString: "#,##0.00\" acres\"",
					indexLabel: "{label} ({y})",
					dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
				}]
			});
			chart.render();

			// Bar chart
			// var chart = new CanvasJS.Chart("barChartContainer1", {
			// 	animationEnabled: true,
			// 	title:{
			// 		text: "Required crop land sizes"
			// 	},
			// 	axisY: {
			// 		title: "Uva Province",
			// 		includeZero: true,
			// 		suffix:  " acres"
			// 	},
			// 	data: [{
			// 		type: "bar",
			// 		yValueFormatString: "#### acres",
			// 		indexLabel: "{y}",
			// 		indexLabelPlacement: "inside",
			// 		indexLabelFontWeight: "bolder",
			// 		indexLabelFontColor: "white",
			// 		dataPoints: <?php //echo json_encode($required, JSON_NUMERIC_CHECK); ?>
			// 	}]
			// });
			// chart.render();

			// var chart1 = new CanvasJS.Chart("barChartContainer2", {
			// 	animationEnabled: true,
			// 	title:{
			// 		text: "Cultivated crop land sizes"
			// 	},
			// 	axisY: {
			// 		title: "Uva Province",
			// 		includeZero: true,
			// 		suffix:  " acres"
			// 	},
			// 	data: [{
			// 		type: "bar",
			// 		yValueFormatString: "#### acres",
			// 		indexLabel: "{y}",
			// 		indexLabelPlacement: "inside",
			// 		indexLabelFontWeight: "bolder",
			// 		indexLabelFontColor: "white",
			// 		dataPoints: <?php //echo json_encode($cultivated, JSON_NUMERIC_CHECK); ?>
			// 	}]
			// });
			// chart1.render();
			var chart = new CanvasJS.Chart("barChartContainer1", {
				animationEnabled: true,
				exportEnabled: true,
				theme: "light1", // "light1", "light2", "dark1", "dark2"
				title:{
					text: "Required vs Cultivated by land sizes"
				},
				axisX:{
					reversed: true
				},
				axisY:{
					includeZero: true
				},
				toolTip:{
					shared: true
				},
				data: [{
					type: "stackedBar",
					name: "Required",
					yValueFormatString: "#,##0.00\" acres\"",
					dataPoints: <?php echo json_encode($required, JSON_NUMERIC_CHECK); ?>
				},{
					type: "stackedBar",
					name: "Cultivated",
					yValueFormatString: "#,##0.00\" acres\"",
					dataPoints: <?php echo json_encode($cultivated, JSON_NUMERIC_CHECK); ?>
				}]
			});
			chart.render();
		});
	</script>

	<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

</body>
</html>

<?php mysqli_close($connection); ?>