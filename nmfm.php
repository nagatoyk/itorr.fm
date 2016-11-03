<?php
/**
 * Created by PhpStorm.
 * User: misaka
 * Date: 2016-11-3
 * Time: 12:51
 */
require '../x/mysql.class.php';
require 'NeteaseMusic.class.php';

function get_count($sql, $pid, $rid = false)
{
    $w = array();
    if ($rid) {
        $w[] = '`rid` = 12';
    } else {
        $w[] = '`rid` < 12';
    }
    $w[] = '`pid` = ' . $pid;
    $where = implode(' AND ', $w);
    $c = $sql->getLine("SELECT `pcount` FROM `imouto_playcount` WHERE {$where}");
    return isset($c['pcount']) ? $c['pcount'] : 0;
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
    if ($_GET['rid'] > 0) {
        $w[] = 'aid="' . $_GET['rid'] . '"';
    }
    $w = implode(' && ', $w);
    if ($w) {
        $w = ' WHERE ' . $w;
    }
    $r = $sql->getData('SELECT `sid` AS `xid`, `aid` AS `album_id`, `name` AS `title`, `album` AS `album_name`, `artists` AS `artist`,`play`,`img`,`mp3`,`duration` AS `length` FROM `imouto_music`' . $w . ' ORDER BY RAND() LIMIT 5');
    $r = array_map(function ($o) {
        $o['xid'] = (int)$o['xid'];
        $o['play'] = (int)$o['play'];
        $o['length'] = (int)$o['length'];
        $o['album_id'] = (int)$o['album_id'];
        $o['title'] = htmlspecialchars_decode($o['title'], ENT_QUOTES);
        $o['artist'] = htmlspecialchars_decode($o['artist'], ENT_QUOTES);
        $o['album_name'] = htmlspecialchars_decode($o['album_name'], ENT_QUOTES);
        return $o;
    }, $r);
} elseif ($_GET['a'] == 'song') {
    $id = (int)$_GET['id'];
    $r = $sql->getData("SELECT `sid` AS `xid`, `aid` AS `album_id`, `name` AS `title`, `album` AS `album_name`, `artists` AS `artist`,`play`,`img`,`mp3`,`duration` AS `length` FROM `imouto_music` WHERE `sid`={$id} LIMIT 1");
    $r = array_map(function ($o) {
        $o['xid'] = (int)$o['xid'];
        $o['play'] = (int)$o['play'];
        $o['length'] = (int)$o['length'];
        $o['album_id'] = (int)$o['album_id'];
        $o['title'] = htmlspecialchars_decode($o['title'], ENT_QUOTES);
        $o['artist'] = htmlspecialchars_decode($o['artist'], ENT_QUOTES);
        $o['album_name'] = htmlspecialchars_decode($o['album_name'], ENT_QUOTES);
        return $o;
    }, $r);
} elseif ($_GET['a'] == 'lrc') {
    $id = (int)$_GET['id'];
    $lrc_info = json_decode($music->get_music_lyric($id), true);
//        if(isset($lrc_info['lrc']['lyric'])){
//            echo $lrc_info['lrc']['lyric'];
//        }
    if (isset($lrc_info['lyric'])) {
        $lrc = $lrc_info['lyric'];
    }
} elseif ($_GET['a'] == 'log') {
    if (!empty($_POST)) {
        $p = $_POST;
        $sql->runSql("UPDATE `imouto_music` SET play=play+1 WHERE sid={$p['pid']}");
        if ($sql->affectedRows()) {
            $r = array(
                'id' => $p['pid'],
                'msg' => '更新成功'
            );
        } else {
            $r = array(
                'id' => $p['pid'],
                'msg' => '更新失败'
            );
        }
    }
} elseif ($_GET['a'] == 'report') {
    $r = array('msg' => '错误报告提交成功!', 'p' => $_POST);
}
$_GET['a'] != 'lrc' ? header('Content-type: application/json;charset=utf-8') : header('Content-Type: text/plain;charset=utf-8');
echo $_GET['a'] != 'lrc' ? json_encode($r) : $lrc;
