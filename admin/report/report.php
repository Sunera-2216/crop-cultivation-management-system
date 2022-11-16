<?php
	require_once('../../include/connection.php');
	require_once('../../include/function.php');

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

	// Get crop details
	$crop_out = '';

	$rep_crops = mysqli_query($connection, "SELECT * FROM crop_details");

	while ($crp = mysqli_fetch_array($rep_crops)) {
		$crop_out .= '<tr>
						<td>'.$crp['crop_id'].'</td>
						<td><img src=\'../images/'.$crp['image'].'\' style=\'height:50px; width:50px;\'></td>
						<td>'.$crp['crop_name'].'</td>
						<td>'.$crp['crop_land_size'].' acres</td>
						<td>'.$crp['crop_cultivation_start_date'].'</td>
						<td>'.$crp['crop_cultivation_end_date'].'</td>
					</tr>';
	}


	// Get cultivated crop details
	$cultivated_out = '';

	$reportRes = mysqli_query($connection, "SELECT request_crop_id, request_farmer_nic, SUM(request_land_size) as size FROM request_details WHERE request_status = 1 GROUP BY request_crop_id");

	while ($req_crop = mysqli_fetch_array($reportRes)) {
		$cr = mysqli_query($connection, "SELECT * FROM crop_details WHERE crop_id = '{$req_crop['request_crop_id']}'");
		$crop = mysqli_fetch_assoc($cr);

		$far = mysqli_query($connection, "SELECT asc_id FROM farmer_asc_details WHERE farmer_nic = '{$req_crop['request_farmer_nic']}'");
		$asc_id = mysqli_fetch_assoc($far)['asc_id'];

		$asc = mysqli_query($connection, "SELECT asc_name FROM asc_details WHERE asc_id = '{$asc_id}'");
		$asc_name = mysqli_fetch_assoc($asc)['asc_name'];

		$cultivated_out .= '<tr>
								<td><img src=\'../images/'.$crop['image'].'\' style=\'height:50px; width:50px;\'></td>
								<td>'.$crop['crop_name'].'</td>
								<td>'.$req_crop['size'].' acres</td>
								<td>'.$req_crop['request_farmer_nic'].'</td>
								<td>'.$asc_id.'</td>
								<td>'.$asc_name.'</td>
								<td>Uva Province</td>
							</tr>';
	}

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Cultivation Report - Department of Agrarian Services</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" referrerpolicy="no-referrer"></script>
</head>
<body>

	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h1 style="text-align:center;font-weight: bold;margin-top: 20px;">Cultivation Report - 2021</h1>
				<button class="btn btn-primary" style="float:right;" onclick="window.print()">Save as pdf</button>
			</div>
		</div>
		<div class="row" style="margin-bottom:20px;">
			<div class="col-md-12">
				<h3 style="text-align:center;font-weight: bold;margin-top: 20px;">Crop Details</h3>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<table class="table table-striped">
					<tr>
						<th>Crop ID</th>
						<th></th>
						<th>Crop Name</th>
						<th>Required Land Size</th>
						<th>Cultivation Start Date</th>
						<th>Cultivation End Date</th>
					</tr>
					<?php echo $crop_out; ?>
				</table>
			</div>
		</div>

		<div class="row" style="margin-bottom:20px;">
			<div class="col-md-12">
				<h3 style="text-align:center;font-weight: bold;margin-top: 20px;">Cultivation Details</h3>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<table class="table table-striped">
					<tr>
						<th></th>
						<th>Crop Name</th>
						<th>Cultivated Land Size</th>
						<th>Cultivated Farmer NIC</th>
						<th>ASC ID</th>
						<th>ASC Name</th>
						<th>Province</th>
					</tr>
					<?php echo $cultivated_out; ?>
				</table>
			</div>
		</div>

		<div class="row" style="margin-bottom:10px;">
			<div class="col-md-12">
				<h3 style="text-align:center;font-weight: bold;">Charts and graphs</h3>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div id="chartContainer" style="height: 370px; width: 100%; margin-top: 30px;"></div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div id="barChartContainer1" style="height: 370px; width: 100%; margin-top: 120px; margin-right: 50px;"></div>
			</div>
			<div class="col-md-6">
				<div id="barChartContainer2" style="height: 370px; width: 100%; margin-top: 120px; margin-right: 50px;"></div>
			</div>
		</div>

		<div class="row" style="margin-bottom:50px;">
			<div class="col-md-12">
				<div id="barChartContainer3" style="height: 370px; width: 100%; margin-top: 120px; margin-right: 50px;"></div>
			</div>
		</div>
		<hr>

	</div>


	<script type="text/javascript">
		$(document).ready(function() {
			// Pie chart
			var chart = new CanvasJS.Chart("chartContainer", {
				animationEnabled: true,
				title: {
					text: "Extent of land cultivated by crop type"
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

			var chart1 = new CanvasJS.Chart("barChartContainer1", {
				animationEnabled: true,
				title:{
					text: "Required crop land sizes"
				},
				axisY: {
					title: "Uva Province",
					includeZero: true,
					suffix:  " acres"
				},
				data: [{
					type: "bar",
					yValueFormatString: "#### acres",
					indexLabel: "{y}",
					indexLabelPlacement: "inside",
					indexLabelFontWeight: "bolder",
					indexLabelFontColor: "white",
					dataPoints: <?php echo json_encode($required, JSON_NUMERIC_CHECK); ?>
				}]
			});
			chart1.render();

			var chart2 = new CanvasJS.Chart("barChartContainer2", {
				animationEnabled: true,
				title:{
					text: "Cultivated crop land sizes"
				},
				axisY: {
					title: "Uva Province",
					includeZero: true,
					suffix:  " acres"
				},
				data: [{
					type: "bar",
					yValueFormatString: "#### acres",
					indexLabel: "{y}",
					indexLabelPlacement: "inside",
					indexLabelFontWeight: "bolder",
					indexLabelFontColor: "white",
					dataPoints: <?php echo json_encode($cultivated, JSON_NUMERIC_CHECK); ?>
				}]
			});
			chart2.render();

			var chart3 = new CanvasJS.Chart("barChartContainer3", {
				animationEnabled: true,
				exportEnabled: true,
				theme: "light1", // "light1", "light2", "dark1", "dark2"
				title:{
					text: "Required vs Cultivated land sizes"
				},
				axisX:{
					reversed: true
				},
				axisY:{
					title: "Uva Province",
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
			chart3.render();
		});
	</script>
	
	<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

</body>
</html>