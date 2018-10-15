<?php

/**
 * @Author: iJiabao
 * @Date:   2018-10-15 23:57:54
 * @Last Modified by:   iJiabao
 * @Last Modified time: 2018-10-16 01:42:48
 */

// 参见： https://github.com/ijiabao/packagist-service
// the github webhook payload url like this:
//		http://this_php_page?pkg=YOUR_PACKAGIST_URL
// example for PACKAGIST_URL:
//		https://packagist.org/packages/ijiabao/laravel-dbdump
// when github is pushed, then hooked this url, and update for your packagists

// put into github, webhook secret， random
$secret = 'YOUR_GITHUB_WEBHOOK_SECRET';

// Your username / api_token for packagist.org
$user = 'YOUR_PACKAGIST_USER_NAME';
$token = 'YOUR_PACKAGIST_API_TOKEN';


$packagist_url = isset($_GET['pkg']) ? $_GET['pkg'] : '';

function abort_unless($bool, $err='error'){
	if(!$bool){
		header('HTTP/1.1 401 Unauthorized'); die($err);
	}
}


abort_unless(isset($_SERVER['HTTP_X_HUB_SIGNATURE']));
abort_unless($packagist_url);

$payload = isset($_POST['payload']) ? $_POST['payload'] : file_get_contents('php://input', true);

// verify signature, see: https://developer.github.com/webhooks/securing/
$signature = 'sha1=' . hash_hmac('sha1', $payload, $secret);
abort_unless($signature == $_SERVER['HTTP_X_HUB_SIGNATURE'], "Signatures didn't match!");


function postJson($url, $json_str){
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
	    'Content-Type: application/json',
	    'Content-Length: ' . strlen($json_str)
	));

	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_POST,1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $json_str);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$res = curl_exec($curl);
	curl_close($curl);
	return $res;
}

// check the hook event, it only for push
// $json = json_decode($payload);
// $json['hook']['event'] = ['push'];

// push for packagist
$post_url = "https://packagist.org/api/update-package?username={$user}&apiToken={$token}";
$post_data = json_encode(array('repository'=> array('url'=>$packagist_url)));

$result = postJson($post_url, $post_data);

header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
echo $result ? $result : 'push error';


// save log
$log = array(
	'time'=>date('Y-m-d H:i:s'),
	'server'=>$_SERVER,
	'post'=>$_POST,
	'payload'=>$payload,
	'sign'=>$signature
);

$log_txt = var_export($log, true);
@file_put_contents('./hook.log', $log_txt.PHP_EOL, FILE_APPEND);

?>
