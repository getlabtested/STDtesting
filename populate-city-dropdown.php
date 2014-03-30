<?php
require_once './wp-load.php';

// init db connection
global $wpdb;

$state = $_REQUEST['state'];
$sql = $wpdb->prepare("SELECT city,zip FROM nationallocations WHERE state = '$state' group by city order by city");
$cityData = $wpdb->get_results($sql, ARRAY_A);


foreach($cityData as $key=>$value)
{
    $zipcode=$value['zip'];
    if (strlen($zipcode)==4) $zipcode = "0".$zipcode; 
    $city=$value['city'];
    echo "<option value=\"$zipcode\">$city</option>\r\n"; 
}
?>
