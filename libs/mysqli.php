<?php
// 定义一个空的连接资源
$conn = null;
// 链接数据库
function connect()
{
	global $conn;
	// 链接数据库 返回链接资源 mysqli_connect(数据库地址,用户名,密码,数据库名称);
	$conn = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
	// 设置字符编码
	mysqli_query($conn,'set names utf8');
	// 返回链接资源
	return $conn;
}
// 执行sql语句
function query($sql)
{
	// 链接数据库
	$conn = connect();
	// 执行sql语句 返回结果集
	$result = mysqli_query($conn,$sql);
	// 返回结果
	return $result;
}
// 添加
/* array(
	'字段名1'=> '值1',
	'字段名2'=> '值2',
); */
function insert($table,$dataArr)
{
	global $conn;
	// $sql = "insert into 表名 (字段名1,字段名2) values (值1,值2)";
	// 组装sql语句
	$sql = "insert into `{$table}`";
	// 定义两个空数组，用来存放字段名和值
	$columnArr = [];
	$varlueArr = [];
	// 循环遍历数据 把字段名和值分别存储数组
	foreach($dataArr as $key => $value){
		$columnArr[] = "`{$key}`";
		// 把addslashes() " , ' ...等等特殊字符加上转义\"
		$varlueArr[] = "'".addslashes(trim($value))."'";
	}
	// 把字段名数组和值数组转换成字符串
	$columnStr = implode(',',$columnArr);
	$varlueStr = implode(',',$varlueArr);
	$sql .= " ($columnStr) values ($varlueStr)";
	// 执行sql语句
	$result = query($sql);
	// 取得最后插入的id
	return mysqli_insert_id($conn);
}
// 删除
function delete($table,$where=null)
{
	global $conn;
	// $sql = "delete from 表名 where 条件";
	// 组装sql语句
	$sql = "delete from `{$table}`";
	// 如果存在条件
	if( ! empty($where)) {
		$sql .= " where {$where}";
	}
	// 执行sql语句
	$result = query($sql);
	return mysqli_affected_rows($conn);	
}
// 修改
/* array(
	'字段名1'=> '值1',
	'字段名2'=> '值2',
); */
function update($table,$dataArr,$where=null)
{
	global $conn;
	// $sql = "update 表名 set 字段1=值1,字段2=值2 where 条件";
	// 组装sql语句
	$sql = "update `{$table}` set";
	// 定义一个空数组
	$columnValueArr = [];
	// 循环遍历数据
	foreach($dataArr as $key => $value){
		// 把组装数据城 字段=值 并存入数组
		$columnValueArr[] = "`{$key}` = '".addslashes(trim($value))."'";
	}
	// 数组转换成字符串
	$columnValueStr = implode(',',$columnValueArr);
	// 拼接sql
	$sql .= " {$columnValueStr}";
	// 如果存在条件
	if( ! empty($where)) {
		$sql .= " where {$where}";
	}
	// 执行sql语句
	$result = query($sql);
	// 返回受影响条数
	return mysqli_affected_rows($conn);
}

// 查询一条
function find($table,$where=null,$fields="*",$order=null)
{
	// $sql = "select 字段名 from 表名 where 条件 order by 排序 limit 0,1";
	// 组装sql语句
	$sql = "select {$fields} from `{$table}`";
	// 如果存在条件 则拼接上where条件
	if( ! empty($where)) {
		$sql .= " where {$where}";
	}
	// 如果存在排序
	if( ! empty($order)) {
		$sql .= " order by {$order}";
	}
	$sql .= " limit 0,1";
	// 执行sql语句
	$result = query($sql);
	// 返回单条数据
	return mysqli_fetch_assoc($result);
}
// 查询多条
/*array(
	'table1',	
	'table2',
	'关联条件',
	'left',
)*/
function select($table,$where=null,$fields="*",$order=null,$start=0,$limit=null)
{
	// $sql = "select 字段名 from 表名 where 条件 order by 排序 limit 条数";
	if(is_array($table)){
		$sql = "select {$fields} from ";
		$type = 'left';
		if( ! empty($table[3])) {
			$type = $table[3];
		}
		$sql .= " {$table[0]} {$type} join {$table[1]} on {$table[2]} ";
	}else{
		$sql = "select {$fields} from `{$table}`";
	}
	
	// 如果存在条件 则拼接上where条件
	if( ! empty($where)) {
		$sql .= " where {$where}";
	}
	// 如果存在排序
	if( ! empty($order)) {
		$sql .= " order by {$order}";
	}
	// 如果有限制条数
	if( ! empty($limit)) {
		$sql .= " limit {$start},{$limit}";
	}
	
	// 执行sql语句
	$result = query($sql);
	// 定义一个空数组 用来存放每次循环的数据
	$data = [];
	while($row = mysqli_fetch_assoc($result)){
		$data[] = $row;
	}
	// 返回结果
	return $data;
}
// 查询数量
function rows($table,$where=null)
{
	// select count(id) as count from article;
	// 组装sql语句
	//$sql = "select count(*) as count from {$table}";
	if(is_array($table)){
		$sql = "select count(*) as count from ";
		$type = 'left';
		if( ! empty($table[3])) {
			$type = $table[3];
		}
		$sql .= " {$table[0]} {$type} join {$table[1]} on {$table[2]} ";
	}else{
		$sql = "select count(*) as count from `{$table}`";
	}
	// 当有where条件的时候
	if( ! empty($where)) {
		$sql .= " where {$where}";
	}
	// 查找数据
	$result = query($sql);
	// 获取一行数据
	$data = mysqli_fetch_assoc($result);
	// 返回总数
	return $data['count'];
}
