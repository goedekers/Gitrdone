<?php

class App {
	public function __construct($configName) {
		global $registry;
		$registry = array();
		$config = simplexml_load_file(APP_DIR . '/' . $configName);
		date_default_timezone_set((string)$config->timezone);
		self::set('config', $config);

		self::load(FRAMEWORK_DIR);
		self::load(APP_DIR);

		self::set('logger', new Lumberjack((string)$config->log));
		
		//only route web requests
		if(php_sapi_name() != 'cli')
			Router::route();
	}

	public static function load($path = '') {
		$path = $path ? $path : __DIR__;
		$path = preg_replace('/\/$/', '', $path);

		$dir = scandir($path);
		unset($dir[0], $dir[1]);

		foreach($dir as $file) {
			$filename = $path . '/' . $file;
			if(is_file($filename)) {
				if(preg_match('/\.php$/S', $filename))
					require_once($filename);
			}
			else
				self::load($filename);
		}
	}

	public static function get($varName) {
		global $registry;

		return isset($registry[$varName]) ? $registry[$varName] : null;
	}

	public static function set($varName, $val) {
		global $registry;

		$registry[$varName] = $val;
	}

	public static function redirect($url) {
		if(!preg_match('/^https?:/S', $url)) {
			$url = self::get('config')->baseUri . preg_replace('/^\//S', '', $url);
		}

		header('location: ' . $url);
		exit;
	}

	public static function initMemcached() {
		$config = self::get('config');
		self::set('memcachedSalt', (string)$config->appId);

		$m = new Memcached();
		$m->addServer((string)$config->memcached->host, (int)$config->memcached->port);
		self::set('memcached', $m);
	}

	public static function closeMemcached() {
		if(gettype(self::get('memcached')) === 'object')
			self::get('memcached')->quit();
		self::set('memcached', false);
	}

	public static function cacheGet($key) {
		if(!self::get('memcached'))
			self::initMemcached();
		$key = self::saltMemcacheKey($key);

		return self::get('memcached')->get($key);
	}

	public static function cacheSet($key, $val) {
		if(!self::get('memcached'))
			self::initMemcached();
		$key = self::saltMemcacheKey($key);

		self::get('memcached')->set($key, $val);
	}

	public static function log($msg) {
		self::get('logger')->log($msg);
	}

	public static function filename2Url($filename) {
		$url = '';
		if($filename) {
			$url = substr($filename, strlen(BASE_DIR));
		}

		return $url;
	}

	protected static function saltMemcacheKey($key) {
		return self::get('memcachedSalt') . '-' . $key;
	}

	public function __destruct() {
		if(is_object(self::get('memcached')))
			self::get('memcached')->quit();
	}
}