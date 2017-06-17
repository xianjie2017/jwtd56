<?php
if( ! empty($_POST)){
	// 判断是否有接收到id
	if(empty($_POST['id'])){
		msg('非法操作!');
	}
	
	// 判断标题是否为空
	if(empty($_POST['title'])){
		msg('标题不能为空!');
	}
	// 判断分类是否为空
	if(empty($_POST['cid'])){
		msg('请选择分类!');
	}
	
	if( ! empty($_FILES['picFile']['name'])){
		// 如果上传出错
		if( ! empty($_FILES['picFile']['error'])){
			msg('上传出错');
		}
		
		$uploadResult = upload($_FILES['picFile']);
		if($uploadResult['error'] !== false){
			msg($uploadResult['error']);		
		}
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
	
	// 接收原来的商品相册
	$photoArray = ! empty($_POST['old']) ? $_POST['old'] : array();
	
	if( ! empty($photo) && count($photoArray)<5){
		$photoArray = array_merge($photoArray,$photo);
	}
	
	$photoStr = implode('|',$photoArray);	
	
	// 组装数据
	$data = array(
		'goods_name'	=> $_POST['title'],
		'cid' 			=> $_POST['cid'],
		'photo'			=> isset($photoStr) ? $photoStr : '',
		'content' 		=> isset($_POST['content']) ? $_POST['content'] : '',
		'is_rec'		=> isset($_POST['is_rec']) ? $_POST['is_rec'] : 0
	);
	
	// 如果用户有上传主图的时候，则修改主图
	if( ! empty($uploadResult['path'])){
		$data['image'] = $uploadResult['path'];
	}
	
	// 修改数据
	$result = update('goods',$data,"id={$_POST['id']}");
	if(empty($result)){
		msg('商品修改失败!');
	}
	
	// 删除图片
	if( ! empty($_POST['delimg'])){
		foreach($_POST['delimg'] as $key => $value){
			if(file_exists($value)){
				$picInfo = pathinfo($value);
				// 删除原图
				unlink($value);
				// 删除水印
				if(file_exists($value."water.".$picInfo['extension'])){
					unlink($value."water.".$picInfo['extension']);
				}
				// 删除缩略图
				if(file_exists($value."100x100.".$picInfo['extension'])){
					unlink($value."100x100.".$picInfo['extension']);
				}
			}
		}
	}

	msg('商品修改成功',url('lists'));
}

if(empty($_GET['id'])){
	msg('非法操作');
}

$id = $_GET['id'];

// 查找商品数据
$goodsData = find('goods',"id={$id}");
if(empty($goodsData)){
	msg('找不到该商品');
}

// 找出所有的分类
$cateData = select('category');
// 调用递归函数
$cateArray = get_cate_child_array($cateData);
// 取得下拉表单
$cateSelect = get_cate_select($cateArray,$goodsData['cid']);

if( ! empty($goodsData['photo'])){
	$goodsData['photo'] = explode('|',$goodsData['photo']);
}

$data = [
	'goods' => $goodsData,
	'cateSelect' => $cateSelect,
];

view($data);
?>