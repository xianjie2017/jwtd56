<?php
// 查询运单号
$result = [];
if( ! empty( $_POST) ){
    $no = ! empty($_POST['no']) ? $_POST['no'] : '';
    if( empty($no)){
        msg('运单号不能为空！');
    }

    $no_array = explode("\r\n",$no);

    // 获取每个单号的结果
    $result = [];
    foreach ( $no_array as $key => $value ) {
        $result[$value] = getNoResult($value);
    }
}

view([
    'result' => $result
]);

// 获取运单号物流状态
function getNoResult($no)
{
    $url = 'www.yn56rj.com/a/M56Web.aspx?Co=SZJWTDGYLGLYXGS_20170529_34&No=' . $no;
    $data = pcurl($url);
    
    $rules = '/<div id="results" class="rw">[.\w\W\s\S]*?<div>([.\w\W\s\S]*?)<\/div>/';
    $res = preg_match($rules,$data,$matches);

    if($res){
        return trim($matches[1]);
    }else{
        return false;
    }
}