<?php
if(! empty($_POST)) {
	// 判断文章标题是否为空
	if(empty($_POST['title'])){
		msg('标题不能为空!',url());
	}
	// 判断文章分类是否为空
	if(empty($_POST['cid'])){
		msg('请选择文章分类!',url());
	}
	// 组装数据
	$data = array(
		'title'		=> $_POST['title'],
		'author' 	=> $_POST['author'],
		'cid' 		=> $_POST['cid'],
		'content' 	=> isset($_POST['content']) ? $_POST['content'] : '',
		'create_time' 	=> date('Y-m-d H:i:s'),
	);
	// 插入数据
	$result = insert('article',$data);
	if(empty($result)){
		msg('文章添加失败!',url());
	}
	msg('文章添加成功',url('lists'));
}


// 先找出所有的文章分类
$cateData = select('article_category');
// 分配到视图
$data = array(
	'cateData' => $cateData,
);
// 加载视图
view($data);
?>