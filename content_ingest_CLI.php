<?php

/*
==================================================================
Free Agent - Content Ingest
==================================================================
version 1.0 by D Furlotte, darrenfurlotte (the at sign) gmail (dot) com
------------------------------------------------------------------
	//start debug
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		//end debug
//move this end comment above 'start debug' to enable display errors*/ 
	
// Change THESE variable only to match your environment
$hostname = "localhost";
$username = "root";
$password = "0p3nW1nd0w5";
$datafeedsdbname = "datafeeds";
$contentdbname = "contentdb";


////////////////////////////////////////////////
//DO NOT EDIT BELOW THIS NOTICE
////////////////////////////////////////////////

$log_date = date('Y-m-d H:i:s')." - ";
$countContent = 0;
$log_entry = "";

////////////////////////
//mySQL Statements
////////////////////////
$sql_rss_get_url = "SELECT feed_provider, feed_name, feed_address  FROM datafeeds.feed_urls;";
$sql_content_insert_sql1 = "INSERT INTO content_all (espn_content_id, version, item_title, item_description, item_link,  item_pubDate, item_guid, channel_ttl, channel_language, channel_generator, channel_copyright, channel_lastBuildDate, channel_managingEditor, channel_url, channel_title, channel_description, image_link, image_width, image_height, image_title, image_description, feed_provider) VALUES ";
$sql_contentdb_count ="SELECT COUNT(espn_content_id) FROM contentdb.content_all;";
$sql_content_check = "SELECT * FROM `contentdb`.`content_all` WHERE espn_content_id ";
$sql_content_delete = "Delete FROM   contentdb.content_all  WHERE  DATE(item_pubDate) < DATE_SUB(CURDATE(), INTERVAL 7 DAY); ";

try 
{
/*  datafeedsdb connection. This selects a list of RSS urls.*/
			log_it ($log_date."Setting up CONTENT INGEST\n"); 
	$datafeeddb_conn = new mysqli($hostname, $username, $password, $datafeedsdbname);
	if($datafeeddb_conn->connect_error) 
		die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
			log_it ($log_date. "Connected successfully to: " .$datafeedsdbname ."\n");
/*  contentdb connection. This allows read/write for the RSS content.*/
		$contentdb_conn = new mysqli($hostname, $username, $password, $contentdbname);
	if($contentdb_conn->connect_error) 
		die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
			log_it ($log_date. "Connected successfully to: " .$contentdbname ."\n");
			log_it ($log_date. "CONTENT INGEST setup completed \n");
			log_it ($log_date. "--------------------------------------------------------------\n");
	
// I'm connected now. Want to clean up the old content first.
// add mysql query - "Delete FROM   contentdb.content_all  WHERE  DATE(pubDate) = DATE_SUB(CURDATE(), INTERVAL 4 DAY);"
		$db_delete_start = how_many($datafeeddb_conn,$sql_contentdb_count);
		$content_delete = mysqli_query($datafeeddb_conn,$sql_content_delete);
		$db_delete_end = how_many($datafeeddb_conn,$sql_contentdb_count);	
		$db_total_delete = ($db_delete_end - $db_delete_start);
			log_it ($log_date. "Deleted ".$db_total_delete ." rows of old content.\n");
			log_it ($log_date. "Starting with ".$db_delete_end ." total rows.\n");
			
// Opening the datafeeddb and selecting the rss urls
		$db_count_start = how_many($datafeeddb_conn,$sql_contentdb_count);
		$rss_url = mysqli_query($datafeeddb_conn,$sql_rss_get_url);
		$rss_row_cnt = mysqli_num_rows($rss_url);
			if($rss_row_cnt == false){
				log_it ($log_date. "No RSS URLs retrieved for this content provider.\n");
			}
			
//  First set to big outter loop that goes through each RSS XML document			
foreach ($rss_url as $key => $feed_url) 
	{
	//  Now parse each rss feed to RSS_DOC 
		$countContent = 0;
		$content_feed_provider = $feed_url['feed_provider'];
		$content_feed_name = $feed_url['feed_name'];
		
				libxml_use_internal_errors(true);
				$RSS_DOC = simpleXML_load_file($feed_url['feed_address']);
			if (!$RSS_DOC) 
			{
				log_it ($log_date. "Failed loading XML ".$feed_url['feed_address']. " \n");
				foreach(libxml_get_errors() as $error) 
				{
					log_it ( "\t", $error->message."\n");
				}
				continue;
			}
//start inner channel loop. General data about the RSS channel
		foreach ($RSS_DOC->channel as $RSSchannel)
			{
			$channel_version = $RSSchannel->version;
			$channel_ttl = $RSSchannel->ttl;
			$channel_language = $RSSchannel->language;	
			$channel_generator =  $RSSchannel->generator;
				$channel_generator_clean = mysqli_real_escape_string($datafeeddb_conn,$channel_generator);			
			$channel_copyright = $RSSchannel->copyright;
				$content_channel_copyright_clean = mysqli_real_escape_string($datafeeddb_conn,$channel_copyright);
			$channel_lastBuildDate = $RSSchannel->lastBuildDate;
			$channel_managingEditor = $RSSchannel->managingEditor;
			$channel_url = $RSSchannel->link;
			$channel_title = $RSSchannel->title;
				$channel_title_clean = mysqli_real_escape_string($datafeeddb_conn,$channel_title);
			$channel_description = $RSSchannel->description;
				$channel_description_clean = mysqli_real_escape_string($datafeeddb_conn,$channel_description);
			}
													
//start inner channel->image loop. data for channel image
		foreach ($RSS_DOC->channel->image as $RSSimage)
			{
			$image_title = $RSSimage->title;	
			$image_url = $RSSimage->url;
			$image_link = $RSSimage->link;
			$mage_width = $RSSimage->width;
			$image_height = $RSSimage->height;
			$image_description = $RSSimage->description;
			}											
													
//start inner channel-->item loop		
		foreach($RSS_DOC->channel->item as $RSSitem)
			{
	//Loop through each item in the RSS XML document				
		$content_id	= md5($RSSitem->title);
		$fetch_date = date("Y-m-j G:i:s"); //NOTE: we don't use a DB SQL function so its database independant
		$item_title = $RSSitem->title;
			$item__title_clean = mysqli_real_escape_string($datafeeddb_conn,$item_title);
		$item__description = $RSSitem->description;
			$item__description_clean = mysqli_real_escape_string($datafeeddb_conn,$item__description);
		$item__url	= $RSSitem->link;
			$item__url_clean = mysqli_real_escape_string($datafeeddb_conn,$item__url);
		$item__date  = date("Y-m-j G:i:s", strtotime($RSSitem->pubDate));
		$item__guid  = $RSSitem->guid;
			$item__guid_clean  = mysqli_real_escape_string($datafeeddb_conn,$item__guid);
			
// Before we decide to store the data: Does record already exist? 
		$content_exists_sql =$sql_content_check. "= '".$content_id."';" ;
		$content_exists = mysqli_query($contentdb_conn,$content_exists_sql);
		$content_row_cnt = mysqli_num_rows($content_exists);

//Only insert if new item...		
		if($content_row_cnt == 0)
		{
			$countContent = $countContent + 1 ;
													//echo "..inserting\n";
													//log_it ($log_date. "Inserting ".$countContent." items from ".$content_feed_provider.":".$content_feed_name.".\n");
			$content_insert_sql = $sql_content_insert_sql1." ('$content_id','$channel_version', '$item__title_clean', '$item__description_clean', '$item__url_clean', '$item__date', '$item__guid_clean ', '$channel_ttl', '$channel_language', '$channel_generator_clean ', '$content_channel_copyright_clean', '$channel_lastBuildDate', '$channel_managingEditor', '$channel_url', '$channel_title_clean','$channel_description_clean', '$image_url','image_width', 'image_height', '$image_title', '$image_description','$content_feed_provider')";
			
			//"'$content_channel_clean','$content_description_clean','$content_url_clean','$content_title_ttl', '$content_title_language', '$content_title_generator_clean', '$content_title_copyright_clean', '$content_title_lastBuildDate', '$content_title_managingEditor', '$content_channel_url', '$content_channel_title_clean', '$content_image_link', '$content_image_width', '$content_image_height', '$content_image_title', '$content_image_description', '$content_title_description_clean', '$fetch_date','$content_guid_clean','$content_feed_provider')";
            $retval = mysqli_query($contentdb_conn,$content_insert_sql);
				if(!$retval) 
				{
					die("!!ERROR!!Could not enter data: ".mysqli_error($contentdb_conn)."\n");  
				}
				//log_it ($log_date. "Insert complete.\n");	
		}
	} //end inner loop
				if ($countContent -1 >= 0)
				{
					log_it ($log_date."***Ingest Summary: ". $countContent." Records added from Provider: ".$content_feed_provider.":".$content_feed_name."\n");
				}
/* 				else {
					log_it ($log_date." No records added from Provider: ".$content_feed_provider.":".$content_feed_name."\n");
				} 
*/	 
} // end outter Loop
					
}		//end try
catch (Exception $e)	
	{
	log_it ($log_date. 'Caught exception: '.  $e->getMessage(). "\n");	
	} 	

	
////////////////////////
//trim whitespaces function
////////////////////////		

function trim_spaces ($trim_data)
{
	trim($trim_data(''));
}


////////////////////////
//Log function
////////////////////////	

function log_it($log_data)	
{
	$logfile = fopen("/var/www/html/poc/ingest_logs.txt", "a") or die("Unable to open ingest log file!\n");
	fwrite($logfile, $log_data);
	fclose($logfile);
}

////////////////////////
//How Many? Counts # of rows in the contentdb
////////////////////////
function how_many($datafeeddb_conn,$contentdb_count)
{
	$howmanyuser_query=$datafeeddb_conn->query($contentdb_count);
	$howmanyuser=$howmanyuser_query->fetch_array(MYSQLI_NUM); 
	return $howmanyuser[0];
}

////////////////////////
//End of script & clean up
////////////////////////
$db_count_end = how_many($datafeeddb_conn,$sql_contentdb_count);
$tot_ingest_count = ($db_count_end - $db_count_start);

$datafeeddb_conn->close();
$contentdb_conn->close();
log_it("Done. ".$tot_ingest_count." new rows added to db. ".$db_count_end." rows in total. \n");
log_it ("--------------------------------------------------------------\n");
?>