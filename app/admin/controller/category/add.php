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
		'name' => $cate_name, // 数据库字段名 = 用户提交上来值
		'pid'  => $_POST['pid'], 
	);
	// 添加数据 得到最后插入的id
	$insert_id = insert('category',$data);
	if(empty($insert_id)) {
		msg('添加失败',url());
	}
	msg('添加成功',url('admin/category/lists'));
}

// 找出所有分类
// $cate = select('category','pid=0');
$cate = select('category');

$cateArray = get_cate_child_array($cate);
// 分类下拉菜单
$cateSelect = get_cate_select($cateArray);

// print_r($cateSelect);
/*
for($i=1;$i<5;$i++){
	
	echo $i.'<br>';
	
	for($j=1;$j<5;$j++){
		echo '-------'.$j.'<br>';
		if($i==$j) {			
			break;
		}
	}
	
	
}



exit;
$a = get_cate_child();

print_r($a);


foreach($cate as $key => $value){
	$cate[$key]['child'] = select('category','pid='.$value['id']);
	foreach($cate[$key]['child'] as $k => $val) {
		$cate[$key]['child'][$k]['child'] = select('category','pid='.$val['id']);
	}
}

print_r($cate);*/
/*foreach($cate as $key => $value) {
	echo '----------------------------'.$value['name'].'-----------------------------<br>';
	$child = select('category','pid='.$value['id']);
	foreach($child as $val) {
		echo '----------------------------'.$value['name'].'------'.$val['name'].'-----------------------------<br>';
		$child2 = select('category','pid='.$val['id']);
		var_dump($child2);
	}
	var_dump($child);
}*/

// 分配到视图
$data = array(
	'cateSelect' => $cateSelect
);

// 加载视图
view($data);

?>