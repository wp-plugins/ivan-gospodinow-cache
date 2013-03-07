<?php
// this is the uninstall handler
// include unregister_setting, delete_option, and other uninstall behavior here
require_once 'classes/ig_class_base.php';
require_once 'classes/ig_class_cache.php';
require_once 'config.php';


$options = ig_cache_get_options();
$options['ig_cache_on_off'] = 0;
ig_cache_set_options($options);


?>