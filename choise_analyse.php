<?php
    header("Content-type: text/html; charset=utf-8"); 
    require_once 'Classes/PHPExcel.php';

    $filePath="choise.xls";

    function excel_analyse($filePath)
    {
        $dbname='check_class';
        $host='localhost';
        $port=3306;

        $dsn="mysql:dbname=$dbname;host=$host;port=$port";
        $user='root';
        $password='root';

        $pdo=new PDO($dsn,$user,$password); 
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("set names utf8"); 

        $get_course_id_list=$pdo->prepare('SELECT `course_id` FROM `course_class_information` GROUP BY `course_id`;');
        $get_course_id_list->execute();
        $get_course_id_list=$get_course_id_list->fetchAll(PDO::FETCH_ASSOC);//课程号合集

        $course_class_id_list = NULL;//课程号及其对应的班级
        for($i=0;$i<count($get_course_id_list);$i++)
        {
            $course_id=$get_course_id_list[$i]['course_id'];
            $get_course_class_id_list=$pdo->prepare('SELECT `class_id` FROM `course_class_information` WHERE `course_id` =:course_id;');
            $get_course_class_id_list->bindValue(':course_id',$course_id);
            $get_course_class_id_list->execute();
            while($class_id=$get_course_class_id_list->fetch(PDO::FETCH_ASSOC))
            {
                $course_class_id_list[$course_id][]=$class_id['class_id'];
            }
        }


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

            $colIndex='C';
            $addr = $colIndex.$rowIndex;
            $cell = $currentSheet->getCell($addr)->getValue();
            if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
                $cell = $cell->__toString();     
            $course_result['course_week_name']=$cell;

            $colIndex='J';
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

            $colIndex='O';
            $addr = $colIndex.$rowIndex;
            $cell = $currentSheet->getCell($addr)->getValue();
            if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
                $cell = $cell->__toString();  
            class_analyse($cell);   
            $course_result['class']=class_analyse($cell);

            $colIndex='D';
            $addr = $colIndex.$rowIndex;
            $cell = $currentSheet->getCell($addr)->getValue();
            if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
                $cell = $cell->__toString();     
            $course_result['teacher_name']=$cell;

            $colIndex='B';
            $addr = $colIndex.$rowIndex;
            $cell = $currentSheet->getCell($addr)->getValue();
            if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
                $cell = $cell->__toString();     
            $course_result['choices_number']=$cell;
            
            var_dump($course_result['course_week_name']);

            $course_class_id_result_list = NULL;//比较过班级数组之后得到的课程号
            foreach ($course_class_id_list as $key => $course_class_id)
            {
                if(array_diff($course_result['class'],$course_class_id)==NULL)
                    $course_class_id_result_list[]=$key;
            }

            if($course_class_id_result_list)
            {
                $course_id = implode(',',$course_class_id_result_list); 
                // $course_id_list=$pdo->prepare('SELECT `course_id` FROM `course_information` WHERE `course_name` =:course_name AND `tercher_name` =:tercher_name AND `start_week` =:start_week AND `end_week` =:end_week AND `course_id` IN ('.$course_id.');');
                // $course_id_list->bindValue(':course_name',$course_result['course_week_name']);
                // $course_id_list->bindValue(':tercher_name',$course_result['teacher_name']);
                // $course_id_list->bindValue(':start_week',$course_result['start_week']);
                // $course_id_list->bindValue(':end_week',$course_result['end_week']);

                $course_id_list=$pdo->prepare('SELECT `course_id` FROM `course_information` WHERE `course_name` =:course_name AND `tercher_name` =:tercher_name AND `course_id` IN ('.$course_id.');');
                $course_id_list->bindValue(':course_name',$course_result['course_week_name']);
                $course_id_list->bindValue(':tercher_name',$course_result['teacher_name']);
                $course_id_list->execute();
                $course_id_list=$course_id_list->fetchAll(PDO::FETCH_ASSOC);

                //var_dump($course_id_list);

                foreach ($course_id_list as $key => $value) 
                {
                    $course_id=$value['course_id'];
                    $choices_number= $course_result['choices_number'];
                    var_dump($course_id);
                    // var_dump($choices_number);
                    choice_number_update($pdo,$choices_number,$course_id);
                }
            }   
            
        }
    }

    /**
     * 分析班级处理信息，并在course_class_information表中写入内容
     * 
     * @param 班级字符串
     * @param 对应要插入课程的course_id值
     */
    function class_analyse($class_str)
    {
        $class_arr = explode(",",$class_str);

        foreach ($class_arr as $key => $value)//去除Q130104(DK)这种强化班带括号后缀的元素
         {
            if(preg_match("/\)$/",$value,$match))
            {
                //unset($class_arr[$key]); 
            }
        }
        
        foreach ($class_arr as $key => $value)//补充省略号中的班级
         {
            if(preg_match("/\.\.\./",$value,$match))
            {
                for($class=$class_arr[$key-1]+1;$class<$class_arr[$key+1];$class++)
                    array_push($class_arr,$class);
                unset($class_arr[$key]);//去除数组中的省略号
            }
        }

        foreach ($class_arr as $key => $value)//补充横杠省略的班级
         {
            if(preg_match("/-/",$value,$match))
            {
                $take_apart_class_array=explode("-",$value);

                unset($class_arr[$key]);//去除数组中的原来带横杠的班级
                array_push($class_arr,$take_apart_class_array[0]);//插入班级的起始值

                $class_final=substr($take_apart_class_array[0],-2)+1;//将要插入的班级尾号起始值
                $class_header=substr($take_apart_class_array[0],0,5);//将要插入的班级头（B130101）

                for(;$class_final<=$take_apart_class_array[1];$class_final++)
                {
                    $class_final=str_pad($class_final,2,"0",STR_PAD_LEFT);
                    array_push($class_arr,$class_header.$class_final);
                }
                    
            }
        }

        foreach ($class_arr as $key => $value)//补充班级前面的字母
         {
            if(!preg_match("/[BQHF]/",$value,$match))
            {
                preg_match("/[BQHF]/",$class_arr[0],$match);
				echo "test";
                $class_arr[$key]=$match[0].$class_arr[$key];
            }
        }

        return $class_arr;
    }

    /**
     * 在数据库course_class_information表中写入内容
     * 
     * @param 解析好的班级数组
     * @param 对应要插入课程的course_id值
     */
    function choice_number_update($pdo,$choices_number,$course_id)
    {
        $input_information=$pdo->prepare('UPDATE `course_information` SET `choices_number`=:choices_number WHERE `course_id` =:course_id;');
        $input_information->bindValue(':choices_number',$choices_number);
        $input_information->bindValue(':course_id',$course_id);
        $input_information->execute();
    }

    excel_analyse($filePath);
    echo "Done!";