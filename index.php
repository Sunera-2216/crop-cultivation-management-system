<?php
	session_start();
	require_once('include/connection.php');
	require_once('include/function.php');

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

<!DOCTYPE html>

<html>

<head>
	<title>Saubagya - Krushi Niyamaka</title>
	<link rel="stylesheet" type="text/css" href="assets/css/main.css">
	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" referrerpolicy="no-referrer"></script>

	<style type="text/css">
		.navbar {
		  overflow: hidden;
		  background-color: #333;
		}

		.navbar a {
		  float: left;
		  font-size: 16px;
		  color: white;
		  text-align: center;
		  padding: 14px 16px;
		  text-decoration: none;
		}

		.dropdown {
		  float: left;
		  overflow: hidden;
		}

		.dropdown .dropbtn {
		  font-size: 16px;  
		  border: none;
		  outline: none;
		  color: white;
		  padding: 14px 16px;
		  background-color: inherit;
		  font-family: inherit;
		  margin: 0;
		}

		.navbar a:hover, .dropdown:hover .dropbtn {
		  background-color: red;
		}

		.dropdown-content {
		  display: none;
		  position: absolute;
		  background-color: #f9f9f9;
		  min-width: 160px;
		  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
		  z-index: 1;
		}

		.dropdown-content a {
		  float: none;
		  color: black;
		  padding: 12px 16px;
		  text-decoration: none;
		  display: block;
		  text-align: left;
		}

		.dropdown-content a:hover {
		  background-color: #ddd;
		}

		.dropdown:hover .dropdown-content {
		  display: block;
		}

		/* end of first navbar */

		/* Add a black background color to the top navigation */
		.topnav {
		 background-color: #333;
		  overflow: hidden;
		}

		/* Style thelinks inside the navigation bar */
		.topnav a {
		  float: left;
		 display: block;
		  color: #f2f2f2;
		  text-align: center;
		 padding: 14px 16px;
		  text-decoration: none;
		  font-size: 17px;
		}

		/* Add an active class to highlight the current page */
		.active {
		  background-color: #04AA6D;
		  color: white;
		}

		/* Hide thelink that should open and close the topnav on small screens */
		.topnav.icon {
		  display: none;
		}

		/* Dropdown container - needed toposition the dropdown content */
		.dropdown {
		  float: left;
		  overflow: hidden;
		}

		/* Style thedropdown button to fit inside the topnav */
		.dropdown .dropbtn {
		 font-size: 17px; 
		  border: none;
		 outline: none;
		  color: white;
		 padding: 14px 16px;
		  background-color: inherit;
		 font-family: inherit;
		  margin: 0;
		}

		/* Stylethe dropdown content (hidden by default) */
		.dropdown-content {
		  display: none;
		 position: absolute;
		  background-color: #f9f9f9;
		 min-width: 160px;
		  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
		 z-index: 1;
		}

		/* Style the links inside the dropdown */
		.dropdown-content a {
		  float: none;
		 color: black;
		  padding: 12px 16px;
		 text-decoration: none;
		  display: block;
		 text-align: left;
		}

		/* Add a dark background on topnav links and thedropdown button on hover */
		.topnav a:hover, .dropdown:hover .dropbtn {
		 background-color: #555;
		  color: white;
		}

		/* Adda grey background to dropdown links on hover */
		.dropdown-content a:hover {
		  background-color: #ddd;
		 color: black;
		}

		/* Show the dropdown menu when the user moves themouse over the dropdown button */
		.dropdown:hover.dropdown-content {
		  display: block;
		}

		/* When the screen is less than 600 pixels wide, hide all links, except for the first one ("Home"). Show the link that contains should open and close the topnav (.icon) */
		@media screen and(max-width: 600px) {
		  .topnav a:not(:first-child), .dropdown .dropbtn {
		    display: none;
		  }
		  .topnav a.icon {
		   float: right;
		    display: block;
		  }
		}

		/* The "responsive" class is added to the topnav with JavaScript when the user clicks on the icon. This class makes the topnav look good on small screens (display the links vertically instead of horizontally) */
		@media screen and (max-width: 600px) {
		  .topnav.responsive {position: relative;}
		  .topnav.responsive a.icon {
		    position: absolute;
		    right: 0;
		    top: 0;
		  }
		  .topnav.responsive a {
		    float: none;
		    display: block;
		    text-align: left;
		  }
		  .topnav.responsive.dropdown {float: none;}
		  .topnav.responsive .dropdown-content {position: relative;}
		  .topnav.responsive .dropdown .dropbtn {
		   display: block;
		    width: 100%;
		   text-align: left;
		  }
	}
	</style>
</head>

<body>
	<div class="wrapper">
		<div class="top-bar clearfix">
			<div class="top-bar-links">
				<ul>
					<li><a href="register/">Register</a></li>
					<li><a href="login/">Log In</a></li>
				</ul>
			</div><!--top-bar-links-->

		<!--	<div class="site-search">
				<form method="get" action="index.html">
					<input type="search" name="search-box">
					<button type="submit"></button>
				</form>
				<img src="assets/images/user.png" width="30px">
			</div>	site-search-->
		</div><!--top-bar-->

		<header class="clearfix">
			<div class="logo">
				<h1>Saubagya</h1>
				<p>Krushi Niyamaka</p>
			</div><!--logo-->
			
			<div class="slideshow-container">

			<div class="mySlides fade">
				<img src="assets/images/kkkkkk.jpg">
			</div>

			<div class="mySlides fade">
				<img src="assets/images/newagro1.png">
			</div>

			<br>

			<div style="text-align:center">
				<span class="dot"></span> 
				<span class="dot"></span> 
			</div>

</div>
			
			<!--<div class="logoimage">
				<img src="assets/images/kkkkkk.jpg" alt="Agrarian image">
			</div>-->

			<div>
				<img src="assets/images/gov_logo.png" width="80px" style="margin-left: 50px; margin-top: 10px;">
			</div><!--socialmedia-->
		</header>

		<!-- <nav>
			<ul>
				<li><a href="#">Home</a></li>
				<li><a href="#">About Us</a></li>
				<li><a href="#">Services</a></li>
				<li><a href="#">Division</a></li>
				<li><a href="#">Contact Us</a></li>
			</ul>
		</nav> -->

		<div class="topnav" id="myTopnav">
		  <a href="#" id="tab_home" class="active" style="width: 140px;">Home</a>
		  <a href="#" id="tab_about" style="width: 140px;">About us</a>
		  <a href="#" id="tab_division" style="width: 140px;">Service Centers</a>
		  <div class="dropdown">
		    <button class="dropbtn" style="width: 140px;">Services 
		      <i class="fa fa-caret-down"></i>
		    </button>
		    <div class="dropdown-content">
		      <a href="assets/home_tabs/statistical_data.php" style="font-family: sans-serif;">Statistical Data</a>
		      <!-- <a href="#" style="font-family: sans-serif;">Cultivated Reports</a>
		      <a href="#" style="font-family: sans-serif;">Cultivation Approvals</a> -->
		    </div>
		  </div>
		  <a href="#" id="tab_notice" style="width: 140px;">Notices</a>
		  <a href="#" id="tab_contact" style="width: 140px;">Contact us</a>
		</div> 

		<div id="main_container">
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
			<div id="chartContainer" style="height: 320px; width: 400px; margin-top: 50px; margin-bottom: 50px; float: left;"></div>
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
		</div>

		<footer>
			<div class="footercol">
				<h4>Department of Agrarian Services</h4>
				<div class="blockpost">
					<div>
						<img src="assets/images/das.jpg" width="200">
					</div>
				</div><!--blockpost-->
			</div><!--footercol-->
			
			<div class="footercol">
				<h4>Quick Navigation</h4>
				<ul class="links">
					<li><a href="#">Home</a></li>
					<li><a href="#">Services</a></li>
					<li><a href="#">Division</a></li>
					<li><a href="#">Contact Us</a></li>
				</ul>
			</div><!--footercol-->

			<div class="footercol">
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

			<div class="footercol">
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
		</footer>

		<div class="copyrights">
			<div class="left">
				Copyrights &copy; 2021. All Rights Reserved. 
			</div><!--left-->

			<div class="right">
				Website by: CST Project Group 8
			</div><!--right-->
		</div><!--copyrights-->

	</div><!--wrapper-->
	
	<script>
		$(document).ready(function() {
			$("#search").keyup(function() {
				var keyword = $("#search").val();

				$.get("assets/get-data.php?search=" + keyword, function(data, status) {
					$("#result").html(data);
				});
			});
		});
	</script>

	<script>
		var slideIndex = 0;
		showSlides();

		function showSlides() {
		  var i;
		  var slides = document.getElementsByClassName("mySlides");
		  var dots = document.getElementsByClassName("dot");
		  for (i = 0; i < slides.length; i++) {
		    slides[i].style.display = "none";  
		  }
		  slideIndex++;
		  if (slideIndex > slides.length) {slideIndex = 1}    
		  for (i = 0; i < dots.length; i++) {
		    dots[i].className = dots[i].className.replace(" active", "");
		  }
		  slides[slideIndex-1].style.display = "block";  
		  dots[slideIndex-1].className += " active";
		  setTimeout(showSlides, 2000); // Change image every 2 seconds
		}


		$(document).ready(function() {
			$("a#tab_home").click(function() {
		        $.get("assets/home_tabs/home.php", function(data, status) {
		          $("#main_container").html(data);
		        });

		        $("a#tab_about").removeClass('active');
		        $("a#tab_contact").removeClass('active');
		        $("a#tab_division").removeClass('active');
		        $("a#tab_home").toggleClass('active');
		        $("a#tab_notice").removeClass('active');
		    });

			$("a#tab_about").click(function() {
		        $.get("assets/home_tabs/about_us.php", function(data, status) {
		          $("#main_container").html(data);
		        });

		        $("a#tab_home").removeClass('active');
		        $("a#tab_contact").removeClass('active');
		        $("a#tab_division").removeClass('active');
		        $("a#tab_about").toggleClass('active');
		        $("a#tab_notice").removeClass('active');
		    });

		    $("a#tab_contact").click(function() {
		        $.get("assets/home_tabs/contact_us.php", function(data, status) {
		          $("#main_container").html(data);
		        });

		        $("a#tab_home").removeClass('active');
		        $("a#tab_division").removeClass('active');
		        $("a#tab_about").removeClass('active');
		        $("a#tab_contact").toggleClass('active');
		        $("a#tab_notice").removeClass('active');
		    });

		    $("a#tab_notice").click(function() {
		        $.get("assets/home_tabs/notice.php", function(data, status) {
		          $("#main_container").html(data);
		        });

		        $("a#tab_home").removeClass('active');
		        $("a#tab_division").removeClass('active');
		        $("a#tab_about").removeClass('active');
		        $("a#tab_contact").removeClass('active');
		        $("a#tab_notice").toggleClass('active');
		    });

		    $("a#tab_division").click(function() {
		        $.get("assets/home_tabs/division.php", function(data, status) {
		          $("#main_container").html(data);
		        });

		        $("a#tab_home").removeClass('active');
		        $("a#tab_division").toggleClass('active');
		        $("a#tab_about").removeClass('active');
		        $("a#tab_contact").removeClass('active');
		        $("a#tab_notice").removeClass('active');
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
	<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>