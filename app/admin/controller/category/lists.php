<?php

// 第一步 ： 从数据库读取分类数据
$catData = select('category');

$cateArray = get_cate_child_array($catData);

// 分类tr
$cateTrHtml = get_cate_tr_html($cateArray);

// 把变量分配到视图
$data = array(
	'cateTrHtml' => $cateTrHtml
);
// 加载视图
view('',$data);
?>