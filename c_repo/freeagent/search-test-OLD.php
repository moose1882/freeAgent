
<?php

/*	//start debug
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

//echo "<h2>Search Results are here:</h2><p>";
echo "<h2>Hello world";
if(isset($_POST['search']))
    {
        $find =$_POST['find'];
            //If they did not enter a search term we give them an error
            if ($find == "")
            {
            echo "<p>You forgot to enter a search term!!!";
            exit;
            }

        // Otherwise we connect to our Database
		/*  contentdb connection. This allows read/write for the RSS content.*/
	
		$contentdb_conn = new mysqli($hostname, $username, $password, $contentdbname);
			if($contentdb_conn->connect_error) 
				die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
				echo "<font color = purple><B>Connected successfully to: " .$contentdbname."</font></B><br/>" ;
			echo "content database connection setup completed</br>";
			echo "---------------------</br>";

        // We perform a bit of filtering
        $find = strtoupper($find);
        $find = strip_tags($find);
        $find = trim ($find);

        //Now we search for our search term, in the field the user specified
        $iname = mysqli_query($contentdb_conn, "SELECT * FROM contentdb.espn_content where description like '%$find%'")
			or die(mysqli_error());

        //And we display the search count and search term
        //This counts the number or results - and if there wasn't any it gives them a little message explaining that
        $anymatches = mysqli_num_rows( $iname);
			if ($anymatches == 0)
			{
				echo "<font color = red>Sorry, but we can not find an entry to match your query...</font><br><br>";
				}
			else
			{
				echo "<font color = blue>Search Returned " .$anymatches.".</br>";
				}
				echo "<b>Searched For:</b> " .$find."</br>";
				echo "---------------------</br></font>";
            
			// Now disply the results
			while($result = mysqli_fetch_array(  $iname ))
			{
				echo "Published Date :" .$result['pubDate'];
					echo "<br> ";
				echo "Title :" .$result['title'];
					echo "<br> ";
				echo "Description :".$result['description'];
					echo "<br>";
				echo "Link: " .$result['link'];
					echo "<br>";
				echo "Click link to go to the article: <a href='".$result['link']. "'> Link </a>"; 
					echo "<br>";
					echo "<br>";
				}
    }
?> 
