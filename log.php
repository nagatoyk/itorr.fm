<?php
require 'moefou.class.php';
require '../x/mysql.class.php';
$pid = $_POST['pid'];
$rid = $_POST['rid'];
if(preg_match('/^[0-9]{5,12}$/', $pid)/* && preg_match('/^[0-9]{1,3}$/', $rid)*/){
	$data = $sql->getLine('SELECT * FROM imouto_playcount WHERE pid=\''.$pid.'\'');
	if($data){
		$sql->runSql('UPDATE imouto_playcount SET pcount=pcount+1 WHERE pid=\''.$pid.'\'');
		$r = array(
			'id'=>$pid,
			'msg'=>'更新记录成功!',
			'count'=>($data['pcount'] + 1)
		);
	}else{
		$sql->runSql('INSERT INTO imouto_playcount (`pid`,`rid`,`pcount`) VALUES ('.$pid.','.$rid.',1)');
		$r = array(
			'id'=>$sql->lastId(),
			'msg'=>'添加记录成功!',
			'count'=>1
		);
	}
	/*if($rid == 12){
		$r = $MoeFM->set_log($_SESSION['moefou']['oauth_token'], $_SESSION['moefou']['oauth_token_secret'], $pid);
		if($r['response']['status']){
			unset($r['response']['information']);
		}
	}*/
	header('Content-type: application/json;charset=utf-8');
	echo json_encode($r);
}


