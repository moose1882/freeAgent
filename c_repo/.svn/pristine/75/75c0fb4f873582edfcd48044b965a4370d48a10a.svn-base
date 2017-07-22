<?php
	//start debug
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		//end debug
		
 $host = "localhost";
 $username = "root";
 $pass = "0p3nW1nd0w5";
 $database = "contentdb";

 $conn = mysqli_connect($host, $username, $pass) or die (mysqli_error());
 //mysqli_select_db($database, $conn);

 $query = "SELECT distinct channel_title FROM contentdb.content_all where channel_title  not like '' order by channel_title ASC;";

 $result = mysqli_query($conn, $query) or die (mysqli_error());

 $dropdown = "<select name='cameraDD' '>"; //onchange='this.form.submit()
 while($row = mysqli_fetch_assoc($result)) {
  $dropdown .= "\r\n\<option value='{$row['channel_title']}'>{$row['channel_title']}</option>";
 }
 $dropdown .= "\r\n</select>";
 echo '<html>';
 echo '<head>';
echo '<title>Query Build</title>';
echo '</head>';
echo '<body>';
echo '</br></br>Select which feed you want to see:</br></br>';
 echo ('<form action="formProcessing.php" method="POST id="FruitList">');
 //echo '<select project="FruitList" ">';
 echo ($dropdown);
 echo '<input type="submit" name="submit" id="submit" value="Submit" />';
 echo ('</form>');
echo '</body>';
echo '</html>';
?>