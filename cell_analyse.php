<?php
	require_once 'class_analyse.php';

	/**
	 * create_user function.
	 * 
	 * @param 单元格中已拆成四行的课程数据
	 * @return 课程每门课程的array
	 */
	function course_analyse($course_date,$classroom,$class_time,$week)
	{
		$str=preg_replace("/(?:\()(.*)(?:\))/i",'',$course_date[1]);//1-17单(去除了括号后面的具体节数)
		$odd_even=substr($str,-3);//单双周情况

		preg_match("|^\d*|",$str,$match);
		$start_week=$match[0];//开始周数
		$str=preg_replace("|$match[0]-|",'',$str);
		preg_match("/\d*/",$str,$match);
		$end_week=$match[0];//结束周数
		$odd_even=preg_replace("|$match[0]|",'',$str);
		$odd_even=get_odd_even($odd_even);

		$course_result=array(
			'course_name' => $course_date[0],//课程名称
			'start_week' => $start_week,//开始周数
			'end_week' => $end_week,//结束周数
			'odd_even' => $odd_even,//单双周情况
			'class_time' => $class_time,//第几大节课
			'weekday' => $week,//星期几
			'classroom' => $classroom,//教室地址
			'tercher_name' => $course_date[3],//教师名字
			'class' => $course_date[2]//上课班级
			);

		if(strlen($course_date[2])<50)
		{
			//var_dump($course_result);
			$course_id=course_information_input($course_result);//将课程插入course_information表，并取得返回的$course_id索引值
			class_analyse($course_date[2],$course_id);
		}
		else
			var_dump($course_result);

		return $course_result;
	}

	/**
	 * 一个单元格中有两节课的情况
	 * 
	 * @param 单元格中有两节课的数据
	 * @return success or failure
	 */
	function include_two_classes($cell_date,$classroom,$class_time,$week)
	{
		$course1=array_slice($cell_date,0,3); 
		$course2=array_slice($cell_date,3); 

		//$teacher_name1=preg_replace('|^[BHQ][0-9/-]+|','',$course1[2]);//teacher_name1
		//$teacher_name2=preg_replace('|^[BHQ][0-9/-]+|','',$course2[2]);//teacher_name2

		//$class1=preg_replace("|$teacher_name1$|",'',$course1[2]);//class1
		//$class2=preg_replace("|$teacher_name2$|",'',$course2[2]);//class2




		// $teacher_name_class_arr1=explode(" ",$course1[2]);
		// $teacher_name_class_arr2=explode(" ",$course2[2]);

		// $teacher_name1=$teacher_name_class_arr1[1];
		// $teacher_name2=$teacher_name_class_arr2[1];

		// $class1=$teacher_name_class_arr1[0];
		// $class2=$teacher_name_class_arr2[0];

		// if(empty($teacher_name1))
		// {
		// 	$teacher_name1.=preg_replace('|^[BHQ][0-9/-]+|','',$class1);
		// 	$class1=preg_replace("|$teacher_name1$|",'',$class1);
		// }
		
		// if(empty($teacher_name2))
		// {
		// 	$teacher_name2.=preg_replace('|^[BHQ][0-9/-]+|','',$class2);
		// 	$class2=preg_replace("|$teacher_name2$|",'',$class2);
		// }

		preg_match_all("/[\x{4e00}-\x{9fa5}]+/u",$course1[2],$match);
		$teacher_name1=$match[0][0];
		$class1=preg_replace("|$teacher_name1$|",'',$course1[2]);

		preg_match_all("/[\x{4e00}-\x{9fa5}]+/u",$course2[2],$match);
		$teacher_name2=$match[0][0];
		$class2=preg_replace("|$teacher_name1$|",'',$course2[2]);
		
		$course1[2]=$class1;
		$course2[2]=$class2;

		$course1[3]=$teacher_name1;
		$course2[3]=$teacher_name2;

		course_analyse($course1,$classroom,$class_time,$week);//分析第一节课写入
		course_analyse($course2,$classroom,$class_time,$week);//分析第二节课写入
	}


	/**
	 * 拆分单元格中的节数并录入
	 * 
	 * @param 单元格中的数据
	 * @return success or failure
	 */
	function cell_analyse($cell_date,$classroom,$class_time,$week)
	{
		$cell_date=explode("\n",str_replace(' ','',$cell_date));

		if(count($cell_date)==4)
		{
			if($cell_date[2]!="其他课程")
				course_analyse($cell_date,$classroom,$class_time,$week);
		}
		if(count($cell_date)==5)
		{
			$cell_date[2].=$cell_date[3];
			$cell_date[3]=$cell_date[4];
			course_analyse($cell_date,$classroom,$class_time,$week);
		}
		if(count($cell_date)==6)
			include_two_classes($cell_date,$classroom,$class_time,$week);
	}


	/**
	 * 获取列数对应星期几
	 * 
	 * @param 列数
	 * @return 星期几（0代表周日）
	 */
	function get_week($str)
	{
	    switch($str)
	    {
	        case "C":
	            return "1";
	            break;
	        case "D":
	            return "2";
	            break;
	        case "E":
	            return "3";
	            break;
	        case "F":
	            return "4";
	            break;
	        case "G":
	            return "5";
	            break;
	        case "H":
	            return "6";
	            break;
	        case "I":
	            return "0";
	            break;
	    }
	}	

	/**
	 * 获取列数对应星期几
	 * 
	 * @param 文件名
	 * @return 去掉后缀的文件名
	 */
	function get_classroom($str)
	{
	    $file_name_take_apart=explode(".",str_replace(' ','',$str));
	    return $file_name_take_apart[0];
	}	

	/**
	 * 获取列数对应星期几
	 * 
	 * @param 单双周情况字段
	 * @return 单双周情况代码
	 */
	function get_odd_even($str)
	{
	    switch($str)
	    {
	        case "单":
	            return "1";
	            break;
	        case "双":
	            return "2";
	            break;
	        default:
	            return "0";
	            break;
	    }
	    return $odd_even;
	}

	/**
	 * 在数据库写入course_information表中的内容
	 * 
	 * @param 解析好的课程信息
	 * @return 插入的course_id值
	 */
	function course_information_input($course_result)
	{
		//数据库连接常量，修改此处
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

		$input_information=$pdo->prepare('INSERT INTO `check_class`.`course_information` (`course_id`, `course_name`, `school_year`, `term`, `start_week`, `end_week`, `odd_even`, `class_time`, `weekday`, `classroom`, `tercher_name`, `choices_number`) VALUES (NULL, :course_name, :school_year, :term, :start_week, :end_week, :odd_even, :class_time, :weekday,:classroom, :tercher_name, NULL);');
		$input_information->bindValue(':course_name',$course_result['course_name']);
		$input_information->bindValue(':school_year',"2016-17");
		$input_information->bindValue(':term',"2");
		$input_information->bindValue(':start_week',$course_result['start_week']);
		$input_information->bindValue(':end_week',$course_result['end_week']);
		$input_information->bindValue(':odd_even',$course_result['odd_even']);
		$input_information->bindValue(':class_time',$course_result['class_time']);
		$input_information->bindValue(':weekday',$course_result['weekday']);
		$input_information->bindValue(':classroom',$course_result['classroom']);
		$input_information->bindValue(':tercher_name',$course_result['tercher_name']);
		$input_information->execute();

		return $pdo->lastInsertId();
	}