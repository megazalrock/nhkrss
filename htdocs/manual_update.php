<?php
date_default_timezone_set('Asia/Tokyo');
require_once(dirname(__FILE__) . '/../lib/db.php');
require_once(dirname(__FILE__) . '/../lib/feed.php');

Feed::update_feed_database('http://www3.nhk.or.jp/rss/news/cat0.xml');
Feed::update_feed_database('http://www3.nhk.or.jp/rss/news/cat1.xml');
Feed::update_feed_database('http://www3.nhk.or.jp/rss/news/cat3.xml');
Feed::update_feed_database('http://www3.nhk.or.jp/rss/news/cat4.xml');
Feed::update_feed_database('http://www3.nhk.or.jp/rss/news/cat5.xml');
Feed::update_feed_database('http://www3.nhk.or.jp/rss/news/cat6.xml');

$db = new DataBase();
$db->sweep_db();

$last_updated_date = dirname(__FILE__) . '/../htdocs/last_updated';
if(file_exists($last_updated_date)){
	unlink($last_updated_date);
}
$handle = fopen($last_updated_date, 'w');
fwrite($handle, date('c') . ' by manual');
fclose($handle);

echo 'done !';