<?php 
	session_start();
	require_once('../include/connection.php');

	if (!isset($_SESSION['asc_id'])) {
    	header('location: index.php');
  	}

  	$asc_id = $_SESSION['asc_id'];

  
  if (isset($_POST['view'])) {
 //  	if($_POST["view"] != '')
	// {
	//    $update_query = "UPDATE request_details SET request_status = 0 WHERE hospital_id = '{$hospital_id}' AND notification_status = 1";
	//    mysqli_query($connection, $update_query);
	// }

	$query = "SELECT request_crop_id, request_farmer_nic, request_land_size FROM request_details WHERE request_id IN (SELECT request_id FROM asc_request_details WHERE asc_id = '{$asc_id}') AND request_status = -1";
	$result = mysqli_query($connection, $query);

	$output = '';
	$count = 0;

	if(mysqli_num_rows($result) > 0) {
		$count = mysqli_num_rows($result);

		// while($row = mysqli_fetch_array($result)) {
		//   $output .= '
		//   <li>
		//   <a href="#">
		//   <strong>'.$row["request_farmer_nic"].'</strong>
		//   <small>'.$row["request_crop_id"].'</small>
		//   <small>'.$row["request_land_size"].'</small>
		//   </a>
		//   </li>
		//   ';
		// }
	} else {
	    $output .= '<li><a href="#" class="text-bold text-italic">No New Requests.</a></li>';
	}

	$data = array(
	   //'notification' => $output,
	   'unseen_notification'  => $count
	);
	echo json_encode($data);
  }

?>
