<?php
/*
1 ----- 0     (1 - 1) * $showCount
2 ----- 10    (2 - 1) * $showCount
3 ----- 20    (3 - 1) * $showCount
4 ----- 30    (4 - 1) * $showCount
5 ----- 40    (5 - 1) * $showCount

celi((10 -1 ) / 2) = 5
celi((11 -1 ) / 2) = 5

*/

$page = ! empty($_GET['page']) ? $_GET['page'] : 1;
// 找出所有文章总数
$articleCount = rows('article');
// 每页需要显示的条数
$showCount = 10;
// 需要显示的页码数
$showPage = 10;
// 页面总数 = 文章总条数  /  每页要显示的数量
$pageCount = ceil($articleCount / $showCount);
// 步长
$step = ceil(($showPage - 1) / 2);
// 开始显示的页码
$startPage = $page - $step;
// 结束的页码
$endPage = $page + ($showPage - 1 - $step);
// 当开始页码<=1的时候
if($startPage<=1) {
	$startPage 	= 1;
	$endPage 	= $showPage;
}
// 当前页码>=总页码数的时候
if($endPage >= $pageCount){
	$endPage 	= $pageCount;
	$startPage 	= $pageCount - $showPage + 1;
	if($startPage <= 1){
		$startPage = 1;
	}
}
// 每页要开始显示的位置 (当前页面 - 1) * 每页要显示的数量
$offsetPage = ($page - 1) * $showCount;
// 上一页
$prePage = $page - 1;
if($prePage<1){
	$prePage = 1;
}
// 下一页
$nextPage = $page + 1;
if($nextPage > $pageCount) {
	$nextPage = $pageCount;
}
// 找出文章列表
$lists = select('article','','*','',$offsetPage,$showCount);
// 遍历$lists数组
foreach($lists as $key => $value) {
	// 找出文章分类中id=$value['cid']的那条数据
	$cateData = find('article_category','id='.$value['cid']);
	// 给$lists数组中的第key个元素的数组增加一个元素
	$lists[$key]['cat_name'] = $cateData['name'];	
}
// 分配到视图
$data = array(
	'lists' 	=> $lists,
	'pageCount' => $pageCount,
	'prePage' 	=> $prePage,
	'nextPage' 	=> $nextPage,
	'page' 		=> $page,
	'startPage' => $startPage,
	'endPage' 	=> $endPage,
	//'title'		=> '文章列表'
);
// 加载视图
view($data);
?>