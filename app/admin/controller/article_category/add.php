<?php
// 判断是否有提交数据
if(! empty($_POST)){
	// 接收用户提交cate_name
	$cate_name = $_POST['cate_name'];
	// 判断提交上了的分类名称是否为空
	if(empty($cate_name)) {
		msg('分类名不能为空',url());
	}
	// 组装数据
	$data = array(
		'name' => $cate_name // 数据库字段名 = 用户提交上来值
	);
	// 添加数据 得到最后插入的id
	$insert_id = insert('article_category',$data);
	if(empty($insert_id)) {
		msg('添加失败',url());
	}
	msg('添加成功',url('admin/article_category/lists'));
}

// 加载视图
view();

?>