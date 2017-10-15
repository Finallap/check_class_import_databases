<?php
    header("Content-type: text/html; charset=utf-8"); 

    require_once 'Classes/PHPExcel.php';
    $filePath = "classroom.xls";

    //建立reader对象
    $PHPReader = new PHPExcel_Reader_Excel2007();
    if(!$PHPReader->canRead($filePath)){
        $PHPReader = new PHPExcel_Reader_Excel5();
        if(!$PHPReader->canRead($filePath)){
            echo 'no Excel';
            return ;
        }
    }

    //建立excel对象，此时你即可以通过excel对象读取文件，也可以通过它写入文件
    $PHPExcel = $PHPReader->load($filePath);

    /**读取excel文件中的第一个工作表*/
    $currentSheet = $PHPExcel->getSheet(0);
    /**取得最大的列号*/
    $allColumn = $currentSheet->getHighestColumn();
    /**取得一共有多少行*/
    $allRow = $currentSheet->getHighestRow();

    $cell = $currentSheet->getCell("E6")->getValue();
    if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
    	$cell = $cell->__toString();
    //cell_analyse($cell);

    //循环读取每个单元格的内容。注意行从1开始，列从A开始
    for($rowIndex=1;$rowIndex<=$allRow;$rowIndex++){
        for($colIndex='A';$colIndex<=$allColumn;$colIndex++){
            $addr = $colIndex.$rowIndex;
    		$cell = $currentSheet->getCell($addr)->getValue();
            if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
    			$cell = $cell->__toString();     
           classroom_analyse($cell);
        }
    }

    function classroom_analyse($str)
    {
        if(preg_match("/\d/",$str,$match))
        {
            $teaching_building_number=$match[0];
            preg_match("/\d\d\d/",$str,$match);
            $classroom_number=$match[0];
            $full_number=$teaching_building_number."-".$classroom_number;
            classroom_information_input($full_number,$teaching_building_number,$classroom_number);
        }
        
    }

    function classroom_information_input($full_number,$teaching_building_number,$classroom_number)
    {
        //数据库连接常量，修改此处
        $dbname='check_class';
        $host='localhost';
        $port=3306;

        $dsn="mysql:dbname=$dbname;host=$host;port=$port";
        include("./database.php");

        $pdo=new PDO($dsn,$user,$password); 
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("set names utf8"); 

        $input_information=$pdo->prepare('INSERT INTO `check_class`.`classroom_information` (`full_number`, `teaching_building_number`, `classroom_number`) VALUES (:full_number, :teaching_building_number, :classroom_number);');
        $input_information->bindValue(':full_number',$full_number);
        $input_information->bindValue(':teaching_building_number',$teaching_building_number);
        $input_information->bindValue(':classroom_number',$classroom_number);
        $input_information->execute();
    }