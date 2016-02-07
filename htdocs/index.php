<?php
date_default_timezone_set('Asia/Tokyo');
require_once(dirname(__FILE__) . '/../lib/db.php');
require_once(dirname(__FILE__) . '/../lib/feed.php');
require_once(dirname(__FILE__) . '/../lib/cache.php');

Feed::update_feed_database('http://www3.nhk.or.jp/rss/news/cat0.xml');
Feed::update_feed_database('http://www3.nhk.or.jp/rss/news/cat1.xml');
Feed::update_feed_database('http://www3.nhk.or.jp/rss/news/cat3.xml');
Feed::update_feed_database('http://www3.nhk.or.jp/rss/news/cat4.xml');
Feed::update_feed_database('http://www3.nhk.or.jp/rss/news/cat5.xml');
Feed::update_feed_database('http://www3.nhk.or.jp/rss/news/cat6.xml');

$db = new DataBase();
$db->sweep_db();
$items = Feed::get_feed_items();

$count = count($items);
$newest_date = date('Y/m/d H:i:s', $items[0]->datetime);
$newest_title = $items[0]->title;
$newest_link = $items[0]->link;

$oldest_date = date('Y/m/d H:i:s', $items[$count - 1]->datetime);
$oldest_title = $items[$count - 1]->title;
$oldest_link = $items[$count - 1]->link;
?>
<style>
	body{
		font-family: "Migu 1M", "MigMix 1M", sans-selif;
	}
</style>
<?php
echo "Item Count : ${count} <br><br>";
echo "Newest: ${newest_date} ${newest_title}<br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"${newest_link}\" target=\"_blank\">${newest_link}</a><br><br>";
echo "Oldest: ${oldest_date} ${oldest_title}<br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"${oldest_link}\" target=\"_blank\">${oldest_link}</a>";
