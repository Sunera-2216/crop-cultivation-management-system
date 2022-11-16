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


?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Statistical Data</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../css/main.css">

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" referrerpolicy="no-referrer"></script>
</head>
<body>

	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h1 style="text-align:center;font-weight: bold;margin-top: 20px;">Statistical Data</h1>
			</div>
		</div>

		<hr>

		<div class="row">
			<div class="col-md-12">
				<div id="chartContainer" style="height: 370px; width: 100%; margin-top: 50px;"></div>
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

	<div class="footer" style="margin-top:50px;">
		<div class="row" style="margin:20px;">
			<div class="col-md-4">
				<h4>Quick Navigation</h4>
				<ul class="links">
					<li><a href="../../index.php">Home</a></li>
					<li><a href="../../index.php">Services</a></li>
					<li><a href="../../index.php">Division</a></li>
					<li><a href="../../index.php">Contact Us</a></li>
				</ul>
			</div>

			<div class="col-md-4">
				<h4>Related Links</h4>
				<ul class="links">
					<li><a href="http://www.agrimin.gov.lk/web/index.php/en">Ministry of Agriculture</a></li>
					<li><a href="https://www.doa.gov.lk/index.php/en/">Department of Agriculture</a></li>
					<li><a href="https://www.agrariandept.gov.lk/web/index.php?lang=en">Department of Agrarian Development</a></li>
					<li><a href="http://www.dea.gov.lk/dept-of-export-agriculture/">Department of Export Agriculture</a></li>
					<li><a href="http://www.doa.gov.lk/HORDI/index.php/en/crop-en/38-capsium-e">Crop Research Center - Gannoruwa</a></li>
					<li><a href="https://www.treasury.gov.lk/">Ministry of Finance</a></li>	
				</ul>
			</div><!--footercol-->

			<div class="col-md-4">
				<h4>Contact Us</h4>
					<h5>Address</h5>
					<p>No : 42,<br>Sri Marcus Fernando Mawatha,<br>P.O. Box 537, Colombo 07</p>
					<h5>Telephone</h5>
					<p>011 2 694 231/3</p>
					<h5>Fax</h5>
					<p>011 2 694 231</p>
					<h5>E-mail</h5>
					<p>info@agrariandept.gov.lk</p>
				
			</div><!--footercol-->

		<div class="row">
			<div class="col-md-12">
				<div class="copyrights">
			<div class="left">
				Copyrights &copy; 2021. All Rights Reserved. 
			</div><!--left-->

			<div class="right">
				Website by: CST Project Group 8
			</div><!--right-->
		</div><!--copyrights-->
			</div>
		</div>
		
		</div>
		</div>

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