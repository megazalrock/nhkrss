<?php

date_default_timezone_set('Asia/Tokyo');
class Cache{
	const cache_dir_name = 'cache';
	const cache_expired = 300; // 5 * 60

	private function get_cache_dir_path(){
		return dirname(dirname(__FILE__) . '/../' . self::cache_dir_name) . '/' . self::cache_dir_name;
	}

	private function get_cache_file_path($filename){
		return self::get_cache_dir_path() . '/' . $filename;
	}

	private function make_cache_dir(){
		$dir = self::get_cache_dir_path();
		if(!file_exists($dir)){
			mkdir($dir);
			return file_exists($dir);
		}
		return true;
	}

	private function cahce_file_exist($filename){
		return file_exists(self::get_cache_file_path($filename));
	}

	public function get_cache_file($filename){
		$file_path = self::get_cache_file_path($filename);
		if(self::is_cache_valid($filename)){
			return file_get_contents($file_path);
		}else{
			return false;
		}
	}

	public function make_cache_file($str, $filename = null){
		self::make_cache_dir();
		if(is_null($filename)){
			$filename = md5($str);
		}
		$cache_file_path = self::get_cache_file_path($filename);
		if(file_exists($cache_file_path)){
			unlink($cache_file_path);
		}
		$handle = fopen($cache_file_path, 'w');
		fwrite($handle, $str);
		fclose($handle);
	}

	public function is_cache_valid($filename){
		$file_path = self::get_cache_file_path($filename);
		return file_exists($file_path) && time() - fileatime($file_path) < self::cache_expired;
	}

	public function sweep_cache($force = false){
		$dir = opendir(self::get_cache_dir_path());
		while(($file = readdir()) !== false){
			$file_path = self::get_cache_file_path($file);
			if(!is_file($file_path)){
				continue;
			}
			if((self::cache_expired < time() - fileatime($file_path)) || $force){
				unlink($file_path);
			}
		}
	}
}