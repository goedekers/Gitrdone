<?php

class ApiController extends BaseController {
	public function branchAction() {
		$git = new Git();
		echo $git->getCurrentBranch();
	}

	public function contributorsAction() {
		$git = new Git();
		echo json_encode($git->getContributors());
	}

	public function commitsAction() {
		$git = new Git();
		echo json_encode($git->loadCommits());
	}
}