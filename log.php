<?php
require 'moefou.class.php';
require '../x/mysql.class.php';
//$pid = $_POST['pid'];
//$rid = $_POST['rid'];
if(!empty($_POST['pid'])){
    $rid = (int) $_POST['rid'];
    $pid = (int) $_POST['pid'];
	if($rid == 11){
		$sql->runSql("UPDATE imouto_music SET play=play+1 WHERE sid={$pid}");
	}
	$data = $sql->getLine("SELECT * FROM imouto_playcount WHERE pid={$pid}");
	if($data){
	    $modified=time();
		$sql->runSql("UPDATE imouto_playcount SET pcount=pcount+1 AND modified={$modified} WHERE pid='$pid'");
		$r = array(
			'id'=>$data['id'],
			'pid'=>$pid,
			'msg'=>'更新记录成功!',
			'count'=>($data['pcount'] + 1)
		);
	}else{
	    $time=time();
		$sql->runSql("INSERT INTO imouto_playcount (`pid`,`rid`,`pcount`,`created`,`modified`) VALUES ({$pid},{$rid},1,{$time},{$time})");
		$r = array(
			'id'=>$sql->lastId(),
			'pid'=>$pid,
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


