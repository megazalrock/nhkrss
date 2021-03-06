<?php
date_default_timezone_set('Asia/Tokyo');
require_once(dirname(__FILE__) . '/../lib/db.php');
require_once(dirname(__FILE__) . '/../lib/feed.php');

$items = Feed::get_feed_items();

$count = count($items);
$newest_date = date('Y/m/d H:i:s', $items[0]->datetime);
$newest_title = $items[0]->title;
$newest_link = $items[0]->link;

$oldest_date = date('Y/m/d H:i:s', $items[$count - 1]->datetime);
$oldest_title = $items[$count - 1]->title;
$oldest_link = $items[$count - 1]->link;

$last_updated = file_get_contents('last_updated');

?>
<style>
	body{
		font-family: "Migu 1M", "MigMix 1M", sans-selif;
	}
</style>
<?php
echo "Last Updated : ${last_updated}<br><br>";
echo "Item Count : ${count} <br><br>";
echo "Newest: ${newest_date} ${newest_title}<br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"${newest_link}\" target=\"_blank\">${newest_link}</a><br><br>";
echo "Oldest: ${oldest_date} ${oldest_title}<br>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"${oldest_link}\" target=\"_blank\">${oldest_link}</a>";
