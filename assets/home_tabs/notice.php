<?php 
require_once('../../include/connection.php');
require_once('../../include/function.php');

    $notice_id = "";

 	$query = "SELECT * FROM notice_details ORDER BY notice_id DESC";

    $result = mysqli_query($connection, $query);

    $notice_list = "";

    verify_query ($result);
        while ($notice = mysqli_fetch_assoc($result)) {
            $notice_list .= "<table class=table table-bordered>";
            $notice_list .= "<tr>";
            $notice_list .= "<td style='border-bottom:1px solid #888;padding:10px;'>{$notice['notice']}</td>";
            $notice_list .= "</tr>";
        } 
?>

<div>
	<h1 style="text-align: center;margin-bottom: 50px;margin-top: 50px;">Notices</h1>

	<div class="container" style="margin-bottom:100px;">
        <div class="row">
            <div class="col-md-12">
                <div class="main">
                    <table class="masterlist" class="table-responsive">
                        <tr>
                            <td><?php echo $notice_list; ?></td>
                        </tr>                           
                    </table>
                    
                </div>        
            </div>
        </div>
    </div>
</div>