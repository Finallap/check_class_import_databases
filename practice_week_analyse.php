<?php
    header("Content-type: text/html; charset=utf-8"); 
    require_once 'Classes/PHPExcel.php';

    $filePath="practice_week.xlsx";

    function excel_analyse($filePath)
    {
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
        

        //循环读取每个单元格的内容。注意行从1开始，列从A开始
        for($rowIndex=1;$rowIndex<=$allRow;$rowIndex++)
        { 
            $course_result=array();

            $colIndex='D';
            $addr = $colIndex.$rowIndex;
            $cell = $currentSheet->getCell($addr)->getValue();
            if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
                $cell = $cell->__toString();     
            $course_result['practice_week_name']=$cell;

            $colIndex='K';
            $addr = $colIndex.$rowIndex;
            $cell = $currentSheet->getCell($addr)->getValue();
            if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
                $cell = $cell->__toString();     ;

            preg_match("|^\d*|",$cell,$match);
            $start_week=$match[0];//开始周数
            $cell=preg_replace("|$match[0]-|",'',$cell);
            preg_match("/\d*/",$cell,$match);
            $end_week=$match[0];//结束周数

            $course_result['start_week']=$start_week;
            $course_result['end_week']=$end_week;

            $colIndex='V';
            $addr = $colIndex.$rowIndex;
            $cell = $currentSheet->getCell($addr)->getValue();
            if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
                $cell = $cell->__toString();     
            $course_result['class']=$cell;

            $colIndex='E';
            $addr = $colIndex.$rowIndex;
            $cell = $currentSheet->getCell($addr)->getValue();
            if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
                $cell = $cell->__toString();     
            $course_result['teacher_name']=$cell;
            
            $course_id=practice_week_information_input($course_result);
            class_analyse($course_result['class'],$course_id);
        }
    }


    function practice_week_information_input($course_result)
    {
        //数据库连接常量，修改此处
        $dbname='check_class';
        $host='localhost';
        $port=3306;

        $dsn="mysql:dbname=$dbname;host=$host;port=$port";
        $user='root';
        $password='';

        $pdo=new PDO($dsn,$user,$password); 
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("set names utf8"); 

        $input_information=$pdo->prepare('INSERT INTO `check_class`.`practice_week_information` (`practice_week_id`, `school_year`, `term`, `practice_week_name`, `teacher_name`, `start_week`, `end_week`) VALUES (NULL,:school_year,:term, :practice_week_name,:teacher_name,:start_week,:end_week);');
        $input_information->bindValue(':practice_week_name',$course_result['practice_week_name']);
        $input_information->bindValue(':school_year',"2015-16");
        $input_information->bindValue(':term',"2");
        $input_information->bindValue(':start_week',$course_result['start_week']);
        $input_information->bindValue(':end_week',$course_result['end_week']);
        $input_information->bindValue(':teacher_name',$course_result['teacher_name']);
        $input_information->execute();

        return $pdo->lastInsertId();
    }


    /**
     * 分析班级处理信息，并在course_class_information表中写入内容
     * 
     * @param 班级字符串
     * @param 对应要插入课程的practice_week_id值
     */
    function class_analyse($class_str,$practice_week_id)
    {
        $class_arr = explode(",",$class_str);
        course_class_information_input($class_arr,$practice_week_id);
    }


    /**
     * 在数据库course_class_information表中写入内容
     * 
     * @param 解析好的班级数组
     * @param 对应要插入课程的course_id值
     */
    function course_class_information_input($class_arr,$practice_week_id)
    {
        //数据库连接常量，修改此处
        $dbname='check_class';
        $host='localhost';
        $port=3306;

        $dsn="mysql:dbname=$dbname;host=$host;port=$port";
        $user='root';
        $password='';

        $pdo=new PDO($dsn,$user,$password); 
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("set names utf8"); 

        $input_information=$pdo->prepare('INSERT INTO `check_class`.`practice_week_class_information` (`practice_week_class_id`, `school_year`, `term`, `class_id`, `practice_week_id`) VALUES (NULL, :school_year, :term, :class_id,:practice_week_id);');
        $input_information->bindValue(':school_year',"2015-2016");
        $input_information->bindValue(':term',"2");
        $input_information->bindValue(':practice_week_id',$practice_week_id);
        
        foreach ($class_arr as $key => $value) 
        {
            $input_information->bindValue(':class_id',$value);
            $input_information->execute();
        }
    }

    excel_analyse($filePath);
    echo "Done!";