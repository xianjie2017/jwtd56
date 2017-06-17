<?php
if(empty($_GET['id'])){
	msg('非法操作');
}

$id = $_GET['id'];

// 找出该管理员
$adminData = find('admin',"id={$id}");
if(empty($adminData)){
	msg('找不到该管理员');
}

if(! empty($_POST)){
	
	if(empty($_POST['username'])){
		msg('用户名不能为空');
	}
	
	$username = $_POST['username'];
	if(mb_strlen($username) < 6 || mb_strlen($username) > 18){
		msg('用户名为6到18个字符');
	}
	
	// 组装数据
	$data = [
		'username' => $username
	];
	
	// 修改密码
	if( ! empty($_POST['password'])){	
		$password = $_POST['password'];
		$password2 = ! empty($_POST['password2']) ? $_POST['password2'] : '';
		
		if($password != $password2){
			msg('确认密码不一致');
		}
		
		if(mb_strlen($password) < 6 || mb_strlen($password) > 18){
			msg('密码为6到18个字符');
		}
		
		$data['password'] = md5($password);
	}
	
	// 更改权限
	$data['auth'] = !empty($_POST['auth']) ? strtolower(serialize($_POST['auth'])) : '';
	
	$row = update('admin',$data,"id={$id}");
	if(empty($row)){
		msg('更新失败');
	}
	
	msg('更新成功');
}

// 处理权限数组
$adminData['auth'] = unserialize($adminData['auth']);

$menu = simplexml_load_file('./libs/menu.xml');
$data=[
	'menu' => $menu,
	'adminData' => $adminData,
];
view($data);