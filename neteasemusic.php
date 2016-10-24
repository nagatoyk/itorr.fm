<?php
/**
 * Created by PhpStorm.
 * User: misaka
 * Date: 2016-10-24
 * Time: 11:10
 */
error_reporting('E_ALL');
require '../x/mysql.class.php';
require 'NeteaseMusic.class.php';
if (!function_exists('p')) {
    function p($var)
    {
        echo '<pre>' . print_r($var, true) . '</pre>';
    }
}
if (!function_exists('dd')) {
    function dd($var)
    {
        ob_start();
        var_dump($var);
        echo '<pre>' . ob_get_clean() . '</pre>';
    }
}
$d = array();
if ($_GET['a'] == 'get') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : '2660385';
    $data = json_decode($music->get_playlist_info($id), true);
    $insertSql = array();
    foreach ($data['result']['tracks'] as $k => $v) {
        $artists = $music->artists($v['artists']);
        $mp3Url = str_replace('://m', '://p', $v['mp3Url']);
        $values = array(
            $v['id'],
            $v['album']['id'],
            $v['album']['picUrl'],
            $mp3Url,
            htmlspecialchars(ltrim(rtrim($v['name'])), ENT_QUOTES),
            htmlspecialchars($v['album']['name'], ENT_QUOTES),
            htmlspecialchars($artists, ENT_QUOTES),
            $v['mvid'],
            $v['duration']
        );
        $insertSql[] = '(\'' . implode('\',\'', $values) . '\')';
    }
    $values = implode(',', $insertSql);
    $insertSql = "INSERT INTO imouto_music (`sid`,`aid`,`img`,`mp3`,`name`,`album`,`artists`,`mvid`,`duration`) VALUES {$values};";
//	 echo $insertSql;
    dd($sql->runSql($insertSql));
    p($sql->error());
} elseif ($_GET['a'] == 'radio') {
//	 $e = $sql->runSql("SELECT `sid` AS `xid`, `aid` AS `album_id`, `name` AS `title`, `album` AS `album_name`, `artists` AS `artist`,`play`,`img`,`mp3` FROM imouto_music WHERE id >= (SELECT FLOOR(MAX(id) * RAND()) FROM `imouto_music`) ORDER BY RAND() LIMIT 5");
    $d = $sql->getData('SELECT `sid` AS `xid`, `aid` AS `album_id`, `name` AS `title`, `album` AS `album_name`, `artists` AS `artist`,`play`,`img`,`mp3`,`duration` FROM imouto_music ORDER BY RAND() LIMIT 5');
    header('Content-type: application/json;charset=utf-8');
    echo json_encode($d);

} elseif ($_GET['a'] == 'song') {
    $id = (int)$_GET['id'];
    $d = $sql->getData('SELECT `sid` AS `xid`, `aid` AS `album_id`, `name` AS `title`, `album` AS `album_name`, `artists` AS `artist`,`play`,`img`,`mp3` FROM imouto_music WHERE `sid`=\'' . $id . '\' LIMIT 1');
    header('Content-type: application/json;charset=utf-8');
    echo json_encode($d);
} elseif ($_GET['a'] == 'lrc') {
    $id = (int)$_GET['id'];
    $lrc_info = json_decode($music->get_music_lyric($id), true);
    /*if(isset($lrc_info['lrc']['lyric'])){
        echo $lrc_info['lrc']['lyric'];
    }*/
    if (isset($lrc_info['lyric'])) {
        header('Content-Type: text/plain');
        echo $lrc_info['lyric'];
    }
}