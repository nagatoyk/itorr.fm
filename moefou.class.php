<?php
/**
 * 萌否电台
 */
class MoefouOAuth{
	const VERSION = '0.1.1';
	public $consumer_key;
	public $consumer_secret;
	public $callback;
	public $oauth_token;
	public $oauth_token_secret;
	public $http_code;
	public $url;
	public $host = 'http://api.moefou.org/';
	public $timeout = 30;
	public $connecttimeout = 30;
	public $ssl_verifypeer = false;
	public $format = 'json';
	public $decode_json = true;
	public $http_info;
	public $useragent = 'MoeFM T OAuth1 v0.1';
	public $debug = false;
	public static $boundary = '';

	public function __construct($consumer_key, $consumer_secret, $callback){
		$this->consumer_key = $consumer_key;
		$this->consumer_secret = $consumer_secret;
		$this->callback = $callback;
	}

	public function accessTokenURL(){
		return 'http://api.moefou.org/oauth/access_token';
	}

	public function authorizeURL(){
		return 'http://api.moefou.org/oauth/authorize';
	}

	public function requestTokenURL(){
		return 'http://api.moefou.org/oauth/request_token';
	}

	public function getRequestToken(){
		$params = array();
		$params['oauth_version'] = '1.0';
		$params['oauth_signature_method'] = 'HMAC-SHA1';
		$params['oauth_timestamp'] = time();
		$params['oauth_nonce'] = mt_rand();
		$params['oauth_consumer_key'] = $this->consumer_key;
		$params['oauth_signature'] = rawurlencode($this->get_signature('GET&'.rawurlencode($this->requestTokenURL()).'&'.rawurlencode($this->get_normalized_string($params)), $this->consumer_secret.'&'));
		return $this->oAuthRequest($this->requestTokenURL(), 'GET', $params);
	}
	public function getAuthorizeURL($location = false){
		$params = array();
		$params['oauth_consumer_key'] = $this->consumer_key;
		parse_str($this->getRequestToken());
		$_SESSION['moefou']['request_token'] = $oauth_token;
		$_SESSION['moefou']['request_token_secret'] = $oauth_token_secret;
		$params['oauth_token'] = $oauth_token;
		$params['oauth_callback'] = $this->callback;
		$link = $this->authorizeURL().'?'.http_build_query($params);
		if($location){
			header('Location:'.$link);
			exit();
		}else{
			return $link;
		}
	}

	public function getAccessToken($request_token, $request_token_secret, $vericode){
		$params = array();
		$params['oauth_verifier'] = $vericode;
		$params['oauth_version'] = '1.0';
		$params['oauth_signature_method'] = 'HMAC-SHA1';
		$params['oauth_timestamp'] = time();
		$params['oauth_nonce'] = mt_rand();
		$params['oauth_consumer_key'] = $this->consumer_key;
		$params['oauth_token'] = $request_token;
		$params['oauth_signature'] = rawurlencode($this->get_signature('GET&'.rawurlencode($this->accessTokenURL()).'&'.rawurlencode($this->get_normalized_string($params)), $this->consumer_secret.'&'.$request_token_secret));
		return $this->oAuthRequest($this->accessTokenURL(), 'GET', $params);
	}

	public function get_normalized_string($params){
		ksort($params);
		$normalized = array();
		foreach($params as $key => $val){
			$normalized[] = $key.'='.$val;
		}
		return implode('&', $normalized);
	}

	public function get_signature($str, $key){
		$signature = '';
		if(function_exists('hash_hmac')){
			$signature = base64_encode(hash_hmac('sha1', $str, $key, true));
		}else{
			$blocksize = 64;
			$hashfunc = 'sha1';
			if(strlen($key) > $blocksize){
				$key = pack('H*', $hashfunc($key));
			}
			$key = str_pad($key, $blocksize, chr(0x00));
			$ipad = str_repeat(chr(0x36), $blocksize);
			$opad = str_repeat(chr(0x5c), $blocksize);
			$hmac = pack('H*', $hashfunc(($key^$opad).pack('H*', $hashfunc(($key^$ipad).$str))));
			$signature = base64_encode($hmac);
		}
		return $signature;
	}

	public function get_urlencode_string($params){
		ksort($params);
		$normalized = array();
		foreach($params as $key => $val){
			$normalized[] = $key.'='.rawurlencode($val);
		}
		return implode('&', $normalized);
	}

	public function get($url, $parameters = array()){
		$response = $this->oAuthRequest($url, 'GET', $parameters);
		if($this->format === 'json' && $this->decode_json){
			return json_decode($response, true);
		}
		return $response;
	}

	public function post($url, $parameters = array(), $multi = false){
		$response = $this->oAuthRequest($url, 'POST', $parameters, $multi);
		if($this->format === 'json' && $this->decode_json){
			return json_decode($response, true);
		}
		return $response;
	}

	public function delete($url, $parameters = array()){
		$response = $this->oAuthRequest($url, 'DELETE', $parameters);
		if($this->format === 'json' && $this->decode_json){
			return json_decode($response, true);
		}
		return $response;
	}

	public function oAuthRequest($url, $method, $parameters, $multi = false){
		if(strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0){
			$url = $this->host.$url.'.'.$this->format;
		}
		switch($method){
			case 'GET':
				$url = $url.'?'.http_build_query($parameters);
				return $this->http($url, 'GET');
			default:
				$headers = array();
				if(!$multi && (is_array($parameters) || is_object($parameters))){
					$body = http_build_query($parameters);
				}else{
					$body = self::build_http_query_multi($parameters);
					$headers[] = 'Content-Type: multipart/form-data; boundary='.self::$boundary;
				}
				return $this->http($url, $method, $body, $headers);
		}
	}

	public function http($url, $method, $postfields = NULL, $headers = array()){
		$this->http_info = array();
		$ci = curl_init();
		curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ci, CURLOPT_ENCODING, '');
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
		if(version_compare(phpversion(), '5.4.0', '<')){
			curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 1);
		}else{
			curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 2);
		}
		curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
		curl_setopt($ci, CURLOPT_HEADER, false);
		switch($method){
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, true);
				if(!empty($postfields)){
					curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
					$this->postdata = $postfields;
				}
				break;
			case 'DELETE':
				curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
				if(!empty($postfields)){
					$url = $url.'?'.$postfields;
				}
		}
		if(isset($this->access_token) && $this->access_token){
			$headers[] = 'Authorization: OAuth1 '.$this->access_token;
		}
		if(!empty($this->remote_ip)){
			if(defined('SAE_ACCESSKEY')){
				$headers[] = 'SaeRemoteIP: '.$this->remote_ip;
			}else{
				$headers[] = 'API-RemoteIP: '.$this->remote_ip;
			}
		}else{
			if(!defined('SAE_ACCESSKEY')){
				$headers[] = 'API-RemoteIP: '.$_SERVER['REMOTE_ADDR'];
			}
		}
		curl_setopt($ci, CURLOPT_URL, $url);
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ci, CURLINFO_HEADER_OUT, true);
		$response = curl_exec($ci);
		$this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
		$this->http_info = array_merge($this->http_info, curl_getinfo($ci));
		$this->url = $url;
		if($this->debug){
			echo '=====post data======'."\r\n";
			var_dump($postfields);

			echo '=====headers======'."\r\n";
			print_r($headers);

			echo '=====request info====='."\r\n";
			print_r(curl_getinfo($ci));

			echo '=====response====='."\r\n";
			print_r($response);
		}
		curl_close($ci);
		return $response;
	}

	public function getHeader($ch, $header){
		$i = strpos($header, ':');
		if(!empty($i)){
			$key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
			$value = trim(substr($header, $i + 2));
			$this->http_header[$key] = $value;
		}
		return strlen($header);
	}

	public static function build_http_query_multi($params){
		if(!$params)return '';
		uksort($params, 'strcmp');
		$pairs = array();
		self::$boundary = $boundary = uniqid('------------------');
		$MPboundary = '--'.$boundary;
		$endMPboundary = $MPboundary.'--';
		$multipartbody = '';
		foreach($params as $parameter => $value){
			if(in_array($parameter, array('pic', 'image')) && $value{0} == '@'){
				$url = ltrim($value, '@');
				$content = file_get_contents($url);
				$array = explode( '?', basename($url));
				$filename = $array[0];
				$multipartbody .= $MPboundary."\r\n";
				$multipartbody .= 'Content-Disposition: form-data; name="'.$parameter.'"; filename="'.$filename.'"'."\r\n";
				$multipartbody .= 'Content-Type: image/unknown'."\r\n\r\n";
				$multipartbody .= $content."\r\n";
			}else{
				$multipartbody .= $MPboundary."\r\n";
				$multipartbody .= 'content-disposition: form-data; name="'.$parameter."\"\r\n\r\n";
				$multipartbody .= $value."\r\n";
			}
		}
		$multipartbody .= $endMPboundary;
		return $multipartbody;
	}
}
class MoeFM{
	const VERSION = '0.1.1';
	public $oauth;
	public function __construct($consumer_key, $consumer_secret, $callback){
		$this->oauth = new MoefouOAuth($consumer_key, $consumer_secret, $callback);
	}
	public function get_user_info($access_token, $access_token_secret){
		//获取用户信息的接口地址, 不要更改!!
		$url = 'http://api.moefou.org/user/detail.json';
		$params = array();
		$params['oauth_version'] = '1.0';
		$params['oauth_signature_method'] = 'HMAC-SHA1';
		$params['oauth_timestamp'] = time();
		$params['oauth_nonce'] = mt_rand();
		$params['oauth_consumer_key'] = $this->oauth->consumer_key;
		$params['oauth_token'] = $access_token;
		$params['oauth_signature'] = rawurlencode($this->oauth->get_signature('GET&'.rawurlencode($url).'&'.rawurlencode($this->oauth->get_normalized_string($params)), $this->oauth->consumer_secret.'&'.$access_token_secret));
		return $this->oauth->get($url, $params);
	}
	// 添加收藏或删除收藏
	public function add_like_fav($access_token, $access_token_secret, $fav_obj_id){
		$url = 'http://api.moefou.org/fav/add.json';
		$params = array();
		$params['oauth_version'] = '1.0';
		$params['oauth_signature_method'] = 'HMAC-SHA1';
		$params['oauth_timestamp'] = time();
		$params['oauth_nonce'] = mt_rand();
		$params['oauth_consumer_key'] = $this->oauth->consumer_key;
		$params['oauth_token'] = $access_token;
		$params['oauth_signature'] = rawurlencode($this->oauth->get_signature('GET&'.rawurlencode($url).'&'.rawurlencode($this->oauth->get_normalized_string($params)), $this->oauth->consumer_secret.'&'.$access_token_secret));
		$params['fav_type'] = 1;
		$params['fav_obj_type'] = 'song';
		$params['fav_obj_id'] = $fav_obj_id;
		return $this->oauth->get($url, $params);
	}
	// 收听接口
	public function listen($access_token, $access_token_secret, $parameters = array()){
		$url = 'http://moe.fm/listen/playlist';
		$params = array();
		$params['api'] = 'json';
		if(!$access_token && !$access_token_secret){
			$params['api_key'] = $this->oauth->consumer_key;
			return $this->oauth->get($url, array_merge($params, $parameters));
		}else{
			$params = array();
			$params['oauth_version'] = '1.0';
			$params['oauth_signature_method'] = 'HMAC-SHA1';
			$params['oauth_timestamp'] = time();
			$params['oauth_nonce'] = mt_rand();
			$params['oauth_consumer_key'] = $this->oauth->consumer_key;
			$params['oauth_token'] = $access_token;
			$params['oauth_signature'] = rawurlencode($this->oauth->get_signature('GET&'.rawurlencode($url).'&'.rawurlencode($this->oauth->get_normalized_string($params)), $this->oauth->consumer_secret.'&'.$access_token_secret));
			return $this->oauth->get($url, array_merge($params, $parameters));
		}
	}
	// 听歌记录
	public function set_log($access_token, $access_token_secret, $obj_id){
		$url = 'http://moe.fm/ajax/log';
		$params = array();
		$params['oauth_version'] = '1.0';
		$params['oauth_signature_method'] = 'HMAC-SHA1';
		$params['oauth_timestamp'] = time();
		$params['oauth_nonce'] = mt_rand();
		$params['oauth_consumer_key'] = $this->oauth->consumer_key;
		$params['oauth_token'] = $access_token;
		$params['oauth_signature'] = rawurlencode($this->oauth->get_signature('GET&'.rawurlencode($url).'&'.rawurlencode($this->oauth->get_normalized_string($params)), $this->oauth->consumer_secret.'&'.$access_token_secret));
		$params['log_obj_type'] = 'sub';
		$params['log_type'] = 'listen';
		$params['obj_type'] = 'song';
		$params['api'] = 'json';
		$params['obj_id'] = $obj_id;
		return $this->oauth->get($url, $params);
	}
	// 收藏的专辑
	public function music_favs($access_token, $access_token_secret){
		$url = 'http://api.moefou.org/user/favs/wiki.json';
		$params = array();
		$params['oauth_version'] = '1.0';
		$params['oauth_signature_method'] = 'HMAC-SHA1';
		$params['oauth_timestamp'] = time();
		$params['oauth_nonce'] = mt_rand();
		$params['oauth_consumer_key'] = $this->oauth->consumer_key;
		$params['oauth_token'] = $access_token;
		$params['oauth_signature'] = rawurlencode($this->oauth->get_signature('GET&'.rawurlencode($url).'&'.rawurlencode($this->oauth->get_normalized_string($params)), $this->oauth->consumer_secret.'&'.$access_token_secret));
		return $this->oauth->get($url, $params);
	}
}
session_start();
$Moe = new MoefouOAuth('18f95c02504fb5a0fdd83b205e7e1aee05421a58b', 'a3af2e9f06faaefb9408897388f0f916', 'http://kloli.tk/fm/login.php');
$MoeFM = new MoeFM($Moe->consumer_key, $Moe->consumer_secret, $Moe->callback);