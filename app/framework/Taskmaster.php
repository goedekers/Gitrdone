<?php

class Taskmaster {
	public static function isRunning() {
		return (bool)App::cacheGet(App::get('config')->appId . '_running');
	}

	public static function setRunning($running) {
		if($running) {
			App::cacheSet(App::get('config')->appId . '_startTime', time());
		}
		App::cacheSet(App::get('config')->appId . '_running', (bool)$running);
	}

	public static function isQueued() {
		return (bool)App::cacheGet(App::get('config')->appId . '_queued');
	}

	public static function queue() {
		$semId = self::lock();
		if(!self::isRunning()) {
			App::cacheSet(App::get('config')->appId . '_queued', true);
		}
		self::unlock($semId);
	}

	public static function unQueue() {
		App::cacheSet(App::get('config')->appId . '_queued', false);
	}

	public static function getProgress() {
		$progress = App::cacheGet(App::get('config')->appId . '_progress');
		$progress = $progress ? $progress : array(0, 0);

		return $progress;
	}

	public static function updateProgress($current, $max = false) {
		$semId = self::lock();
		$progress = self::getProgress();
		$progress[0] = $current;
		if($max !== false)
			$progress[1] = $max;
		App::cacheSet(App::get('config')->appId . '_progress', $progress);
		self::unlock($semId);
	}

	public static function stop() {
		self::unQueue();
		self::setRunning(false);
	}

	public static function lock() {
		$semKey = ftok(__FILE__, 'F');
		$semId = sem_get($semKey);
		sem_acquire($semId);

		return $semId;
	}

	public static function unlock($semId) {
		sem_release($semId);
	}
}