<?php

// 第一步 ： 从数据库读取分类数据
$catData = select('article_category');
// 把变量分配到视图
$data = array(
	'catData' => $catData
);
// 加载视图
view('',$data);
?>