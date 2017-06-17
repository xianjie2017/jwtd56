<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
	die(json_encode(['error'=>true,'msg'=>'非法操作']));
}

if(empty($_POST['id'])){
	die(json_encode(['error'=>true,'msg'=>'非法操作']));
}

$id = $_POST['id'];

if(is_array($id)){
	$id = implode(',',$id);
}

$row = delete('goods',"id in ({$id})");
if(empty($row)){
	die(json_encode(['error'=>true,'msg'=>'删除失败']));
}

die(json_encode(['error'=>false]));
