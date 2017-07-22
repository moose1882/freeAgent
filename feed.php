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

date_default_timezone_set('Europe/London'); //set your SERVER timezone

header('Content-Type: text/xml; charset=utf-8', true); //set document header content type to be XML

$rss = new SimpleXMLElement('<rss xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:atom="http://www.w3.org/2005/Atom"></rss>');
$rss->addAttribute('version', '2.0');

$channel = $rss->addChild('channel'); //add channel node

$category = $channel->addChild('category', 'Sports'); //add category node
$category = $channel->addChild('category', 'Global Sports'); //add category node
//$category = $channel->addChild('category', 'etc. etc. etc.'); //add category node

$atom = $channel->addChild('atom:atom:link'); //add atom node
$atom->addAttribute('href', 'http://54.179.154.121/poc/search/rss_feeds/feed.php'); //add atom node attribute
$atom->addAttribute('rel', 'self');
$atom->addAttribute('type', 'application/rss+xml');

$title = $channel->addChild('title','My Sports'); //title of the feed
$description = $channel->addChild('description','The sports and atheletes YOU want to know about!'); //feed description
$link = $channel->addChild('link','http://54.179.154.121/poc/search/rss_feeds/feed.php'); //feed site
$language = $channel->addChild('language','en-gb'); //language

$atom = $channel->addChild('image','rss_icon.jpeg'); //add an image (logo)
$atom->addAttribute('title', 'My Sports');
$atom->addAttribute('url', 'http://54.179.154.121/poc/search/rss_feeds/rss_icon.jpeg');
$atom->addAttribute('link', 'http://54.179.154.121/poc/search/rss_feeds/');
$atom->addAttribute('width', '90'); //just numbers
$atom->addAttribute('height', '90'); //just numbers

//Create RFC822 Date format to comply with RFC822
$date_f = date("D, d M Y H:i:s T", time());
$build_date = gmdate(DATE_RFC2822, strtotime($date_f));
$lastBuildDate = $channel->addChild('lastBuildDate',$date_f); //feed last build date

$generator = $channel->addChild('generator','PHP Simple XML'); //add generator node

//connect to MySQL - mysqli(HOST, USERNAME, PASSWORD, DATABASE);
$mysqli = new mysqli($mysql_host, $mysql_username, $mysql_password, $mysql_database);
//Output any connection error
if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}

$results = $mysqli->query("SELECT espn_content_id, item_title, item_pubDate,  item_description, item_link, channel_lastBuildDate, channel_language feed_provider FROM contentdb.content_all  WHERE  DATE(item_pubDate) = DATE_SUB(CURDATE(), INTERVAL 0 DAY) and channel_language  like 'en%' order by channel_lastBuildDate DESC;");

if($results){ //we have records
    while($row = $results->fetch_object()) {
        $item = $channel->addChild('item'); //add item node
        $title = $item->addChild('title', $row->item_title); //add title node under item
        $link = $item->addChild('link', $row->item_link); //add link node under item
        $guid = $item->addChild('guid', $row->espn_content_id); //add guid node under item
        $guid->addAttribute('isPermaLink', 'true'); //add guid node attribute - true or false
       
        $description = $item->addChild('description', htmlspecialchars($row->item_description)); //add description
       
        $date_rfc = gmdate(DATE_RFC2822, strtotime($row->item_pubDate));
        $item = $item->addChild('pubDate', $date_rfc); //add pubDate node
    }
}

echo $rss->asXML(); //output XML
?>