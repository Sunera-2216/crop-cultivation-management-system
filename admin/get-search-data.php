<?php 
	require_once('../include/connection.php'); 
	require_once('../include/function.php');

	// Get search data
    if (isset($_GET['search'])) {
        $out = '';

        $search_s = mysqli_real_escape_string($connection, $_GET['search']);

        $sres = mysqli_query($connection, "SELECT * FROM crop_details WHERE crop_name LIKE '%{$search_s}%' AND is_deleted = 0");

        if (mysqli_num_rows($sres) > 0) {
        
        $out .= '
                    <table>
                                <tr>
                                    <th style="width: 160px;">Images</th>
                                    <th style="width: 50px;">Crop ID</th>
                                    <th style="width: 80px;">Crop Name</th>
                                    <th style="width: 70px;">Land Size</th>
                                    <th style="width: 140px;">Cultivation Start Date</th>
                                    <th style="width: 130px;">Cultivation End Date</th>
                                    <th style="width: 70px;">Edit</th>
                                    <th style="width: 90px;">Delete</th>
                                </tr>
                ';

        while ($scrops = mysqli_fetch_assoc($sres)) {
            //$out .= "<table class=table table-bordered>";
            $out .= "<tr>";
            $out .= "<td><img src=\"images/{$scrops['image']}\" style=\"height:100px; width:100px;\"></td>";
            $out .= "<td>{$scrops['crop_id']}</td>";
            $out .= "<td>{$scrops['crop_name']}</td>";
            $out .= "<td>{$scrops['crop_land_size']}</td>";
            $out .= "<td>{$scrops['crop_cultivation_start_date']}</td>";
            $out .= "<td>{$scrops['crop_cultivation_end_date']}</td>";
            $out .= "<td><a href=\"modify_crop.php?crop_id={$scrops['crop_id']}\">Edit</a></td>";
            $out .= "<td><a href=\"delete_crop.php?crop_id={$scrops['crop_id']}\" onclick = \"return confirm ('Are you sure?');\">Delete</a></td>";
            $out .= "</tr>";
            
        }
        $out .= "</table>";
        echo $out;

        }else {
        	echo "No result found.";
        }
    }
?>