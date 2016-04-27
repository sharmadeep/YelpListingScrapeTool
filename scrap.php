<?php
ini_set('max_execution_time', 90000);
function curl($url)
{
    
    $proxies = array(); // Declaring an array to store the proxy list
    
    // Adding list of proxies to the $proxies array
    $proxies[] = 'user:password@173.234.11.134:54253'; // Some proxies require user, password, IP and port number
    $proxies[] = 'user:password@173.234.120.69:54253';
    $proxies[] = 'user:password@173.234.46.176:54253';
    $proxies[] = '173.234.92.107'; // Some proxies only require IP
    $proxies[] = '173.234.93.94';
    $proxies[] = '173.234.94.90:54253'; // Some proxies require IP and port number
    $proxies[] = '69.147.240.61:54253';
    
    // Choose a random proxy
    if (isset($proxies)) { // If the $proxies array contains items, then
        $proxy = $proxies[array_rand($proxies)]; // Select a random proxy from the array and assign to $proxy variable
    }
    
    $options = Array(
        CURLOPT_HEADER => FALSE,
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_COOKIESESSION => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_FOLLOWLOCATION => TRUE,
        CURLOPT_AUTOREFERER => TRUE,
        CURLOPT_CONNECTTIMEOUT => 120,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_USERAGENT => "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1a2pre) Gecko/2008073000 Shredder/3.0a2pre ThunderBrowse/3.2.1.8",
        CURLOPT_URL => $url
    );
    
    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

 $continue = TRUE;   // Assigning a boolean value of TRUE to the $continue variable
//$url          = "http://www.yelp.com/biz/love-field-chrysler-dodge-jeep-dallas-2";
$url          = $_POST['url'];
$firstUrl          = $_POST['url'];
 while ($continue == TRUE) {
$results_page = curl($url);

$results_page = scrape_between($results_page, "<div class=\"review-list\">", "<div class=\"not-recommended ysection\">");
$separate_results = explode("<div class=\"review-content\">", $results_page);
$separate_results_author = explode("<div class=\"media-story\">", $results_page);
$results_urls = array();
foreach ($separate_results as $separate_result) {
	
	
    if ($separate_result != "") {
        $review[] =  scrape_between($separate_result, "<p itemprop=\"description\" lang=\"en\">", "</p>");
		 $rating[] =  scrape_between($separate_result, "<meta itemprop=\"ratingValue\" content=\"", "\">");
		  $date[] =  scrape_between($separate_result, "<meta itemprop=\"datePublished\" content=\"", "\">");
		  
		
    }
}
foreach ($separate_results_author as $separate_result_author) {
	
    if ($separate_result != "") {
       
		
		$author_name[] =  str_replace (" ", "", strip_tags(scrape_between($separate_result_author, "<li class=\"user-name\">", "<li class=\"user-location responsive-hidden-small\">")));
		
    }
}


if (strpos($results_page, "pagination-links_anchor\">Next")) {
            $continue = TRUE;
             $url = scrape_between($results_page, "<a class=\"u-decoration-none next pagination-links_anchor\" href=\"", "\">
                        <span");
						
        } else {
            $continue = FALSE;  // Setting $continue to FALSE if there's no 'Next' link
        }
        sleep(rand(3,5));   // Sleep for 3 to 5 seconds. Useful if not using proxies. We don't want to get into trouble.
		$lastUrl = $url;
 }
 
//while statment end
$results_page = curl($url);
$results_page_info = scrape_between($results_page,  "<div class=\"biz-page-header-left\">", "<div class=\"biz-page-header-right u-relative\">");
$separate_results_info = explode("<div class=\"rating-very-large\">", $results_page_info);

foreach ($separate_results_info as $separate_result_info) {
	
	
    if ($separate_result_info != "") {
       
		 $overall_rating[] =  scrape_between($separate_result_info, "<meta itemprop=\"ratingValue\" content=\"", "\">");
		 $no_of_reviews[] =  scrape_between($separate_result_info, "<span itemprop=\"reviewCount\">", "</span>");
		 $listing_name[] =  scrape_between($separate_result_info, "<h1 class=\"biz-page-title embossed-text-white\" itemprop=\"name\">", "</h1>");
		}
}

function scrape_between($data, $start, $end)
{
    $data = stristr($data, $start);
    $data = substr($data, strlen($start));
    $stop = stripos($data, $end);
    $data = substr($data, 0, $stop);
    return $data;
}

echo "<a href= 'index.php' >Home page</a>"."<br/>";
 echo "Listing Name - ".$listing_name['0']."<br/>";
echo "Overall Rating -".$overall_rating['1']."<br/>";
echo "Total Number of Reviews - ".$no_of_reviews['1']."<br/>";

?>

<table border="1" style="width:100%">
<tr>
    <th>Author</th>
    <th>Review</th> 
    <th>Rating</th>
	<th>Date</th>
  </tr>
<?php 
for($i=0;$i < sizeof($review); $i++)
{
?>
  <tr>
    <td><?php echo $author_name[$i]; ?></td>
    <td><?php echo $review[$i]; ?></td> 
    <td><?php echo $rating[$i]; ?></td>
	<td><?php echo $date[$i]; ?></td>
  </tr>
  <?php
 
}
?>
  </table>