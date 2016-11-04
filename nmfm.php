<?php
/**
 * Created by PhpStorm.
 * User: misaka
 * Date: 2016-11-3
 * Time: 12:51
 */
require '../x/mysql.class.php';
require 'NeteaseMusic.class.php';

function get_count($sql, $pid)
{
    $c = $sql->getLine('SELECT play FROM imouto_play WHERE pid = ' . $pid);
    return isset($c['play']) ? $c['play'] : 0;
}

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
$r = array();
$lrc = '';
if ($_GET['a'] == 'random') {
    $w = array();
    if (isset($_COOKIE['aid'])) {
        $aid = $_COOKIE['aid'];
        $r = $music->get_album_info($aid);
        $list = $r['album']['songs'];
        foreach ($list as $k => $v) {
            $r[] = array(
                'xid' => (int)$v['id'],
                'album_id' => (int)$aid,
                'title' => $v['name'],
                'album_name' => $r['album']['name'],
                'artist' => $music->artists($v['artists']),
                'play' => (int)get_count($sql, $v['id']),
                'img' => $r['album']['picUrl'],
                'mp3' => $v['mp3Url'],
                'length' => (int)($v['duration'] / 1000)
            );
        }
        unset($r['code']);
        unset($r['album']);
    } else {
        $r = $music->get_playlist_info(2660385);
        $list = $r['result']['tracks'];
        $l = array();
        foreach ($list as $k => $v) {
            $l[] = array(
                'xid' => (int)$v['id'],
                'album_id' => (int)$v['album']['id'],
                'title' => $v['name'],
                'album_name' => $v['album']['name'],
                'artist' => $music->artists($v['artists']),
                'play' => (int)get_count($sql, $v['id']),
                'img' => $v['album']['picUrl'],
                'mp3' => $v['mp3Url'],
                'length' => (int)($v['duration'] / 1000)
            );
        }
        foreach (array_rand($l, 5) as $k) {
            $r[] = $l[$k];
        }
        unset($r['code']);
        unset($r['result']);
    }
} elseif ($_GET['a'] == 'song') {
    $id = (int)$_GET['id'];
    $r = $sql->getData('SELECT sid AS xid, aid AS album_id, `name` AS title, album AS album_name, artists AS artist,img,mp3,duration AS `length` FROM imouto_music WHERE sid=' . $id);
    if ($r) {
        $r = $music->get_music_info($id);
        foreach ($r['songs'] as $k => $v) {
            $r[] = array(
                'xid' => (int)$v['id'],
                'play' => (int)get_count($sql, $v['id']),
                'length' => (int)($v['duration'] / 1000),
                'album_id' => (int)$v['album']['id'],
                'album_name' => $v['album']['name'],
                'title' => $v['name'],
                'aitist' => $music->artists($v['artists']),
                'mp3' => $v['mp3Url'],
                'img' => $v['album']['picUrl']
            );
        }
        unset($r['code']);
        unset($r['equalizers']);
        unset($r['songs']);
    } else {
        foreach ($r as $k => $v) {
            $r[] = array(
                'xid' => (int)$v['xid'],
                'play' => (int)get_count($sql, $id),
                'length' => (int)$v['length'],
                'album_id' => (int)$v['album_id'],
                'title' => htmlspecialchars_decode($v['title'], ENT_QUOTES),
                'artist' => htmlspecialchars_decode($v['artist'], ENT_QUOTES),
                'album_name' => htmlspecialchars_decode($v['album_name'], ENT_QUOTES)
            );
        }
    }
} elseif ($_GET['a'] == 'lrc') {
    $id = (int)$_GET['id'];
    $lrc_info = $music->get_music_lyric($id);
//    if (isset($lrc_info['lrc']['lyric'])) {
//        echo $lrc_info['lrc']['lyric'];
//    }
    if (isset($lrc_info['lyric'])) {
        $lrc = $lrc_info['lyric'];
    }
} elseif ($_GET['a'] == 'log') {
    if (!empty($_POST['pid'])) {
        if (!preg_match('/^\d+$/', $_POST['pid'])) {
            exit(json_encode(array('msg' => '恶意请求!')));
        }
        $pid = $_POST['pid'];
        $r = $sql->getLine('SELECT * FROM imouto_play WHERE pid=' . $pid);
        if ($r) {
            $sql->runSql('UPDATE imouto_play SET play=play+1 WHERE pid=' . $pid);
            if ($sql->affectedRows()) {
                $r = array(
                    'pid' => $pid,
                    'msg' => '更新成功'
                );
            }
        } else {
            $sql->runSql('INSERT INTO imouto_play (pid,play) VALUES (' . $pid . ',1)');
            if ($sql->lastId()) {
                $r = array(
                    'pid' => $pid,
                    'msg' => '插入成功'
                );
            }
        }
    }
} elseif ($_GET['a'] == 'report') {
    if (!empty($_POST)) {
        $p = $_POST;
        $sql->runSql('INSERT INTO imouto_report (pid,title,msg) VALUES (' . $p['pid'] . ',\'' . $p['title'] . '\',\'' . serialize($_POST) . '\')');
        if ($sql->lastId()) {
            $r = array(
                'msg' => '错误报告提交成功!'
            );
        } else {
            $r = array(
                'msg' => '错误报告已提交过了!'
            );
        }
    }
}
$ct = $_GET['a'] != 'lrc' ? 'application/json' : 'text/plain';
header('Content-Type: ' . $ct . '; charset=utf-8');
echo $_GET['a'] != 'lrc' ? json_encode($r) : $lrc;
