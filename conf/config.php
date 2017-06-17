<?php
//配置数据库
define("DB_HOST",'localhost'); # 数据库服务器地址
define("DB_USER",'root'); # 数据库用户名
define("DB_PASSWORD",''); # 数据库密码
define("DB_NAME",'1001db'); # 数据名

// 配置视图文件后缀名
define("TPL_SUFFIX",'.html');

// 配置路径替换常量
define("__PUBLIC__","/statics/".MODULE_NAME.'/'); # 配置应用静态文件路径

// 配置后台权限例外页面
$GLOBALS['auth'] = array(
	'nologin' => array(
		'login'=> array('login','vcode'),
	),
	'exception' => array(
		'home' => 'all',
		'login' => array('logout')
	),
);

// 配置上传图片的类型
define('UPLOAD_TYPE','jpg,png,gif,jpeg');
// 配置上图片的大小
define('UPLOAD_SIZE',1024*1024);
// 配置上图片的图片路径
define('UPLOAD_PATH','statics/uploads/');
// 配置是否生成缩略图
define('UPLOAD_IS_THUMB',true);
// 配置是否生成水印
define('UPLOAD_IS_WATER',true);
// 配置水印字体
define('UPLOAD_WATER_FONT_PATH','statics/public/fonts/1.ttf');
// 配置水印文字
define('UPLOAD_WATER_TEXT','DODI');
// 配置水印文字大小
define('UPLOAD_WATER_FONT_SIZE',18);
// 配置水印的位置
define('UPLOAD_WATER_POSITION',1);



