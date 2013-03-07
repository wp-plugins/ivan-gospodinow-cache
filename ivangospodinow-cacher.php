<?php
require_once 'classes/ig_class_base.php';
require_once 'classes/ig_class_cache.php';
require_once 'config.php';
function ig_cache_init(){
	$identifier = sha1($_SERVER['REQUEST_URI']);
	$options = ig_cache_get_options();
	//if plugin is on
	if ($options['ig_cache_on_off'] && ig_cache_can_cache()) {
		$data = ig::get('cache') -> get($identifier, $options['ig_cache_time'] * 60);
		if (!$data) {
			$_SERVER['ig_cacher_start_time'] = microtime(true);
			ob_start();
			register_shutdown_function('ig_cache_shutdown');
		} else {
			$options['times_used_cache']++;
			$options['script_runs']++;
			ig_cache_set_options($options);
			echo $data;
			exit ;
		}
	
	} 
	//if is cached will not go here.
	$options['script_runs']++;
	ig_cache_set_options($options);
}
ig_cache_init(); 

function ig_cache_shutdown() {
	$errors = array();
	if (function_exists('error_get_last')) {
		$errors = error_get_last();
	}
	$options = ig_cache_get_options();
	$time = round( microtime(true) - $_SERVER['ig_cacher_start_time'],4);
	$options['average_execution_time'] = round(($time + $options['average_execution_time']) / 2,4);
	ig_cache_set_options($options);
	//var_dump($options);
	if($options['ig_cache_prevent_on_errors']){
		if(empty($errors)){
			$identifier = sha1($_SERVER['REQUEST_URI']);
			ig::get('cache') -> set($identifier, ob_get_contents());
		} else {
			//DO NOTHING
		}
	} else {
		$identifier = sha1($_SERVER['REQUEST_URI']);
		ig::get('cache') -> set($identifier, ob_get_contents());
	}

}
?>