<?php session_start(); ?>
<?php require_once('../include/connection.php'); ?>
<?php require_once('../include/function.php'); ?>
<?php 

//checking if a user is logged in
	if (!isset($_SESSION['das_username'])) {
    header('location: index.php');
  }

	if (isset($_GET['crop_id'])) {
		//delete the crop
		$crop_id = mysqli_real_escape_string($connection, $_GET['crop_id']);
		$query = "UPDATE crop_details SET is_deleted = 1 WHERE crop_id = {$crop_id}";

		$result = mysqli_query($connection, $query);

		verify_query ($result);
		if ($result) {
				//crop deleted
				header('Location: add_crop_details.php?msg=crop_deleted');
		}else {
				header('Location: add_crop_details.php?err=crop_delete_failed');
		}
	}
		
 	
?>

<?php mysqli_close($connection); ?>
