<?php
/*
 Plugin Name: Ivan Gospodinow Cache
 Plugin URI:
 Description:
 Author: Ivan Gospodinow
 Version: 1.1
 Author URI: http://www.ivangospodinow.com
 */
require_once 'classes/ig_class_base.php';
require_once 'classes/ig_class_cache.php';
require_once 'config.php';


if (is_admin()) {
	//adding last login time
	add_action( 'init', 'ig_cache_admin_last_login' );
	function ig_cache_admin_last_login(){
		$user = wp_get_current_user();
		$options = ig_cache_get_options();
		$options['admins_last_login'][$user->ID] = time(); 
		$options['last_cache_clear'] = time(); 
		ig_cache_set_options($options);
		//clears cache
		ig::get('cache')->clear();
	} 
	 
	add_action('admin_menu','ig_cache_admin_menu'); 
	function ig_cache_admin_menu(){
		add_options_page(__('Ivan Gospodinow Cache'),__('ig cache'),'manage_options','ig_cache','ig_cache_admin_content');
	}
	function ig_cache_admin_content(){
		$options = ig_cache_get_options();

		if(isset($_POST['ig_cache_admin_content_config_submit'])){
			$options['ig_cache_on_off'] = isset($_POST['ig_cache_on_off']) ? 1 : 0;
			$options['ig_cache_prevent_on_errors'] = isset($_POST['ig_cache_prevent_on_errors']) ? 1 : 0;
			$options['ig_cache_time'] = (int)$_POST['ig_cache_time'] < 1 ? 1 :(int) $_POST['ig_cache_time'];
			
			$options = ig_cache_set_options($options);
		}
		$info = ig::get('cache')->cacheInfo();

		?>
		<div id="ig_cache_admin_content" style="width: 100%;">
			<h3>Wellcome to Ivan Gospodinow`s cache plugin.The fastest cache plugin for wordpress.</h3>
			<h4>For questions or information go to <a href="http://www.ivangospodinow.com/?p=149">Official site : IvanGospodinow.com</a></h4>
			<form style="border: 1px dashed #c3c3c3;padding: 5px;" method="post">
				<h4 style="margin: 0;">Configuration<?=!$options['ig_cache_on_off'] ? '&nbsp;<span style="color:red;">Plugin is turned off.</span>' : '&nbsp;<span style="color:green;">Plugin is turned on.</span>'?></h4>
				<div style="font-style: italic;">
					Server time saved <?=ig_cache_secondsToTime(round($options['average_execution_time'] * $options['times_used_cache']));?> on last <?=number_format($options['times_used_cache'],0,'.',' ');?> runs. Total script runs : <?=number_format($options['script_runs'],0,'.',' ');?>.
				</div>
				<div style="font-style: italic;">
					Last cache clear at <?=date('d.m.Y H:i:s',$options['last_cache_clear']);?>
				</div>
				
				<br/>
				<label for="ig_cache_time" style="margin-bottom: 5px;">
					Cache time in minutes
					<input type="text" name="ig_cache_time" style="display: block;" value="<?=$options['ig_cache_time'];?>"/>
				</label>
				<label for="ig_cache_on_off" style="display:block;margin-bottom: 5px;">
					Prevent cache on errors
					<input type="checkbox" name="ig_cache_prevent_on_errors" value="1" <?=$options['ig_cache_prevent_on_errors'] ? 'checked="checked"': '';?>/>
				</label>
				<label for="ig_cache_on_off" style="display:block;margin-bottom: 5px;">
					Turn on/off cache
					<input type="checkbox" name="ig_cache_on_off" value="1" <?=$options['ig_cache_on_off'] ? 'checked="checked"': '';?>/>
				</label>
				<input type="submit" name="ig_cache_admin_content_config_submit" value="Submit" />
			</form>
			<div id="ig_cache_admin_content_index_file">
				<h4>index.php</h4>
				<pre style="border: 1px dashed #c3c3c3;margin: 0;padding: 5px;">
					<?php
						$index = realpath(dirname(__FILE__).'/../../../').'/index.php';
						$lines = file($index);
						echo htmlspecialchars(trim(implode('',$lines)));
					?>
				</pre>
			</div>
		</div>
		
		<?
	}  
}

function ig_cache_secondsToTime($seconds)
{
    // extract hours
    $days = floor($seconds / (60 * 60 * 24));
	
    $hours = floor($seconds % (60 * 60 * 24) / (60 * 60));
 
    // extract minutes
    $divisor_for_minutes = $seconds % (60 * 60);
    $minutes = floor($divisor_for_minutes / 60);
 
    // extract the remaining seconds
    $divisor_for_seconds = $divisor_for_minutes % 60;
    $seconds = ceil($divisor_for_seconds);
 
    // return the final array
  
    $hours = (int) $hours;
    $minutes = (int) $minutes;
    $seconds = (int) $seconds;
	
	if($days && $hours && $minutes && $seconds){
		return $days.'d '.$hours.'h '.$minutes.'m '.$seconds.'s';
	}

	if($hours && $minutes && $seconds){
		return $hours.'h '.$minutes.'m '.$seconds.'s';
	}
	
	if($minutes && $seconds){
		return $minutes.'m '.$seconds.'s';
	}
	return $seconds.'s';
}