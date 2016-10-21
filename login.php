<?php
require 'moefou.class.php';
if(isset($_GET['redirect'])){
	$_SESSION['redirect'] = $_GET['redirect'];
	$Moe->getAuthorizeURL(true);
}elseif(isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])){
	$oauth_token = $_GET['oauth_token'];
	$oauth_verifier = $_GET['oauth_verifier'];
	$data = $Moe->getAccessToken($oauth_token, $_SESSION['moefou']['request_token_secret'], $oauth_verifier);
	parse_str($data);
	$_SESSION['moefou']['oauth_token'] = $oauth_token;
	$_SESSION['moefou']['oauth_token_secret'] = $oauth_token_secret;
	// setcookie('moufou', json_encode($_SESSION['moefou']));
	header('Location:/fm/login.php?a=in');
	exit();
}elseif($_GET['a'] == 'in'){
	// $info = $MoeFM->get_user_info($_SESSION['moefou']['oauth_token'], $_SESSION['moefou']['oauth_token_secret']);
	header('Location:'.$_SESSION['redirect']);
	exit();
}elseif($_GET['a'] == 'sss' && !empty($_GET['cb'])){
	if(isset($_SESSION['moefou']['oauth_token'])){
		$arr = array(
			'sss'=>json_encode(array(
				't'=>base64_encode($_SESSION['moefou']['oauth_token']),
				's'=>base64_encode($_SESSION['moefou']['oauth_token_secret'])
			))
		);
	}else{
		$arr = array(
			'error'=>'未登录'
		);
	}
	header('Content-type: application/json;charset=utf-8');
	echo $_GET['cb'].'('.json_encode($arr).')';
}elseif(!empty($_POST['sss'])){
	$sss = json_decode($_POST['sss'], true);
	$oauth_token = base64_decode($sss['t']);
	$oauth_token_secret = base64_decode($sss['s']);
	$info = $MoeFM->get_user_info($oauth_token, $oauth_token_secret);
	$user = $info['response']['user'];
	header('Content-type: application/json;charset=utf-8');
	$arr = array(
		'name'=>$user['user_nickname'],
		'avatar'=>$user['user_avatar'],
		'home'=>$user['user_fm_url']
	);
	echo json_encode($arr);
}