<?php
/* 
	//start debug
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		//end debug
 */	
// Start or continue a session

session_start();


// Has the user

if (! isset($_SESSION['userid']))
{

   if (! isset($_POST['email']))
   {

      echo "<form action='feed.php' method='post'>";
      echo "Email:<br />";
      echo "<input type='text' name='email' size='20'
                   maxlength='55' value='' /><br />";
      echo "Password:<br />";
      echo "<input type='password' name='pswd' size='20'
                   maxlength='20' value='' /><br />";
      echo "<input type='submit' value='login'>";
      echo "</form>";

   } else {

      mysql_connect("localhost","aggregator","secret");
      mysql_select_db("rssfeeds");

      $email = $_POST['email'];
      $pswd = md5($_POST['pswd']);

      $query = "SELECT rowID, email, pswd FROM user 
                WHERE email='$email' AND '$pswd'";
      $result = mysql_query($query);

      if (mysql_numrows($result) != 1)
      {
         echo "<p>Could not login!</p>";
      } else {
         list($rowID, $email, $pswd) = mysql_fetch_row($result);
         $_SESSION['userid'] = $rowID;
      }

      mysql_close();

   } // end isset[email]

}
$mysql_host = 'localhost'; //host
$mysql_username = 'root'; //username
$mysql_password = '0p3nW1nd0w5'; //password
$mysql_database = 'contentdb'; //db

?>