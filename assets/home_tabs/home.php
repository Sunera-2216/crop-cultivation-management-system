<?php
	session_start();
	require_once('../../include/connection.php');
	require_once('../../include/function.php');

	$query = "SELECT * FROM crop_details LIMIT 4";

	$result = mysqli_query($connection, $query);

	$output = '';

	if (mysqli_num_rows($result) > 0) {
		while ($crop = mysqli_fetch_array($result)) {
			$output .= '
					<table style="border-bottom=">
					<tr>
						<td style="width: 120px;"><img src="admin/images/'.$crop['image'].'" style=\'height:50px; width:50px;\'></td>
						<td style="width: 120px;">'.$crop['crop_name'].'</td>
						<td style="width: 130px;">'.$crop['crop_land_size'].'</td>
						<td style="width: 188px;">'.$crop['crop_cultivation_start_date'].'</td>
			            <td style="width: 188px;">'.$crop['crop_cultivation_end_date'].'</td>
			         </tr>
			         </table>';
		}

		$_SESSION['output'] = $output;
	}else {
		echo "No Result Found";
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

<div class="intro clearfix">
			<div class="introimage">
				<img src="assets/images/02.png" alt="Agrarian image">
			</div><!--introimage-->

			<div class="introtext">
				<h1>Saubagya <br> Krushi Niyamaka</h1>
				<p>Sri Lanka is an agricultural country.  Currently, one of the major problem facing agricultural sector in the country is cultivation of crops without proper management. As a result some crop harvests going to waste due to the excess and some crop harvests are in short supply. Farmers have been facing a number of problems over the years due to the inability to sell their harvests due to the excess of certain crops. And also customers are facing severe difficulties due to the unavoidable increase in the prices of certain crops due to the shortage of certain agricultural products.  </p>

				<a href="#">Read More &raquo;</a>
			</div><!--introtext-->
		</div><!--intro-->
		
		
		<h1></h1>
		<div class="home" style="height: 320px;">
			<div id="chartContainer" style="height: 320px; width: 400px; margin-top: 50px; margin-bottom: 50px; float:left;"></div>
			<div id="barChartContainer1" style="height: 320px; width: 500px; margin-top: 120px; margin-top: 50px; float: right; margin-bottom: 50px;"></div>
		</div><!--home-->

		<!-- Crop table -->
		<div style="height: 320px;">
			<img src="assets/images/uva.jpg" width="280" style="float: right;margin-top: 30px;">
			<div class="search-bar clearfix" style="width: 700px; height: 25px; background: orange;">
				<div class="top-bar-links">
					<form action="" method="GET">
						<input type="text" name="search" id="search" placeholder="Search crop" style="margin-left: 5px; margin-top: 2.5px;">
					</form>
				</div>
			</div>
			<div id="result">
				<table>
					<tr>
						<th style="width: 100px;">Image</th>
						<th style="width: 80px;">Crop Name</th>
						<th style="width: 140px;">Land Size</th>
						<th style="width: 158px;">Start Date to Apply</th>
						<th style="width: 188px;">End Date to Apply</th>
					</tr>
					<?php echo $output; ?>
				</table>
			</div>
			
		</div>

		<div style="margin-bottom: 20px; margin-top: 20px;">
			<hr width="700px">
		</div>

		<div class="homecontent clearfix">
			<div class="homecol">
				<h2>A Little about Us</h2>
				<img src="assets/images/unnamed.png" alt="about-us">
				<p>The Department of Agrarian Development was established on 01st October 1957 with the objective of providing necessary facilities to Sri Lankan farmer community abolishing the hitherto existed Food Department. </p>
				<p>*********************************</p>
			</div><!--homecol-->

			<div class="homecol">
				<h2>Some of Our Services </h2>
				<div class="services">
					<img src="assets/images/services/statistics-market-icon.png">
					<h3><a href="assets/home_tabs/statistical_data.php" style="text-decoration:none;color: black;float: left;box-sizing: border-box;">Statistical Data</a><br></h3>
					<p >See current statistical data on crop cultivation process.</p>
				</div><!--services-->

				<div class="services">
					<img src="assets/images/services/certificate-icon.png">
					<h3><a href="" style="text-decoration:none;color: black;float: left;box-sizing: border-box;">Cultivated Reports</a><br></h3>
					<p>See current and past cultivation reports.</p>	
				</div><!--services-->

				<div class="services">
					<img src="assets/images/services/lock-icon.png">
					<h3><a href="" style="text-decoration:none;color: black;float: left;box-sizing: border-box;">Cultivation Approvals</a><br></h3>
					<p>See approval of cultivation requests.</p>
				</div><!--services-->

				<div class="services">
					<img src="assets/images/services/dollar-rotation-icon.png">
					<h3><a id="link_notices" href="#" style="text-decoration:none;color: black;float: left;box-sizing: border-box;">Notices</a><br></h3>
					<p>See all notices published by Department of Agrarian &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Services.</p>
				</div><!--services-->
			</div><!--homecol-->
			
			<div class="homecol">
				<h2>Our Notices</h2>

				<div class="quote">
					<img src="assets/images/images.png">
					<h3>Latest Notice</h3>
					<p> <?php 
							$noti = mysqli_query($connection, "SELECT notice FROM notice_details ORDER BY notice_id DESC LIMIT 1");
							echo mysqli_fetch_assoc($noti)['notice'];
						?> </p>
				</div><!--quote-->
			</div><!--homecol-->
		</div><!--homecontent-->

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

			// Bar chart
			var chart = new CanvasJS.Chart("barChartContainer1", {
				animationEnabled: true,
				exportEnabled: true,
				theme: "light1", // "light1", "light2", "dark1", "dark2"
				title:{
					text: "Required vs Cultivated"
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

	<script>
		$(document).ready(function() {
			$("#search").keyup(function() {
				var keyword = $("#search").val();

				$.get("assets/get-data.php?search=" + keyword, function(data, status) {
					$("#result").html(data);
				});
			});

			$("a#link_notices").click(function() {
		        $.get("assets/home_tabs/notice.php", function(data, status) {
		          $("#main_container").html(data);
		        });

		        $("a#tab_home").removeClass('active');
		        $("a#tab_division").removeClass('active');
		        $("a#tab_about").removeClass('active');
		        $("a#tab_contact").removeClass('active');
		        $("a#tab_notice").toggleClass('active');
		    });
		});
	</script>