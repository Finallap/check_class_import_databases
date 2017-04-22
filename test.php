<?php
	require_once '/Classes/PHPExcel.php';     //修改为自己的目录
	//echo "TEST PHPExcel 1.8.0: read xlsx file";
	$filename="3-305.xlsx";
	$objReader = PHPExcel_IOFactory::createReaderForFile($filename);
	$objPHPExcel = $objReader->load($filename);
	$objPHPExcel->setActiveSheetIndex(1);
	$date = $objPHPExcel->getActiveSheet()->getCell('C4')->getValue();
	var_dump($date);