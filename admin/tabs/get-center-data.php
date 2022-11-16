<?php
	session_start();
	require_once('../../include/connection.php');
	require_once('../../include/function.php');

	$query = "SELECT * FROM asc_details ORDER BY asc_id";
	$result = mysqli_query($connection, $query);

	$output = '';

	if (mysqli_num_rows($result) > 0) {
		$output .= '<div class="table-responsive">
						<table class=table table-bordered>
							<tr>
								<th style="width: 60px;">Province</th>
								<th style="width: 240px;">District</th>
								<th style="width: 70px;">ASC ID</th>
								<th style="width: 200px;">Name</th>
								<th style="width: 200px;">Email</th>
							</tr>
							</table>
							</div>';
		while ($asc_details = mysqli_fetch_array($result)) {
			// Get district code
			$q_dis = "SELECT district_code FROM asc_district_details WHERE asc_id = '". $asc_details['asc_id'] ."'";
			$res_dis = mysqli_query($connection, $q_dis);
			$des_code = mysqli_fetch_assoc($res_dis);
			// Get district name
			$res_dis_name = mysqli_query($connection, "SELECT district_name FROM district_details WHERE district_code = '". $des_code['district_code'] ."'");
			$des_name = mysqli_fetch_assoc($res_dis_name);

			// Get province code
			$res_prov_code = mysqli_query($connection, "SELECT province_code FROM district_province_details WHERE district_code = '". $des_code['district_code'] ."'");
			$prov_code = mysqli_fetch_assoc($res_prov_code);
			// Get province name
			$res_prov_name = mysqli_query($connection, "SELECT province_name FROM province_details WHERE province_code = '". $prov_code['province_code'] ."'");
			$prov_name = mysqli_fetch_assoc($res_prov_name);

			$output .= '
					<table class=table table-bordered>
					<tr>
						<td style="width: 150px;">'.$prov_name['province_name'].'</td>
						<td style="width: 150px;">'.$des_name['district_name'].'</td>
						<td style="width: 150px;">'.$asc_details['asc_id'].'</td>
						<td style="width: 200px;">'.$asc_details['asc_name'].'</td>
						<td style="width: 200px;">'.$asc_details['asc_email'].'</td>
			         </tr>
			         </table>';
			}

			echo $output;

		}else {
			echo "No Result Found";
		}

	mysqli_close($connection);

?>