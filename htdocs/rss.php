<?php
date_default_timezone_set('Asia/Tokyo');
require_once(dirname(__FILE__) . '/../lib/feed.php');

$rss = Feed::get_feed_xml();
header('Content-Type: application/xml');
echo $rss;