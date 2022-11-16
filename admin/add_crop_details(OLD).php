<?php 
    session_start();
    require_once('../include/connection.php');

    if (!isset($_SESSION['das_username'])) {
        header('location: index.php');
    }

    if(isset($_POST['add'])){
         $crop_id = $_POST['crop_id'];
         $crop_name = $_POST['crop_name'];
         $crop_land_size =$_POST['crop_land_size'];
         $crop_cultivation_start_date =$_POST['crop_cultivation_start_date'];
         $crop_cultivation_end_date = $_POST['crop_cultivation_end_date'];

        $query = "INSERT INTO crop_details(crop_id,crop_name, crop_land_size, crop_cultivation_start_date, crop_cultivation_end_date) VALUES ('{$crop_id}','{$crop_name}','{$crop_land_size}','{$crop_cultivation_start_date}','{$crop_cultivation_end_date}')" ;
        $result = mysqli_query($connection,$query);

        if($result){
            echo "<script>alert('1 Crop Added.')</script>";

        }else{
             echo "<script>alert('Failed.')</script>";
        }
    }
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
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div style="margin-top: 30px;">
                        <h2>Added Crops</h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="main" style="border-left: 1px solid #888;">
                        <form action="add_crop_details.php" method="post" class="form-group">
                            <h2>Add New Crop</h2>
                                <label for="">Crop Id</label>
                                <input type="text" name="crop_id" id="id" placeholder="Crop Id" class="form-control" required>
                            
                                <label for="">Crop Name</label>
                                <input type="text" name="crop_name" id="name" placeholder="Crop Name" class="form-control" required>
                            
                                <label for="">Land Size (in Hectare)</label>
                                <input type="text" name="crop_land_size" id="size" placeholder="Land Size" class="form-control" required>
                            
                                <label for="">Cultivation Start Date</label>
                                <input type="Date" name="crop_cultivation_start_date" id="startdate" class="form-control" placeholder="Starting Date" required>
                        
                                <label for="">Cultivation End Date</label>
                                <input type="Date" name="crop_cultivation_end_date" id="enddate" class="form-control" placeholder="Ending Date" required>
                            
                                <button type="submit" name="add" id="add">Add</button>
                        </form> 
                    </div>        
                </div>
            </div>
        </div>
    </section>
              
</body>
</html>
<?php mysqli_close($connection); ?>