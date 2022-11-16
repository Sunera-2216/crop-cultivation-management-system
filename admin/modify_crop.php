<?php session_start(); ?>
<?php require_once('../include/connection.php'); ?>
<?php require_once('../include/function.php'); ?>
<?php 


if (!isset($_SESSION['das_username'])) {
    header('location: index.php');
  }

	$errors  = array();
	$crop_id = '';
	$crop_name = '';
	$crop_land_size = '';
	$crop_cultivation_start_date = '';
	$crop_cultivation_end_date = '';
	$image = '';


	if (isset($_GET['crop_id'])) {
		//getting the user information
		$crop_id = mysqli_real_escape_string($connection, $_GET['crop_id']);
		$query = "SELECT * FROM crop_details WHERE crop_id = {$crop_id} LIMIT 1";

		$result_set = mysqli_query($connection, $query);

		if ($result_set) {
			if (mysqli_num_rows($result_set) == 1) {
				//user found
				$result = mysqli_fetch_assoc($result_set);
				$crop_id = $result['crop_id'];
				$crop_name = $result['crop_name'];
				$crop_land_size = $result['crop_land_size'];
				$crop_cultivation_start_date = $result['crop_cultivation_start_date'];
				$crop_cultivation_end_date = $result['crop_cultivation_end_date'];
				$image = $result['image'];
				
			}else {
				//user not found
				header('Location: add_crop_details.php.php?err=crop_not_found');
			}
		}else {
			//query unsuccessful
			header('Location: add_crop_details.php?err=query_failed');
		}
	}


	if (isset($_POST['submit'])) {

		$file_name = $image;

		$file_name = $_FILES['image']['name'];
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];
        $temp_name = $_FILES['image']['tmp_name'];

		$crop_id    = $_POST['crop_id'];
		$crop_name  = $_POST['crop_name'];
		$crop_land_size  = $_POST['crop_land_size'];
		$crop_cultivation_start_date = $_POST['crop_cultivation_start_date'];
		$crop_cultivation_end_date   = $_POST['crop_cultivation_end_date'];

		$upload_to = 'images/' . $file_name;

        if ($file_uploaded = move_uploaded_file($temp_name, $upload_to)) {
            $img = "Image data insert successfuly.." ;
        } else {
            echo "Image Uploding fail...";
        }

		//checking required fields(empty fields)
		$req_fields = array('crop_id','crop_name','crop_land_size','crop_cultivation_start_date', 'crop_cultivation_end_date');

		foreach ($req_fields as  $field) {
			if (empty(trim($_POST[$field]))) {
			$errors[] = 'Please enter your ' . $field;

			}
		}

		//checking max length
		$max_len_fields = array('crop_id' =>10,'crop_name' =>20);

		foreach ($max_len_fields as  $field => $max_len) {
			if (strlen(trim($_POST[$field])) > $max_len) {
			$errors[] = $field . 'must be less than ' . $max_len . 'characters.';

			}
		}

		if (empty($errors)) {

			$query = "UPDATE crop_details SET crop_id = '{$crop_id}', crop_name = '{$crop_name}', crop_land_size = '{$crop_land_size}', crop_cultivation_start_date = '{$crop_cultivation_start_date}', crop_cultivation_end_date = '{$crop_cultivation_end_date}' , image = '{$file_name}' WHERE crop_id = '{$crop_id}' LIMIT 1";

			$result = mysqli_query($connection, $query);

			if ($result) {
				//query successfull
				header('Location: add_crop_details.php?user_modified=ture');
			}else{
				$errors[] = 'Failed to modify the recode.';
			}
		}
	}

 ?>



<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>View / Modify Crop</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/modify_crop.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
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
	<main>
		<h2>View / Modify Crop</h2>

		<?php 

		if (!empty($errors)) {
			echo '<div class="errmsg">';
			echo "<b>There were error(s) on your form.</b><br>";
			foreach ($errors as $error) {
				$error = ucfirst(str_replace("_", " ", $error));
				echo $error . '<br>';
			}
			echo '</div>';
		}

		 ?>
		<div class="col-md-6"style="margin-top: 10px;">
			<form action="modify_crop.php" method="POST" class="form-group" enctype="multipart/form-data">
			<input type="hidden" name="crop_id" value="<?php echo $crop_id ?>">
			<p>
				<label for="">Crop ID:</label>
				<input type="text" name="crop_name" placeholder="Crop ID" class="form-control" <?php echo 'value ="' . $crop_id . '"'; ?>>
			</p>

			<p>
				<label for="">Crop Name:</label>
				<input type="text" name="crop_name" placeholder="Crop Name" class="form-control" <?php echo 'value ="' . $crop_name . '"'; ?>>
			</p>

			<p>
				<label for="">Crop Land Size:</label>
				<input type="text" name="crop_land_size" placeholder="Crop Land Size" class="form-control" <?php echo 'value ="' . $crop_land_size . '"'; ?>>
			</p>

			<p>
				<label for="">Crop Cultivation Start Date:</label>
				<input type="date" name="crop_cultivation_start_date" placeholder="Crop Cultivation Start Date" class="form-control" <?php echo 'value ="' . $crop_cultivation_start_date . '"'; ?>>
			</p>
			
			<p>
				<label for="">Crop Cultivation End Date:</label>
				<input type="date" name="crop_cultivation_end_date" placeholder="Crop Cultivation End Date"  class="form-control" <?php echo 'value ="' . $crop_cultivation_end_date . '"'; ?>>
			</p>

			<p>
				<label for="">Change Picture:</label>
				<input type="file" name="image" <?php echo 'value ="' . $image . '"'; ?>>
			</p>

			<p>
				<label for="">&nbsp;</label>
				<button type="submit" name="submit">Save</button>
			</p>
			
		</form>
		</div>
		
		
	</main>
	

</body>
</html>


<?php mysqli_close($connection); ?>