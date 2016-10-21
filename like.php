<?php
require 'moefou.class.php';

if($_GET['a'] == 'like'){
	$pid = $_POST['pid'];
	// $rid = $_POST['rid'];
	$result = $MoeFM->add_like_fav($_SESSION['moefou']['oauth_token'], $_SESSION['moefou']['oauth_token_secret'], $pid);
	if($result['response']['fav']['fav_obj_id'] == $pid){
		$r = array(
			'msg'=>'加入收藏成功'
		);
	}
	header('Content-type: application/json;charset=utf-8');
	echo json_encode($r);
}
