<?php
/* 
	//start debug
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		//end debug
 */		
$mysql_host = 'localhost'; //host
$mysql_username = 'root'; //username
$mysql_password = '0p3nW1nd0w5'; //password
$mysql_database = 'contentdb'; //db

header('Content-Type: text/xml; charset=utf-8', true); //set document header content type to be XML

$rss = new SimpleXMLElement('<rss xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom"></rss>');
$rss->addAttribute('version', '2.0');

$channel = $rss->addChild('channel'); //add channel node

$atom = $rss->addChild('atom:atom:link'); //add atom node
$atom->addAttribute('href', 'http://54.179.154.121/poc/search/rss_feeds/rss_feed.php'); //add atom node attribute
$atom->addAttribute('rel', 'self');
$atom->addAttribute('type', 'application/rss+xml');

$title = $rss->addChild('title','Free Agent Sports'); //title of the feed
$description = $rss->addChild('description','Your Sports, Your Stars, Your Feed'); //feed description
$link = $rss->addChild('link','http://54.179.154.121/poc/search/rss_feeds/rss_feed.php'); //feed site
$language = $rss->addChild('language','en-us'); //language

//Create RFC822 Date format to comply with RFC822
$date_f = date("D, d M Y H:i:s T", time());
$build_date = gmdate(DATE_RFC2822, strtotime($date_f)); 
$lastBuildDate = $rss->addChild('lastBuildDate',$date_f); //feed last build date
$generator = $rss->addChild('generator','PHP Simple XML'); //add generator node

//connect to MySQL - mysqli(HOST, USERNAME, PASSWORD, DATABASE);
$mysqli = new mysqli($mysql_host, $mysql_username, $mysql_password, $mysql_database);
//Output any connection error
if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}
$results = $mysqli->query("SELECT espn_content_id, item_title, item_pubDate,  item_description, item_link, channel_lastBuildDate, channel_language feed_provider FROM contentdb.content_all  WHERE feed_provider like 'CBC' and item_title like '%olympic%' and DATE(item_pubDate) = DATE_SUB(CURDATE(), INTERVAL 0 DAY) and channel_language  like 'en%' order by channel_lastBuildDate DESC;");

if($results){ //we have records 
	while($row = $results->fetch_object()) //loop through each row
	{
		$item = $rss->addChild('item'); //add item node
		$title = $item->addChild('title', $row->item_title); //add title node under item
		$guid = $item->addChild('guid', $row->espn_content_id); //add guid node under item		
		$link = $item->addChild('link', $row->item_link); //add link node under item
		$description = $item->addChild('description', '<![CDATA['. htmlspecialchars($row->item_description) . ']]>'); //add description
		$guid->addAttribute('isPermaLink', 'false'); //add guid node attribute
		$date_rfc = gmdate(DATE_RFC2822, strtotime($row->item_pubDate));
		$item = $item->addChild('pubDate', $date_rfc); //add pubDate node
	}
}

echo $rss->asXML(); //output XML
?>