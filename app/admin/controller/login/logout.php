<?php
// 销毁一个session
// unset($_SESSION['admin']);

//全部清除session
session_destroy();


msg('成功跳出登录',url('login'));

?>