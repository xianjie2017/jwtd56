<?php
header("Content-type:text/html;charset=utf-8");
// 设置默认时区
date_default_timezone_set("PRC");
// 开启session
session_start();

// 默认模块名
define('DEFAULT_MODULE','front');
// 默认控制器名
define('DEFAULT_CONTROLLER','home');
// 默认方法名
define('DEFAULT_ACTION','index');
// 开启伪静态
define('OPEN_REWRITE',false);
// 伪静态后缀
define('HTML_SUFFIX','.html');
// PAHT_INFO
$PATH_INFO = ! empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
if(empty($PATH_INFO)){
	$PATH_INFO = ! empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
}
define('PATH_INFO',$PATH_INFO);

// 模块
$module 	= isset($_GET['m']) ? $_GET['m'] : DEFAULT_MODULE; # 模块
$controller = isset($_GET['c']) ? $_GET['c'] : DEFAULT_CONTROLLER; # 控制器
$action 	= isset($_GET['a']) ? $_GET['a'] : DEFAULT_ACTION; # 方法

if(OPEN_REWRITE === true && ! empty(PATH_INFO) && $module == DEFAULT_MODULE){
	$uriPathInfo = PATH_INFO;	
	if($uriPathInfo == '/'){
		$controller = DEFAULT_CONTROLLER;
		$action		= DEFAULT_ACTION;
	}else{
		// 去除第一个'/'
		$uriPathInfo = mb_substr($uriPathInfo,1);
		// 先找出最右边的'.'的位置
		$rightPostion = mb_strrpos($uriPathInfo,'.html');
		if($rightPostion>0){
			$uriPathInfo = mb_substr($uriPathInfo,0,$rightPostion);
		}
		// 以'/'为分隔符，把字符串转换为数组
		$uriPathInfoArray = explode('/',$uriPathInfo);
		
		$controller = $uriPathInfoArray[0];
		$action		= $uriPathInfoArray[1];
	}
}

// 定义路径
$modulePath 	= "app/{$module}/"; # 模块路径
$controllerPath = $modulePath."controller/{$controller}/"; # 控制器路径
$actionPath 	= $controllerPath."{$action}.php"; # 方法路径

// file_exists 判断一个文件或文件夹是否存在
if( ! file_exists($modulePath)) { # 判断模块是否存在
	die('该模块不存在');
}
if( ! file_exists($controllerPath)) {  # 判断控制器是否存在
	die('该控制器不存在');
}
if( ! file_exists($actionPath)) { # 判断方法是否存在
	die('该方法不存在');
}

$view_path = $modulePath.'view/'; # 视图路径

// 定义一些常量
define("VIEW_PATH",$view_path); #视图路径
define("MODULE_PATH",$modulePath); # 模块路径
define("CONTROLLER_PATH",$controllerPath); # 控制器路径
define("ACTION_PATH",$actionPath); # 方法路径

define("MODULE_NAME",$module); # 模块名称
define("CONTROLLER_NAME",$controller); # 控制器名称
define("ACTION_NAME",$action); # 方法名称

define("CONF_PATH",'conf/'); # 配置目录
define("LIBS_PATH",'libs/'); # 库目录

// 加载公共文件
include_once LIBS_PATH.'mysqli.php'; # 加载数据封装函数
include_once LIBS_PATH.'tools.php'; # 加载公共函数
include_once LIBS_PATH.'functions.php'; # 加载公共函数
include_once CONF_PATH.'config.php'; # 加载配置文件

// 检查权限
if(MODULE_NAME=='admin'){
	check_auth();
}

include_once $actionPath; # 加载控制器--里面的方法
/*
系统目录结构
app 应用目录
	admin 后台应用
		controller 控制器
			product
				add.php 方法名 action
				update.php
				...
			news
				add.php 方法名 action
				update.php
				...
			...
		view
			product
				add.html 视图文件
				update.html
				...
			news
				add.html 视图文件
				update.html
				...
			...
	front 前台应用
	...
libs 公共文件
statics 静态文件
	应用目录 
	...
conf 配置文件
index.php 系统入口文件


//伪静态
加载 LoadModule rewrite_module modules/mod_rewrite.so模块

AllowOverride none 修改为 AllowOverride all

在网站根目录下创建 .htaccess 文件


路由：PAHT_INFO
www.1001.com/front/控制器名/方法名/参数1-123-参数2-236-参数3-5646.html

*/






