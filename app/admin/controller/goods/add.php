<?php
if(! empty($_POST)) {
	
	// 超全局的预定义数组
	// $_FILES
	
	// 判断标题是否为空
	if(empty($_POST['title'])){
		msg('标题不能为空!',url());
	}
	// 判断分类是否为空
	if(empty($_POST['cid'])){
		msg('请选择文章分类!',url());
	}
	
	//判断是否有上传图片
	if(empty($_FILES['picFile']['name'])) {
		msg('请上传商品图片');
	}
	
	// 如果上传出错
	if( ! empty($_FILES['picFile']['error'])){
		msg('上传出错');
	}

	$uploadResult = upload($_FILES['picFile']);
	if($uploadResult['error'] !== false){
		msg($uploadResult['error']);		
	}
	
	// 如果有上传商品相册
	if( ! empty($_FILES['photoFile']['name'][0])){
		$photo = [];
		foreach($_FILES['photoFile']['name'] as $key => $value){
			$temp = [
				'name' 		=> $value,
				'type' 		=> $_FILES['photoFile']['type'][$key],
				'tmp_name' 	=> $_FILES['photoFile']['tmp_name'][$key],
				'error' 	=> $_FILES['photoFile']['error'][$key],
				'size' 		=> $_FILES['photoFile']['size'][$key],
			];
			
			$photoTemp = upload($temp);
			if($photoTemp['error'] === false) {
				$photo[] = $photoTemp['path'];
			}
		}
	}
	
	// 组装数据
	$data = array(
		'goods_name'	=> $_POST['title'],
		'cid' 			=> $_POST['cid'],
		'image'    		=> $uploadResult['path'],
		'photo'			=> isset($photo) ? implode('|',$photo) : '',
		'content' 		=> isset($_POST['content']) ? $_POST['content'] : '',
		'create_time' 	=> time(),
		'is_rec'		=> isset($_POST['is_rec']) ? $_POST['is_rec'] : 0
	);
	// 插入数据
	$result = insert('goods',$data);
	if(empty($result)){
		msg('商品添加失败!',url());
	}
	msg('商品添加成功',url('lists'));
}


// 先找出所有的文章分类
$cate = select('category');

$cateArray = get_cate_child_array($cate);
// 分类下拉菜单
$cateSelect = get_cate_select($cateArray);

// 分配到视图
$data = array(
	'cateSelect' => $cateSelect,
);
// 加载视图
view($data);
?>