<?php
  session_start();
  require_once('../include/connection.php');

  if (!isset($_SESSION['das_username'])) {
    header('location: index.php');
  }

  // Get number of provinces
  $province_count = 0;
  $res_province = mysqli_query($connection, "SELECT COUNT(*) FROM province_details");
  if ($res_province) {
    $province_count = mysqli_fetch_assoc($res_province)['COUNT(*)'];
  }

  // Get number of districts
  $district_count = 0;
  $res_district = mysqli_query($connection, "SELECT COUNT(*) FROM district_details");
  if ($res_district) {
    $district_count = mysqli_fetch_assoc($res_district)['COUNT(*)'];
  }

  // Get number of ASCs
  $asc_count = 0;
  $res_asc = mysqli_query($connection, "SELECT COUNT(*) FROM asc_details");
  if ($res_asc) {
    $asc_count = mysqli_fetch_assoc($res_asc)['COUNT(*)'];
  }

  // Get number of farmers
  $farmer_count = 0;
  $res_farmer = mysqli_query($connection, "SELECT COUNT(*) FROM farmer_details");
  if ($res_farmer) {
    $farmer_count = mysqli_fetch_assoc($res_farmer)['COUNT(*)'];
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

<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <title>Dashboard</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous"/>
  <link rel="stylesheet" href="css/admin_dashboard.css" />

  <script defer src="js/script.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" referrerpolicy="no-referrer"></script>
</head>

<body id="body">
  <div class="container">
    <nav class="navbar"  >
      <div class="nav_icon" onclick="">
        <i class="fa fa-bars" style="color: #333333" aria-hidden="true"></i>
      </div>
      <div>
        <button style="border-radius: 5px;width: 100px;height: 40px;font-weight: bold;font-size: 14px;border: none;background-color: #7AAD37;"><a href="logout.php" style="text-decoration: none;color: #FFF;">Log Out</a></button>
      </div>
    </nav>

    <main>
      <div id="main_container" class="main__container">
           
        <h1>Dashboard</h1>

        <div class="main__cards">
          <div class="card">
            <i
              class="fa fa-area-chart fa-2x text-green" aria-hidden="true"></i>
            <div class="card_inner">
              <p class="text-primary-p">Number of Provinces</p>
              <span class="font-bold text-title"><?php echo $province_count; ?></span>
            </div>
          </div>

          <div class="card">
            <i class="fa fa-building fa-2x text-green" aria-hidden="true"></i>
            <div class="card_inner">
              <p class="text-primary-p">Number of Districts</p>
              <span class="font-bold text-title"><?php echo $district_count; ?></span>
            </div>
          </div>

          <div class="card">
            <i class="fa fa-building fa-2x text-green" aria-hidden="true"></i>
            <div class="card_inner">
              <p class="text-primary-p">Number of Agrarian Services Centers</p>
              <span class="font-bold text-title"><?php echo $asc_count; ?></span>
            </div>
          </div>

          <div class="card">
            <i class="fa fa-user fa-2x text-green" aria-hidden="true"></i>
            <div class="card_inner">
              <p class="text-primary-p">Number of Registered Farmers</p>
              <span class="font-bold text-title"><?php echo $farmer_count; ?></span>
            </div>
          </div>
        </div>
        <!-- MAIN CARDS ENDS  -->

        <!-- ACTIONS -->
        <div class="charts">
          <div class="charts__left">
            <div class="charts__left__title">
              <div>
                <h1 style="color: #2e4a66">Daily Reports</h1>
              </div>
            </div>
            <div id="chartContainer" style="height: 370px; width: 100%; margin-top: 50px;"></div>
            <div id="barChartContainer1" style="height: 370px; width: 100%; margin-top: 120px; margin-right: 50px;"></div>
            <!-- <div id="barChartContainer2" style="height: 370px; width: 100%; margin-top: 120px; margin-right: 50px;"></div> -->
            <div id="apex1"></div>
          </div>

          <div class="charts__right">
            <div class="charts__right__title">
              <div>
                <h1>Actions</h1>
              </div>
            </div>

            <div class="charts__right__cards">
              <div class="card1">
                <a href="add_crop_details.php" class="button">Manage Crops</a>
              </div>

              <div class="card2">
                <a href="publish_notices.php" class="button">Publish Notices</a>
              </div>

              <div class="card3">
                <a href="report/report.php" target="_blank" class="button">Generate Report</a>
              </div>

              <div class="card4">
                <a id="main_tile" href="" class="button">Registered Farmers</a>
              </div>

            </div>
          </div>
        </div>
        <!-- CHARTS ENDS  -->
      </div>
    </main>

    <div id="sidebar">
      <div class="sidebar__title">
        <h1>DEPARTMENT OF AGRARIAN SERVICES</h1>
        <i onclick="closeSidebar()" class="fa fa-times" id="sidebarIcon" aria-hidden="true"></i>
      </div>

      <div class="sidebar__menu">
        <div id="dashboard_div" class="sidebar__link active_menu_link">
          <i class="fa fa-home"></i>
          <a id="dashboard" href="#">Dashboard</a>
        </div>

        <div id="asc_div" class="sidebar__link">
          <i class="fa fa-building-o"></i>
          <a id="reg_asc" href="#">Agrarian Services Centers </a>
        </div>

        <div id="farmer_div" class="sidebar__link">
          <i class="fa fa-building-o"></i>
          <a id="reg_farmers" href="#">Registered Farmers</a>
        </div>

      </div>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function() {
      $("a#reg_asc").click(function() {
        $.get("tabs/get-center-data.php", function(data, status) {
          $("#main_container").html(data);
        });

        $("div#dashboard_div").removeClass('active_menu_link');
        $("div#farmer_div").removeClass('active_menu_link');
        $("div#asc_div").toggleClass('active_menu_link');
      });

      $("a#dashboard").click(function() {
        $("#main_container").load('tabs/super-dashboard.html');

        $("div#asc_div").removeClass('active_menu_link');
        $("div#dashboard_div").toggleClass('active_menu_link');
        $("div#farmer_div").removeClass('active_menu_link');
      });

      $("a#reg_farmers").click(function() {
        $("#main_container").load('tabs/get-all-farmer-data.php');

        $("div#asc_div").removeClass('active_menu_link');
        $("div#dashboard_div").removeClass('active_menu_link');
        $("div#farmer_div").toggleClass('active_menu_link');
      });

      $("a#main_tile").click(function(e) {
        e.preventDefault();
        $("#main_container").load('tabs/get-all-farmer-data.php');

        $("div#asc_div").removeClass('active_menu_link');
        $("div#dashboard_div").removeClass('active_menu_link');
        $("div#farmer_div").toggleClass('active_menu_link');
      });


      // Pie chart
      var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        title: {
          text: "Extent of land by cultivated crop"
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

      var chart = new CanvasJS.Chart("barChartContainer1", {
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
