<?php
//debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
==================================================================
Free Agent - RSS to mySQL
==================================================================
Based on 
RSS Ingest v 0.1 - by Daniel Iversen, daniel (a dot) iversen (the at sign) gmail (another dot) com
But highly modified by D Furlotte, darrenfurlotte (the at sign) gmail (another dot) com
------------------------------------------------------------------
INTRO:
- - - - - - - - -
 - Just a tiny primitive PHP script that you can run on a cron to ingest
   an RSS feed into a flat database table.
 - uses mySQLi

INSTALLATION AND USAGE:
- - - - - - - - - - - - - - -
 - Change the few variables in this PHP (db connection info and access key/password)
	The 4 variables you need to look for in this PHP (usually grouped) are;

	$db_hostname="    	- ....your db hostname here - maybe 'localhost' is enough...
	$db_username="     	- ....your db username here...
	$db_password="     	- ....your db password here...
	$database="     	- ....your db name here
	$feed_url="     	- ....your rss feed url

 - create table using SQL below
 - create table indexes if needed

CREATE TABLE SQL:
- - - - - - - - - - - - - - -
	CREATE TABLE  `rssingest` (
	 `item_id` VARCHAR( 32 ) NOT NULL ,
	 `feed_url` VARCHAR( 512 ) NOT NULL ,
	 `item_content` VARCHAR( 4000 ) NULL ,
	 `item_title` VARCHAR( 255 ) NOT NULL ,
	 `item_date` TIMESTAMP NOT NULL ,
	 `item_url` VARCHAR( 512 ) NOT NULL ,
	 `item_status` CHAR( 2 ) NOT NULL ,
	 `item_category_id` INT NULL ,
	 `fetch_date` TIMESTAMP NOT NULL
	) ENGINE = MYISAM ;

==================================================================
==================================================================
*/
// Check a few bits and pieces
$hostname = "localhost";
$username = "root";
$password = "0p3nW1nd0w5";
$database = "rssdb";
$feed_url = "http://www.instapaper.com/rss/864982/OQT9BkmHESbcj42WP1MDJLxEw8";

try 
{
/*  initial database connection*/
	$dbconnect = new mysqli($hostname, $username, $password, $database);
	if($dbconnect->connect_error) 
		die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
	echo "<font color = green>Connected successfully <br/>" ;
	echo "Starting to work with feed URL - <B>'" . $feed_url . "'</B><br/>";
	echo "---------------<br/>" ;
	echo "---------------</font><br/><br/>" ;
/*  parse rss feed to rss_doc */
	
	libxml_use_internal_errors(true);
	$RSS_DOC = simpleXML_load_file($feed_url);
	if (!$RSS_DOC) {
		echo "Failed loading XML\n <br/>";
		foreach(libxml_get_errors() as $error) {
			echo "\t", $error->message;
		}
	}

/* Get title, link, managing editor, and copyright from the document  */
//this currently serves no purpose
	$rss_title = $RSS_DOC->channel->title;
	$rss_link = $RSS_DOC->channel->link;
	$rss_editor = $RSS_DOC->channel->description;
	$rss_copyright = $RSS_DOC->channel->guid;
	$rss_date = $RSS_DOC->channel->pubDate;

//Loop through each item in the RSS document
	foreach($RSS_DOC->channel->item as $RSSitem)
	{
		$item_id 	= md5($RSSitem->title);
		$fetch_date = date("Y-m-j G:i:s"); //NOTE: we don't use a DB SQL function so its database independant
		$item_title = $RSSitem->title;
			$item_title_clean = mysqli_real_escape_string($dbconnect,$item_title);
		$item_description = $RSSitem->description;
			$item_description_clean = mysqli_real_escape_string($dbconnect,$item_description);
		$item_date  = date("Y-m-j G:i:s", strtotime($RSSitem->pubDate));
		$item_url	= $RSSitem->link;
//output to screen
		echo "Processing item '" , $item_id , "' on " , $fetch_date 	, "<br/>";
		echo "Article Title: ", $item_title_clean, " <br/> ";
		echo "Article Description: ", $item_description_clean, " <br/> ";
		echo "Article Date: ", $item_date, "<br/>";
		echo "Article URL: ", $item_url, "<br/>";
		echo "--------------- NOTICE: " ;
// Before we decide to store the data: Does record already exist? 
		$item_exists_sql = "SELECT item_id FROM rssingest where item_id = '" . $item_id . "'";
		$item_exists = mysqli_query($dbconnect,$item_exists_sql);
		$row_cnt = mysqli_num_rows($item_exists);
//Only insert if new item...		
		if($row_cnt == false){
			echo "<font color=green>Inserting new item....</font>";
			$sql = "INSERT INTO rssingest "."(item_id, feed_url, item_title, item_date, item_url, fetch_date)"." VALUES ('$item_id','$feed_url','$item_title_clean','$item_date','$item_url','$fetch_date')";
            $retval = mysqli_query($dbconnect,$sql);
		if(!$retval) {
			die('<font color=red>!!ERROR!!Could not enter data: </font>'.mysqli_error($dbconnect));  }
			echo "<font color=green>Entered data successfully\n</font>";	}
		else {
			echo "<font color=blue>Not inserting existing item..</font><br/>";	}	
		echo "<br/>";
	}	//end foreach
	echo "---------------<br/>" ;
	echo "Finished with feed URL - <B>'" . $feed_url . "'</B><br/>";
	echo "---------------<br/>" ;
}		//end try
catch (Exception $e)	{
		echo 'Caught exception: ',  $e->getMessage(), "\n";	} 
/* catch (Exception $e) {
  echo $e->getMessage();
  echo "---";
  echo mysql_error(); */
//close db connection		
	$dbconnect->close();
?>