<?php
class ig_class_cache {
	private $_cacheDir;
	private $_cacheExt = '.cache';
	private static $_instance;
	const FOR_DAY = '86400';
	const FOR_WEEK = '604800';
	const FOR_MONTH = '2419200';

	public function __construct($dir = null, $ext = '.cache') {
		$this -> _cacheExt = $ext;

		if (!$dir) {
			$dir = realpath(dirname(__FILE__) . '/../../../') . '/cache';

			if (!file_exists($dir)) {
				mkdir($dir, 0755);
			}
			if (!file_exists($dir .= '/ig_cache')) {
				mkdir($dir, 0755);
			}
		}

		$this -> _cacheDir = $dir . '/';

	}

	public function exist($identifier, $toTime = null) {
		$file = $this -> _cacheDir . $identifier . $this -> _cacheExt;

		if (file_exists($file)) {
			if ($toTime) {
				$created = filemtime($filename);
				if (time() >= $created + $toTime) {
					unlink($file);
					return false;
				} else {
					return true;
				}
			} else {
				return true;
			}

		} else {
			return false;
		}

	}

	public function set($identifier, $data) {
		$file = $this -> _cacheDir . $identifier . $this -> _cacheExt;
		$exist = file_exists($file);
		if ($exist) {
			unlink($file);
		}
		$fh = fopen($file, 'w');
		fwrite($fh, serialize($data));
		fclose($fh);
	}

	public function get($identifier, $toTime = null) {
		$file = $this -> _cacheDir . $identifier . $this -> _cacheExt;
		$exist = file_exists($file);
		if ($exist) {
			if ($toTime) {
				$created = filemtime($file);
				if (time() >= $created + $toTime) {
					unlink($file);
					$data = null;
				} else {
					$data = unserialize(file_get_contents($file));
				}
			} else {
				$data = unserialize(file_get_contents($file));
			}
		} else {
			$data = null;
		}
		return $data;
	}

	public function clear($hasString = null) {
		if (file_exists($this -> _cacheDir)) {

			$files = scandir($this -> _cacheDir);
			if (is_array($files)) {
				foreach ($files as $file) {
					if ($file == '.' || $file == '..') {
						continue;
					}
					if ($hasString && strpos($file, $hasString) !== false) {
						unlink($this -> _cacheDir . $file);
					} else if (null === $hasString) {
						unlink($this -> _cacheDir . $file);
					}
				}
			}
			if (null === $hasString) {
				rmdir($this -> _cacheDir);

			}
		}

	}

	public function getCacheDir() {
		return $this -> _cacheDir;

	}

	public function cacheInfo() {
		$size = 0;
		if (file_exists($this -> _cacheDir)) {
			$files = scandir($this -> _cacheDir);
			if (is_array($files)) {
				$count = count($files) - 2;
				foreach ($files as $file) {
					if ($file == '.' || $file == '..') {
						continue;
					}
					$size += filesize($this -> _cacheDir . $file);
				}
			} else {
				$count = 0;
			}
		} else {
			$count = 0;
		}

		if ($size > 0) {
			$size = round($size / 1000000, 2);
		}

		return array('size' => $size, 'count' => $count);
	}

	public static function getInstance() {
		if (null === self::$_instance) {
			self::$_instance = new Core_Cache();
		}

		return self::$_instance;
	}

}
