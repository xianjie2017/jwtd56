<?php

if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
	$action = isset($_POST['action']) ? $_POST['action'] : '';
	switch($action){
		case 'username' : //检查用户名
			if(empty($_POST['username'])){
				//die(json_encode(['error'=>true,'msg'=>'用户名不能为空']));
				die('false');
			}
			$username = $_POST['username'];
			$userData = find('admin',"username='{$username}'");
			if(empty($userData)){
				//die(json_encode(['error'=>true,'msg'=>'该用户名不存在']));
				die('false');
			}
			//die(json_encode(['error'=>false]));
			die('true');
			break;
		case 'vcode' : //检查验证码
			if(empty($_POST['vcode'])){
				//die(json_encode(['error'=>true,'msg'=>'验证码不能为空']));
				die('false');
			}
			$vcode = $_POST['vcode'];
			if(strtolower($vcode) != strtolower($_SESSION['vcode'])){
				//die(json_encode(['error'=>true,'msg'=>'验证码不正确']));
				die('false');
			}
			//die(json_encode(['error'=>false]));
			die('true');
			break;
		default: // 登陆
			logins();		
			break;
	}
}

function logins()
{
	if(empty($_POST['username'])) {
		die(json_encode(['error'=>true,'msg'=>'用户名不能为空']));
	}

	if(empty($_POST['password'])) {
		die(json_encode(['error'=>true,'msg'=>'密码不能为空']));
	}
	
	if(empty($_POST['vcode'])){
		die(json_encode(['error'=>true,'msg'=>'验证码不能为空']));
	}

	if(strtolower($_POST['vcode']) != strtolower($_SESSION['vcode'])){
		die(json_encode(['error'=>true,'msg'=>'验证码不正确']));
	}
	
	$username = $_POST['username'];
	$password = $_POST['password'];

	// 查询数据库
	$result = find('admin',"username='{$username}'");
	if(empty($result)) {
		die(json_encode(['error'=>true,'msg'=>'不存在该用户']));
	}
	// 比对密码
	if($result['password'] != md5($password)) {
		die(json_encode(['error'=>true,'msg'=>'密码错误']));
	}

	// 保存登录状态
	$_SESSION['admin'] = [
		'id' 		=> $result['id'],
		'username' 	=> $result['username'],
		'auth' 		=> unserialize($result['auth']),
	];
	
	// 记住密码
	if( ! empty($_POST['reme'])) {		
		setcookie('username',$username,time()+3600,'/');
		setcookie('password',$password,time()+3600,'/');		
		setcookie('reme',1,time()+3600,'/');
	}else{
		setcookie('username','',time()-1,'/');
		setcookie('password','',time()-1,'/');
		setcookie('reme',0,time()-1,'/');
	}
	
	die(json_encode(['error'=>false,'msg'=>'登陆成功']));
}

if( ! empty($_SESSION['admin'])){
	
	header('location:'.url('home/index'));
	exit;
}

$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
$password = isset($_COOKIE['password']) ? $_COOKIE['password'] : '';
$reme 	  = isset($_COOKIE['reme']) ? $_COOKIE['reme'] : '';

// 取出用户名和密码
// $saveLoginStatusJson = isset($_COOKIE['saveLoginStatus']) ? $_COOKIE['saveLoginStatus'] : '';
// 转换成数组
// $saveLoginStatusArr = json_decode($saveLoginStatusJson,true);
// 组装要分配到视图的数据

$data = [
	// 'saveLoginStatusArr' => $saveLoginStatusArr,
	'username' => $username,
	'password' => $password,
	'reme'	   => $reme
];

// 加载视图
// include_once VIEW_PATH.'login/login.html';
view($data,'','');
?>