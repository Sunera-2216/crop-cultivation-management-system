<?php session_start(); ?>
<?php require_once('../include/connection.php'); ?>
<?php require_once('../include/function.php');

if (!isset($_SESSION['das_username'])) {
    header('location: index.php');
  }

    if(isset($_POST['add'])){

        $file_name = $_FILES['image']['name'];
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];
        $temp_name = $_FILES['image']['tmp_name'];

        $crop_id = $_POST['crop_id'];
        $crop_name = $_POST['crop_name'];
        $crop_land_size =$_POST['crop_land_size'];
        $crop_cultivation_start_date = $_POST['crop_cultivation_start_date'];
        $crop_cultivation_end_date = $_POST['crop_cultivation_end_date'];

        $upload_to = 'images/' . $file_name;

        if ($file_uploaded = move_uploaded_file($temp_name, $upload_to)) {
            $img = "Image data insert successfuly.." ;
        } else {
            echo "Image Uploding fail...";
        }

        $today = date("Y-m-d");

        if ($crop_cultivation_start_date < $today) {
            echo "<script>
                    alert('Incorrect start date. Select future date.');
                    window.location.href='add_crop_details.php';
                    </script>";
            return;
        }

        if ($crop_cultivation_start_date > $crop_cultivation_end_date) {
            echo "<script>
                    alert('Incorrect end date. Select date after start date.');
                    window.location.href='add_crop_details.php';
                    </script>";
            return;
        }

        $query = "INSERT INTO crop_details(crop_id,crop_name, crop_land_size, crop_cultivation_start_date, crop_cultivation_end_date, image, is_deleted) VALUES ('{$crop_id}','{$crop_name}',{$crop_land_size},'{$crop_cultivation_start_date}','{$crop_cultivation_end_date}', '{$file_name}', 0)" ;
        $result = mysqli_query($connection,$query);

        if($result){
            echo "<script>
                        alert('1 Crop Added.');
                        window.location.href = 'add_crop_details.php';
                    </script>";

        }else{
             echo "<script>alert('Failed.')</script>";
        }
    }

    //getting the list of crops
    $output = '';

    //getting the list of users
    $query = "SELECT * FROM crop_details WHERE is_deleted = 0";
    $result = mysqli_query($connection, $query); 

    verify_query ($result);
        while ($crops = mysqli_fetch_assoc($result)) {
            $output .= "<table class=table table-bordered>";
            $output .= "<tr>";
            $output .= "<td><img src=\"images/{$crops['image']}\" style=\"height:100px; width:100px;\"></td>";
            $output .= "<td>{$crops['crop_id']}</td>";
            $output .= "<td>{$crops['crop_name']}</td>";
            $output .= "<td>{$crops['crop_land_size']}</td>";
            $output .= "<td>{$crops['crop_cultivation_start_date']}</td>";
            $output .= "<td>{$crops['crop_cultivation_end_date']}</td>";
            $output .= "<td><a href=\"modify_crop.php?crop_id={$crops['crop_id']}\">Edit</a></td>";
            $output .= "<td><a href=\"delete_crop.php?crop_id={$crops['crop_id']}\" onclick = \"return confirm ('Are you sure?');\">Delete</a></td>";
            $output .= "</tr>";
            $output .= "</table>";
        }
    $_SESSION['output'] = $output;

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Crop Details</title>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/add_crop_details.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body >
    <header>
        <section>
            <div class="row">
                <div class="col-md-12" >
                    <nav>
                        <div class="navbar">
                            <ul class="ul-nav">
                                <li>
                                    <a href="admin_dashboard.php" >Admin</a>
                                </li>
                                <li>
                                    <a  href="add_crop_details.php" >Add Crop Details</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </section>
    </header>
    
    <section>
        <div class="container-fluid" style="margin: 20px;">
            <div class="row">
                <div class="col-md-7">
                    <div style="margin-top: 30px;">
                        <h2>Added Crops</h2>
                        <div class="search-bar clearfix" style="width: 700px; height: 50px; background: orange; padding: 3px 5px 3px 10px;">
                        <div class="top-bar-links">
                            <form action="" method="GET">
                                <input type="text" name="search" id="search" placeholder="Search crop" style="margin-left: 10px; margin-top: 2.5px; border-radius: 10px;">
                            </form>
                        </div>
                        </div>
                        <div id="result" class="table-responsive">
                            <table>
                                <tr>
                                    <th style="width: 160px;">Images</th>
                                    <th style="width: 50px;">Crop ID</th>
                                    <th style="width: 80px;">Crop Name</th>
                                    <th style="width: 70px;">Land Size (Acres)</th>
                                    <th style="width: 140px;">Cultivation Start Date</th>
                                    <th style="width: 130px;">Cultivation End Date</th>
                                    <th style="width: 70px;">Edit</th>
                                    <th style="width: 90px;">Delete</th>
                                </tr>
                                
                                <?php echo $output; ?>
                            </table>  
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="main" style="border-left: 1px solid #888;">
                        <form action="add_crop_details.php" method="post" class="form-group" enctype="multipart/form-data">
                            <h2>Add Crop Details</h2>
                                <label for="">Enter the Crop Id:</label>
                                <input type="text" name="crop_id" id="id" placeholder="Crop Id" class="form-control" required>
                            
                                <label for="">Enter the Crop Name</label>
                                <input type="text" name="crop_name" id="name" placeholder="Crop Name" class="form-control" required>
                            
                                <label for="">Enter the Land Size</label>
                                <input type="text" name="crop_land_size" id="size" placeholder="Land Size" class="form-control" required>
                            
                                <label for="">Enter the Crop Cultivation Start Date</label>
                                <input type="Date" name="crop_cultivation_start_date" id="startdate" class="form-control" placeholder="Starting Date" required>
                        
                                <label for="">Enter the Crop Cultivation End Date</label>
                                <input type="Date" name="crop_cultivation_end_date" id="enddate" class="form-control" placeholder="Ending Date" required>

                                <label for="">Choose Crop Picture</label>
                                <input type="file" name="image" id="image" required>
                            
                                <button type="submit" name="add" id="add">Add</button>
                        </form> 
                    </div>        
                </div>
            </div>
        </div>
    </section>
    
    <script>
        $(document).ready(function() {
            $("#search").keyup(function() {
                var keyword = $("#search").val();

                $.get("get-search-data.php?search=" + keyword, function(data, status) {
                    $("#result").html(data);
                });
            });
        });
    </script>

</body>
</html>

<?php mysqli_close($connection); ?>