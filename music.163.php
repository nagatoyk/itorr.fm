<?php
/**
 * Created by PhpStorm.
 * User: Moon
 * Date: 2014/11/26 0026
 * Time: 2:06
 * Url: http://moonlib.com/606.html
 */
error_reporting('E_ALL');
require '../x/mysql.class.php';
class music {
	const refer = 'http://music.163.com/';
	public function curl_get($url){
		$header[] = 'Cookie: appver=1.5.0.75771;';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_REFERER, self::refer);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
	public function get_music_info($music_id){
		$url = 'http://music.163.com/api/song/detail/?id='.$music_id.'&ids=%5B'.$music_id.'%5D';
		return self::curl_get($url);
	}
	public function get_artist_album($artist_id, $limit){
		$url = 'http://music.163.com/api/artist/albums/'.$artist_id.'?limit='.$limit;
		return self::curl_get($url);
	}
	public function get_album_info($album_id){
		$url = 'http://music.163.com/api/album/'.$album_id;
		return self::curl_get($url);
	}
	public function get_playlist_info($playlist_id){
		$url = 'http://music.163.com/api/playlist/detail?id='.$playlist_id;
		return self::curl_get($url);
	}
	public function get_music_lyric($music_id){
		// $url = 'http://music.163.com/api/song/lyric?os=pc&id='.$music_id.'&lv=-1&kv=-1&tv=-1';
		$url = 'http://music.163.com/api/song/media?id='.$music_id;
		return self::curl_get($url);
	}
	public function get_mv_info($mvid, $type = 'mp4'){
		$url = 'http://music.163.com/api/mv/detail?id='.$mvid.'&type='.$type;
		return self::curl_get($url);
	}
	public function artists($list){
		$name = '';
		foreach($list as $k){
			$name .= ','.$k['name'];
		}
		return ltrim($name, ',');
	}
}
if(!function_exists('p')){
	function p($var){
		echo '<pre>'.print_r($var, TRUE).'</pre>';
	}
}
if(!function_exists('dd')){
	function dd($var){
		ob_start();
		var_dump($var);
		echo '<pre>'.ob_get_clean().'</pre>';
	}
}
$music = new music();
$d = array();
if($_GET['a'] == 'get'){
	$data = json_decode($music->get_playlist_info('2660385'), true);
	$insertSql = array();
	foreach($data['result']['tracks'] as $k => $v){
		$artists = $music->artists($v['artists']);
		$mp3Url = str_replace('://m', '://p', $v['mp3Url']);
		$_sql = array(
			$v['id'],
			$v['album']['id'],
			$v['album']['picUrl'],
			$mp3Url,
			htmlspecialchars($v['name'], ENT_QUOTES),
			htmlspecialchars($v['album']['name'], ENT_QUOTES),
			htmlspecialchars($artists, ENT_QUOTES),
			$v['mvid']
		);
		$insertSql[] = '(\''.implode('\',\'', $_sql).'\')';
	}
	$insertSql = 'insert into imouto_music (`sid`,`aid`,`img`,`mp3`,`name`,`album`,`artists`,`mvid`) values '.implode(',', $insertSql).';';
	$sql->runSql($insertSql);
}elseif($_GET['a'] == 'radio'){
	// $e = $sql->runSql('SELECT `sid` AS `xid`, `aid` AS `album_id`, `name` AS `title`, `album` AS `album_name`, `artists` AS `artist`,`play`,`img`,`mp3` FROM imouto_music WHERE id >= (SELECT FLOOR(MAX(id) * RAND()) FROM `imouto_music`) ORDER BY RAND() LIMIT 5');
	$d = $sql->getData('SELECT `sid` AS `xid`, `aid` AS `album_id`, `name` AS `title`, `album` AS `album_name`, `artists` AS `artist`,`play`,`img`,`mp3` FROM imouto_music ORDER BY RAND() LIMIT 5');
	header('Content-type: application/json;charset=utf-8');
	echo json_encode($d);

}elseif($_GET['a'] == 'song'){
	$id = (int)$_GET['id'];
	$d = $sql->getData('SELECT `sid` AS `xid`, `aid` AS `album_id`, `name` AS `title`, `album` AS `album_name`, `artists` AS `artist`,`play`,`img`,`mp3` FROM imouto_music WHERE `sid`=\''.$id.'\' LIMIT 1');
	header('Content-type: application/json;charset=utf-8');
	echo json_encode($d);
}elseif($_GET['a'] == 'lrc'){
	$id = (int)$_GET['id'];
	$lrc_info = json_decode($music->get_music_lyric($id), true);
	/*if(isset($lrc_info['lrc']['lyric'])){
		echo $lrc_info['lrc']['lyric'];
	}*/
	if(isset($lrc_info['lyric'])){
		header('Content-Type: text/plain');
		echo $lrc_info['lyric'];
	}
}