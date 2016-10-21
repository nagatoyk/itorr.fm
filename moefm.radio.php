<?php
require 'moefou.class.php';
require '../x/mysql.class.php';
function get_playcount($sql, $pid){
	$data = $sql->getLine('SELECT * FROM imouto_playcount WHERE pid=\''.$pid.'\' AND rid=\'12\'');
	if($data['pcount']){
		return $data['pcount'];
	}else{
		return 0;
	}
}
function time2sec($time){
	$t = split(':', $time);
	switch (count($t)) {
		case 2:
			$m = preg_replace('/^0+/', '', $t[0]);
			$s = preg_replace('/^0+/', '', $t[1]);
			$m = $m * 60;
			$time = $m + $s;
			break;
		
		case 3:
			$h = preg_replace('/^0+/', '', $t[0]);
			$m = preg_replace('/^0+/', '', $t[1]);
			$s = preg_replace('/^0+/', '', $t[2]);
			$h = $h * 60 * 60;
			$m = $m * 60;
			$time = $m + $s;
			break;
	}
	return $time;
}
$out = array();
$params = array();
$params['page'] = 1;
/*if($_SESSION['moefou']){
	$params['fav'] = 'song';
}*/
if(!empty($_GET['music'])){
	$params[$_GET['a']] = $_GET['music'];
}elseif(isset($_GET['song'])){
	$params[$_GET['a']] = $_GET['song'];
}else{
	$params['perpage'] = 30;
}
$json = $MoeFM->listen($_SESSION['moefou']['oauth_token'], $_SESSION['moefou']['oauth_token_secret'], $params);
if($_SESSION['moefou']){
	$data = $json['playlist'];
}else{
	$data = $json['response']['playlist'];
}
foreach($data as $key => $val){
	$out[] = array(
		'xid' => $val['sub_id'],
		'title'=>htmlspecialchars_decode($val['sub_title'], ENT_QUOTES),
		'img'=>$val['cover']['large'],
		'mp3'=>$val['url'],
		'album_name'=>htmlspecialchars_decode($val['wiki_title'], ENT_QUOTES),
		'artist'=>htmlspecialchars_decode($val['artist'], ENT_QUOTES),
		'album_id'=>$val['wiki_id'],
		'length'=>time2sec($val['stream_time']),
		'play'=>get_playcount($sql, $val['sub_id']),
		'fav_sub'=>$val['fav_sub'],
		'fav_wiki'=>$val['fav_wiki']
	);
}
header('Content-type: application/json;charset=utf-8');
echo json_encode($out);
