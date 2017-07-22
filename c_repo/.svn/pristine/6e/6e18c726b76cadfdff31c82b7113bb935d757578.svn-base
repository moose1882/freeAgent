<?php
/*
==================================================================
Free Agent - Search Content Test
==================================================================
version 1.0 by D Furlotte, darrenfurlotte (the at sign) gmail (dot) com
------------------------------------------------------------------
	//start debug//move this end comment above 'start debug' to enable display errors*/ 
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		//end debug

	
// Change these variable ONLY to match your environment
$hostname = "localhost";
$username = "root";
$password = "0p3nW1nd0w5";
$contentdbname = "contentdb";







////////////////////////////////////////////////
//DO NOT EDIT BELOW THIS NOTICE
////////////////////////////////////////////////

//Search

/*

if ( record_exists ( 'employee', 'username', $username ){
    echo "Username is not available. Try something else.";
}else{
    echo "Username is available";
}



//Check if a value exists in a table
function record_exists ($table, $column, $value) {
    global $connection;
    $query = "SELECT * FROM {$table} WHERE {$column} = {$value}";
    $result = mysql_query ( $query, $connection );
    if ( mysql_num_rows ( $result ) ) {
        return TRUE;
    } else {
        return FALSE;
    }
}
*/

/*  contentdb connection. This allows read/write for the RSS content.*/
		$contentdb_conn = new mysqli($hostname, $username, $password, $contentdbname);
	if($contentdb_conn->connect_error) 
		die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
		echo "<font color = purple><B>Connected successfully to: " .$contentdbname."</font></B><br/>" ;
	echo "Content Search test setup completed</br>";

// Opening the content db and reading in the stuff

		$rss_get_content = "SELECT * FROM contentdb.espn_content;";
		$rss_content = mysqli_query($contentdb_conn,$rss_get_content);
		$rss_row_cnt = mysqli_num_rows($rss_content);
			if($rss_row_cnt == false){
			echo "<font color=RED><B>No content retrieved.</font></B>";
			}

		
////////////////////////
//End of script clean up
////////////////////////

$contentdb_conn->close();
?>