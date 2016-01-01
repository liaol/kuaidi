<?php

//快递100的appkey
//详见http://www.kuaidi100.com/openapi/
$kuaidi100_app_key = 'yourkey';

//server酱的appkey
//详见http://sc.ftqq.com/
$server_chan_app_key = 'yourkey';


/**
	* 查询快递公司代码
	*
	* @param $nu string 单号
	*
	* @return  string|boolean
 */
function queryCom($nu) 
{
	$url = 'http://m.kuaidi100.com/autonumber/auto?num='. $nu;
	$result = file_get_contents($url);

	if ($result && $data = json_decode($result, true)) {
		return $data[0]['comCode'];
	}

	return false;
}

/**
	* 查询单号
	*
	* @param $nu string 单号
	*
	* @return array|boolean 
 */
function query($nu) 
{
	$com = queryCom($nu);
	if (!$com) 
		return false;
	global $kuaidi100_app_key;

	$url = vsprintf('http://api.kuaidi100.com/api?id=%s&nu=%s&com=%s', array($kuaidi100_app_key, $nu, $com));
	$result = file_get_contents($url);

	if ($result && $data = json_decode($result, true)) {
		return $data;
	}

	return false;
}

/**
	* 写配置文件 
	*
	* @param $path 路径
	* @param $assoc_array 配置数组
	*
	* @return  boolean
 */
function write_ini_file($path, $assoc_array) {
	$content = '';
	foreach ($assoc_array as $key => $item) {
		if (is_array($item)) {
			$content .= "\n[{$key}]\n";
			foreach ($item as $key2 => $item2) {
				if (is_numeric($item2) || is_bool($item2))
					$content .= "{$key2} = {$item2}\n";
				else
					$content .= "{$key2} = \"{$item2}\"\n";
			}
		}else {
			if (is_numeric($item) || is_bool($item))
				$content .= "{$key} = {$item}\n";
			else
				$content .= "{$key} = \"{$item}\"\n";
		}
	}
	if (!$handle = fopen($path, 'w')) {
		return false;
	}

	if (!fwrite($handle, $content)) {
		return false;
	}

	fclose($handle);
	return true;
}

/**
	* 发通知 
	*
	* @param $msg string 
	*
	* @return void
 */
function sendNotice($msg) 
{
	global $server_chan_app_key;
	$title = '快递信息';
	$query_string = http_build_query(
		array(
			'text' => $title,
			'desp' => $msg,
		)
	);
	$url = 'http://sc.ftqq.com/' . $server_chan_app_key . '.send?' . $query_string;
	file_get_contents($url);
}