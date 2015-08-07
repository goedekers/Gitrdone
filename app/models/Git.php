<?php

class Git {
	protected $_commits = [];
	protected $_branch = '';
	protected $_repoUrl = '';
	protected $_repoDir = '';
	protected $_baseCmd = '';

	public function __construct() {
		$config = App::get('config')->git;
		$this->_repoDir = (string)$config->repoDir;
		$this->_baseCmd = 'git -C ' . $this->_repoDir . ' --no-pager ';
	}

	public function loadCommits() {
		if($this->_commits)
			return $this->_commits;

		$cmd = $this->_baseCmd . 'log';
		$result = shell_exec($cmd);
		$this->_commits = $this->_processCommits($result);

		return $this->_commits;
	}

	public function getContributors() {
		$this->loadCommits();
		
		$contributors = [];
		foreach($this->_commits as $commit) {
			$contributors[] = $commit['author'];
		}

		return array_count_values($contributors);
	}

	public function getCurrentBranch() {
		if($this->_branch)
			return $this->_branch;

		$cmd = $this->_baseCmd . 'branch';
		$result = shell_exec($cmd);
		preg_match('/\* (.*?)\v/S', $result, $matches);
		$this->_branch = $matches[1];

		return $this->_branch;
	}

	public function getRepoUrl() {
		if($this->_repoUrl)
			return $this->_repoUrl;

		$cmd = $this->_baseCmd . 'config --get remote.origin.url';
		$result = shell_exec($cmd);
		$url = trim($result);
		$url = preg_replace('/\/\/[^\/]*@/S', '//', $url);
		$url = preg_replace('/\.git$/S', '', $url);
		$this->_repoUrl = $url;

		return $this->_repoUrl;
	}

	protected function _processCommits($log) {
		$rawCommits = preg_split('/\v+(?=commit)/S', $log);
		$commits = [];
		foreach($rawCommits as $rawCommit) {
			$commits[] = $this->_processCommit($rawCommit);
		}

		return $commits;
	}

	protected function _processCommit($rawCommit) {
		$commit = [];
		
		preg_match('/^commit (.*?)\v/S', $rawCommit, $matches);
		$commit['hash'] = isset($matches[1]) ? $matches[1] : '';

		preg_match('/\vMerge: (.*?)\v/S', $rawCommit, $matches);
		$commit['merge'] = isset($matches[1]) ? $matches[1] : '';

		preg_match('/\vAuthor: (.*?)\v/S', $rawCommit, $matches);
		$author = isset($matches[1]) ? $matches[1] : '';
		$author = preg_replace('/ <.*/S', '', $author);
		$commit['author'] = $author;

		preg_match('/\vDate: (.*?) -?\d+\v/S', $rawCommit, $matches);
		$commit['date'] = isset($matches[1]) ? trim($matches[1]) : '';

		$msg = end(explode("\n\n", $rawCommit, 2));
		$msg = preg_replace('/(^|\n)    /S', "\n", $msg);
		$commit['msg'] = trim($msg);

		$commit['url'] = $this->getRepoUrl() . '/commit/' . $commit['hash'];

		return $commit;
	}
}