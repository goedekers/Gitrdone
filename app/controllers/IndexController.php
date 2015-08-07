<?php

class IndexController extends BaseController {
	public function indexAction() {
		View::render('master', ['innerTemplate' => 'index/index']);
	}
}