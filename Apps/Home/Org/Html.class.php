<?php 
namespace Home\Org;

class Html
{
    public $html = '';
	function ul($data = null)
	{
		if($data !== null)
			$this->li($data);
		$this->html = '<ul>'.$this->html.'</ul>';
		return $this->html;
	}
	
	function li($data)
	{
		foreach($data as $k=>$v)
		{

			if(is_numeric($k))
			{
				if(strstr($v,'type="hidden'))
				{
					$this->html  .= $v ;
				}
				else
				{
					$this->html  .= '<li><div class="l">&nbsp;</div><div class="r">'.$v .'</div></li>';
				}
			}
			else
			{
				$this->html  .= '<li><div class="l">' . $k .'：</div><div class="r">'.$v .'</div></li>';
				
			}
		}
		return $this->html;
	}
	
	function form($action,$method = 'post',$file = true)
	{
		if(!$file)
		{
			$this->html = '<form action="'.$action.'" method="'.$method.'">'.$this->html.'</form>';
		}
		else
		{
			$this->html = '<form action="'.$action.'" method="'.$method.'" ENCTYPE="multipart/form-data">'.$this->html.'</form>';
			
		}
		return $this->html;
	}
	
	
	function createForm($html,$action,$method = 'post',$file = true)
	{
		if(!$file)
		{
			$re = '<form action="'.$action.'" method="'.$method.'">'.$html.'</form>';
		}
		else
		{
			$re = '<form action="'.$action.'" method="'.$method.'" ENCTYPE="multipart/form-data">'.$html.'</form>';
			
		}
		return $re;
	}

	/*生成table，数据格式如下：
	 * $field = array(10) {
	 *			  ["ID"] => string(2) "id"
	 *			  ["登录名"] => string(5) "uname"
	 *			  ["公司名称"] => string(7) "company"
				  
	 * $data = array(2)
	 * 		 [0] => array(3) {
			    ["id"] => string(1) "4"
			    ["uname"] => string(82) "<a href="/app/admin.php/Table?table=log_login&where=[uname]eq[shenhe]" >shenhe</a>"
			    ["pwd"] => string(32) "c3284d0f94606de1fd2af172aba15bf3"
			    ["company"] => string(74) "<a href="/app/admin.php/Table?table=product&where=[attr]eq[4]" >shenhe</a>"
	 * 		[1] => array(3) {
			    ["id"] => string(1) "4"
			    ["uname"] => string(82) "<a href="/app/admin.php/Table?table=log_login&where=[uname]eq[shenhe]" >shenhe</a>"
			    ["pwd"] => string(32) "c3284d0f94606de1fd2af172aba15bf3"
			    ["company"] => string(74) "<a href="/app/admin.php/Table?table=product&where=[attr]eq[4]" >shenhe</a>"
	 * 
	 * $attr 是针对表格的起始行 （th 那一行）的格式
	 * $attrs = array(
	 *  	["table"] => array(1) {
    				["attr"] => string(19) "style="width:100px""
  					}
			  ["read"] => array(1) {
			    ["attr"] => string(19) "style="width:100px""
			  })
	 */
	
	function table($field,$data,$attrs , $toggle = null)
	{
        $del = false;
        if(!empty($field['_del']))
        {
            $del = true;
            unset($field['_del']);
        }
		$column_count = count($field);
		$this->html = '';
		$this->html .= '<table><tr>';
        if($del)
            $this->html .= '<th><input type="checkbox" id="table-select-all" /></th>';
		$toggle_field = array();
		foreach($field as $k=>$v)
		{
			if($toggle && isset($toggle[0][$v])) //排除toggle的值
			{
				$toggle_field[$v] = $k; //将内容加入toggle_field
				unset($field[$k]);  //删除field
				continue;
			}
			$attr = isset($attrs[$v]['attr']) ? $attrs[$v]['attr'] : '' ;
			$this->html .= '<th '. $attr .'>'.$k.'</th>';
		}
		$this->html .= '</tr>';

        $toggle_html = '';
        $activity_play = 0;
        $activity_total = 0;
		foreach($data as $k=>$v)
		{
            $class = '';
            if (isset($v['_class'])) {
                $class = $v['_class'];
                unset($v['_class']);
            }
            $this->html .= '<tr class="' . $class . '">';
            if($del)
                $this->html .= "<td><input type=\"checkbox\" name=\"id[]\" value=\"{$v['id']}\" /></td>";
			foreach($field as $v2)
			{
				$attr2 = isset($attrs[$v2]['attr2']) ?$attrs[$v2]['attr2'] : '';
				$this->html .= '<td ' .$attr2. '>';
				$exp = explode('|',$v2);
				if(count($exp)>1)
				{
					foreach ($exp as $v3)
					{
						$this->html .= isset($v[$v3]) ?$v[$v3] . ' ': '';
					}	
				}
				else
				{
					$this->html .= isset($v[$v2]) ? $v[$v2] : '';
				}
				$this->html .= '</td>';

			}
            if($_GET["table"] == "redis_bear_activity"){
                if($_POST['activity_id'] == 1695){
                    $userRecord = json_decode($v['value'],true);
                    if($userRecord){
                        foreach($userRecord as $userR){
                            if(!$userR['used']) continue;
                            $activity_play += count($userR['used']);
                        }
                    }
                }else{
                    $activity_play += $v['valueCount'];
                }
                $activity_total ++;
            }
			$this->html .= '</tr>';

            //dump($toggle);
			if($toggle)
			{
				$toggle_html .="<div class=\"toggle\">" ;
				$toggle_html .= "<div class=\"operate\"><span class=\"icon_delete\"></span></div>" ;
				$toggle_html .="<table class=\"two_column\">";
				foreach($toggle[$k] as $toggle_k=>$toggle_v)
				{
					$toggle_html .="<tr>";
					$toggle_html .="<td>". $toggle_field[$toggle_k]."</td>";
					$toggle_html .="<th>". $toggle_v."</th>";
					$toggle_html .= "</tr>";
				}
				$toggle_html .="</table></div>";
			}
			
		}
		if($del)
        {
            $this->html .= <<<EOF
        <tr><td colspan='{$column_count}'><input type="submit" name="del" value="删除选中项" onclick="return confirm('确定要删除选中项目?');" /></td></tr>
        <script>
        $("#table-select-all").click(function(){
             if($(this).is(':checked'))
                    $("input[type='checkbox']",$(this).parent().parent().parent()).prop("checked","true");
                else
                    $("input[type='checkbox']",$(this).parent().parent().parent()).removeAttr("checked");
        });
        </script>
EOF;
        }
        if($_GET["table"] == "redis_bear_activity"){
            $this->html .=  "<tr>
                <td colspan='5'>
                    <span style='color:red;font-size:16px;'>总计：".$activity_total."</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style='color:red;font-size:16px;'>参与次数总计：".$activity_play."</span>
                </td>
            </tr>";
        }
		$this->html .= '</table>';
		$this->html .= $toggle_html;
		return $this->html;
	}

    function sortTable($field, $data , $attrs )
    {
        //dump($attrs);
        unset($field['操作']);
        foreach($field as $k=>$v)
        {
            if( strstr($v,'|') )
            {
                $exp = explode('|',$v);
                foreach($exp as $v2)
                {
                    foreach($data as $data_k=>$data_v)
                    {
                        if(isset($data_v[$v2]))
                            $data[$data_k][$v] .= '【' .$data_v[$v2].'】 ';
                    }

                }
            }
            $fields[$v] = $k;
        }

        $h = array();
        foreach($data as $k=>$v)
        {
            $temp = '<table><tr>';
            foreach($v as $k2=>$v2)
            {
                $tempAttr = empty($attrs[$k2]['attr2'])? '':$attrs[$k2]['attr2'];
                if($k2 == 'id')
                    $temp.="<td {$tempAttr}><input type=\"hidden\" name=\"id[]\" value=\"{$v2}\"/>{$fields[$k2]}:<span>{$v2}</span></td>";
                else if(isset($fields[$k2]))
                    $temp.="<td {$tempAttr}>{$fields[$k2]}:<span>{$v2}</span></td>";
            }
            $temp .="<td class=\"drag\">拖动排序</td>";
            $h[] = $temp . '</tr></table>';
        }

        $re = '<ul id="sortable" class="sort_table">';
        foreach($h as $v)
            $re .="<li>{$v}</li>";
        $re .= '</ul>';
        $re.="<hr />&nbsp;&nbsp;&nbsp;<input type=\"submit\" value=\"提交排序\" />";
        $re .= <<<EOT
            <script>
                $(function() {
                    $( "#sortable" ).sortable();
                    $( "#sortable" ).disableSelection();
                });
            </script>
EOT;
        return "<form method=\"post\">$re</form>";
    }
	
	function twoColumnTable($field,$data)
	{
		foreach($field as $k=>$v)
		{
			if(strpos($v,'|') !== false)
			{
				$exp = explode('|',$v);
				foreach($exp as $v2)
				{
					if(isset($data[$v2]))
					{
						$data[$v] .= $data[$v2].' ';
					}
				}
			}
			$fields[$v] = $k;
		}
		
		$this->html .= '<table class="two_column">';
		foreach($data as $k=>$v)
		{
			if(isset($fields[$k]))
				$this->html.="<tr><td>{$fields[$k]}</td><th>{$v}</th></tr>";
		}
		$this->html .='</table>';
	}
	
	/**
	 * 根据传入的类型，名称，值，数据 生成对应的input的html
	 * 
	 * @param string $type   , 类型，如：text hidden submit file textarea checkbox ……
	 * @param string $name ，名称，如：<input type="text" name="$name
	 * @param string $value  ，值，如：<input type="text" name="$name" value="$value"
	 * @param array  $data   ，数据：当$type为select或checkbox时，需要传递数据。如：array('下载'=>'1','分享'=>'2','邀请'=>'3')
	 * @param array  $attr   ,  属性：array('size'=>'60','rows'=>'6'),将被解析为 size="60" rows="6"
     * @return mixed
	 */
	function createInput($type,$name = null,$value = null,$data = null,$attr = null)
	{
		if(!$type)
			return '';
		
		$attributes = $this->parse_attr($attr);
        switch($type)
        {
            case 'number':
            case 'text':
            case 'hidden':
            case 'submit':
            case 'file':
                $html = '<input type="'.$type .'"';
                if($name)
                    $html.= ' name="'.$name .'"';
                if($value !== null)
                    $html.= ' value=\''.$value .'\'';
                $html .= $attributes . ' />';
                $html .= $type=='file' && $value  ?  '<br/><span>已上传：'.$value."</span>" : '';
                return $html;
            case 'readonly':
                $html = '<input type="hidden"';
                if($name)
                    $html.= ' name="'.$name .'"';
                if($value !== null)
                    $html.= ' value="'.$value .'"';
                $html .=  ' />' . $value;
                return $html;
//                return $value;

            case 'textarea':
                if($attributes == '')
                    $html = '<textarea rows="5" cols="60"' ;  //默认大小
                else
                    $html = '<textarea '.$attributes ;

                if($name)
                    $html.= ' name="'.$name.'">';
                if($value)
                    $html.= $value;
                $html .= '</textarea>';
                return $html;

            case 'textarea2':
                if($attributes == '')
                    $html = '<textarea rows="5" cols="60"' ;  //默认大小
                else
                    $html = '<textarea '.$attributes ;

                if($name)
                    $html.= ' name="'.$name.'">';
                if($value)
                    $html.= br2n($value);
                $html .= '</textarea>';
                return $html;
            case 'select':
            case 'checkbox':
                if(!is_array($data))
                    return '无数据';
                return $this->$type($name,$data,$value,$attr);

            case 'radio':
                return $this->$type($name,$data,$value,$attr);

            case 'date':
                static $js_date_import = 0;
                $v= '';
                if($value)
                {
                    if(is_numeric($value) && $value>1000 )
                    {
                        $v = ' value="'.date('Y-m-d',$value).'" ';
                    }
                    else
                    {
                        $v = ' value="'.$value.'" ';
                    }
                }
                $html = '<input type="text" name="' .$name.  '"'.$v . ' onclick="fPopCalendar(event,this,this)" onfocus="this.select()" readonly="readonly" />';
                if($js_date_import == 0 )
                {
                    $html .= '<script lanugae="javascript" src="Public/js/date.js"></script>';
                    $js_date_import = 1;
                }
                return $html;

            case 'datetime':
                static $jquery_ui_import = 0;
                $v= '';
                if($value)
                {
                    if(is_numeric($value) && $value>1000 )
                    {
                        $v = ' value="'.date('Y-m-d H:i:s',$value).'" ';
                    }
                    else
                    {
                        $v = ' value="'.$value.'" ';
                    }
                }
                $html = '<input type="text" name="' .$name.  '"'.$v . ' class="datetimetext" />';
                if($jquery_ui_import == 0 )
                {
                    $html .= '<script langugae="javascript" src="Public/js/datetimepicker.js"></script>';
                    //onclick="fPopCalendar(event,this,this)" onfocus="this.select()"
                    $html .= <<<EOF
                <script>
                    $(function(){
                        $('.datetimetext').datetimepicker({dateFormat:'yy-mm-dd',});
                    });
                </script>
EOF;
                    $jquery_ui_import = 1;
                }
                return $html;

            case 'editor':
                if($attributes == '')
                    $html = '<textarea rows="5" cols="60"' ;  //默认大小
                else
                    $html = '<textarea '.$attributes ;

                if($name)
                    $html.= ' name="'.$name.'">';
                if($value)
                    $html.= $value;
                $html .= '</textarea>';
                static $import ;
                if(!$import)
                {
                    $html .='<script src="Public/ckeditor/ckeditor.js" type="text/javascript"></script>';
                    $import = true;
                }
                $html .= '<script type="text/javascript">
                            var editor = CKEDITOR.replace("'.$name.'");
                            ckfinder_path = "Public/ckfinder";
                            CKFinder.SetupCKEditor(editor, "Public/ckfinder/");
                        </script>';
                return $html;


            case 'selectmove':
                static $move_select_count = null;
                $h = '';
                if($move_select_count === null)
                {
                    //$h .= '<script type="text/javascript" src="'.__PUBLIC__.'/js/admin/select_move.js"></script> ';
                    $move_select_count = 1;
                }
                else
                {
                    $move_select_count++;
                }
                $id_prefix = "move-select-{$move_select_count}-";
                $base_id_attr = $attr." id=\"{$id_prefix}base\"";
                $con_id_attr = $attr." id=\"{$id_prefix}container\"";

                //$data 为存储在待选select的数据  ，$save_data为以选中的数据
                $def = strToArray($value);
                $save_data  = array();
                foreach($data as $k=>$v)
                {
                    if(in_array($v , $def))
                    {
                        $save_data[$k] = $v;
                        unset($data[$k]);
                    }
                }

                //原始数据的select
                $base ="<div class=\"select_move_base\"> ".$this->select("{$name}_old" , $data , null ,  $base_id_attr ,  true) ."</div>";

                //按钮的内容
                $btn = <<<EOT
                <div class="select_move_btn">
                    <a id="{$id_prefix}in"> > </a>
                    <a id="{$id_prefix}fill"> >> </a>
                    <a id="{$id_prefix}out"> < </a>
                    <a id="{$id_prefix}empty"> << </a>
                </div>
EOT;
                //保存的select
                $save ="<div class=\"select_move_container\"> ".$this->select("{$name}[]" , $save_data  ,null , $con_id_attr ,  true) ."</div>";

                $html = $h . '<div id="select_move_'.$move_select_count.'" class="select_move">'. $base.$btn.$save . "</div>";
                $html .= <<<EOT
            <script>
                $(function(){
                    $("#select_move_{$move_select_count}").moveSelect({prefix : "#{$id_prefix}"});
                });
            </script>
EOT;
                return $html;
            case 'ajaxtext':
                return $this->ajaxText($name , $value , $attr);
            default:
                return '';
        }
	}

    /**
     * 将array("rows"=>"100","cols"=>"100")  转换为 rows="100" cols="100"
     * @param string|array $attr
     * @return null|string
     */
	function parse_attr($attr = null)
	{
		$re = '';
		if(is_string($attr))
			return $attr;
		
		if(is_array($attr))
		{
			foreach($attr as $k=>$v)
			{
				$re .= $k .'="'.$v.'" ';
			}
		}
		return $re;
	}
	
	/**
	 * 
	 * @param string $name 
	 * @param array $data
	 * @param unknown_type $value
	 * @param unknown_type $attr
	 * @return string
	 */
	function checkbox($name,$data,$value = null,$attr = null)
	{
		$arr = array();
		if(is_string($value))
		{
			$arr = strToArray($value);
		}
		else
		{
			$arr = $value;
		}
	
		$html = '<table><tr>';
        $html .= '<td><input type="checkbox"  id="selectall'.$name.'"/><label for="selectall'.$name.'">全选</label></td>';

		$i = 1;
		foreach($data as $k=>$v)
		{
			$v = (string)$v;
			$check = $td ='';
			if(is_array($arr) && in_array($v, $arr))
			$check = " checked=checked";
			$td = 'class="selected_td"';
			$html .= '<td><input id="' .$name.$v. '" type="checkbox" name="'.$name.'[]" value="'.$v.'"'.$check. $this->parse_attr($attr) .'/> <label for="'.$name.$v.'">'.$k.'</label></td>';
			if($i++%8 == 0)
				$html .="</tr><tr>";
		}
		$html.="</tr></table>";

        $html .= <<<EOF
        <script>
        $(function(){
            $("#selectall{$name}").click(function(){
                if($(this).is(':checked'))
                    $("input[type='checkbox']",$(this).parent().parent().parent()).prop("checked","true");
                else
                    $("input[type='checkbox']",$(this).parent().parent().parent()).removeAttr("checked");
            });
        });
        </script>
EOF;
        return $html;

	}
	
	
	
//	function select($name,$data,$value=null,$attr = null)
//	{
//		$html = '<select name="'.$name.'"'. $this->parse_attr($attr) .'><option value="">请选择</option>';
//		foreach($data as $k=>$v)
//		{
//			$selected='';
//			if(is_string($value) && $value == $v)
//			$selected = " selected=selected";
//
//			$html .= '<option  value="'.$v.'" '. $selected .'>'.$k.'</option>';
//		}
//		$html.='</select>';
//		return $html;
//	}
    function select($name,$data,$value=null,$attr = null , $mutil = false)
    {
        $mutil_html = $mutil ? 'multiple="multiple"' : '';
        $def = $mutil || count($data) == 1 ? '' : '<option value="">请选择</option>';
        $html = '<select '.$mutil_html.' name="'.$name.'"'. $this->parse_attr($attr) .'>'.$def;
        foreach($data as $k=>$v)
        {
            $selected='';
            if(is_string($value) && $value == $v)
                $selected = " selected=selected";

            $html .= '<option  value="'.$v.'" '. $selected .'>'.$k.'</option>';
        }
        $html.='</select>';
        return $html;
    }
	
	function radio($name,$data,$value,$attr = null)
	{
		if(is_array($data))
		{
			$html = '';
			foreach($data as $k=>$v)
			{
				$checked = (string)$value === (string)$v ? "checked=checked" :"";
				$id = $name.$v;
				$html .='<label for="'.$id.'">' .$k .'</label><input type="radio" id="'.$id.'" name="'.$name.'" value="'.$v.'" '.$checked.' '. $this->parse_attr($attr) . '/> &nbsp;&nbsp;&nbsp;&nbsp;';
			}
			return $html;
		}
		
		if($value === '1' || $value === 1)
		{
			$html = '<input type="radio" group name="'.$name.'" value="1" checked="checked" '. $this->parse_attr($attr) . '/>是 &nbsp;&nbsp;&nbsp;&nbsp;'.
			'<input type="radio" name="'.$name.'" value="0" '. $this->parse_attr($attr) . '/>否';
		}
		else if($value === '0' || $value === 0)
		{
			$html = '<input type="radio" name="'.$name.'" value="1" '. $this->parse_attr($attr) . '/>是 &nbsp;&nbsp;&nbsp;&nbsp;'.
					'<input type="radio" name="'.$name.'" value="0" checked="checked" '. $this->parse_attr($attr) . '/>否';
		}
		else
		{
			$html = '<input type="radio" name="'.$name.'" value="1" '. $this->parse_attr($attr) . '/>是 &nbsp;&nbsp;&nbsp;&nbsp;'.
					'<input type="radio" name="'.$name.'" value="0" '. $this->parse_attr($attr) . '/>否';
		}
		return $html;
	}


    /**
     * @param $name
     * @param $value
     * @param $attr
     * $attr 必须配置 : url="url地址"  :
     * 如：http://gcenter.joymeng.com/index.php?m=Home&c=Table&a=index&table=channel_mst&val=
     * 注意：url的末尾必须定义当前text的值的键，在进行ajax提交时，系统会自动把当前text的值串联到url地址里面，并进行相应请求
     */
    function ajaxText($name , $value , $attr)
    {
        static $import ;
        $script = '';
        if($import === null)
        {
            $script = <<<EOT
                <script>
                    $(function(){
                        $(".ajaxtext").ajaxError(function(){
                            loading(0);
                            notice("ajax请求错误");
                        });
                        $(".ajaxtext").blur(function(){
                            var the = this;
                            var data = $(this).attr("data");
                            var val = $(this).val();
                            var url = $(this).attr("url");

                            if(!url || !val)
                                return ;

                            if(val == data)
                            {
                                notice("未修改");
                                return ;
                            }
                            url += val;
                            debug(url);
                            var load = setTimeout('loading(1)',300);
                            $.post(url,'',function(data){
                                $(the).attr("data" , val);
                                clearTimeout(load);
                                loading(0);
                                notice(data.info);
                            },'json');
                        });
                    });
                </script>
EOT;
            $import = true;
        }

        $attr = $this->parseAttr($attr);
        $re = "<input class=\"ajaxtext\" type=\"text\" value=\"{$value}\" name=\"{$name}\" data=\"{$value}\" {$attr}/> {$script}";
        return $re;
    }
}

?>
