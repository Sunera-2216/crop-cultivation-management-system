<?php 
require_once('../../include/connection.php');
require_once('../../include/function.php');

    $asc_centers = '';

 	$query = "SELECT * FROM asc_details ORDER BY asc_id";

    $result = mysqli_query($connection, $query);

    verify_query ($result);

    $district;

    while ($asc = mysqli_fetch_assoc($result)) {
        if (strpos($asc['asc_id'], 'LK-81') !== false) {
            $district = 'Badulla';
        }else if (strpos($asc['asc_id'], 'LK-82') !== false) {
            $district = 'Monaragala';
        }

        $asc_centers .= "<tr>";
        $asc_centers .= "<td style='border-bottom:1px solid #888;padding:10px;width:100px;font-family:sans-serif;'>{$asc['asc_id']}</td>";
        $asc_centers .= "<td style='border-bottom:1px solid #888;padding:10px;width:150px;font-family:sans-serif;'>{$asc['asc_name']}</td>";
        $asc_centers .= "<td style='border-bottom:1px solid #888;padding:10px;font-family:sans-serif;'>{$asc['asc_email']}</td>";
        $asc_centers .= "<td style='border-bottom:1px solid #888;padding:10px;width:150px;font-family:sans-serif;'>{$district}</td>";
        $asc_centers .= "<td style='border-bottom:1px solid #888;padding:10px;width:150px;font-family:sans-serif;'>Uva Province</td>";
        $asc_centers .= "</tr>";
    }
?>

<div>
	<h1 style="text-align: center;margin-bottom: 50px;margin-top: 50px;">Agrarian Services Centers Details</h1>

	<div class="container" style="margin-bottom:100px;">
        <div class="row">
            <div class="col-md-12">
                <div class="main">
                    <table class="masterlist table-responsive" style="margin: 0 auto;">
                    	<tr>
                    		<th style="padding-left: 10px; text-align:left;font-family:sans-serif;">ASC ID</th>
                    		<th style="padding-left: 10px; text-align:left;font-family:sans-serif;">ASC Name</th>
                            <th style="padding-left: 10px; text-align:left;font-family:sans-serif;">ASC Email</th>
                            <th style="padding-left: 10px; text-align:left;font-family:sans-serif;">District</th>
                    		<th style="padding-left: 10px; text-align:left;font-family:sans-serif;">Province</th>
                    	</tr>
                        <?php echo $asc_centers; ?>                          
                    </table>
                    
                </div>        
            </div>
        </div>
    </div>
</div>