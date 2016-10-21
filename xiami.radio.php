<?php
require '../x/mysql.class.php';
function writelog($str){
	$open = fopen('../data/fm_getxml_log.txt', 'a');
	fwrite($open, $str);
	fclose($open);
}
function get_xml($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	// curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'CLIENT-IP:123.125.68.'.mt_rand(0, 254),
		'X-FORWARDED-FOR:125.90.88.'.mt_rand(0, 254)
	));
	if(!curl_exec($ch)){
		$errno = curl_errno($ch);
		writelog('['.date(time(), 'Y-m-d H:i:s').']: __ 抓取失败, 错误码: '.$errno.";\n\r");
		$data = false;
	}else{
		$data = curl_multi_getcontent($ch);
	}
	curl_close($ch);
	return $data;
}
function get_playcount($sql, $pid, $rid = 11){
	$r = $sql->getLine('SELECT `pcount` FROM imouto_playcount WHERE pid=\''.$pid.'\' AND rid=\''.$rid.'\'');
	return $r ? $r['pcount'] : 0;
}
$out = array();
if($_GET['a'] == 'radio' && $_GET['rid'] == 11){
	$url = 'http://www.xiami.com/radio/xml/type/8/id/6961722?v='.time();
	$xml = get_xml($url);
	if($xml){
		$doc = new DOMDocument();
		$doc->loadXML($xml);
		$items = $doc->getElementsByTagName('track');
		foreach($items as $node){
			$song_ids = $node->getElementsByTagName('song_id');
			$titles = $node->getElementsByTagName('title');
			$pics = $node->getElementsByTagName('pic');
			$locations = $node->getElementsByTagName('location');
			$album_names = $node->getElementsByTagName('album_name');
			$artists = $node->getElementsByTagName('artist');
			$album_ids = $node->getElementsByTagName('album_id');
			$lengths = $node->getElementsByTagName('length');
			$song_id = $song_ids->item(0)->nodeValue;
			$title = $titles->item(0)->nodeValue;
			$pic = $pics->item(0)->nodeValue;
			$location = $locations->item(0)->nodeValue;
			$artist = $artists->item(0)->nodeValue;
			$album_name = $album_names->item(0)->nodeValue;
			$album_id = $album_ids->item(0)->nodeValue;
			$length = $lengths->item(0)->nodeValue;
			$out[] = array(
				'xid'=>$song_id,
				'title'=>htmlspecialchars_decode($title, ENT_QUOTES),
				'img'=>str_replace('http://img.xiami.net/images/album/', '', $pic),
				'mp3'=>$location,
				'album_name'=>htmlspecialchars_decode($album_name, ENT_QUOTES),
				'artist'=>htmlspecialchars_decode($artist, ENT_QUOTES),
				'album_id'=>$album_id,
				'length'=>$length*1000000,
				'play'=>get_playcount($sql, $song_id)
			);
		}
	}else{
		$out = array();
	}

}
header('Content-type: application/json;charset=utf-8');
echo json_encode($out);
