<?php
/*
==================================================================
Free Agent - RSS to mySQL
==================================================================
version 2.0 by D Furlotte, darrenfurlotte (the at sign) gmail (dot) com
------------------------------------------------------------------
	//start debug//move this end comment above 'start debug' to enable display errors*/ 
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		//end debug

	
// Change THESE variable only to match your environment
$hostname = "localhost";
$username = "root";
$password = "0p3nW1nd0w5";
$datafeedsdbname = "datafeeds";
$contentdbname = "contentdb";
$log_data = date('Y-m-d H:i:s');
$countContent = 0;




////////////////////////////////////////////////
//DO NOT EDIT BELOW THIS NOTICE
////////////////////////////////////////////////
try 
{
/*  datafeedsdb connection. This selects a list of RSS urls.*/
	echo "Setting up ESPN test</br>";
		$datafeeddb_conn = new mysqli($hostname, $username, $password, $datafeedsdbname);
	if($datafeeddb_conn->connect_error) 
		die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
		echo "<font color = blue>Connected successfully to: " .$datafeedsdbname." </font> <br/>" ;
/*  contentdb connection. This allows read/write for the RSS content.*/
		$contentdb_conn = new mysqli($hostname, $username, $password, $contentdbname);
	if($contentdb_conn->connect_error) 
		die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
		echo "<font color = purple><B>Connected successfully to: " .$contentdbname."</font></B><br/>" ;
	echo "ESPN test setup completed</br>";

// Opening the datafeeddb and selecting the rss urls

		$rss_get_url = "SELECT feed_name,feed_address  FROM datafeeds.feed_urls;";
		$rss_url = mysqli_query($datafeeddb_conn,$rss_get_url);
		$rss_row_cnt = mysqli_num_rows($rss_url);
			if($rss_row_cnt == false){
			echo "<font color=RED><B>No RSS URLs retrieved for this content provider.</font></B>";
			}
//  First set to big outter loop that goes through each RSS XML document			
foreach ($rss_url as $key => $feed_url) {
	//  Now parse each rss feed to RSS_DOC 
			echo "<font color=brown<B>Starting to work with feed URL - <B>'" . $feed_url['feed_address']. "'</B><br/>";
			echo "---------------<br/>" ;
			echo "---------------</font></B><br/><br/>" ;	
				libxml_use_internal_errors(true);
				$RSS_DOC = simpleXML_load_file($feed_url['feed_address']);
			if (!$RSS_DOC) 
			{
				echo "Failed loading XML\n <br/>";
				foreach(libxml_get_errors() as $error) 
				{
					echo "\t", $error->message;
				}
			}
//start inner loop		
		foreach($RSS_DOC->channel->item as $RSSitem)
			{
	//Loop through each item in the RSS XML document				
		$content_id	= md5($RSSitem->title);
		$fetch_date = date("Y-m-j G:i:s"); //NOTE: we don't use a DB SQL function so its database independant
		$content_title = $RSSitem->title;
			$content_title_clean = mysqli_real_escape_string($datafeeddb_conn,$content_title);
		$content_description = $RSSitem->description;
			$content_description_clean = mysqli_real_escape_string($datafeeddb_conn,$content_description);
		$content_date  = date("Y-m-j G:i:s", strtotime($RSSitem->pubDate));
		$content_url	= $RSSitem->link;
		$content_guid  = $RSSitem->guid ;
	//output to screen
		echo "Processing item '" , $content_id  , "' on " , $fetch_date 	, "<br/>";
		echo "<B>Feed Name: ", 		$feed_url['feed_name'], " </B><br/> ";
		echo "Article Title: ", $content_title_clean, " <br/> ";
		echo "guid : ", $content_guid , "<br/>";		
		echo "Article Description: ", $content_description_clean, " <br/> ";
		echo "Article Date: ", $content_date, "<br/>";
		echo "Article URL: ", $content_url, "<br/>";
		echo "---------------" ;	
//$datafeeddb_conn->close();		
		
// Before we decide to store the data: Does record already exist? 
		$content_exists_sql = "SELECT * FROM espn_content where espn_content_id = '" . $content_id ."';" ;
		$content_exists = mysqli_query($contentdb_conn,$content_exists_sql);
		$content_row_cnt = mysqli_num_rows($content_exists);
		$countContent = $countContent + 1;
		
//Only insert if new item...		
		if($content_row_cnt == 0){
			echo "<font color=green>Inserting new item....</br></font>";
			$content_insert_sql = "INSERT INTO espn_content (espn_content_id, title, description, link, pubDate,guid) VALUES ('$content_id','$content_title_clean','$content_description_clean','$content_url','$fetch_date','$content_guid')";
            $retval = mysqli_query($contentdb_conn,$content_insert_sql);
				if(!$retval) {
					die('<font color=red>!!ERROR!!Could not enter data: </font>'.mysqli_error($contentdb_conn));  
					echo "<font color=green>Entered data successfully\n</font>";	}
		}
		else {
			echo "<font color=blue>Not inserting existing item..</font><br/>";	}	
		echo "<br/>";

	} //end inner loop
	$log_data = $log_data." - We retrieved " .$countContent. " rows of content.";
	echo "---------------<br/>" ;
	echo "Finished with feed URL - <B>'" . $feed_url['feed_address'] . "'</B><br/>";
	echo "---------------<br/>" ;
} // end outter Loop
			
	
}		//end try
catch (Exception $e)	{echo 'Caught exception: ',  $e->getMessage(), "\n";	} 	
		
////////////////////////
//End of script & clean up
////////////////////////
file_put_contents('./log/rss_ingest.log', $log_data, FILE_APPEND);

$datafeeddb_conn->close();
$contentdb_conn->close();
?>