<?php 
date_default_timezone_set('Asia/Tokyo');
require_once(dirname(__FILE__) . '/cache.php');
require_once(dirname(__FILE__) . '/db.php');

class Feed {

	private function is_exist_url($url){
		$header = get_headers($url);
		return preg_match('/200 OK/', $header[0]);
	}

	public function update_feed_database($url){
		if(!self::is_exist_url($url)){
			$messeage = array(
				"フィードが取得できません",
				"フィードのURL : " . $url,
				$header[0]
			);
			throw new Exception(implode("\n", $messeage));
		}

		$db = new DataBase();
		$feed = simplexml_load_file($url);
		foreach ($feed->channel->item as $feed_item) {
			$db->save_feed_item(new FeedItem($feed_item, $url));
		}
	}

	public function get_feed_items(){
		$db = new DataBase();
		$result = $db->get_all();
		$feed = array();
		foreach ($result as $feed_item) {
			array_push($feed, new FeedItem($feed_item));
		}
		return $feed;
	}

	public function get_feed_xml(){
		$cache_file_name = 'rss.xml';
		$feed_item_template = '<item>
		<title>%1$s</title>
		<link>%2$s</link>
		<guid isPermaLink="true">%2$s</guid>
		<pubDate>%3$s</pubDate>
		<description>%4$s</description>
		</item>';
		$feed = Feed::get_feed_items();
		$feed_xml = array();
		array_push($feed_xml, '<rss xmlns:nhknews="http://www.nhk.or.jp/rss/rss2.0/modules/nhknews/" version="2.0">');
		array_push($feed_xml, '<channel>');
		array_push($feed_xml, '<description>NHKニュース</description>');
		array_push($feed_xml, '<link>http://nhk.mgzl.jp</link>');
		array_push($feed_xml, '<lastBuildDate>' . date('r') . '</lastBuildDate>');
		foreach ($feed as $feed_item) {
			array_push($feed_xml, sprintf(
				$feed_item_template,
				$feed_item->title,
				$feed_item->link,
				$feed_item->date_string,
				$feed_item->description,
				$feed_item->source
			));
		}
		array_push($feed_xml, '</channel>');
		array_push($feed_xml, '</rss>');
		return implode($feed_xml, '');
	}
}

class FeedItem {
	function __construct($feed_item, $source = null){
		if(!is_array($feed_item) && get_class($feed_item) === 'SimpleXMLElement'){
			$this->title = (String) $feed_item->title;
			$this->link = (String) $feed_item->link;
			$this->date_string = (String) $feed_item->pubDate;
			$this->date = strtotime($this->date_string);
			$this->description = (String) $feed_item->description;
			$this->unique_key = md5($this->title.$this->description);
			$this->source = $source;
		}else{
			$this->title = $feed_item['title'];
			$this->link =  $feed_item['link'];
			$this->date_string = date('r', $feed_item['datetime']);
			$this->datetime = $feed_item['datetime'];
			$this->description = $feed_item['description'];
			$this->unique_key = $feed_item['unique_key'];
			$this->source = $feed_item['source'];
		}
	}

	function get_array(){
		return array(
			'title' => $this->title,
			'link' => $this->link,
			'guid' => $this->guid,
			'pub_date' => $this->pub_date,
			'description' => $this->description,
			'id' => $this->id,
		);
	}
}