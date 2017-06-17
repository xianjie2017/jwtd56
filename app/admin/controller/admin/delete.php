<?php
// 接收参数id
if(empty($_GET['id'])) {
	msg('非法操作',url('lists'));
}

$id = $_GET['id'];

// 删除
$result = delete('admin',"id={$id}");
if(empty($result)) {
	msg('删除失败',url('lists'));
}

msg('删除成功',url('lists'));