<?php
/**
 * Class NeteaseMusic
 * Url: http://moonlib.com/606.html
 */
class NeteaseMusic{
    /**
     *
     */
    const refer = 'http://music.163.com/';

	/*public function __construct(argument){
		# code...
	}*/
    /**
     * 获取方法
     * @param $url
     * @return mixed
     */
	private function http($url){
        $header[] = 'Cookie: appver=1.5.0.75771;';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_REFERER, self::refer);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * 获取歌单
     * @param $playlist_id
     * @return mixed
     */
    public function get_playlist_info($playlist_id){
        $url = 'http://music.163.com/api/playlist/detail?id='.(int)$playlist_id;
        return self::http($url);
    }

    /**
     * 获取单曲信息
     * @param $music_id
     * @return mixed
     */
    public function get_music_info($music_id){
        $url = 'http://music.163.com/api/song/detail/?id='.(int)$music_id.'&ids=%5B'.(int)$music_id.'%5D';
        return self::http($url);
    }

    /**
     * 获取音乐歌词
     * @param $music_id
     * @return mixed
     */
    public function get_music_lyric($music_id){
//        $url = 'http://music.163.com/api/song/lyric?os=pc&id='.(int)$music_id.'&lv=-1&kv=-1&tv=-1';
        $url = 'http://music.163.com/api/song/media?id='.(int)$music_id;
        return self::http($url);
    }

    /**
     * 获取歌手专辑列表
     * @param $artist_id
     * @param $limit
     * @return mixed
     */
    public function get_artist_album($artist_id, $limit){
        $url = 'http://music.163.com/api/artist/albums/'.(int)$artist_id.'?limit='.(int)$limit;
        return self::http($url);
    }

    /**
     * 获取专辑信息
     * @param $album_id
     * @return mixed
     */
    public function get_album_info($album_id){
        $url = 'http://music.163.com/api/album/'.(int)$album_id;
        return self::http($url);
    }

    /**
     * 获取MV信息
     * @param $mvid
     * @param string $type
     * @return mixed
     */
    public function get_mv_info($mvid, $type = 'mp4'){
        $url = 'http://music.163.com/api/mv/detail?id='.(int)$mvid.'&type='.$type;
        return self::http($url);
    }

    /**
     * 歌手列表转换成字符串
     * @param $artists
     * @return string
     */
    public function artists($artists){
        $name = '';
        foreach($artists as $k=>$v){
            $name .= ','.$v['name'];
        }
        return ltrim($name, ',');
    }
}
$music = new NeteaseMusic();