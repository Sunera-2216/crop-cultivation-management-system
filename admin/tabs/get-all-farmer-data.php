<?php
	session_start();
	require_once('../../include/connection.php');
	require_once('../../include/function.php');

	$query = "SELECT * FROM farmer_details";
	$result = mysqli_query($connection, $query);

	$output = '';

	if (mysqli_num_rows($result) > 0) {
		$output .= '<div class="table-responsive">
						<table class=table table-bordered>
							<tr>
								<th style="width: 100px;">NIC</th>
								<th style="width: 150px;">First Name</th>
								<th style="width: 200px;">Last Name</th>
								<th style="width: 250px;">Address</th>
								<th style="width: 150px;">Contact Number</th>
								<th style="width: 150px;">ASC</th>
							</tr>
							</table>
							</div>';
		while ($farmer_details = mysqli_fetch_array($result)) {
			$fascq = mysqli_query($connection, "SELECT asc_id FROM farmer_asc_details WHERE farmer_nic = '{$farmer_details['farmer_nic']}' LIMIT 1");
			$fasc = mysqli_fetch_assoc($fascq)['asc_id'];

			$ascd = mysqli_query($connection, "SELECT asc_name FROM asc_details WHERE asc_id = '{$fasc}' LIMIT 1");
			$ascname = mysqli_fetch_assoc($ascd)['asc_name'];

			$output .= '
					<table class=table table-bordered>
					<tr>
						<td style="width: 150px;">'.$farmer_details['farmer_nic'].'</td>
						<td style="width: 200px;">'.$farmer_details['farmer_first_name'].'</td>
						<td style="width: 200px;">'.$farmer_details['farmer_last_name'].'</td>
			            <td style="width: 200px;">'.$farmer_details['farmer_address'].'</td>
						<td style="width: 150px;">'.$farmer_details['farmer_contact_no'].'</td>
						<td style="width: 150px;">'.$ascname.'</td>
			         </tr>
			         </table>';
			}

			echo $output;

		}else {
			echo "No Result Found";
		}

	mysqli_close($connection);

?>