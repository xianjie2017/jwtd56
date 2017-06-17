<?php
/**
 * curl
 *
 * @author XJ
 * @param 地址 $url
 * @param 数组 $post_data
 * @param 文件名 $cookiefile
 * @param 是否xml $xml
 * @return 内容
 */
function pcurl($url,$post_data=array(),$cookiefile='',$xml=''){

	if(!$cookiefile)
	$cookiefile = tempnam('./temp','cookie');
	$ch = curl_init($url);

	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);

	curl_setopt($ch, CURLOPT_REFERER, $url);

	if($xml){
		$header=array("Content-type: text/xml");//定义content-type为xml
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}

	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$contents = curl_exec($ch);
	curl_close($ch);

	$_SESSION['cookiefile']=$cookiefile;
	return $contents;
}

// 递归找出所有子分类id
function get_cate_child_id_array($array,$pid=0){
		
	// 定义一个空数组，用来存放查找出来的分类id
	$id_array = array();
	foreach($array as $key => $value){
		if($value['pid'] == $pid) {
			$id_array[] = $value['id'];
			$temp = get_cate_child_id_array($array,$value['id']);
			// 数组的合并
			$id_array = array_merge($id_array,$temp);
		}
	}	
	
	return $id_array;
}

// 递归找出子分类
function get_cate_child_array($array,$pid=0)
{
	// 定义空数组
	$temp = array();
	foreach($array as $key => $value){	
		// 判断自己的pid是否等于接收到的$pid参数
		if($value['pid'] == $pid){
			// 调用自己
			$value['child'] = get_cate_child_array($array,$value['id']);
			$temp[] = $value;
		}
	}
	return $temp;
}

// 递归分类下拉表单
function get_cate_select($array,$pid=0,$level=0)
{
	$html = '';
	foreach($array as $key => $value) {
		$nbspStr = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',$level);
		/*$nbspHtml = '';
		if($level >= 1){
			for($i=1;$i<=$level;$i++){
				$nbspHtml .= $nbspStr;
			}
		}*/
		$html .= "<option value='{$value['id']}' ".($pid==$value['id'] ? " selected='selected'" : '').">{$nbspStr}{$value['name']}</option>";
		if( ! empty($value['child'])){
			$html .= get_cate_select($value['child'],$pid,$level+1);
		}
	}
	return $html;
}

function get_cate_tr_html($array,$level=0)
{
	$html = '';
	
	
	$colorStr = '0123456789abc';
	
	$color = "#".$colorStr[mt_rand(0,strlen($colorStr)-1)].$colorStr[mt_rand(0,strlen($colorStr)-1)].$colorStr[mt_rand(0,strlen($colorStr)-1)].$colorStr[mt_rand(0,strlen($colorStr)-1)].$colorStr[mt_rand(0,strlen($colorStr)-1)].$colorStr[mt_rand(0,strlen($colorStr)-1)];
	
	
	foreach($array as $key => $value) {	
	
		$parent = ! empty($value['pid']) ? "data-tt-parent-id='{$value['pid']}'" : '';
		$updateUrl = url('update',array('id'=>$value['id']));
		
		$str = <<<EOF
			<tr data-tt-id="{$value['id']}" {$parent}>
				<td>{$value['id']}</td>
				<td>{$value['name']}</td>
				<td>
					<a href="{$updateUrl}" style="margin-right:10px;"><span class="glyphicon glyphicon-pencil"></span></a>
					<a href="javascript:;" onclick="del({$value['id']})" style="margin-right:10px;"><span class="glyphicon glyphicon-trash"></span></a>
				</td>
			</tr>
EOF;
		$html .= $str;
		if( ! empty($value['child'])){
			$html .= get_cate_tr_html($value['child'],$level+1);
		}
	}
	return $html;
}