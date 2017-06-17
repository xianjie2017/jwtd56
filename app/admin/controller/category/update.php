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
	// 第四步：找出该分类的所有子分类id
	
	$pid = $_POST['pid'];
	
	// 找出所有分类
	$cate = select('category');
	
	$cate_id_array = get_cate_child_id_array($cate,$id);
	
	// 在加上自己的id
	$cate_id_array[] = $id;
	
	// 如果存在于$cate_id_array 自己以及自己的子分类中
	if(in_array($pid,$cate_id_array)){
		msg('上级分类不能是自己或者自己的子分类');
	}

	// 组装要修改的数组
	$dataArr = array(
		'name' => $cate_name,
		'pid'  => $pid,
	);
	
	$rows = update('category',$dataArr,"id={$id}");
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
$catData = find('category',"id={$id}");
if(empty($catData)){
	msg('找不到该分类',url('lists'));
}

// 找出所有分类
$cate = select('category');

$cateArray = get_cate_child_array($cate);

// 分类下拉菜单
$cateSelect = get_cate_select($cateArray,$catData['pid']);

// 组装需要分配到视图的数据
$data = array(
	'catData' => $catData,
	'cateSelect' => $cateSelect,
);
// 第一步：加载视图
view($data); // 第四步：分配变量到视图