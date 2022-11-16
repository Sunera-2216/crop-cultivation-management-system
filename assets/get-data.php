<?php 
session_start();
require_once('../include/connection.php');
require_once('../include/function.php');


if (isset($_GET['search'])) {
	$search_s = mysqli_real_escape_string($connection, $_GET['search']);

	if ($search_s == '') {
		echo '<table class="table">
							<tr>
								<th style="width: 100px;">Image</th>
								<th style="width: 80px;">Crop Name</th>
								<th style="width: 160px;">Land Size</th>
								<th style="width: 158px;">Cultivation Start Date</th>
								<th style="width: 188px;">Cultivation End Date</th>
							</tr>
							<?php echo $output; ?>
						</table>';
		echo $_SESSION['output'];
		return;
	}

	$query = "SELECT * FROM crop_details WHERE crop_name LIKE '%{$search_s}%'";

	$result = mysqli_query($connection, $query);

	$output = '';

	if (mysqli_num_rows($result) > 0) {
		$output .= '<div class="table-responsive">
						<table class="table">
							<tr>
								<th style="width: 100px;">Image</th>
								<th style="width: 80px;">Crop Name</th>
								<th style="width: 160px;">Land Size</th>
								<th style="width: 158px;">Cultivation Start Date</th>
								<th style="width: 188px;">Cultivation End Date</th>
							</tr>
							</table>
							</div>';
		while ($crop = mysqli_fetch_array($result)) {
			$output .= '
					<table class=table table-bordered>
					<tr>
						<td style="width: 120px;"><img src="../project_01/admin/images/'.$crop['image'].'" style=\'height:50px; width:50px;\'></td>
						<td style="width: 150px;">'.$crop['crop_name'].'</td>
						<td style="width: 100px;">'.$crop['crop_land_size'].'</td>
						<td style="width: 188px;">'.$crop['crop_cultivation_start_date'].'</td>
			            <td style="width: 188px;">'.$crop['crop_cultivation_end_date'].'</td>
			         </tr>
			         </table>';
			}

			echo $output;

		}else {
			echo "No Result Found";
		}
	}

	mysqli_close($connection);

?>