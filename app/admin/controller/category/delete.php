<?php
// 第一步：判断是否有id传递过来
if( ! isset($_GET['id'])) {
	msg('非法操作',url('lists'));
}

// 第二步：接收id
$id = $_GET['id'];

// 先判断该分类是否有子分类
$child = find('category',"pid={$id}");
if( ! empty($child )) {
	msg('先删除所有子分类后再执行该操作');
}

// 删除数据
$rows = delete('category',"id={$id}");

if(empty($rows)) {
	msg('分类删除失败',url('lists'));
}

msg('分类删除成功',url('lists'));
?>