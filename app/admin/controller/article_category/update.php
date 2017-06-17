<?php
// 先要判断，是否有接收到post过来的数据
if( ! empty($_POST)) {
	// 第一步：接收参数
	$id = ! empty($_POST['id']) ? $_POST['id'] : 0;
	$cate_name = isset($_POST['cate_name']) ? $_POST['cate_name'] : '';
	// 第二步:判断id是否为空
	if(empty($id)) {
		msg('非法操作',url('lists'));
	}	
	// 第三步：判断用户输入的分类名称是否为空，为空则提示不能输入空值
	if(empty($cate_name)){
		msg('分类名称不能为空',url('update',array('id'=>$id)));
	}
	// 组装要修改的数组
	$dataArr = array(
		'name' => $cate_name
	);

	$rows = update('article_category',$dataArr,"id={$id}");
	if(empty($rows)) {
		msg('更新失败',url('update',array('id'=>$id)));
	}
	// 结束程序继续运行
	msg('更新成功',url('lists'));
}

//------------------------------显示视图
// 当接收参数id为空的时候
if(empty($_GET['id'])){
	msg('参数接收出错',url('lists'));
}
// 第二步：接受参数
$id = $_GET['id'];
// 第三步：通过id找出该分类的数据
$catData = find('article_category',"id={$id}");
if(empty($catData)){
	msg('找不到该分类',url('lists'));
}
// 组装需要分配到视图的数据
$data = array(
	'catData' => $catData,
);
// 第一步：加载视图
view($data); // 第四步：分配变量到视图