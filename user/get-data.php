<?php 
session_start();
require_once('../include/connection.php');
require_once('../include/function.php');


if (isset($_GET['search'])) {
	$search_s = mysqli_real_escape_string($connection, $_GET['search']);
	
	$query = "SELECT * FROM crop_details WHERE crop_name LIKE '%{$search_s}%' AND is_deleted = 0";

	$result = mysqli_query($connection, $query);

	$output = '';

	if (mysqli_num_rows($result) > 0) {
		$output .= '<div class="table-responsive">
						<table class=table table-bordered>
							<tr>
								<th>Image</th>
								<th>Crop Name</th>
								<th>Crop Land Size (Acres)</th>
								<th>Cultivation Start Date</th>
								<th>Cultivation End Date</th>
								<th></th>
							</tr>
							<form method="POST" action="home.php">';

		$todate = date("Y-m-d");

		while ($crop = mysqli_fetch_array($result)) {
			if ($crop['crop_cultivation_end_date'] >=  $todate) {
			$output .= '
				<table class=table table-bordered>
				<tr>
					<td><img src="../admin/images/'.$crop['image'].'" style=\'height:50px; width:50px;\'></td>
					<td>'.$crop['crop_name'].'</td>
					<td>'.$crop['crop_land_size'].' acres</td>
					<td>'.$crop['crop_cultivation_start_date'].'</td>
		            <td>'.$crop['crop_cultivation_end_date'].'</td>
					<td><button type="submit" name="selectBtn" class="btn btn-success" value="'.$crop['crop_id'].'">Select</button></td>
		         </tr>
		         </table>';
		    }
		}

		$output .= '</form>
					</table>
					</div>';

		echo $output;

	}else {
		echo "No Result Found";
	}
}

mysqli_close($connection);

?>