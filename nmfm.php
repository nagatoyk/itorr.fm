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
                'xid' => $v['id'],
                'album_id' => $aid,
                'title' => $v['name'],
                'album_name' => $r['album']['name'],
                'artist' => $music->artists($v['artists']),
                'play' => get_count($sql, $v['id']),
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
                'xid' => $v['id'],
                'album_id' => $v['album']['id'],
                'title' => $v['name'],
                'album_name' => $v['album']['name'],
                'artist' => $music->artists($v['artists']),
                'play' => get_count($sql, $v['id']),
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
    $r = $sql->getData('SELECT sid AS xid, aid AS album_id, `name` AS title, album AS album_name, artists AS artist,img,mp3,duration AS `length` FROM imouto_music WHERE sid='.$id);
    if (!$r) {
        $r = $music->get_music_info($id);
        foreach ($r['songs'] as $k => $v) {
            $r[] = array(
                'xid' => $v['id'],
                'play' => get_count($sql, $v['id']),
                'length' => ($v['duration'] / 1000),
                'album_id' => $v['album']['id'],
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
        $r = array_map(function ($o) {
            $o['xid'] = $o['xid'];
            $o['play'] = $o['play'];
            $o['length'] = $o['length'];
            $o['album_id'] = $o['album_id'];
            $o['title'] = htmlspecialchars_decode($o['title'], ENT_QUOTES);
            $o['artist'] = htmlspecialchars_decode($o['artist'], ENT_QUOTES);
            $o['album_name'] = htmlspecialchars_decode($o['album_name'], ENT_QUOTES);
            return $o;
        }, $r);
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
    if (!empty($_POST)) {
        $p = $_POST;
        $r = $sql->getLine('SELECT * FROM imouto_play WHERE pid=' . $p['pid']);
        if ($r) {
            $sql->runSql('UPDATE imouto_play SET play=play+1 WHERE pid=' . $p['pid']);
            if ($sql->affectedRows()) {
                $r = array(
                    'pid' => $p['pid'],
                    'msg' => '更新成功'
                );
            }
        } else {
            $sql->runSql('INSERT INTO imouto_play (pid,play) VALUES (' . $p['pid'] . ',1)');
            if ($sql->lastId()) {
                $r = array(
                    'pid' => $p['pid'],
                    'msg' => '插入成功'
                );
            }
        }
    }
} elseif ($_GET['a'] == 'report') {
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
$ct = $_GET['a'] != 'lrc' ? 'application/json' : 'text/plain';
header('Content-Type: ' . $ct . '; charset=utf-8');
echo $_GET['a'] != 'lrc' ? json_encode($r) : $lrc;
