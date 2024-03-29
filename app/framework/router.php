<?php

class Router {
	public static function route() {
		$baseUri = preg_replace('/\/$/', '', App::get('config')->baseUri) . '/';
		$path = strlen($_SERVER['REQUEST_URI']) > strlen($baseUri) ? substr($_SERVER['REQUEST_URI'], strlen($baseUri)) : 'index/index';
		$path = preg_replace('/\?.*/S', '', $path);
		$path = preg_replace('/[^a-z0-9\-_\/\.]/Si', '', str_replace("\0", '', urldecode($path)));		//sanitize the path
		$path = explode('/', $path);
		if(sizeof($path) == 1)
			$path[] = 'index';
		$path[0] = str_replace('.', '', $path[0]);
		if(isset($path[1]))
			$path[1] = str_replace('.', '', $path[1]);
		App::set('route', $path);

		$valid = true;
		$className = ucfirst($path[0]) . 'Controller';
		if(class_exists($className)) {
			$methodName = $path[1] . 'Action';
			if(!method_exists($className, $methodName))
				$valid = false;
		}
		else
			$valid = false;
		
		if($valid) {
			App::set('route', $path);

			$controller = new $className();
			$controller->$methodName();
		}
		else
			echo 'Invalid route';
	}
}