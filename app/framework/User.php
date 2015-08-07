<?php

class User {
	protected $_users = array();
	
	protected function _loadUsers() {
		$userFile = DATA_DIR . '/users.xml';
		if(!$this->_users && file_exists($userFile)) {
			$this->_users = simplexml_load_file($userFile);
		}
	}

	public function logIn($username, $pass) {
		if(!$this->validateLogin($username, $pass))
			return false;

		$user = $this->getUser($username);
		$credentials = array('username' => (string)$user->name);
		$_SESSION['credentials'] = $credentials;

		return true;
	}

	public function logOut() {
		unset($_SESSION['credentials']);
	}

	public static function isLoggedIn() {
		return isset($_SESSION['credentials']) && $_SESSION['credentials'];
	}

	public function validateLogin($username, $pass) {
		$user = $this->getUser($username);
		if(!$user)
			return false;

		list($salt, $hash) = explode(':', (string)$user->pass);

		return self::hash($pass, $salt) == $hash;
	}

	public function getUser($username) {
		$this->_loadUsers();
		foreach($this->_users as $user) {
			if((string)$user->name == $username)
				return $user;
		}

		return false;
	}

	//could be improved by using something better than sha, but other 
	//algorithms might not be available in all environments
	public static function hash($pass, $salt) {
		return sha1($pass . $salt);
	}

	//this could be made more secure by using a truly random source
	public static function generateSalt($len = 8) {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

		$salt = '';
		for($i = 0; $i != $len; ++$i)
			$salt .= $chars[mt_rand(0, strlen($chars) - 1)];

		return $salt;
	}
}