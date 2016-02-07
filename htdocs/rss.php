<?php
date_default_timezone_set('Asia/Tokyo');
require_once(dirname(__FILE__) . '/../lib/feed.php');
require_once(dirname(__FILE__) . '/../lib/cache.php');

$cache_file_name = 'rss.xml';
Cache::sweep_cache();
$rss = Cache::get_cache_file($cache_file_name);
if(!$rss){
	$rss = Feed::get_feed_xml();
	Cache::make_cache_file($rss, $cache_file_name);
}
header('Content-Type: application/xml');
echo $rss;