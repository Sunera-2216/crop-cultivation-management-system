<?php
	require_once('../../include/connection.php');
	require('fpdf.php');
	
	$pdf = new FPDF();

	$pdf->SetFont('Helvetica','B',20);
	$pdf->SetTextColor(50,60,100);
	$pdf->AddPage('P');
	$pdf->SetTitle('Report');

	$pdf->SetXY(50,10);
	$pdf->SetDrawColor(50,60,100);
	$pdf->Cell(100,10,'Crop Details',0,0,'C',0);

	//Set x and y position for the main text, reduce font size and write content
	$pdf->SetXY (10,30);
	$pdf->SetFontSize(10);


	$reportHeader = mysqli_query($connection, "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = crop_details");

	$rep_crops = mysqli_query($connection, "SELECT * FROM crop_details");

	$reportRes = mysqli_query($connection, "SELECT request_crop_id, request_farmer_nic, SUM(request_land_size) as size FROM request_details WHERE request_status = 1 GROUP BY request_crop_id");


	$pdf->Write(5, 'Crop Name    |');
	$pdf->Write(5, '    Crop Land Size    |');
	$pdf->Write(5, '    Crop Cultivation Start Date    |');
	$pdf->Write(5, '    Crop Cultivation End Date    |');

	$pdf->Ln(10);

	while ($cropdata = mysqli_fetch_array($rep_crops)) {
		$pdf->Cell(30, 12, $cropdata['crop_name'], 0, 'L');
		$pdf->Cell(40, 12, $cropdata['crop_land_size'].' acres', 0, 'R');
		$pdf->Cell(55, 12, $cropdata['crop_cultivation_start_date'], 0, 'L');
		$pdf->Cell(150, 12, $cropdata['crop_cultivation_end_date'], 0, 'L');
		$pdf->Ln(8);
	}

	$pdf->SetXY(50,90);
	$pdf->SetFont('Helvetica','B',20);
	$pdf->SetDrawColor(50,60,100);
	$pdf->Cell(100,10,'Cultivation of Crops',0,0,'C',0);

	$pdf->SetXY (10,110);
	$pdf->SetFontSize(10);

	$pdf->Write(5, 'Crop Name    |');
	$pdf->Write(5, '    Cultivated Farmer NIC    |');
	$pdf->Write(5, '    Cultivated Land Size     |');
	$pdf->Write(5, '      ASC ID       |');
	$pdf->Write(5, '        ASC Name      |');

	$pdf->Ln(10);
	//$pdf->Write(5, '    Crop Cultivation End Date    |');

	while ($req_crop = mysqli_fetch_array($reportRes)) {
		$cr = mysqli_query($connection, "SELECT crop_name FROM crop_details WHERE crop_id = '{$req_crop['request_crop_id']}'");
		$crop = mysqli_fetch_assoc($cr);

		$far = mysqli_query($connection, "SELECT asc_id FROM farmer_asc_details WHERE farmer_nic = '{$req_crop['request_farmer_nic']}'");
		$asc_id = mysqli_fetch_assoc($far)['asc_id'];

		$asc = mysqli_query($connection, "SELECT asc_name FROM asc_details WHERE asc_id = '{$asc_id}'");
		$asc_name = mysqli_fetch_assoc($asc)['asc_name'];

		$pdf->Cell(35, 12, $crop['crop_name'], 0, 'L');
		$pdf->Cell(48, 12, $req_crop['request_farmer_nic'], 0, 'L');
		$pdf->Cell(35, 12, $req_crop['size'].' acres', 0, 'L');
		$pdf->Cell(30, 12, $asc_id, 0, 'L');
		$pdf->Cell(35, 12, $asc_name, 0, 'L');
		$pdf->Ln(8);
	}

	//Output the document
	$pdf->Output('report.pdf','I');

?>