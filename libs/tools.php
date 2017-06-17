<?php
// 获取参数
function get($paramName='')
{
	$uriPathInfo = PATH_INFO;
	$params = [];
	if( ! empty($uriPathInfo)){
		// 去除第一个'/'
		$uriPathInfo = mb_substr($uriPathInfo,1);
		// 先找出最右边的'.'的位置
		$rightPostion = mb_strrpos($uriPathInfo,'.html');
		if($rightPostion>0){
			$uriPathInfo = mb_substr($uriPathInfo,0,$rightPostion);
		}
		
		// 以'/'为分隔符，把字符串转换为数组
		$uriPathInfoArray = explode('/',$uriPathInfo);
		
		if(!empty($uriPathInfoArray[2])){
		
			// 取得参数名与参数值的字符串
			$paramStr = $uriPathInfoArray[2];
			// 以'-'为分隔符，把参数字符串转换为数组
			$paramArray = explode('-',$paramStr);		
			foreach($paramArray as $key => $value){
				if(($key+1)%2 == 0){
					$params[$paramArray[$key-1]] = $value;
				}
			}
			// 把原生$_GET数组里面的参数合并到$params
		}
	}
	$params = array_merge($params,$_GET);
	
	if(empty($paramName)){
		return $params;
	}
	
	if( ! isset($params[$paramName])){
		return null;
	}
	
	return $params[$paramName];	
}

// 检查权限
function check_auth()
{
	$auth = $GLOBALS['auth'];
	// 判断是否设置了无须登陆列外
	if(isset($auth['nologin'])){
		// 判断当前的控制器是否存在于例外数组中
		if(isset($auth['nologin'][CONTROLLER_NAME])){
			// 如果数组的值是一个字符串 并且字符串的值等于'all' 则整个控制里面的所有方法都拥有访问权限
			if(is_string($auth['nologin'][CONTROLLER_NAME]) && $auth['nologin'][CONTROLLER_NAME] == 'all'){
				return true;
			}
			// 如果数组的值是一个数组 并且当前的方法名存在于数组中 则整个当前方法都拥有访问权限
			if(is_array($auth['nologin'][CONTROLLER_NAME]) && in_array(ACTION_NAME,$auth['nologin'][CONTROLLER_NAME])){
				return true;
			}
		}
	}
	
	// 检查登录
	if(empty($_SESSION['admin'])) {
		msg('请登录后再回来','index.php?m=admin&c=login&a=login');
	}
	
	// ----------------------权限的判断
	
	// 判断是否设置了无须登陆列外
	if(isset($auth['exception'])){
		// 判断当前的控制器是否存在于例外数组中
		if(isset($auth['exception'][CONTROLLER_NAME])){
			// 如果数组的值是一个字符串 并且字符串的值等于'all' 则整个控制里面的所有方法都拥有访问权限
			if(is_string($auth['exception'][CONTROLLER_NAME]) && $auth['exception'][CONTROLLER_NAME] == 'all'){
				return true;
			}
			// 如果数组的值是一个数组 并且当前的方法名存在于数组中 则整个当前方法都拥有访问权限
			if(is_array($auth['exception'][CONTROLLER_NAME]) && in_array(ACTION_NAME,$auth['exception'][CONTROLLER_NAME])){
				return true;
			}
		}
	}
	
	// 先取得用户所有的权限数组
	$authArray = $_SESSION['admin']['auth'];
	
	// 判断当前控制器是否存在于权限数组当中
	if( ! isset($authArray[strtolower(CONTROLLER_NAME)])){
		msg('没有权限访问该页面，请联系管理员',url('home/welcome'));
	}
	
	if( ! in_array(strtolower(ACTION_NAME),$authArray[strtolower(CONTROLLER_NAME)])){
		msg('没有权限访问该页面，请联系管理员',url('home/welcome'));
	}
}

// 验证码
/*
$count: 验证码的个数
$width：验证码图片的宽度
$height：验证码图片的高度
$fontLevel：验证码字体的等级1，2，3
*/
function vcode($count=4,$width=100,$height=40,$fontLevel=2)
{
	// 创建一张画布
	$im = imagecreatetruecolor($width,$height);
	// 验证码随机背景
	$bgColor = imagecolorallocate($im,mt_rand(180,255),mt_rand(180,255),mt_rand(180,255));
	// 填充背景
	imagefilledrectangle($im,0,0,$width,$height,$bgColor);

	// 画雪花
	for($i=0;$i<100;$i++){
		// 雪花的颜色
		$iceColor = imagecolorallocate($im,mt_rand(0,250),mt_rand(0,250),mt_rand(0,250));
		// 画雪花点
		imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$iceColor);	
	}

	// 画划弧线
	for($i=0;$i<3;$i++){
		$arcColor = imagecolorallocate($im,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
		imagearc($im,mt_rand(0,$width),mt_rand(0,$height),mt_rand(0,$width),mt_rand(0,$height),mt_rand(0,360),mt_rand(0,360),$arcColor);
	}

	// 验证码中的字符串
	$str = "ABCDEFGHJKMEPQRSTUVWXWZabcdefghjkmepqrstuvwxwz23456789";
	// 步长
	$step = $width / $count;	
	// 验证码中的字符串
	$textStr = '';
	
	$fontsSize = array(
		1 => array(12,20),
		2 => array(16,26),
		3 => array(20,35),
		4 => array(25,40),
	);
	
	for($i=0;$i<$count;$i++) {
		$setStr = $str[mt_rand(0,strlen($str)-1)];
		$textStr .= $setStr;
		// 文字出现的X坐标
		$x = mt_rand($step*$i-$step*0.1,($step*($i+1))-$step*0.4);
		
		// 文字出现的Y坐标
		$y = $height/2+$fontsSize[$fontLevel][0]-10;

		$fontColor = imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
		imagefttext($im,mt_rand($fontsSize[$fontLevel][0],$fontsSize[$fontLevel][1]),mt_rand(-30,30),$x,$y,$fontColor,'./statics/public/fonts/'.mt_rand(1,4).'.ttf',$setStr);
	}

	// 写入session
	$_SESSION['vcode'] = $textStr;

	// 画线条
	for($i=0;$i<5;$i++){
		$lineColor = imagecolorallocate($im,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
		imageline($im,mt_rand(0,$width),mt_rand(0,$height),mt_rand(0,$width),mt_rand(0,$height),$lineColor);
	}

	header("Content-type: image/png;charset=utf8");
	// 生成图片
	imagepng($im);
	// 销毁图片
	imagedestroy($im);
}

/* 
login/login.html
		login.html
		''
*/

/*
$array = array(
	'abc' => 'abc',
	'def' => 'def'
);
*/
function view($tplPath='',$params=[],$header="layout/header",$footer="layout/footer")
{
	// 当$tplPath 是一个数组的时候
	if(is_array($tplPath)) {
		// 将数组展开，键名作为变量名，元素值为变量值
		extract($tplPath);		
		// 组装视图路径
		$tplPathStr = VIEW_PATH.CONTROLLER_NAME.'/'.ACTION_NAME;
		
		if(is_string($params)){			
			$footer = $header;
			$header = $params;
		}		
	}else{
		/* foreach($params as $key => $value) {
			$$key = $value;
			// $键名[把键变成变量] = 键值[把键变成变量的值]
		}  */
		// 将数组展开，键名作为变量名，元素值为变量值
		extract($params);
		// 组装视图文件地址
		$tplPathStr = VIEW_PATH;
		// 把字符串转换成数组
		$tplPathArr = explode('/',$tplPath);
		switch(count($tplPathArr)) {
			case 2:
				$tplPathStr .= $tplPath;
				break;
			case 1:
			default:
				$tplPathStr .= CONTROLLER_NAME.'/';
				if( ! empty($tplPath)) {
					$tplPathStr .= $tplPath;
				}else{
					$tplPathStr .= ACTION_NAME;
				}
				break;
		}
	}
	// 判断是否输入了文件后缀名
	$pathinfo = pathinfo($tplPathStr);
	if(empty($pathinfo['extension'])) {
		$tplPathStr .= TPL_SUFFIX; # 加上后缀
	}
	// 判断该视图文件是否存在
	if( ! file_exists($tplPathStr)){
		die($tplPathStr.'视图不存在！');
	}
	
	if( ! empty($header)){
		// 组装头部视图地址
		$tplHeaderPathStr = VIEW_PATH.$header;
		// 判断是否输入了文件后缀名
		$pathinfo = pathinfo($tplHeaderPathStr);
		if(empty($pathinfo['extension'])) {
			$tplHeaderPathStr .= TPL_SUFFIX; # 加上后缀
		}
	}
	
	if( ! empty($footer)){
		// 组装头部视图地址
		$tplFooterPathStr = VIEW_PATH.$footer;
		// 判断是否输入了文件后缀名
		$pathinfo = pathinfo($tplFooterPathStr);
		if(empty($pathinfo['extension'])) {
			$tplFooterPathStr .= TPL_SUFFIX; # 加上后缀
		}
	}
	
	// 网页的头部
	if(isset($tplHeaderPathStr)){
		include_once $tplHeaderPathStr;
	}
	
	// 网站主体
	include_once $tplPathStr;
	
	// 网页的底部
	if(isset($tplFooterPathStr)){
		include_once $tplFooterPathStr;
	}
}
/* 
url函数
模块名/控制器名/方法名
		  控制器名/方法名
				   方法名
					''
字符串或数值都可以
		id=1&username=lisi
		['id'=>1,'usernmae'=>'lisi'] 
*/
function url($url='',$params=[])
{
	if(is_array($url)){
		$m = MODULE_NAME;
		$c = CONTROLLER_NAME;
		$a = ACTION_NAME;
		
		// 组装url地址
		$urlStr = "index.php?m={$m}&c={$c}&a={$a}";
		if(OPEN_REWRITE=== true && ! empty(PATH_INFO) && $m==DEFAULT_MODULE){
			$urlStr = "{$c}/{$a}";
		}
		
		// 数组转换成字符串
		$paramsTempArr = array();
		foreach($url as $key => $value) {
			if(OPEN_REWRITE=== true && ! empty(PATH_INFO) && $m==DEFAULT_MODULE){
				$paramsTempArr[] = "{$key}-{$value}";
			}else{
				$paramsTempArr[] = "{$key}={$value}";
			}			
		}
		$paramsStr = implode('&',$paramsTempArr);

		// 判断$paramsStr是否为空
		if( ! empty($paramsStr)) {			
			if(OPEN_REWRITE=== true && ! empty(PATH_INFO) && $m==DEFAULT_MODULE){
				$urlStr .= "/{$paramsStr}";
			}else{
				$urlStr .= "&{$paramsStr}";
			}	
		}
	}else{
		// 把字符串转换成数组
		$urlArr = explode('/',$url);
		// 根据数组长度判断用户输入的情况
		switch(count($urlArr))
		{
			case 2:
				$m = MODULE_NAME;
				$c = $urlArr[0];
				$a = $urlArr[1];
				break;
			case 3:
				$m = $urlArr[0];
				$c = $urlArr[1];
				$a = $urlArr[2];
				break;
			case 1:
			default:
				$m = MODULE_NAME;
				$c = CONTROLLER_NAME;
				$a = ! empty($url) ? $url : ACTION_NAME;
				break;
		}
		// 组装url地址
		$urlStr = "index.php?m={$m}&c={$c}&a={$a}";
		if(OPEN_REWRITE=== true && ! empty(PATH_INFO) && $m==DEFAULT_MODULE){
			$urlStr = "{$c}/{$a}";
		}
		// 判断用户是否输入了参数
		if( ! empty($params)){
			// 如果是一个字符串
			if(is_string($params)) {
				$paramsStr = $params;
			}
			// 如果是一个数组
			if(is_array($params)){
				// 数组转换成字符串
				$paramsTempArr = array();
				foreach($params as $key => $value) {					
					if(OPEN_REWRITE=== true && ! empty(PATH_INFO) && $m==DEFAULT_MODULE){
						$paramsTempArr[] = "{$key}-{$value}";
					}else{
						$paramsTempArr[] = "{$key}={$value}";
					}
				}
				$paramsStr = implode('&',$paramsTempArr);
			}
			// 判断$paramsStr是否为空
			if( ! empty($paramsStr)) {
				if(OPEN_REWRITE=== true && ! empty(PATH_INFO) && $m==DEFAULT_MODULE){
					$urlStr .= "/{$paramsStr}";
				}else{
					$urlStr .= "&{$paramsStr}";
				}	
			}
		}
	}
	if(OPEN_REWRITE=== true && ! empty(PATH_INFO) && $m==DEFAULT_MODULE){
		return '/'.$urlStr.HTML_SUFFIX;
	}
	return $urlStr;
}
// 封装提示信息
function msg($message,$url='',$time=5)
{
	// 如果$url为空，则给url赋值为来源页面
	if(empty($url)){
		$url = $_SERVER['HTTP_REFERER'];
	}
	
	$html = <<<EOT
		<div style="width:500px;height:200px;background:white;border:#ccc solid 1px;position:fixed;top:50%;left:50%;margin-left:-250px;margin-top:-100px;z-index:999;">
			<div style="height:45px;line-height:45px;text-align:center;background:#dedede;border-bottom:solid 1px #ccc;font-size:16px;">提示信息</div>
			<div style="padding:20px;position:relative;height:112px;">
				{$message}
				<div style="position:absolute;bottom:0px;height:50px;line-height:50px;text-align:center;width:100%;">
					<span id="timer">{$time}</span>秒后自动跳转 <a href="{$url}">立即跳转</a>
				</div>
			</div>
		</div>
		<script>
			window.onload = function(){
				var t = document.getElementById('timer');
				//
				timer = t.innerHTML;
				
				// 计时器
				tInt = setInterval(function(){
					timer--;
					t.innerHTML = timer;
					if(timer <= 0) {
						clearInterval(tInt);
						window.location.href = '{$url}';
					}
					
				},1000);				
			}
		</script>
EOT;

	die($html);
}

// 上传
function upload($fileArray)
{
	if(empty($fileArray)) {
		return ['error'=>'上传文件不能为空'];
	}
	
	if( ! empty($fileArray['error'])) {
		return ['error'=>'上传出错'];
	}
	
	if(empty($fileArray['name']) || empty($fileArray['tmp_name']) || empty($fileArray['size'])){
		return ['error'=>'上传出错'];
	}
	
	// 定义一个允许的文件类型
	$allowType = array('jpg','png','gif','jpeg');
	
	// 如果有图片的类型配置项
	if( ! empty(UPLOAD_TYPE)){
		$allowType = explode(',',UPLOAD_TYPE);
	}
	
	// 取出文件的信息
	$uploadFileInfo = pathinfo($fileArray['name']);
	
	//取出文件的后缀名，并转换成小写
	$uploadFileExtension = strtolower($uploadFileInfo['extension']);
	
	//判断上传文件的类型是否合法
	if( ! in_array($uploadFileExtension,$allowType)){
		return ['error'=>'上传文件只允许'.implode('|',$allowType).'格式'];
	}
	
	// 定义上传文件的大小
	$uploadSize = 1024*1024;
	
	if( ! empty(UPLOAD_SIZE) && is_integer(UPLOAD_SIZE) && UPLOAD_SIZE > 0){
		$uploadSize = UPLOAD_SIZE;
	}
	
	if($fileArray['size'] > $uploadSize){
		return ['error'=>'上传文件大小只允许'.$uploadSize.'b'];
	}
	
	// 文上传路径
	$uploadPath = './statics/uploads/'.date('Ymd').'/';
	if( ! empty(UPLOAD_PATH)){
		$uploadPath = UPLOAD_PATH.date('Ymd').'/';
	}
	
	// 判断上传文件路径是否存在
	if( ! file_exists($uploadPath)){
		// 替换路径中的./ 和../
		$uploadPath = str_replace('../','',str_replace('./','',$uploadPath));
		$uploadPathArray = explode('/',$uploadPath);
		// 定义一个空路径
		$tempPath = '';
		foreach($uploadPathArray as $value){
			if(empty($value)) {
				break;
			}
			// 组装路径
			$tempPath .= (! empty($tempPath) ? '/' : '') . $value;
			// 如果路径不存在，则创建路径
			if(! file_exists($tempPath)){
				mkdir($tempPath);
			}
		}
	}
	
	// 给上传文件指定命名
	$fileName = time().mt_rand(1000,9999).'.'.$uploadFileExtension;
	
	// 上传后的文件名
	$uploadFileName = $uploadPath.$fileName;
	
	// 把图片移动到指定位置
	$result = move_uploaded_file($fileArray['tmp_name'],$uploadFileName);
	if($result !== true) {
		return ['error'=>'上传失败'];
	}

	$resultArray = ['error'=>false,'path'=>$uploadFileName];
	
	if( ! empty(UPLOAD_IS_THUMB)){
		// 生成缩略图
		$thumbResult = thumb($uploadFileName);
		if($thumbResult['error'] !== false){
			$resultArray['error'] = $thumbResult['error'];
		}else{
			$resultArray['thumb'] = $thumbResult['path'];
		}		
	}
	
	if( ! empty(UPLOAD_IS_WATER)){
		$waterResult = text_water($uploadFileName);
		if($waterResult['error'] !== false){
			$resultArray['error'] = $waterResult['error'];
		}else{
			$resultArray['water'] = $waterResult['path'];
		}
	}

	return $resultArray;	
}

// 缩略图
function thumb($fileName,$thumbWidth=100,$thumbHeight=100)
{
	if(empty($fileName)) {
		return ['error'=>'图片名称不能为空'];
	}
	
	if( ! file_exists($fileName)){
		return ['error'=>'图片不存在'];
	}
	
	// 取出文件的信息
	$uploadFileInfo = pathinfo($fileName);
	
	//取出文件的后缀名，并转换成小写
	$uploadFileExtension = strtolower($uploadFileInfo['extension']);
	
	// 获取文件名跟据文件类型，把图片转换为资源
	switch($uploadFileExtension)
	{
		case 'jpg':
		case 'jpeg':
			$im = imagecreatefromjpeg($fileName);
			break;
		case 'gif':
			return ['error'=>'图片类型不支持生成水印'];
			break;
		case 'png':
			$im = imagecreatefrompng($fileName);
			break;
	}
	
	// 取得图片的宽度与高度
	$imageWidth  = imagesx($im);
	$imageHeight = imagesy($im);
	
	// 创建一张画布
	$canvas = imagecreatetruecolor($thumbWidth,$thumbHeight);
	
	// 得到一个背景颜色
	$whiteBg = imagecolorallocate($canvas,255,255,255);

	// 如果上传图片类型png 则填充透明背景
	if($uploadFileExtension == 'png'){
		imagecolortransparent($canvas,$whiteBg);
	}
	
	// 填充背景
	imagefilledrectangle($canvas,0,0,$thumbWidth,$thumbHeight,$whiteBg);
	
	// 计算图片的缩小比例
	if($imageWidth >= $imageHeight){
		$scale = $thumbWidth / $imageWidth;
	}else{
		$scale = $thumbHeight / $imageHeight;
	}
	
	// 缩略图里面图片的宽高
	$thumbImageWidth  = $imageWidth * $scale;
	$thumbImageHeight = $imageHeight * $scale;
	
	// 计算新图的X坐标与Y坐标
	$x = ($thumbWidth - $thumbImageWidth) / 2;
	$y = ($thumbHeight - $thumbImageHeight) / 2;
	
	// 拷贝原图为缩略图
	imagecopyresized($canvas,$im,$x,$y,0,0,$thumbImageWidth,$thumbImageHeight,$imageWidth,$imageHeight);
	
	// 缩略图的路径
	$thumbFileName = $fileName.$thumbWidth.'x'.$thumbHeight.'.'.$uploadFileExtension;
	
	// 生成缩略图
	switch($uploadFileExtension)
	{
		case 'jpg':
		case 'jpeg':	
			imagejpeg($canvas,$thumbFileName);
			break;		
		case 'png':
			imagepng($canvas,$thumbFileName);
			break;
	}
	return ['error'=>false,'path'=>$thumbFileName];
}

// 文字水印
function text_water($fileName,$text=null,$position=null,$fontSize=null)
{
	if(empty($fileName)){
		return ['error'=>'图片名称不能为空'];
	}
	
	if( ! file_exists($fileName)){
		return ['error'=>'图片没找到'];
	}
	
	// 取出文件的信息
	$uploadFileInfo = pathinfo($fileName);
	
	//取出文件的后缀名，并转换成小写
	$uploadFileExtension = strtolower($uploadFileInfo['extension']);
	
	// 获取文件名跟据文件类型，把图片转换为资源
	switch($uploadFileExtension)
	{
		case 'jpg':
		case 'jpeg':
			$im = imagecreatefromjpeg($fileName);
			break;
		case 'gif':
			return ['error'=>'图片类型不支持生成水印'];
			break;
		case 'png':
			$im = imagecreatefrompng($fileName);
			break;
	}
	
	// 取得图片的宽度与高度
	$imageWidth  = imagesx($im);
	$imageHeight = imagesy($im);
	
	// 配置水印字体
	$fontPath = './statics/public/fonts/1.ttf';
	if( ! empty(UPLOAD_WATER_FONT_PATH) && file_exists(UPLOAD_WATER_FONT_PATH)){
		$fontPath = UPLOAD_WATER_FONT_PATH;
	}
	
	// 水印文字
	$waterText = 'dodi';
	if( ! empty(UPLOAD_WATER_TEXT)) {
		$waterText = UPLOAD_WATER_TEXT;
	}
	if( ! empty($text)) {
		$waterText = $text;
	}
	
	// 文字大小
	$waterFontSize = 18;
	if( ! empty(UPLOAD_WATER_FONT_SIZE) && is_integer(UPLOAD_WATER_FONT_SIZE) && UPLOAD_WATER_FONT_SIZE > 11) {
		$waterFontSize = UPLOAD_WATER_FONT_SIZE;
	}
	if( ! empty($fontSize)&& is_integer($fontSize) && $fontSize > 11) {
		$waterFontSize = $fontSize;
	}
	
	// 水印文字的位置
	$waterPosition = 1;
	if( ! empty(UPLOAD_WATER_POSITION) && is_integer(UPLOAD_WATER_POSITION) && UPLOAD_WATER_POSITION>0 && UPLOAD_WATER_POSITION<6) {
		$waterPosition = UPLOAD_WATER_POSITION;
	}
	if( ! empty($position) && is_integer($position) && $position>0 && $position<6) {
		$waterPosition = $position;
	}
	
	// 取得使用 TrueType 字体的文本的范围  文字盒子的四个点的X坐标与Y坐标
	$wateFontInfo = imagettfbbox($waterFontSize,0,$fontPath,$waterText);
	
	// 计算文字盒子的宽高
	$fontWidth  = abs($wateFontInfo[2] - $wateFontInfo[0]);
	$fontHeight = abs($wateFontInfo[5] - $wateFontInfo[3]);
	
	// 水印出现的位置
	switch($waterPosition){		
		case 1 :  // 左上角
			$waterX = $waterFontSize;
			$waterY = $waterFontSize + $waterFontSize *1/2;
			break;
		case 2 :  // 右上角
			$waterX = $imageWidth - $fontWidth - $waterFontSize;
			$waterY = $waterFontSize + $waterFontSize *1/2;
			break;
		case 3 :  // 右下角
			$waterX = $imageWidth - $fontWidth - $waterFontSize;
			$waterY = $imageHeight - $fontHeight;
			break;
		case 4 : // 左下角
			$waterX = $waterFontSize;
			$waterY = $imageHeight - $fontHeight;
			break;
		case 5 : // 中间
			$waterX = ($imageWidth -$fontWidth) / 2;
			$waterY = ($imageHeight - $fontHeight) / 2;
			break;
	}
	
	// 水印字体颜色
	$color = imagecolorallocate($im,0,0,0);
	
	imagettftext($im,$waterFontSize,0,$waterX,$waterY,$color,$fontPath,$waterText);
	
	// 定义水印文件名
	$waterFileName = $fileName."water.{$uploadFileExtension}";
	
	// 生成水印图
	switch($uploadFileExtension)
	{
		case 'jpg':
		case 'jpeg':
			imagejpeg($im,$waterFileName);
			break;
		case 'gif':
			imagegif($im,$waterFileName);
			break;
		case 'png':
			imagepng($im,$waterFileName);
			break;
	}
	
	return ['error'=>false,'path'=>$waterFileName];
}


function page($table,$where=null,$params=null,$showCount=10,$showPage=10,$fields="*",$order=null)
{
	// 当前页面
	$page = ! empty($params['page']) ? $params['page'] : 1;
	
	// 找出表数据总数
	$tableCount = rows($table,$where);
	
	// 每页需要显示的条数
	$defaultShowCount = 10;
	if(empty($showCount) || ! is_integer($showCount) || $showCount < 1) {
		$showCount = $defaultShowCount;
	} 
	
	// 需要显示的页码数
	$defaultShowPage = 10;
	if(empty($showPage) || ! is_integer($showPage) || $showPage < 4) {
		$showPage = $defaultShowPage;
	} 
	
	// 页面总数 = 文章总条数  /  每页要显示的数量
	$pageCount = ceil($tableCount / $showCount);
	
	// 步长
	$step = ceil(($showPage - 1) / 2);
	
	// 开始显示的页码
	$startPage = $page - $step;
	
	// 结束的页码
	$endPage = $page + ($showPage - 1 - $step);
	
	// 当开始页码<=1的时候
	if($startPage<=1) {
		$startPage 	= 1;
		$endPage 	= $showPage;
	}
	
	// 当前页码>=总页码数的时候
	if($endPage >= $pageCount){
		$endPage 	= $pageCount;
		$startPage 	= $pageCount - $showPage + 1;
		if($startPage <= 1){
			$startPage = 1;
		}
	}
	
	// 每页要开始显示的位置 (当前页面 - 1) * 每页要显示的数量
	$offsetPage = ($page - 1) * $showCount;
	
	// 上一页
	$prePage = $page - 1;
	if($prePage<1){
		$prePage = 1;
	}
	
	// 下一页
	$nextPage = $page + 1;
	if($nextPage > $pageCount) {
		$nextPage = $pageCount;
	}
	
	// 找出列表
	$lists = select($table,$where,$fields,$order,$offsetPage,$showCount);
	// 上一页的URL
	$params['page'] = $prePage;
	$prePageUrl = url($params);
	// 下一页的URL
	$params['page'] = $nextPage;
	$nextPageUrl = url($params);
	
	$liHtml = '';
	for($i=$startPage;$i<=$endPage;$i++){
		if($i== $page){
			$liHtml .= "<li class='active'><span>{$i}</span></li>";
		}else{
			$params['page'] = $i;
			$url = url($params);
			$liHtml .= "<li><a href='{$url}'>{$i}</a></li>";
		}
	}
	
	$pageHtml = <<<EOF
	<ul class="pagination">
		<li><a href="{$prePageUrl}">&laquo;</a></li>
		{$liHtml}
		<li><a href="{$nextPageUrl}">&raquo;</a></li>
	</ul>
EOF;
	return [
		'data' 		=> $lists,
		'page' 		=> $pageHtml,
		'count' 	=> $tableCount,
		'pageCount' => $pageCount,
		'prepage'   => $prePage,
		'nextpage'  => $nextPage,
		'startPage' => $startPage,
		'endPage'   => $endPage,
		'currPage'  => $page
	];
}


