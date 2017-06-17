<?php
$xmlObj = simplexml_load_file('./libs/menu.xml');
$data = [
	'xmlObj' => $xmlObj,
];
view($data,'','');