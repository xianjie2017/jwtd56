<?php
if( ! empty($_POST)){
	if(empty($_POST['listUrl'])){
		msg('请输入文章列表的地址');
	}
	
	if(empty($_POST['urlReg'])){
		msg('请输入文章列表的正则表达式');
	}
	
	if(empty($_POST['cid'])){
		msg('请选择文章分类');
	}
	
	if(empty($_POST['titleReg'])){
		msg('请输入文章标题表达式');
	}
	
	if(empty($_POST['authorReg'])){
		msg('请输入作者标题表达式');
	}
	
	if(empty($_POST['contentReg'])){
		msg('请输入内容标题表达式');
	}
	
	// 采集列表页html数据
	$listHtml = file_get_contents($_POST['listUrl']);
	//正则匹配
	$listRow = preg_match_all($_POST['urlReg'],$listHtml,$listMatches);
	if(empty($listRow)){
		msg('文章列表采集失败');
	}

	$listUrlArray = $listMatches[1];
	
	if(! empty($listUrlArray)){
		foreach($listUrlArray as $key=> $value){
			// 定义一个空数组，用来组装数据
			$data = [];
			// 采集内容页HTML数据
			$descHtml = file_get_contents($value);
			// 标题采集
			$titleRow = preg_match($_POST['titleReg'],$descHtml,$titleMatches);
			if(empty($titleRow) || empty($titleMatches[1])){
				// 跳过当前循环
				continue;
			}
			// 组装标题
			$data['title'] = $titleMatches[1];
			
			// 作者采集
			$authorRow = preg_match($_POST['authorReg'],$descHtml,$authorMatches);
			if(empty($authorRow)){
				// 跳过当前循环
				continue;
			}
			$data['author'] = $authorMatches[1];
			
			$contentRow = preg_match($_POST['contentReg'],$descHtml,$contentMatches);
			if(empty($contentRow)){
				// 跳过当前循环
				continue;
			}
			
			$data['content'] = $contentMatches[1];
			
			// 添加到数据库
			$data['create_time'] = date('Y-m-d H:i:s',time());
			$data['cid'] = $_POST['cid'];
			$saveRow = insert('article',$data);
			if(empty($saveRow)){
				echo '<span style="color:red;">'.$data['title'].'采集失败</span><br>';
			}else{
				echo $data['title'].'采集成功<br>';
			}
			
			ob_flush();
			flush();
			// sleep(1);			
		}
	}
	msg('采集成功');
}
// 先找出所有的文章分类
$cateData = select('article_category');
// 分配到视图
$data = array(
	'cateData' => $cateData,
);
// 加载视图
view($data);
?>