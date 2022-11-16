<?php
	session_start();
	require_once('../../include/connection.php');
	require_once('../../include/function.php');


	$logged_asc_id= $_SESSION['asc_id'];

	$query_request = "SELECT * FROM request_details WHERE request_id IN (SELECT request_id FROM asc_request_details WHERE asc_id = '{$logged_asc_id}') AND request_status = -1";
	$result_request = mysqli_query($connection, $query_request);

	$output = '<h2 style="margin: 20px; border-bottom: 1px solid #888">Pending Requests</h2>';

	if (mysqli_num_rows($result_request) > 0) {
		$output .= '<div>
						<table style="margin-left:20px;margin-top:20px;">
							<tr>
								<th style="width: 150px;">Farmer NIC</th>
								<th style="width: 150px;">Farmer Name</th>
								<th style="width: 200px;">Requested Crop Name</th>
								<th style="width: 230px;">Requested Land Size (Acres)</th>
								<th style="width: 120px;color:green;">Filled / Target (Acres)</th>
								<th style="width: 80px;"></th>
								<th style="width: 80px;"></th>
							</tr>
							</table>
							</div>';
		while ($request_details = mysqli_fetch_array($result_request)) {
			// Get farmer name
			$query_farmer = "SELECT farmer_first_name, farmer_last_name FROM farmer_details WHERE farmer_nic = '{$request_details['request_farmer_nic']}'";
			$result_farmer = mysqli_query($connection, $query_farmer);
			$farmer_details = mysqli_fetch_assoc($result_farmer);

			// Get crop name
			$query_crop = "SELECT crop_name, crop_land_size FROM crop_details WHERE crop_id = '{$request_details['request_crop_id']}'";
			$result_crop = mysqli_query($connection, $query_crop);
			$crop_details = mysqli_fetch_assoc($result_crop);

			$filled = mysqli_query($connection, "SELECT SUM(request_land_size) as size FROM request_details WHERE request_status = 1 AND request_crop_id = '{$request_details['request_crop_id']}'");
			$filledsize = mysqli_fetch_assoc($filled)['size'];

			if ($filledsize == '') {
				$filledsize = 0;
			}

			$output .= '
					<table style="margin-left:50px;">
						<tr>
							<td style="width: 170px;">'.$request_details['request_farmer_nic'].'</td>
							<td style="width: 200px;">'.$farmer_details['farmer_first_name'].' '. $farmer_details['farmer_last_name'] .'</td>
							<td style="width: 300px;">'.$crop_details['crop_name'].'</td>
				            <td style="width: 280px;">'.$request_details['request_land_size'].'</td>
				            <td style="width: 200px;color:green;">'.$filledsize.' / '.$crop_details['crop_land_size'].'</td>
							<td style="width: 100px;"><button type="submit" name="approveBtn" value="'.$request_details['request_id'].'" style="background-color: #7AAD37;padding: 10px;color: white;border-radius:10px;border:none;cursor:pointer;">Approve</button></td>
							<td style="width: 100px;"><button type="submit" name="rejectBtn" value="'.$request_details['request_id'].'" style="background-color: #AF0000;padding: 10px;color: white;border-radius:10px;border:none;cursor:pointer;">Reject</button></td>
				         </tr>
			         </table>';
			}

			echo $output;

		}else {
			echo "No Result Found";
		}


		$out = '';
		$out .= '<h2 style="margin: 50px 20px 20px 20px; border-bottom: 1px solid #888">All Requests</h2>';

		$alldata = mysqli_query($connection, "SELECT * FROM request_details WHERE request_id IN (SELECT request_id FROM asc_request_details WHERE asc_id = '{$logged_asc_id}')");

		if (mysqli_num_rows($alldata) > 0) {
		$out .= '<div>
						<table style="margin-left:50px;">
							<tr style="height: 50px;">
								<th style="width: 150px;">Farmer NIC</th>
								<th style="width: 150px;">Farmer Name</th>
								<th style="width: 200px;">Requested Crop Name</th>
								<th style="width: 230px;">Requested Land Size (Acres)</th>
								<th style="width: 200px;">Request Status</th>
							</tr>
						</table>
					</div>';
		while ($allres = mysqli_fetch_array($alldata)) {
			// Get farmer name
			$query_farmer = "SELECT farmer_first_name, farmer_last_name FROM farmer_details WHERE farmer_nic = '{$allres['request_farmer_nic']}'";
			$result_farmer = mysqli_query($connection, $query_farmer);
			$farmer_details = mysqli_fetch_assoc($result_farmer);

			// Get crop name
			$query_crop = "SELECT crop_name FROM crop_details WHERE crop_id = '{$allres['request_crop_id']}'";
			$result_crop = mysqli_query($connection, $query_crop);
			$crop_details = mysqli_fetch_assoc($result_crop);

			$reqStatus;

			if ($allres['request_status'] == 0) {
				$reqStatus = "Rejected";
			}else if ($allres['request_status'] == 1) {
				$reqStatus = "Approved";
			}else {
				$reqStatus = "Pending";
			}

			$out .= '
					<table style="margin-left:80px;">
						<tr style="height: 30px;">
							<td style="width: 150px;">'.$allres['request_farmer_nic'].'</td>
							<td style="width: 200px;">'.$farmer_details['farmer_first_name'].' '. $farmer_details['farmer_last_name'] .'</td>
							<td style="width: 200px;">'.$crop_details['crop_name'].'</td>
				            <td style="width: 200px;">'.$allres['request_land_size'].'</td>
							<td style="width: 200px;">'.$reqStatus.'</td>
				         </tr>
			         </table>';
			}

			echo $out;

		}else {
			echo "No Result Found";
		}

	mysqli_close($connection);

?>