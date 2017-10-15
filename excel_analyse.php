<?php
    header("Content-type: text/html; charset=utf-8"); 
    require_once 'Classes/PHPExcel.php';
    require_once 'cell_analyse.php';

    function get_filename()
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

        $get_information=$pdo->prepare('SELECT * FROM `classroom_information`;');
        $get_information->execute();
        $get_information->fetch();

        $res=$get_information->fetchAll(PDO::FETCH_ASSOC);

        foreach ($res as $key => $value) 
        {
            $filePath = $value['teaching_building_number']."-".$value['classroom_number'].".xls";
            excel_analyse($filePath);
        }
    }

    function excel_analyse($filePath)
    {
        //建立reader对象
        $PHPReader = new PHPExcel_Reader_Excel2007();
		echo $filePath;
        if(!$PHPReader->canRead($filePath)){
			echo $filePath;
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
        for($rowIndex=1;$rowIndex<=$allRow;$rowIndex++)
        {
            for($colIndex='C';$colIndex<=$allColumn;$colIndex++)
            {
                $addr = $colIndex.$rowIndex;
                $cell = $currentSheet->getCell($addr)->getValue();
                if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
                    $cell = $cell->__toString();     
                cell_analyse($cell,get_classroom($filePath),$rowIndex-3,get_week($colIndex));
            }    
        }
        echo "$filePath done!<br>\n";
    }

    get_filename();