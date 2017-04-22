<?php
header("Content-type: text/html; charset=utf-8"); 
//首先导入PHPExcel
require_once 'Classes/PHPExcel.php';

$filePath = "3-305.xlsx";

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

//循环读取每个单元格的内容。注意行从1开始，列从A开始
for($rowIndex=1;$rowIndex<=$allRow;$rowIndex++){
    for($colIndex='A';$colIndex<=$allColumn;$colIndex++){
        $addr = $colIndex.$rowIndex;
		$cell = $currentSheet->getCell($addr)->getValue();
        if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
			$cell = $cell->__toString();     
        //echo $cell;
    }
}


$cell = $currentSheet->getCell("D7")->getValue();
if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
	$cell = $cell->__toString();
$var=explode("\n",str_replace(' ','',$cell));
var_dump($var);	
if(count($var)==4)
	$course=$var;
if(count($var)==6)
{
	$course1=array_slice($var,0,3); 
	$course2=array_slice($var,3); 
	print_r($course1);
	print_r($course2);
	//$pattern='[\u4e00-\u9fa5]';
	//preg_match($pattern, $course2[2], $match);
	//print_r($match); 
	echo $teacher_name=preg_replace('|^[BHQ][0-9/-]+|','',$course2[2]);//teacher_name
	echo preg_replace("|$teacher_name$|",'',$course2[2]);//class
	//echo preg_replace('|(.*节)$|','',$course2[1]);
	echo $str=preg_replace("/(?:\()(.*)(?:\))/i",'',$course2[1]);//1-17单

	preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u",$course2[1],$match);
	var_dump($match);

	preg_match("|^\d*|",$str,$match);
	var_dump($match);
	$str=preg_replace("|$match[0]-|",'',$str);
	preg_match("/\d*/",$str,$match);
	var_dump($match);

	echo substr($str,-3);//单双周情况
	$str=preg_replace("|".substr($str,-3)."|",'',$str);
	preg_match("|^\d*|",$str,$match);
	print_r($match);
	preg_match("/\d*$/",$str,$match);
	print_r($match);

}

$cell = $currentSheet->getCell("C4")->getValue();
if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
	$cell = $cell->__toString();
$var=explode("\n",str_replace(' ','',$cell));

var_dump($var);
echo count($var);
?>