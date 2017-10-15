<?php
	//$str1="B140104,140105,140106";
	//$str2="B140304-06";
	//$str3="B130201-04,Q130104(DK)";
	//$str4="B140603,140604,...,B140601,140602";
	//$str5="B130301,130302,...,130306,130307";

	/**
	 * 分析班级处理信息，并在course_class_information表中写入内容
	 * 
	 * @param 班级字符串
	 * @param 对应要插入课程的course_id值
	 */
	function class_analyse($class_str,$course_id)
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
				$class_arr[$key]=$match[0].$class_arr[$key];
			}
		}

		course_class_information_input($class_arr,$course_id);
	}

	/**
	 * 在数据库course_class_information表中写入内容
	 * 
	 * @param 解析好的班级数组
	 * @param 对应要插入课程的course_id值
	 */
	function course_class_information_input($class_arr,$course_id)
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

		$input_information=$pdo->prepare('INSERT INTO `check_class`.`course_class_information` (`course_class_id`, `school_year`, `term`, `class_id`, `course_id`) VALUES (NULL, :school_year, :term, :class_id,:course_id);');
		$input_information->bindValue(':school_year',"2016-17");
		$input_information->bindValue(':term',"2");
		$input_information->bindValue(':course_id',$course_id);
		
		foreach ($class_arr as $key => $value) 
		{
			$input_information->bindValue(':class_id',chinese_filter($value));
			$input_information->execute();
		}
	}

	function chinese_filter($str)
	{
		preg_match_all('/[\x{4e00}-\x{9fff}]+/u', $str, $matches);
		$str1 = join('', $matches[0]);
		$str=preg_replace("|$str1|",'',$str);
		return $str;
	}