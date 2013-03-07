<?php
if (!class_exists('ig')) {
	$___ig = array();
	class ig {
		const classPrefix = 'ig_class_';

		static function get($str = '') {
			global $___ig;
			if (isset($___ig[$str]) && is_object($___ig[$str])) {
				return $___ig[$str];
			} else {
				//trying to create one
				$class = self::classPrefix . $str;
				if (class_exists($class)) {
					$___ig[$str] = new $class();
					return $___ig[$str];
				} else {
					return null;
				}

			}

		}

		static function set($class, $value) {

			$___ig[$class] = $value;
		}

	}

}
