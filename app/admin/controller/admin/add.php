<?php
if( ! empty($_POST)){
	if(empty($_POST['username'])){
		msg('用户名不能为空');
	}
	
	$username = $_POST['username'];
	if(mb_strlen($username) < 6 || mb_strlen($username) > 18){
		msg('用户名为6到18个字符');
	}
	
	if(empty($_POST['password']) || empty($_POST['password2'])){
		msg('密码不能为空');
	}
	
	$password = $_POST['password'];
	$password2 = $_POST['password2'];
	
	if($password != $password2){
		msg('确认密码不一致');
	}
	
	if(mb_strlen($password) < 6 || mb_strlen($password) > 18){
		msg('密码为6到18个字符');
	}
	
	if(!empty($_POST['auth'])){
		$auth = strtolower(serialize($_POST['auth']));
		// serialize 序列化
		// unserialize 反序列化
	}

	// 组装数据
	$data = [
		'username' 	=> $username,
		'password' 	=> md5($password),
		'auth'		=> isset($auth) ? $auth : '' 
	];
	
	$insertId = insert('admin',$data);	
	if(empty($insertId)){
		msg('添加失败');
	}
	
	msg('添加成功');
}

$menu = simplexml_load_file('./libs/menu.xml');
$data=[
	'menu' => $menu,
];
view($data);