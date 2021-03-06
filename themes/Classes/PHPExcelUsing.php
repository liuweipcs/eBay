<?php
/**
 * @todo    获取Excel文件
 * @author  WLZ
 * @version 2013-02-22
 * @param   array  $sheets        Excel表列植 [A列，B列，C列]
 * @param   array  $sheet1_name   Excel表第一行的值
 * @param   string $fname         Excel文件的保存位置
 * @return						  Excel文件的下载
 */
function write_excel($sheet1_name,$sheet_value,$fname,$output=1,$column_width=array()){
	set_include_path(get_include_path() . PATH_SEPARATOR . '../Classes/');/** Include path **/
	
	include 'Classes/PHPExcel.php';/** PHPExcel */
	//require_once 'PHPExcel/Writer/Excel5.php';    // 用于其他低版本xls 
	include 'Classes/PHPExcel/Writer/Excel2007.php';/** PHPExcel_Writer_Excel2007 */
	
	$objPHPExcel = new PHPExcel();// Create new PHPExcel object
	
	// Set properties
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
	$objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
	$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
	$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
	$objPHPExcel->getProperties()->setDescription("Test document, generated by PHPExcel.");  //setDescrīption("Test document for Office 2007 XLSX, generated using php classes.");
	$objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
	$objPHPExcel->getProperties()->setCategory("USING");
	
	$objActSheet = $objPHPExcel->getActiveSheet();
	$objPHPExcel->setActiveSheetIndex(0);
	
	for($t=0;$t<count($sheet1_name);$t++)
	{
		if($t<26){
			$curr_sheet=chr(($t+65));
		}else{
			$curr_sheet = chr(($t+1)/26+64).chr(($t+1)%26+64);
		}
		
		$sheets[]=$curr_sheet;
		if(isset($column_width[$t]) && $column_width[$t] > 0){
			$objActSheet->getColumnDimension($curr_sheet)->setWidth($column_width[$t]);
		}else{
			$objActSheet->getColumnDimension($curr_sheet)->setAutoSize(true);//设置宽度
		}
		$objStyleA5 = $objActSheet->getStyle($curr_sheet.'1');
		$objStyleA5->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
		//设置字体
		$objFontA5 = $objStyleA5->getFont();
		$objFontA5->setName('Courier New');
		$objFontA5->setSize(10);
		$objFontA5->setBold(true);
		$objFontA5->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
		$objFontA5->getColor()->setARGB('FF000000');
		 //Add some data
		$objPHPExcel->getActiveSheet()->setCellValue($curr_sheet.'1', $sheet1_name[$t]);
	}
	
	$i=1;
	$limit = 100;//每隔$limit行，刷新一下缓冲区
	$count = 0;//计数器
	foreach($sheet_value as $v){
		$i++;
		$count = 0;
		if($limit==$count){//刷新一下缓冲区
			ob_flush();
			flush();
			$count = 0;
		}
		for($j=0;$j<count($sheet1_name);$j++){
			if(strpos($v[$sheet1_name[$j]],'displayPic|')===0){//以“displayPic|”开头，表示显示图片
				$length = 60;
				$objDrawing = new PHPExcel_Worksheet_Drawing();
				
				$objActSheet->getRowDimension($i)->setRowHeight($length);//设置行高
				$picpath = str_replace('displayPic|', "", $v[$sheet1_name[$j]]);
				if(file_exists($picpath)){
					$objDrawing->setPath($picpath);
					$objDrawing->setHeight($length);
					$objDrawing->setWidth($length);
					$objDrawing->setCoordinates($sheets[$j].$i);
					$objDrawing->setWorksheet($objActSheet);
				}else{
					$tmp=$picpath;
					$objActSheet->setCellValue($sheets[$j].$i,$v[$tmp]);
				}
			}elseif(is_array($v[$sheet1_name[$j]])){//数组表示以下拉框形式显示
				$objPHPExcel->getActiveSheet()->getColumnDimension($sheets[$j])->setWidth(100);
				$objValidation = $objActSheet->getCell($sheets[$j].$i)->getDataValidation(); //这一句为要设置数据有效性的单元格
				$objValidation -> setType(PHPExcel_Cell_DataValidation::TYPE_LIST)
				-> setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)
				-> setAllowBlank(false)
				-> setShowInputMessage(true)
				-> setShowErrorMessage(true)
				-> setShowDropDown(true)
				-> setErrorTitle('输入的值有误')
				-> setError('您输入的值不在下拉框列表内.')
				-> setPromptTitle($v[$sheet1_name[$j]]['title'])
				-> setFormula1('"'.implode(",",$v[$sheet1_name[$j]]['list']).'"');
			}else{
				$objActSheet->getStyle($sheets[$j].$i)->getAlignment()->setWrapText(true);//自动换行
				$tmp=$sheet1_name[$j];
				$objActSheet->setCellValueExplicit($sheets[$j].$i,$v[$tmp],PHPExcel_Cell_DataType::TYPE_STRING);
			}
		}
	}
	//exit;
	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Simple');

	//$objPHPExcel->setActiveSheetIndex(0);// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);//new PHPExcel_Writer_Excel5($objExcel);//// Save Excel 2007 file
	$objWriter->save($fname);

	//$objWriter->save('php://output');
	if($output){
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$fname.'"');
		header('Cache-Control: max-age=0');
		
		$objWriter1 = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter1->save('php://output');
	}
	
}
/**
 * @todo    获取CSV文件
 * @author  WLZ
 * @version 2013-04-09
 * @param   string  $sql    SQL语句  
 * @param   string  $total  查询结果的总数
 * @param   string $num     每批次[刷新一下输出buffer]写入文件 的数目
 * @param   string $fn    	CSV文件的名字
 * @param   string $head  	CSV文件第一行的值即表头
 * @return	CSV文件的下载
 */
function writeCSV($sql,$total,$num,$fn,$head=''){$file = fopen("test.txt","a+");fwrite($file,"---\r\n");fclose($file);
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$fn.'"');
		header('Cache-Control: max-age=0');

		$fp = fopen('php://output', 'a');// 打开PHP文件句柄，php://output 表示直接输出到浏览器
		// 输出Excel列名信息
		if(is_array($head)){
			foreach ($head as $i => $v) {
				$head[$i] = iconv('utf-8', 'gbk', $v);// CSV的Excel支持GBK编码，一定要转换，否则乱码
			}
			fputcsv($fp, $head);
		}
		$stmt = mysql_query($sql) or die(mysql_error());
		// 从数据库中获取数据，为了节省内存，不要把数据一次性读到内存，从句柄中一行一行读即可
		$cnt = 0;		
		while ($row =mysql_fetch_row($stmt)) {
				$cnt ++;
				if ($num == $cnt) { //刷新一下输出buffer，防止由于数据过多造成问题
					ob_flush();
					flush();
					$cnt = 0;
				}
				foreach ($row as $i => $v) {
					$row1[$i] = iconv('utf-8', 'gbk', $v);
				}
				fputcsv($fp, $row1);unset($row);unset($row1);
			}unset($stmt);		//exit;
}
/**
 * @todo    获取Excel文件
 * @author  WLZ
 * @version 2013-03-22
 * @param   array  $sheets        Excel表列植 [A列，B列，C列]
 * @param   array  $sheet1_name   Excel表第一行的值
 * @param   string $fname         Excel文件的保存位置
 * @return						  Excel文件的下载
 */
function write_excel1($sheet1_name,$sheet_value,$fname){
	set_include_path(get_include_path() . PATH_SEPARATOR . '../Classes/');/** Include path **/
	require_once 'Classes/PHPExcel.php';/** PHPExcel */
	//require_once 'PHPExcel/Writer/Excel5.php';    // 用于其他低版本xls
	require_once 'Classes/PHPExcel/Writer/Excel2007.php';/** PHPExcel_Writer_Excel2007 */

	$objPHPExcel = new PHPExcel();// Create new PHPExcel object
	// Set properties
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
	$objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
	$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
	$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
	$objPHPExcel->getProperties()->setDescription("Test document, generated by PHPExcel.");  //setDescrīption("Test document for Office 2007 XLSX, generated using php classes.");
	$objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
	$objPHPExcel->getProperties()->setCategory("USING");

	$objActSheet = $objPHPExcel->getActiveSheet();
	$objPHPExcel->setActiveSheetIndex(0);
	for($t=0;$t<count($sheet1_name);$t++)
	{
		$curr_sheet=chr(($t+65));
		$sheets[]=$curr_sheet;
		$objActSheet->getColumnDimension($curr_sheet)->setAutoSize(true);//设置宽度
		$objStyleA5 = $objActSheet->getStyle($curr_sheet.'1');
		$objStyleA5->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
		//设置字体
		$objFontA5 = $objStyleA5->getFont();
		$objFontA5->setName('Courier New');
		$objFontA5->setSize(10);
		$objFontA5->setBold(true);
		$objFontA5->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
		$objFontA5->getColor()->setARGB('FF000000');
		$objActSheet->getStyle($curr_sheet.'1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);//设置具体列的颜色值
		if(preg_match("/利润(.*)/",$sheet1_name[$t])){
			$objActSheet->getStyle($curr_sheet.'1')->getFill()->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);//设置具体列的颜色值
		}else{
			$objActSheet->getStyle($curr_sheet.'1')->getFill()->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_YELLOW);//设置具体列的颜色值
		}
		$objPHPExcel->getActiveSheet()->setCellValue($curr_sheet.'1', $sheet1_name[$t]);
	}
	$i=1;
	foreach($sheet_value as $v){
		$i++;
		for($j=0;$j<count($sheet1_name);$j++){
			$tmp=$sheet1_name[$j];
			if($v[$tmp]<0 && preg_match('/利润(.*)/',$tmp))
			{
				$curr_sheet=chr(($j+65));
				$objActSheet->getStyle($curr_sheet.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);//设置具体列的颜色值
				$objActSheet->getStyle($curr_sheet.$i)->getFill()->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);//设置具体列的颜色值
			}elseif(preg_match('/利润(.*)/',$tmp))
			{
				$curr_sheet=chr(($j+65));
				$objActSheet->getStyle($curr_sheet.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);//设置具体列的颜色值
				$objActSheet->getStyle($curr_sheet.$i)->getFill()->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);//设置具体列的颜色值
			}
			$objPHPExcel->getActiveSheet()->setCellValue($sheets[$j].$i,$v[$tmp]);
		}
	}
	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Simple');
	//$objPHPExcel->setActiveSheetIndex(0);// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);//new PHPExcel_Writer_Excel5($objExcel);//// Save Excel 2007 file
	$objWriter->save($fname);
	//$objWriter->save('php://output');
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$fname.'"');
	header('Cache-Control: max-age=0');
	
	$objWriter1 = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter1->save('php://output');
}
/**
 * @todo    获取Excel文件
 * @author  WLZ
 * @version 2013-03-28
 * @param   array  $sheets        Excel表列植 [A列，B列，C列]
 * @param   array  $sheet1_name   Excel表第一行的值
 * @param   string $fname         Excel文件的保存位置
 * @return						  Excel文件的下载
 */
function write_excel3($sheet1_name,$sheet_value,$fname){
	set_include_path(get_include_path() . PATH_SEPARATOR . '../Classes/');/** Include path **/
	include 'Classes/PHPExcel.php';/** PHPExcel */
	//require_once 'PHPExcel/Writer/Excel5.php';    // 用于其他低版本xls
	include 'Classes/PHPExcel/Writer/Excel2007.php';/** PHPExcel_Writer_Excel2007 */

	$objPHPExcel = new PHPExcel();// Create new PHPExcel object

	// Set properties
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
	$objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
	$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
	$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
	$objPHPExcel->getProperties()->setDescription("Test document, generated by PHPExcel.");  //setDescrīption("Test document for Office 2007 XLSX, generated using php classes.");
	$objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
	$objPHPExcel->getProperties()->setCategory("USING");

	$objActSheet = $objPHPExcel->getActiveSheet();
	$objPHPExcel->setActiveSheetIndex(0);
	for($t=0;$t<count($sheet1_name);$t++)
	{
		$curr_sheet=chr(($t+65));
		$sheets[]=$curr_sheet;
		$objActSheet->getColumnDimension($curr_sheet)->setAutoSize(true);//设置宽度
		$objStyleA5 = $objActSheet->getStyle($curr_sheet.'1');
		$objStyleA5->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
		//设置字体
		$objFontA5 = $objStyleA5->getFont();
		$objFontA5->setName('Courier New');
		$objFontA5->setSize(10);
		$objFontA5->setBold(true);
		$objFontA5->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
		$objFontA5->getColor()->setARGB('FF000000');
		//Add some data
		$objPHPExcel->getActiveSheet()->setCellValue($curr_sheet.'1', $sheet1_name[$t]);
		$objActSheet->getStyle($curr_sheet.'1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);//设置具体列的颜色值
		$objActSheet->getStyle($curr_sheet.'1')->getFill()->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_GREEN);//设置具体列的颜色值
	}
	$i=1;
	foreach($sheet_value as $v){
		$i++;
		for($j=0;$j<count($sheet1_name);$j++){
			$tmp=$sheet1_name[$j];
			$objPHPExcel->getActiveSheet()->setCellValue($sheets[$j].$i,$v[$tmp]);
		}
	}
	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Simple');
	//$objPHPExcel->setActiveSheetIndex(0);// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);//new PHPExcel_Writer_Excel5($objExcel);//// Save Excel 2007 file
	$objWriter->save($fname);
	//$objWriter->save('php://output');
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$fname.'"');
	header('Cache-Control: max-age=0');

	$objWriter1 = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter1->save('php://output');
}
/**
 * @todo    获取Excel文件
 * @author  WLZ
 * @version 2013-03-22
 * @param   array  $sheets        Excel表列植 [A列，B列，C列]
 * @param   array  $sheet1_name   Excel表第一行的值
 * @param   string $fname         Excel文件的保存位置
 * @return						  Excel文件的下载
 */
function write_excel4($sheet1_name,$sheet_value,$fname,$Color_column){
	set_include_path(get_include_path() . PATH_SEPARATOR . '../Classes/');/** Include path **/
	require_once 'Classes/PHPExcel.php';/** PHPExcel */
	//require_once 'PHPExcel/Writer/Excel5.php';    // 用于其他低版本xls
	require_once 'Classes/PHPExcel/Writer/Excel2007.php';/** PHPExcel_Writer_Excel2007 */

	$objPHPExcel = new PHPExcel();// Create new PHPExcel object
	// Set properties
	$objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
	$objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
	$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
	$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
	$objPHPExcel->getProperties()->setDescription("Test document, generated by PHPExcel.");  //setDescrīption("Test document for Office 2007 XLSX, generated using php classes.");
	$objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
	$objPHPExcel->getProperties()->setCategory("USING");

	$objActSheet = $objPHPExcel->getActiveSheet();
	$objPHPExcel->setActiveSheetIndex(0);
	$cols=0;
	for($t=0;$t<count($sheet1_name);$t++)
	{
		if(count($sheet1_name)>26 && $t>25){
			$curr_sheet='A'.chr(($t+65-26));
			$cols++;
		}else 
			$curr_sheet=chr(($t+65));
		$sheets[]=$curr_sheet;
		$objActSheet->getColumnDimension($curr_sheet)->setAutoSize(true);//设置宽度
		$objStyleA5 = $objActSheet->getStyle($curr_sheet.'1');
		$objStyleA5->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
		//设置字体
		$objFontA5 = $objStyleA5->getFont();
		$objFontA5->setName('Courier New');
		$objFontA5->setSize(10);
		$objFontA5->setBold(true);
		$objFontA5->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
		$objFontA5->getColor()->setARGB('FF000000');
		$objActSheet->getStyle($curr_sheet.'1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);//设置具体列的颜色值
		if(ord($Color_column['col'])>ord($curr_sheet) && $cols==0){
			$objActSheet->getStyle($curr_sheet.'1')->getFill()->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);//设置具体列的颜色值
		}else{
			$objActSheet->getStyle($curr_sheet.'1')->getFill()->getStartColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);//设置具体列的颜色值
		}
		$objPHPExcel->getActiveSheet()->setCellValue($curr_sheet.'1', $sheet1_name[$t]);
	}
	$i=1;
	foreach($sheet_value as $v){
		$i++;
		for($j=0;$j<count($sheet1_name);$j++){
			$tmp=$sheet1_name[$j];			
			$objPHPExcel->getActiveSheet()->setCellValue($sheets[$j].$i,$v[$tmp]);
		}
	}
	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('Simple');
	//$objPHPExcel->setActiveSheetIndex(0);// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);//new PHPExcel_Writer_Excel5($objExcel);//// Save Excel 2007 file
	$objWriter->save($fname);
	//$objWriter->save('php://output');
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$fname.'"');
	header('Cache-Control: max-age=0');

	$objWriter1 = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter1->save('php://output');
}
?>