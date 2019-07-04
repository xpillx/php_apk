<?php
class WriteExcel
{
	private $cellArray = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ',
			'AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
			);
	private $excel = null;
	private $writer = null;
	private $sheet = null;
	
	
	function __construct(  $templete = null )
	{
		import('@.Org.PHPExcel');
        if($templete)
            $this->excel = PHPExcel_IOFactory::load($templete);
        else
            $this->excel = new PHPExcel();
        $this->writer  =new PHPExcel_Writer_Excel2007($this->excel); // 用于 2007 格式

		$this->sheet = $this->excel->getActiveSheet();
	}
	
	function setActiveSheet($index = 0)
	{
		$this->sheet = $this->excel->setActiveSheet($index) ;
	}
	
	function getColumnName($column)
	{
		if(is_numeric($column))
			$column = $this->cellArray[$column];
		else if(! in_array($column , $this->cellArray ))
			$column = null;
			
		return $column;
	}


	
	function getNextColumnName($column)
    {
    	foreach($this->cellArray as $k=>$v)
    	{
    		if($v == $column)
    			$i = $k+1;
    	}
    	return $this->cellArray[$i];
    }

    //写入二维数组数据
    function setMutilArray($arr , $fromX = null , $fromY = null)
    {
        empty($formX )? $fromX = 'A' : "";
        empty($fromY) ? $fromY = 1 : "";

        foreach($arr as $v)
        {
            foreach($v as $v2)
            {
                $this->setC($fromX.$fromY ,$v2);
//                dump($fromX.$fromY);
                $fromX = $this->getNextColumnName($fromX);
            }
            $fromX = 'A';
            $fromY++;
        }
//        exit;
    }
	
	function setColumnWidth($column , $width)
	{
		if(is_numeric($column))
			$column = $this->cellArray[$column];
		else if(! in_array($column , $this->cellArray ))
			$column = null;
		if($column)
		{
			$this->sheet->getColumnDimension($column)->setWidth($width);
			return true;
		}
		return false;
	}

    public function setC($cell , $value)
    {
        $this->sheet->setCellValue($cell , $value);
    }


	public function setBorder($cell , $border_color = null)
	{
        $objStyleS = $this->sheet->getStyle($cell);
        $objBorder = $objStyleS->getBorders();
        $objBorder->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objBorder->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  
        $objBorder->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  
        $objBorder->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  
        if($border_color)
        {
        	$objBorder ->getTop()->getColor()->setARGB($border_color );	
        	$objBorder ->getBottom()->getColor()->setARGB($border_color );	
        	$objBorder ->getLeft()->getColor()->setARGB($border_color );	
        	$objBorder ->getRight()->getColor()->setARGB($border_color );	
        }
            
	}
	
	public function setCell($cell , $value , $background = null , $bold = false , $fontsize = 9 , $font = "宋体" , $left = false)
	{
		$this->sheet->setCellValue($cell , $value);

        if($left === true)
            $this->setCenter($cell , PHPExcel_Style_Alignment::HORIZONTAL_LEFT );
        else
            $this->setCenter($cell);
        $this->setMiddle($cell);
		$this->setFont($cell , $font , $fontsize);
		$this->setBorder($cell);
		if($background)
			$this->setBackground($cell , $background);
		if($bold)
			$this->setBold($cell );
	}

    function setBold($cell)
    {
        $this->sheet->getStyle($cell)->getFont()->setBold(true);
    }

    public function setCellString($cell , $value , $background = null , $bold = false , $fontsize = 9 , $font = "宋体" , $left = false)
    {
        $this->sheet->setCellValueExplicit($cell , $value);

        if($left === true)
            $this->setCenter($cell , PHPExcel_Style_Alignment::HORIZONTAL_LEFT );
        else
            $this->setCenter($cell);
        $this->setMiddle($cell);
        $this->setFont($cell , $font , $fontsize);
        $this->setBorder($cell);
        if($background)
            $this->setBackground($cell , $background);
//		if($bold)
//			$this->setBold($cell );
    }


    /**
     * 设置单元格格式  如：货币  日期等
     */
    public function setMoneyStyle($cell , $value )
    {
        $this->sheet->setCellValueExplicit( $cell , $value , PHPExcel_Cell_DataType::TYPE_NUMERIC );
    }
	
	//设置字体
	public function setFont($cell , $font , $size)
	{
		    $objFont = $this->sheet->getStyle($cell)->getFont(); 
		    $objFont->setName( $font) ; 
		    $objFont->setSize($size); 
	}
	
	public function setBackground($cell , $background)
	{
		$this->sheet->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);  
    	$this->sheet->getStyle($cell)->getFill()->getStartColor()->setARGB($background);  
	}
	
	//横向居中
	public function setCenter($cell  , $style = PHPExcel_Style_Alignment::HORIZONTAL_CENTER )
	{
		$this->sheet->getStyle($cell)->getAlignment()->setHorizontal( $style );
	}
	
	//垂直居中
	public function setMiddle($cell , $type = PHPExcel_Style_Alignment::VERTICAL_JUSTIFY )
	{
		$this->sheet->getStyle($cell)->getAlignment()->setVertical($type);
	}
	
	//设置一行数据
	public function setRow($row , $value)
	{
		if(!is_array($value))
			$this->setCell('A'.$row , $value);
		else
		{
			foreach($value as $k=>$v)
			{
				$cell = $this->cellArray[$k] . $row;
				$this->setCell($cell , $v);
			}
		}
	}

    public function setLineHeight($row , $height)
    {
        $this->sheet->getRowDimension($row)->setRowHeight($height);
//        $objPHPExcel->getActiveSheet()->getRowDimension('9')->setRowHeight(20);
    }
	//合并单元格
	public function merge($cellstart , $cellend )
	{
		$this->sheet->mergeCells("{$cellstart}:{$cellend}");
		$this->setCenter($cellstart);
		$this->setMiddle($cellstart);
		$this->setBorder("{$cellstart}:{$cellend}");
	}
	
	//分解单元格
	public function unmerge($cellstart , $cellend)
	{
		
	}

    public function save($pathandname)
    {
        return $this->writer->save($pathandname);
    }
	
	public function saveAndDownload($path = null ,$name = null)
	{
        $path = $path ? rtrim($path,"/" ) ."/"  : '';
        if($name === null)
            $name = date('Y-m-d-H-i-s').".xlsx";
        if(strpos($name,'.xlsx') === false)
            $name .= '.xlsx';
        $this->save($path.$name);

        $file = fopen( $path.$name ,"r"); // 打开文件
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length: ". filesize ($path.$name) );
        Header("Content-Disposition: attachment; filename=" . $name);
        // 输出文件内容
        echo fread( $file ,filesize( $path.$name ));
        fclose($file);
        exit();
	}
	
//	public function writeExcel($data ,$name = 'output.xlsx')
//	{
//		//$objWriter->setOffice2003Compatibility(true);
//
//		$cellarr = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ');
//		$i = 0;
//		$objActSheet = $objExcel->getActiveSheet();
////		$objActSheet->getColumnDimension('B')->setWidth(20);
////  		$objActSheet->getColumnDimension('A')->setWidth(20);
//		foreach($data as $k=>$v)
//		{
//			$cell = $cellarr[$i];
//			$date = date("Ymd" , $k);
//			$objActSheet->setCellValue($cell."1", $date); // 字符串内容
//		    $objActSheet->setCellValue($cell."2", (float) $v );            // 数值
//
//			//设置字体
//		    $objFont = $objActSheet ->getStyle($cell."1")->getFont();
//		    $objFont->setName( '新宋体');
//		    $objFont->setSize(10);
//
//			//设置字体
//		    $objFont = $objActSheet ->getStyle($cell."2")->getFont();
//		    $objFont->setName( '新宋体');
//		    $objFont->setSize(10);
//
//		    $i++;
//		}
//
//
//	}
	
}
?>