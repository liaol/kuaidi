<?php
require_once(__DIR__ . '/kuaidi.php');
$config_file = __DIR__ . '/config.ini';
$wait_query_nums = parse_ini_file($config_file);

if (!$wait_query_nums) 
	exit;

$new_wait_query_nums = array();
foreach ($wait_query_nums as $k=>$v) {
	$result = query($k);
	if (empty($result['data']))
	   continue;	
	$data = $result['data'][0];
	if (strtotime($data['time']) > $v) {
		$msg = "{$result['com']} ({$k}) \n {$data['time']} \n {$data['context']}";
		sendNotice($msg);
		$new_wait_query_nums[$k] = strtotime($data['time']);
	} else {
		$new_wait_query_nums[$k] = $v;
	}
	sleep(1);
}

if ($new_wait_query_nums != $wait_query_nums) {
	write_ini_file($config_file, $new_wait_query_nums);
}




