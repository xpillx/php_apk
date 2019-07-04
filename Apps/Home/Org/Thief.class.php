<?php
namespace Home\Org;
class Thief
{
	private $con='' ;     //处理文本内容
	private $header = '';  //headers
	private $all = '' ;  //所有内容 包含header 和con
	private $status='';   //返回状态吗
	private $curl_info = '';   //返回状态数据
	private $url  = null;
	
	private $setContented = false; //con是不是外部设置的，如果是外部设置的那么preg的时候，不需要验证状态码 
	

	/**
	 * $curl_info 数据如下
	 *   ["url"] => string(20) "http://www.baidu.com"
	 *	  ["content_type"] => string(23) "text/html;charset=utf-8"
	*	  ["http_code"] => int(200)
	*	  ["header_size"] => int(579)
	*	  ["request_size"] => int(140)
		  ["filetime"] => int(-1)
		  ["ssl_verify_result"] => int(0)
		  ["redirect_count"] => int(0)
		  ["total_time"] => float(0.359)
		  ["namelookup_time"] => float(0)
		  ["connect_time"] => float(0.141)
		  ["pretransfer_time"] => float(0.141)
		  ["size_upload"] => float(0)
		  ["size_download"] => float(10447)
		  ["speed_download"] => float(29100)
		  ["speed_upload"] => float(0)
		  ["download_content_length"] => float(10447)
		  ["upload_content_length"] => float(0)
		  ["starttransfer_time"] => float(0.265)
		  ["redirect_time"] => float(0)
		  ["certinfo"] => array(0) {
		  }
		  ["redirect_url"] => string(0) ""
	 * 
	 */
	
    
	function __construct($url = null ,$post_data = null , $cookie = null , $charset = 'utf-8')  //$gzip 表示该网页是否经过压缩,如果为ture则会进行gzip解码
	{
		if($url)
		{
			$this->setUrl($url,$post_data , $cookie , $charset);
		}
	}
	
	function getContent()
	{
		return $this->con;
	}
	
	function setContent($con)
	{
		$this->setContented = true;
		$this->con = $con;
	}
	
	function getInfo()
	{
		return $this->curl_info;
	}
	
	function getHeader()
	{
		return $this->header;
	}
	
	function getAll()
	{
		return $this->all;
	}
	
	/**
	 * 单线程模式
	 */
	function setUrl($url ,  $post_data = null ,   $cookie = null , $charset = 'utf-8')
	{
		$this->clear();
		$this->url = $url;

		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_TIMEOUT, 20);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
		curl_setopt($ch,CURLOPT_HEADER, true);
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.48 Safari/537.36');
		
		if($post_data !== null)
		{
			if($charset && $charset !== 'utf-8' )
			{
				foreach($post_data as $k=>$v)
				{
					$post_data[$k] = iconv('UTF-8',$charset.'//IGNORE',$v);	
				}
			}
			curl_setopt($ch,CURLOPT_POST,true);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
		}
		
			
		if($cookie !== null)
		{
			curl_setopt($ch, CURLOPT_COOKIE , $cookie);
		}
			
		if(strpos($url,'https') !== false)  //https请求
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		}
			
			
		
		$this->all = curl_exec($ch);
		$this->status=curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$this->curl_info = curl_getinfo($ch);
		//$encode=curl_getinfo($ch,CURLINFO_CONTENT_TYPE);
		$re = curl_getinfo($ch);
		curl_close($ch);
		
		/*<meta http-equiv="content-type" content="text/html;charset=utf-8">*/

		if($charset && $charset !== 'utf-8' )
		{
			$this->all =iconv($charset,'UTF-8//IGNORE',$this->all);	
		}
	
		
//		
		//将内容分解为 header  和  实际内容两部分
		$header_size = $this->curl_info['header_size'];
		$this->header = substr($this->all, 0 , $header_size) ;
		$this->con = substr($this->all , $header_size) ;

		//debug($this->header , $url . "的Headers");
		//debug($this->con, $url . "的contents");
	}
	
	
	
	/**
	 * 多线程模式
	 * setUrls 将采用多线程方式来获取url内容
	 * 
	 */
	function setUrls($urls , $post_data = null , $cookie = null , $charset = 'utf-8' )
	{
		//TODO windows + nginx + fastcgi 尚未解决此问题
		$re["status"]=0;
		$this->clear();
		$this->url = $urls;
		
		$curls = curl_multi_init();
		$chs = array();
		foreach($urls as $k=>$url)
		{
			$ch=curl_init($url);
			curl_setopt($ch,CURLOPT_TIMEOUT, 20);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
			curl_setopt($ch,CURLOPT_HEADER, true); 
			curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0');
			
			if($post_data !== null)
			{
				if($charset && $charset !== 'utf-8' )
				{
					foreach($post_data as $key=>$v) //转换post的数据编码
					{
						$post_data[$key] = iconv('UTF-8',$charset.'//IGNORE',$v);	
					}
				}
				curl_setopt($ch,CURLOPT_POST,true);
				curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
			}
			
				
			if($cookie !== null)
				curl_setopt($ch, CURLOPT_COOKIE , $cookie);
				
			if(strpos($url,'https') !== false)  //https请求
			{
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			}
		
			$chs[$k] = $ch;
			curl_multi_add_handle( $curls , $chs[$k]);
		}


		$active = null;
		do 
		{ 
			$mrc = curl_multi_exec($curls, $active); 
		} 
		while ($mrc == CURLM_CALL_MULTI_PERFORM); 

		try
		{
			while ($active and $mrc == CURLM_OK) 
			{ 
				// wait for network 
				if (curl_multi_select($curls) != -1) 
				{ 
				// pull in any new data, or at least handle timeouts 
					do
					{ 
						$mrc = curl_multi_exec($curls, $active); 
					}
					while ($mrc == CURLM_CALL_MULTI_PERFORM); 
				}
			}
		}
		catch(Exception $e)
		{
			$re['msg'] = "Thief的多线程模式出现异常：" . $e->getMessage(); 
			return $re;
		}

		if ($mrc != CURLM_OK) 
		{ 
			$re['msg'] = "Thief的多线程模式出现异常：" . $mrc ; 
			return $re;
		}

		foreach($urls as $k=>$v)
		{
			$err = curl_error($chs[$k]) ;
			if( $err == '')
			{
				$this->all[$v] = curl_multi_getcontent($chs[$k]);
				$info =  curl_multi_info_read($chs[$k]);
				if($charset && $charset !== 'utf-8' )
				{
					$this->all[$v] =iconv($charset,'UTF-8//IGNORE',$this->all[$v]);	
				}
				//将内容分解为 header  和  实际内容两部分
				$header_size = $info['header_size'];
				$this->header[$v] = substr($this->all[$v], 0 , $header_size) ;
				$this->con[$v] = substr($this->all[$v] , $header_size) ;
				
			}
			else
			{
				$re['msg'] = "Thief的多线程模式出现异常：" . $err ;
				return $re; 
			}
		  	curl_multi_remove_handle($curls , $chs[$k] ); 
			curl_close($chs[$k]); 

		}	
	
		$re['status'] = 1;
		return $re;
	}
	
	/**
	 * 清空数据，是本类变量还原
	 */
	private function clear()
	{
		$this->con = '';
		$this->header = '';
		$this->curl_info = null;
		$this->status = null;
		$this->url = null;
		$this->setContented = false;
	} 
	
	function getDownloadSize()
	{
		return $this->curl_info['size_download'];
	}
	
	
	function getCookie()
	{
		preg_match('/Set-Cookie:(.+)path.+/U',$this->header , $re );
		if($re[1])
		{
			//debug($re , 'COOKIE :');
			return $re[1];
		}
		else if ( preg_match('/Set-Cookie:(.+)/',$this->header , $re ) )
		{ 
			debug($re , 'COOKIE :');
			return $re[1];
		}
	}
	
	
	
	private function checkErr($mat,$split,$err)  //检查错误
    {
        $re["status"]=0;
		if(!is_array($mat) || empty($mat))   
		{
			$re["msg"]='匹配的正则表达式必须为数组!';
			return $re;
		}
		
		foreach($mat as $v)
		{
			if(strpos($v,'|a') === strlen($v)-2)
				$v = substr($v ,  0 ,strpos($v,'|a'));

			if( preg_match($v,"")===false )
			{
				$re["msg"]='匹配的正则表达式语法错误!' . $v;
				return $re;
			}
		}
		
		if($split && preg_match( $split ,"")===false)
		{
			$re["msg"]="分段的正则表达式结构错误!";
			return $re;
		}
		
		if(!$this->setContented && !$this->con) //非外部设置的内容
		{
			$re["msg"]="获取超时！{$this->url} " ;
			return $re;
		}
		
		if(!$this->setContented && $this->status!=200)
		{
			$re["msg"]="状态码错误！{$this->url}";
			return $re;
		}
		
		if($err)
		{
			$errs = array();
			if(is_string($err))
				$errs[0] = $err;
			else
				$errs = $err;
			foreach($errs as $v)
			{
				if(preg_match($v , '' ) === false)
				{
					$re["msg"]="匹配错误页面的正则表达式不正确! " . $v;
					return $re;
				}
					
				if(preg_match( $v ,$this->con)===1)
				{
					$re["msg"]="错误的页面!";
					return $re;
				}
				
			}
		}
			
			
		$re["status"]=1;
		return $re;
    }
    


	
	/**
	 *	匹配内容
	 * $mat array   如：array('id'=>'lsitid(.+)sss','key'=>'keysdfaslfj(.+)key') ,  返回正则表达式括号内匹配的内容
	 * $spilit string   将整个内容分成各个部分来进行匹配多项 
	 * $err array|string    数组或字符串，如果页面内容包含匹配到$err的正则，则表示进入了错误页面
	 * 
	 */

	function preg( $mat , $split='', $count = 0, $err=NULL)   //匹配并返回
	{
		$stime = microtime(true);
		$checkRe = $this->checkErr($mat,$split,$err);
		$re=$checkRe;
		if($checkRe["status"]==1)
		{
			if($split)
			{   
				if(is_string($split))
				{
					preg_match_all( $split ,$this->con,$split_con);
					$s_con=$split_con[0];
				}
				else
				{
					$re["status"]=0;
					$re["msg"]="分段的正则表达式必须为字符串!";
					return $re;
				}
			}
			else
			{
				$s_con[0] = $this->con;
			}

			$num = 0;
			foreach($s_con as $key=>$val)
			{
				$num++;
				if($count && $count < $num)
					break;
						
				foreach($mat as $k=>$v)
				{
					/*
					preg_match($v,$val,$pipei);
					array_shift($pipei);
					if(count($pipei) === 0 )
						$re["data"][$key][$k]= null ;
					else if(count($pipei) === 1)
						$re["data"][$key][$k]=trim( $pipei[0]);
					else
						$re["data"][$key][$k]=trim( $pipei[count($pipei)-1]);
					 */
					$re['data'][$key][$k] = $this->pregs($val , $v);
				}
			}
				
			
		}
		
		$re['preg_time'] =  round( microtime(true) - $stime , 3)  ;
        //$re["fi"]=$this->mustField;
		return $re;
	}

	/**
	 *
	 *特殊的正则表达式匹配：
	 *支持模式： /aaaaaa/sU|a  ，此时会匹配所有的内容 ，返回的内容是一个数组
	 */
	private function pregs($con , $reg)
	{
		if(strpos($reg , '|a') === strlen($reg)-2 )
		{
			$reg = substr($reg , 0 , strpos($reg,'|a'));
			preg_match_all($reg, $con, $pipei);
		}
		else
		{
			preg_match($reg,$con,$pipei);
					/*
			array_shift($pipei);
					if(count($pipei) === 0 )
						$re["data"][$key][$k]= null ;
					else if(count($pipei) === 1)
						$re["data"][$key][$k]=trim( $pipei[0]);
					else
						$re["data"][$key][$k]=trim( $pipei[count($pipei)-1]);
					 */
		}
		array_shift($pipei);
		if(count($pipei) === 0 )
			$re = null ;
		else if(count($pipei) === 1)
			$re =  $pipei[0] ;
		else
			$re = $pipei[count($pipei)-1];

		if(is_string($re))
			return trim($re);
		if(is_array($re) ) 
			return arrayToStr($re,'|');
		return $re;
	}

    private function redFont($val)
    {
        return "<span style='color:red'>".$val."</span>";
    }
    

}
?>
