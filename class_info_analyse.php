<?php
    header("Content-type: text/html; charset=utf-8"); 

    require_once 'Classes/PHPExcel.php';
    $filePath = "class.xlsx";

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
    for($rowIndex=1;$rowIndex<=$allRow;$rowIndex++)
    {
            $course_result=array();

            $colIndex='A';
            $addr = $colIndex.$rowIndex;
            $cell = $currentSheet->getCell($addr)->getValue();
            if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
                $cell = $cell->__toString();     
            $result['college_id']=get_college_id($cell);

            $colIndex='B';
            $addr = $colIndex.$rowIndex;
            $cell = $currentSheet->getCell($addr)->getValue();
            if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
                $cell = $cell->__toString();
            $result['class_id']=$cell;

            $result['class_final_id']=substr($result['class_id'], -2);
            $result['grade']=substr($result['class_id'],1,2);

            $colIndex='C';
            $addr = $colIndex.$rowIndex;
            $cell = $currentSheet->getCell($addr)->getValue();
            if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
                $cell = $cell->__toString();     
            $result['major']=$cell;

            class_information_input($result);
    }
    echo "Done!";

    function get_college_id($college_name)
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

            $input_information=$pdo->prepare('SELECT `college_id` FROM `college_information` WHERE `college_name` LIKE :college_name');
            $input_information->bindValue(':college_name',$college_name);
            $input_information->execute();
            $res=$input_information->fetchAll();
            return $res[0]['college_id'];
    }

    function class_information_input($result)
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

        $input_information=$pdo->prepare('INSERT INTO `check_class`.`class_information` (`class_id`, `college_id`, `grade`, `class_final_id`, `major`, `password`) VALUES (:class_id, :college_id,:grade, :class_final_id,:major, :class_password);');
        $input_information->bindValue(':class_id',$result['class_id']);
        $input_information->bindValue(':college_id',$result['college_id']);
        $input_information->bindValue(':grade',$result['grade']);
        $input_information->bindValue(':class_final_id',$result['class_final_id']);
        $input_information->bindValue(':major',$result['major']);
        $input_information->bindValue(':class_password',$result['class_id']);
        $input_information->execute();
    }