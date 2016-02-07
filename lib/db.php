<?php
date_default_timezone_set('Asia/Tokyo');
Class DataBase{
	private $config;
	private $min_time;
	private $table_name = 'feed';
	function __construct(){
		$this->min_time = 1 * 24 * 60 * 60;
		$this->config = parse_ini_file(dirname(__FILE__) . '/../config.ini');
		try{
			$dbh = $this->connection();
			$rs = $dbh->query('SHOW TABLES');
			$tables = $rs->fetchAll(PDO::FETCH_COLUMN);
			if(!in_array($this->table_name, $tables)){
				$sth = $dbh->prepare(
					"CREATE TABLE `feed` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `title` longtext,
						  `link` longtext,
						  `datetime` int(11) DEFAULT NULL,
						  `description` longtext,
						  `source` longtext,
						  `unique_key` longtext,
						  PRIMARY KEY (`id`),
						  UNIQUE KEY `unique` (`unique_key`(16))
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
				);
				$sth->execute();
			}
		}catch(PDOException $e){
			die($e->getMessage());
		}

	}
	public function connection(){
		$dsn = 'mysql:host=' . $this->config['host'] . ';dbname=' . $this->config['name'] . ';charset=utf8';
		try{
			$dbh = new PDO($dsn, $this->config['user'], $this->config['password']);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		}catch (PDOException $e){
			die($e->getMessage());
		}
		return $dbh;
	}

	public function save_feed_item($item){
		if(time() - $this->min_time < $item->date){
			try{
				$dbh = $this->connection();
				$sth = $dbh->prepare('INSERT IGNORE INTO feed (title, link, datetime, description, source, unique_key) VALUES (:title, :link, :datetime, :description, :source, :unique_key)');
				$sth->bindParam(':title', $item->title, PDO::PARAM_STR);
				$sth->bindParam(':link', $item->link, PDO::PARAM_STR);
				$sth->bindParam(':datetime', $item->date, PDO::PARAM_INT);
				$sth->bindParam(':description', $item->description, PDO::PARAM_STR);
				$sth->bindParam(':source', $item->source, PDO::PARAM_STR);
				$sth->bindParam(':unique_key', $item->unique_key, PDO::PARAM_STR);
				$result = $sth->execute();
			}catch(PDOException $e){
				echo $e->getMessage();
			}
		}
	}

	public function get_all(){
		try{
			$dbh = $this->connection();
			$sth = $dbh->prepare('SELECT * FROM feed ORDER BY datetime DESC');
			$sth->execute();
			$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			echo($e->getMessage());
		}
		return $result;
	}

	public function sweep_db(){
		$now = time();
		$min = $now - $this->min_time;
		try{
			$dbh = $this->connection();
			$sth = $dbh->prepare('DELETE FROM feed WHERE datetime <= :min');
			$sth->bindParam(':min', $min, PDO::PARAM_INT);
			$sth->execute();
			$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			echo($e->getMessage());
		}
		return $result;
	}
}