<?php
if( ! empty($_POST)) {
	// 首先要判断是否接收到id
	if(empty($_POST['id'])) {
		msg('非法操作');
	}
	$id = $_POST['id'];

	// 判断标题是否为空
	if(empty($_POST['title'])) {
		msg('标题不能为空');
	}

	// 判断文章分类是否为空
	if(empty($_POST['cid'])) {
		msg('请选择文章分类');
	}
	
	// 找出该文章
	$article = find('article',"id={$id}");
	if(empty($article)){
		msg('该文章不存在');
	}
	
	// 组装数据
	$data = array(
		'title' 	=> $_POST['title'],
		'author' 	=> $_POST['author'],
		'cid' 		=> $_POST['cid'],
		'content' 	=> ! empty($_POST['content']) ? $_POST['content'] : ''
	);
	
	// 修改数据
	$result = update('article',$data,"id={$id}");
	
	if(empty($result)) {
		msg('修改失败');
	}
	
	msg('修改成功',url('lists'));	
}


// 先判断是否有id参数
if( !isset($_GET['id'])) {
	msg('非法操作',url('lists'));
}
// 接收参数
$id = $_GET['id'];
// 先找文章的详细信息
$article = find('article',"id={$id}");
// 找出所有文章分类
$category = select('article_category');

// 分配到视图
$data = array(
	'article' 	=> $article,
	'category' 	=> $category
);

// 加载视图
view($data);
?>