<?php
require 'moefou.class.php';
require '../x/mysql.class.php';
// if(!empty($_POST['sss'])){
	// $sss = json_decode($_POST['sss'], true);
	// $oauth_token = base64_decode($sss['t']);
	// $oauth_token_secret = base64_decode($sss['s']);
	// $list = $MoeFM->music_favs($oauth_token, $oauth_token_secret);
	$params = array();
	$data = $MoeFM->music_favs($_SESSION['moefou']['oauth_token'], $_SESSION['moefou']['oauth_token_secret']);
	$list = $data['response']['favs'];
	$out = array();
	foreach($list as $k=>$v){
		$out[] = array(
			'wiki_id'=>$v['obj']['wiki_id'],
			'wiki_title'=>$v['obj']['wiki_title'],
			'wiki_cover'=>$v['obj']['wiki_cover']['large']
		);
	}
	header('Content-type: application/json;charset=utf-8');
	if(empty($out))
		$out = array(
			'error'=>'未登录',
			'msg'=>'未登录'
		);
	echo $_GET['cb']?$_GET['cb'].'('.json_encode($out).')':json_encode($out);
// }

