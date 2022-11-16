<?php
	session_start();
	require_once('../../include/connection.php');
	require_once('../../include/function.php');


	$logged_asc_id= $_SESSION['asc_id'];

	$query = "SELECT * FROM farmer_details WHERE farmer_nic IN (SELECT farmer_nic FROM farmer_asc_details WHERE asc_id = '{$logged_asc_id}')";
	$result = mysqli_query($connection, $query);

	$output = '';

	if (mysqli_num_rows($result) > 0) {
		$output .= '<div class="table-responsive">
						<table class=table table-bordered>
							<tr>
								<th style="width: 80px;">NIC</th>
								<th style="width: 200px;">First Name</th>
								<th style="width: 170px;">Last Name</th>
								<th style="width: 250px;">Address</th>
								<th style="width: 200px;">Contact Number</th>
							</tr>
							</table>
							</div>';
		while ($farmer_details = mysqli_fetch_array($result)) {
			$output .= '
					<table class=table table-bordered>
					<tr>
						<td style="width: 150px;">'.$farmer_details['farmer_nic'].'</td>
						<td style="width: 200px;">'.$farmer_details['farmer_first_name'].'</td>
						<td style="width: 200px;">'.$farmer_details['farmer_last_name'].'</td>
			            <td style="width: 200px;">'.$farmer_details['farmer_address'].'</td>
						<td style="width: 200px;">'.$farmer_details['farmer_contact_no'].'</td>
			         </tr>
			         </table>';
			}

			echo $output;

		}else {
			echo "No Result Found";
		}

	mysqli_close($connection);

?>