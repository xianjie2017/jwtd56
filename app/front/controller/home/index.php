<?php
/*
$arr    = array(  
    array(1),  
    array(2,3),  
    array(4,5,6) ,
	// array(4,5,6,8)
);  


$d = fun($arr); 
print_R($d); 
print_r($res);  
function fun($arr, $tmp = array(),&$data=[])  
{  
    foreach(array_shift($arr) as $value){  
		// 把value赋值给temp数组
        $tmp[]  = $value;  
		// 如果$arr不为空
		if( ! empty($arr)){			
			fun($arr, $tmp,$data);
		}else{
			$GLOBALS["res"][]   = $tmp;
			// $data[] = $tmp;
			$tmpStr = implode(',',$tmp);
			$tdHtml = '';
			foreach($tmp as $v){
				$tdHtml .= "<td>{$v}</td>";
			}
			$data[] = <<<EOF
			<tr>
				{$tdHtml}
				<td>{$tmpStr}</td>
			</tr>
EOF;
		}
		array_pop($tmp);
    }
	return $data;
} 

exit;*/
$rec_goods = select('goods','is_rec=1',"*",null,0,5);
$data = [
	'rec_goods' => $rec_goods
];
view($data);