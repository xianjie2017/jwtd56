<?php
$params['page'] = ! empty($_GET['page']) ? $_GET['page'] : 1;

$where = null;
$keywords = null;
if( ! empty($_POST['keywords'])){
	$keywords = $_POST['keywords'];
	$where = "goods_name like '%{$keywords}%'";
}

$page = page(array('goods as g','category as c','g.cid=c.id'),$where,$params,10,10,'g.*,c.name as cate_name');

foreach($page['data'] as $key => $value){
	
	if( ! empty($value['image'])){	
		$tempImageInfo = pathinfo($value['image']);
		// 缩略图
		$page['data'][$key]['thumb'] =  file_exists($value['image']."100x100.{$tempImageInfo['extension']}") ? $value['image']."100x100.{$tempImageInfo['extension']}" : '';
		// 水印文件
		$page['data'][$key]['water'] = file_exists($value['image']."water.{$tempImageInfo['extension']}") ? $value['image']."water.{$tempImageInfo['extension']}" : '';
	}
	
	$page['data'][$key]['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
}

// 分配到视图
$data = array(
	'page' 	=> $page,
	'keywords' => $keywords
);

// 加载视图
view($data);