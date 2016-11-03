<?php
require '../x/mysql.class.php';
require 'NeteaseMusic.class.php';
require 'moefou.class.php';
function writelog($str)
{
    $open = fopen('../data/fm_getxml_log.txt', 'a');
    fwrite($open, $str);
    fclose($open);
}

function get_xml($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    // curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'CLIENT-IP:123.125.68.' . mt_rand(0, 254),
        'X-FORWARDED-FOR:125.90.88.' . mt_rand(0, 254)
    ));
    if (!curl_exec($ch)) {
        $errno = curl_errno($ch);
        writelog('[' . date(time(), 'Y-m-d H:i:s') . ']: __ 抓取失败, 错误码: ' . $errno . ";\n\r");
        $data = false;
    } else {
        $data = curl_multi_getcontent($ch);
    }
    curl_close($ch);
    return $data;
}

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
    if ($_GET['rid'] == 11) {
        $url = 'http://www.xiami.com/radio/xml/type/4/id/6961722?v=' . time();
        $xml = get_xml($url);
        if ($xml) {
            $doc = new DOMDocument();
            $doc->loadXML($xml);
            $tracks = $doc->getElementsByTagName('track');
            foreach ($tracks as $track) {
                $song_id = $track->getElementsByTagName('song_id')->item(0)->nodeValue;
                $title = $track->getElementsByTagName('title')->item(0)->nodeValue;
                $pic = $track->getElementsByTagName('pic')->item(0)->nodeValue;
                $location = $track->getElementsByTagName('location')->item(0)->nodeValue;
                $album_name = $track->getElementsByTagName('album_name')->item(0)->nodeValue;
                $artist = $track->getElementsByTagName('artist')->item(0)->nodeValue;
                $album_id = $track->getElementsByTagName('album_id')->item(0)->nodeValue;
                $length = $track->getElementsByTagName('length')->item(0)->nodeValue;
                $r[] = array(
                    'xid' => (int)$song_id,
                    'title' => htmlspecialchars_decode($title, ENT_QUOTES),
                    'img' => str_replace('http://img.xiami.net/images/album/', '', $pic),
                    'mp3' => $location,
                    'album_name' => htmlspecialchars_decode($album_name, ENT_QUOTES),
                    'artist' => htmlspecialchars_decode($artist, ENT_QUOTES),
                    'album_id' => (int)$album_id,
                    'length' => (float)$length,
                    'play' => get_count($sql, $song_id)
                );
            }
        }
    } elseif ($_GET['rid'] == 0) {
        $r = $sql->getData('SELECT `sid` AS `xid`, `aid` AS `album_id`, `name` AS `title`, `album` AS `album_name`, `artists` AS `artist`,`play`,`img`,`mp3`,`duration` AS `length` FROM `imouto_music` ORDER BY RAND() LIMIT 5');
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
//    } elseif ($_GET['rid'] < 11) {
//        $r = json_decode(file_get_contents('http://itorr.sinaapp.com/fm/x/?a=radio&rid=' . $_GET['rid']));
    }
} elseif ($_GET['a'] == 'song') {
//    if ($_GET['s'] == 'xiami') {
//        $url = 'http://m.xiami.com/song/playlist/id/' . (int)$_GET['id'] . '?v=' . time();
//        $xml = get_xml($url);
//        $doc = new DOMDocument();
//        $doc->loadXML($xml);
//        $r[] = array(
//            'xid' => (int)$doc->getElementsByTagName('song_id')->item(0)->nodeValue,
//            'title' => htmlspecialchars_decode($doc->getElementsByTagName('title')->item(0)->nodeValue, ENT_QUOTES),
//            'img' => str_replace('http://img.xiami.net/images/album/', '', $doc->getElementsByTagName('album_pic')->item(0)->nodeValue),
//            'mp3' => $doc->getElementsByTagName('location')->item(0)->nodeValue,
//            'album_name' => htmlspecialchars_decode($doc->getElementsByTagName('album_name')->item(0)->nodeValue, ENT_QUOTES),
//            'artist' => htmlspecialchars_decode($doc->getElementsByTagName('artist')->item(0)->nodeValue, ENT_QUOTES),
//            'album_id' => (int)$doc->getElementsByTagName('album_id')->item(0)->nodeValue,
//            'length' => (float)$doc->getElementsByTagName('length')->item(0)->nodeValue,
//            'play' => get_count($sql, $doc->getElementsByTagName('song_id')->item(0)->nodeValue)
//        );
//    } elseif ($_GET['s'] == 'netease') {
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
//    }
} elseif ($_GET['a'] == 'lrc') {
    if ($_GET['s'] == 'xiami') {
        $lrc = file_get_contents('http://itorr.sinaapp.com/fm/x/?a=lrc&id=' . (int)$_GET['id']);
    } elseif ($_GET['s'] == 'netease') {
        $id = (int)$_GET['id'];
        $lrc_info = json_decode($music->get_music_lyric($id), true);
//        if(isset($lrc_info['lrc']['lyric'])){
//            echo $lrc_info['lrc']['lyric'];
//        }
        if (isset($lrc_info['lyric'])) {
            $lrc = $lrc_info['lyric'];
        }
    }
} elseif ($_GET['a'] == 'log') {
    if (!empty($_POST)) {
        $p = $_POST;
        if ($p['rid'] == 12)
            $sql->runSql("UPDATE `imouto_music` SET play=play+1 WHERE sid={$p['pid']}");
        $data = $sql->getLine("SELECT * FROM `imouto_playcount` WHERE pid={$p['pid']}");
        if ($data) {
            $modified = time();
            $sql->runSql("UPDATE `imouto_playcount` SET pcount=pcount+1 AND `modified`={$modified} WHERE pid={$p['pid']} AND rid={$p['rid']}");
            $r = array(
                'id' => $data['id'],
                'pid' => $p['pid'],
                'msg' => '更新记录成功!',
                'count' => ($data['pcount'] + 1)
            );
        } else {
            $time = time();
            $sql->runSql("INSERT INTO imouto_playcount (`pid`,`rid`,`pcount`,`created`,`modified`) VALUES ({$p['pid']},{$p['rid']},1,{$time},{$time})");
            $r = array(
                'id' => $sql->lastId(),
                'pid' => $p['pid'],
                'msg' => '添加记录成功!',
                'count' => 1
            );
        }
//        if ($rid == 12) {
//            $r = $MoeFM->set_log($_SESSION['moefou']['oauth_token'], $_SESSION['moefou']['oauth_token_secret'], $pid);
//            if ($r['response']['status']) {
//                unset($r['response']['information']);
//            }
//        }
    }
}
$_GET['a'] != 'lrc' ? header('Content-type: application/json;charset=utf-8') : header('Content-Type: text/plain;charset=utf-8');
echo $_GET['a'] != 'lrc' ? json_encode($r) : $lrc;
