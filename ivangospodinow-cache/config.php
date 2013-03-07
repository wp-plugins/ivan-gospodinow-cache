<?php

function ig_cache_get_options(){
	$cache = new ig_class_cache(realpath(dirname(__FILE__)),'.ig');
	$config = $cache->get('config');
	if(!$config){
		ig_cache_set_options(array(),true);
	}
	return $config;
}

function ig_cache_set_options($config,$setDefault = false){
	$default = array(
		'ig_cache_time'=>60,
		'ig_cache_on_off'=>0,
		'average_execution_time'=>0,
		'times_used_cache'=>0,
		'script_runs'=>0,
		'ig_cache_prevent_on_errors'=>0,
		'last_cache_clear'=>0,
		'admins_last_login' => array()
	);
	
	if($setDefault){
		$config = $default;
	} else {
		//if options is not saved
		foreach($default as $k=>$v){
			if(!isset($config[$k])){
				$config[$k] = $v;
			}
		}	
	}
	
	
	$cache = new ig_class_cache(realpath(dirname(__FILE__)),'.ig');
	$cache->set('config',$config);
	
	$include = realpath(dirname(__FILE__)).'/ivangospodinow-cacher.php';
	$index = realpath(dirname(__FILE__).'/../../../').'/index.php';
	$lines = file($index);
	//var_dump($lines);
	if($config['ig_cache_on_off']){
		$hasFile = false;
		foreach($lines as $line){
			if(strpos($line, 'ivangospodinow-cacher.php') !== false){
				$hasFile = true;
			}
		}
		$newLines = array();
		if(!$hasFile){
			$newLines[] = '<?php';
			$newLines[] = "\n require_once('$include'); \n";
			$isPhpRemoved = false;
			foreach($lines as $line){
				if(strpos($line, '<?') === false || strpos($line, '<?php')  === false && !$isPhpRemoved){
					$newLines[] = $line;
				} else {
					$isPhpRemoved = true;
				}
			}
			file_put_contents($index, implode("",$newLines));
		}
		
		
	} else {
		foreach($lines as $i=>$line){
			if(strpos($line, 'ivangospodinow-cacher.php') !== false){
				unset($lines[$i]);
			}
		}
		file_put_contents($index, implode("",$lines));
		ig::get('cache')->clear();
	} 
	
	
	return $config;
}
function ig_cache_can_cache(){
	if(is_array($_POST) && !empty($_POST)){
		return false;
	}
	if(is_array($_GET) && count($_GET) > 2){
		return false;
	}
	
	$options = ig_cache_get_options();
	$canCache = true;
	if(is_array($options['admins_last_login'])){
		foreach($options['admins_last_login'] as $userId=>$time){
			if(($time + 1) > time()){
				$canCache = false;
				break;
			}
		}
	}

	return $canCache;
}
?>