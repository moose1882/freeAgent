<?php
/*
==================================================================
Free Agent - Player Scrape
==================================================================
version 1.0 by D Furlotte, darrenfurlotte (the at sign) gmail (dot) com
Get the contents of an html page that contains atheletes names, teams, positions, etc
Searching by HTML Element
cricket players retrieved from: http://www.cricket.com.au/players
MLB players here: http://www.foxsports.com/mlb/players
------------------------------------------------------------------
*/	//start debug
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		//end debug
//move this end comment above 'start debug' to enable display errors*/ 
include_once("simple_html_dom.php");



$html = 'http://www.foxsports.com/mlb/players'; //new simple_html_dom();


$dom = new DomDocument();
@$dom->file_get_html($html);
$xpath = new DOMXpath($dom);
$masterNode = $xpath->query('//div[2]/table/tbody/tr[td]'); #It returns DOMNodeList

# Now from master node we gonna pick what we want.
# Also, $masterNode->item(0) is context node for "P" tags.
$paragraphNodes = $xpath->query('//div[2]/table/tbody/tr[td]', $masterNode->item(0)); 

foreach ($paragraphNodes as $paragraphElement) {
    print $paragraphElement->nodeValue . "\n";
}

























//outter loop for the url. 1 - 155 itterations
/* for($i = 1; $i < 2; $i++){}
$url = 'http://www.foxsports.com/mlb/players';//'http://www.foxsports.com/mlb/players?season=2016&page='.$i.'&position=0';

$html = file_get_html('http://www.foxsports.com/mlb/players');

$content = $xpath->query('//div[@class='wisfb_fullPlayerStacked']');
echo $content; */
//$yourDesiredContent = $html->find('div', 0)->plaintext;
//echo $yourDesiredContent;
   	  


	 
	// Find all images 
//foreach($html->find('span') as $firstname) 
     //echo 'Page: ' . $url.'<br>';
	// echo $firstname->innertext. '<br> ';
//	 foreach($html->find('p.lastname') as $lastname) 
   // echo $lastname->innertext. '<br>';
	 
	 
	 
	 
	 
	  //foreach($html->find('div.ccol-lg-3 col-md-3 col-sm-3 players-slide') as $divBlock) 
 //     echo $divBlock. '<br>';	
/* 
// Include the library
include('simple_html_dom.php');
 
// Retrieve the DOM from a given URL
$html = file_get_html('https://davidwalsh.name/');

// Find all "A" tags and print their HREFs
//foreach($html->find('a') as $e) 
//    echo $e->href . '<br>';

// Retrieve all images and print their SRCs
foreach($html->find('img') as $e)
    echo $e->src . '<br>';

// Find all images, print their text with the "<>" included
foreach($html->find('img') as $e)
    echo "find outertext of img tag: <br>";
	echo $e->outertext . '<br>';

// Find the DIV tag with an id of "myId"
foreach($html->find('div#myId') as $e)
    echo $e->innertext . '<br>';

// Find all SPAN tags that have a class of "myClass"
foreach($html->find('span.myClass') as $e)
    echo $e->outertext . '<br>';

// Find all TD tags with "align=center"
foreach($html->find('td[align=center]') as $e)
    echo $e->innertext . '<br>';
    
// Extract all text from a given cell
echo $html->find('td[align="center"]', 1)->plaintext.'<br><hr>';
 */

?>