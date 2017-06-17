<?php
$adminData = select('admin');
$data = [
	'adminData' => $adminData
];
// 加载视图
view($data);