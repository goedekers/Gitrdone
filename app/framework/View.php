<?php

class View {
	public static function render($template, $vars = []) {
		$template = preg_replace('/\.phtml$/S', '', $template) . '.phtml';
		$filenameToRender = 'templates/' . $template;
		unset($template);
		if(file_exists($filenameToRender)) {
			if(isset($vars['filenameToRender'])) {
				unset($vars['filenameToRender']);

				$caller = debug_backtrace()[0];
				error_log('Warning: Invalid variable "filenameToRender" passed to view in ' . $caller['file'] . ' on line ' . $caller['line']);
				unset($caller);
			}

			extract($vars);
			include($filenameToRender);

			return true;
		}
		else {
			$caller = debug_backtrace()[0];
			error_log('Error: Template "' . $filenameToRender . '" not found in ' . $caller['file'] . ' on line ' . $caller['line']);

			return false;
		}
	}
}