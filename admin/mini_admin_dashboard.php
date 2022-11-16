<?php
  session_start();
  require_once('../include/connection.php');

  if (!isset($_SESSION['das_username'])) {
    header('location: index.php');
  }

  if (isset($_POST['approveBtn'])) {
    $request_id = $_POST['approveBtn'];

    $update_query = "UPDATE request_details SET request_status = 1 WHERE request_id = '{$request_id}'";
    $result = mysqli_query($connection, $update_query);

    if ($result) {
      echo "<script>alert('Approved.')</script>";
    }else {
      echo "<script>Error: </script>" . mysqli_error($connection);
    }
  }

  if (isset($_POST['rejectBtn'])) {
    $request_id = $_POST['rejectBtn'];

    $update_query = "UPDATE request_details SET request_status = 0 WHERE request_id = '{$request_id}'";
    $result = mysqli_query($connection, $update_query);

    if ($result) {
      echo "<script>alert('Rejected.')</script>";
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

<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <title>Dashboard</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous"/>
  <link rel="stylesheet" href="css/mini_admin_dashboard.css" />

  <script defer src="js/script.js"></script>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" referrerpolicy="no-referrer"></script>
  <script type="text/javascript" src="js/notification.js"></script>
</head>

<body id="body">
  <div class="container">
    <nav class="navbar">
      <div class="nav_icon" onclick="">
        <i class="fa fa-bars" style="color: #333333" aria-hidden="true"></i>
      </div>
      <div>
        <div>
          <img src="img/icon-bell.png" width="30px">
            <span class="label count" style="border: none; border-radius:50%; background-color: red; color: white; width: 20px; height: 20px; display: inline-block; text-align: center;padding: 3px;">0</span>
              <!-- <span class="glyphicon glyphicon-bell" style="font-size:18px;"></span> -->
            <ul></ul>
        </div>
            
      </div>
      <div>
        <button style="border-radius: 5px;width: 100px;height: 40px;font-weight: bold;font-size: 14px;border: none;background-color: #800000;"><a href="logout.php" style="text-decoration: none;color: #FFF;">Log Out</a></button>
      </div>
    </nav>

    <main>
      <form action="mini_admin_dashboard.php" method="POST" id="req_form" class="form-group">
        <div id="main_container" class="main__container">

          <h1>Dashboard</h1>

          <div class="main__cards">
            <div class="card">
              <i class="fa fa-envelope fa-2x" aria-hidden="true" style="color: #800000"></i>
              <div class="card_inner">
                <p class="text-primary-p">Number of Requests</p>
                <?php 
                  $r = mysqli_query($connection, "SELECT COUNT(*) as count FROM request_details");
                  $res = mysqli_fetch_assoc($r);
                ?>
                <span class="font-bold text-title"><?php echo $res['count']; ?></span>
              </div>
            </div>

            <div class="card">
              <i class="fa fa-check fa-2x" aria-hidden="true" style="color: #800000"></i>
              <div class="card_inner">
                <p class="text-primary-p">Approved Requests</p>
                <?php 
                  $rr = mysqli_query($connection, "SELECT COUNT(*) as count FROM request_details WHERE request_status = 1");
                  $rres = mysqli_fetch_assoc($rr);
                ?>
                <span class="font-bold text-title"><?php echo $rres['count']; ?></span>
              </div>
            </div>

            <div class="card">
              <i class="fa fa-times fa-2x" aria-hidden="true" style="color: #800000"></i>
              <div class="card_inner">
                <p class="text-primary-p">rejected Requests</p>
                <?php 
                  $rrr = mysqli_query($connection, "SELECT COUNT(*) as count FROM request_details WHERE request_status = 0");
                  $rrres = mysqli_fetch_assoc($rrr);
                ?>
                <span class="font-bold text-title"><?php echo $rrres['count']; ?></span>
              </div>
            </div>

            <div class="card">
              <i class="fa fa-user fa-2x" aria-hidden="true" style="color: #800000"></i>
              <div class="card_inner">
                <p class="text-primary-p">Number of Registered Farmers</p>
                <?php 
                  $fr = mysqli_query($connection, "SELECT COUNT(*) as count FROM farmer_details");
                  $frr = mysqli_fetch_assoc($fr);
                ?>
                <span class="font-bold text-title"><?php echo $frr['count']; ?></span>
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
                  <a class="button" id="approve_request">Request Approval</a>
                </div>

                <div class="card2">
                  <a id="mini_tile" class="button">Registered Farmers</a>
                </div>
  			  
              </div>
            </div>
          </div>
          <!-- CHARTS ENDS  -->
        </div>
      </form>
    </main>

    <div id="sidebar">
      <div class="sidebar__title">
        <h1>AGRARIAN SERVICES CENTER</h1>
        <i onclick="closeSidebar()" class="fa fa-times" id="sidebarIcon" aria-hidden="true"></i>
      </div>

      <div class="sidebar__menu">
        <div id="dashboard_div" class="sidebar__link active_menu_link">
          <i class="fa fa-home"></i>
          <a id="dashboard" href="#">Dashboard</a>
        </div>
<!--       
        <div class="sidebar__link">
          <i class="fa fa-user" aria-hidden="true"></i>
          <a href="#">Profile (<?php //echo $_SESSION['das_username']; ?>)</a>
        </div> -->

        <div id="farmer_div" class="sidebar__link">
          <i class="fa fa-building-o"></i>
          <a id="reg_farmers" href="#">Registered Farmers</a>
        </div>

        <div id="request_div" class="sidebar__link">
          <i class="fa fa-building-o"></i>
          <a id="farmers_requests" href="#">Requests</a>
        </div>

      </div>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function() {
      $("a#reg_farmers").click(function() {
        $.get("tabs/get-farmer-data.php", function(data, status) {
          $("#main_container").html(data);
        });

        $("div#dashboard_div").removeClass('active_menu_link');
        $("div#request_div").removeClass('active_menu_link');
        $("div#farmer_div").toggleClass('active_menu_link');
      });

      $("a#mini_tile").click(function() {
        $.get("tabs/get-farmer-data.php", function(data, status) {
          $("#main_container").html(data);
        });

        $("div#dashboard_div").removeClass('active_menu_link');
        $("div#request_div").removeClass('active_menu_link');
        $("div#farmer_div").toggleClass('active_menu_link');
      });

      $("a#dashboard").click(function() {
        $("#main_container").load('tabs/mini-dashboard.html');

        $("div#farmer_div").removeClass('active_menu_link');
        $("div#request_div").removeClass('active_menu_link');
        $("div#dashboard_div").toggleClass('active_menu_link');
      });

      $("a#farmers_requests").click(function() {
        $.get("tabs/get-request-data.php", function(data, status) {
          $("#req_form").html(data);
        });

        $("div#dashboard_div").removeClass('active_menu_link');
        $("div#farmer_div").removeClass('active_menu_link');
        $("div#request_div").toggleClass('active_menu_link');
      });

      $("a#approve_request").click(function() {
        $.get("tabs/get-request-data.php", function(data, status) {
          $("#req_form").html(data);
        });

        $("div#dashboard_div").removeClass('active_menu_link');
        $("div#farmer_div").removeClass('active_menu_link');
        $("div#request_div").toggleClass('active_menu_link');
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
