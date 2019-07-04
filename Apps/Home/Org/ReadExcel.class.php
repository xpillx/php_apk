<?php
class ReadExcel
{
    function __construct()
    {
        import('@.Org.PHPExcel');
    }

    function read($filePath , $ext = null , $startX = null , $startY = 2 , $endX = null ,$endY = null , $sheetIndex = 0)
    {
        $reader = $this->getReader($filePath , $ext);
        if(!$reader->canRead($filePath))
        {
            $PHPReader = new PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($filePath))
            {
                error("无法读取此excel文件，请联系管理员");
            }
        }

        $excel = $reader->load($filePath);
        $sheet = $excel->getSheet($sheetIndex);
        $endX = $endX ? $endX : $sheet->getHighestColumn();
        $endY = $endY ? $endY : $sheet->getHighestRow();
        $startX = $startX ? $startX : "A";
        $startY = $startY ? $startY : 1;

        for($currentRow = $startY;$currentRow <= $endY ; $currentRow++ ){
            /**从第A列开始输出*/
            for($currentColumn= $startX;$currentColumn<= $endX; $currentColumn++){
                $val = $sheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
                $val = $this->parseNumber($val); //将科学计数法转换为字符串
                $data[$currentRow][$currentColumn] = $val;
            }
        }
        return $data;
    }

    function readConf($filePath , $ext = null , $startX = null , $startY = 2 , $endX = null ,$endY = null , $sheetIndex = 0)
    {
        $reader = $this->getReader($filePath , $ext);
        if(!$reader->canRead($filePath))
        {
            $PHPReader = new PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($filePath))
            {
                error("无法读取此excel文件，请联系管理员");
            }
        }

        $excel = $reader->load($filePath);
        $sheet = $excel->getSheet($sheetIndex);
        $endX = $endX ? $endX : $sheet->getHighestColumn();
        $endY = $endY ? $endY : $sheet->getHighestRow();
        $startX = $startX ? $startX : "A";
        $startY = $startY ? $startY : 1;
        for($currentRow = $startY;$currentRow <= $endY ; $currentRow++ ){
            /**从第A列开始输出*/
            for($currentColumn= 0;$currentColumn<= 41; $currentColumn++){
                $val = $sheet->getCellByColumnAndRow($currentColumn,$currentRow)->getValue();/**ord()将字符转为十进制数*/
                $val = $this->parseNumber($val); //将科学计数法转换为字符串
                $data[$currentRow][$currentColumn] = $val;
            }
        }
        return $data;
    }


    function readstr($filePath , $ext = null , $startX = null , $startY = 2 , $endX = null ,$endY = null , $sheetIndex = 0)
    {
        $reader = $this->getReader($filePath , $ext);
        if(!$reader->canRead($filePath))
        {
            $PHPReader = new PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($filePath))
            {
                error("无法读取此excel文件，请联系管理员");
            }
        }

        $excel = $reader->load($filePath);
        $sheet = $excel->getSheet($sheetIndex);
        $endX = $endX ? $endX : $sheet->getHighestColumn();
        $endY = $endY ? $endY : $sheet->getHighestRow();
        $startX = $startX ? $startX : "A";
        $startY = $startY ? $startY : 1;
        for($currentRow = $startY;$currentRow <= $endY ; $currentRow++ ){
            /**从第A列开始输出*/
            for($currentColumn= 0;$currentColumn<= 40; $currentColumn++){
                $val = $sheet->getCellByColumnAndRow($currentColumn,$currentRow)->getValue();/**ord()将字符转为十进制数*/
                $val = $this->parseNumber($val); //将科学计数法转换为字符串
                $data[$currentRow][$currentColumn] = $val;
            }
        }
        return $data;
    }




    function getReader($filePath , $ext = null)
    {
        $ext = $ext ? $ext : substr( $filePath ,  strrpos($filePath , ".") +1 );
        switch(strtolower( $ext))
        {
            case "xlsx":
                return new PHPExcel_Reader_Excel2007();
            case "xls":
                return new PHPExcel_Reader_Excel5();
            case "csv":
                return new PHPExcel_Reader_CSV();

        }
        error("您上传的文件非excel文件!");
    }

    private function parseNumber($val){ //转换科学技术法为数值
        if(strpos($val ,"E+" ) !== false && intval($val)>0  && strpos($val, ".")!== false)
            return number_format($val , 0 , '' , '');
        return $val;
    }

}
?>