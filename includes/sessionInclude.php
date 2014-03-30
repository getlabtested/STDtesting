<?php
global $wpdb;
$hostName = $_SERVER['HTTP_HOST'];
$hostNameA = explode(".",$hostName);
$domainName = $hostNameA[1];
$domainName = strtolower($domainName);
$domainNameRedir = $domainName.".com";

$filepathloc = "/var/www/vhosts/secure.".$domainName.".com/httpdocs/";

require_once ABSPATH . '/includes/config.php';
//session_set_cookie_params(1,"/",$domainNameRedir,false,true); // For version 5.1 only 4 params allowed
session_set_cookie_params(1,"/",$domainNameRedir,false);
session_start();

$testMode= isInTestMode();
/**
 * A testing environment check function
 * @return bool
 * @todo Put this function in a better spot 
 */
function isInTestMode() {
    $rtn = false;
    if(!empty($_SERVER['APP_ENV'])) {
        if('testing' !== $_SERVER['APP_ENV']) {
            $rtn = false;
        } else {
            $rtn = true;
        }
    } else {
        error_log('There could be a config problem no APP_ENV found defaulting to production.');
    }
    //Check for user test
    if(!empty($_SESSION['mytest']) && true === $_SESSION['mytest']){
        $rtn = true;
    }
    return $rtn;
}

if ($_POST['action'] == "checkout")
  {
      $_SESSION['labName'] = $_REQUEST['labName'];
      $_SESSION['labAddr'] = $_REQUEST['labAddr'];
      $_SESSION['labCity'] = $_REQUEST['labCity'];
      $_SESSION['labState'] = $_REQUEST['labState'];
      $_SESSION['labZipcode'] = $_REQUEST['labZipcode'];
      $_SESSION['labID'] = $_REQUEST['labID'];
      $_SESSION['labPhone'] = $_REQUEST['labPhone'];
      $_SESSION['labHours'] = $_REQUEST['labHours'];
      $_SESSION['codeString'] = $_REQUEST['codeString'];
      $_SESSION['nameString'] = $_REQUEST['nameString'];
      $_SESSION['priceString'] = $_REQUEST['priceString'];
      $_SESSION['totalCost'] = $_REQUEST['totalCost'];
      $_SESSION['packageType'] = $_REQUEST['packageType'];
      $_SESSION['packageName'] = $_REQUEST['packageName'];
      $_SESSION['athome'] = $_REQUEST['athome'];
      $_SESSION['http_referer'] = $_REQUEST['http_referer'];
      $_SESSION['zipinput'] = $_REQUEST['zipinput'];
      $_SESSION['labType'] = $_REQUEST['labType'];
      $_SESSION['testCode'] = $_REQUEST['testCode'];
      $_SESSION['testRec'] = $_REQUEST['testRec'];
      $_SESSION['sessionID'] = $_REQUEST['sessionID'];
      $_SESSION['landingPage'] = $_REQUEST['landingPage'];
      $_SESSION['action'] = $_REQUEST['action'];
      $_SESSION['a_aid'] = $_REQUEST['a_aid'];
      $_SESSION['affPhone'] = $_REQUEST['affPhone'];
  }

if ($_REQUEST['environment'] || $testMode) 
{
    if (substr($_REQUEST['environment'],0,4) == "test" || $testMode)
    {
      $_SESSION['environment'] = "testing";
      $domainNameRedir = "test.".$domainName.".com";
    }
    else $_SESSION['environment'] = "production"; 
}
else
{
   if (isset($_SESSION['environment']))
   {
      if ($_SESSION['environment'] == "testing") $domainNameRedir = "test.".$domainName.".com";
   }
   else $_SESSION['environment'] = "production";  
}

$destroySession = $_GET['destroySession'];

//=============SECURE PAGES REDIRECT=============================================
  if(!$_SERVER['HTTPS']=="on")  
  {     
    $redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header("Location:$redirect");
  }
//=============END SECURE PAGES REDIRECT============================================= 

if ($destroySession)
{
    session_unset();
    session_destroy();
    $_SESSION = array();
}

if (isset($_SESSION['custID']))
{
    $custID =  $_SESSION['custID'];
}
else
{
	  $custID = $_REQUEST['custID'];
	  if ($custID) $_SESSION['custID'] = $custID;
}

if (isset($_SESSION['a_aid']))
{
    $a_aid =  $_SESSION['a_aid'];
}
else
{
	  $a_aid = $_REQUEST['a_aid'];
	  
	  if ($a_aid)
	  {
  	  $_SESSION['a_aid'] = $a_aid;
      //$query = "select * from sources where sourceID='$a_aid'";
      $query = "SELECT * FROM sources WHERE sourceID=%s";
  	  //if (!$result = mysql_query($query)) die ("Query failed: $query");
  	  $row = $wpdb->get_row($wpdb->prepare($query, $a_aid ), ARRAY_A);
  	  //$row = mysql_fetch_array($result);
  	  $promoID = $row['promoID'];
  	  
  	  if ($promoID)
  	  {
        //$query = "select * from promo_codes where id='$promoID'";
        $query = "SELECT * FROM promo_codes WHERE id=%d";
    	  //if (!$result = mysql_query($query)) die ("Query failed: $query");
    	  //$row = mysql_fetch_array($result);
    	  $row = $wpdb->get_row($wpdb->prepare($query, $promoID), ARRAY_A);
    	  $discountAmount = $row['discount_amount'];
    	  $discountPerc = $row['discount_percentage'];
    	  
    	  if ($discountAmount) $_SESSION['discountAmount'] = $discountAmount;
    	  if ($discountPerc) $_SESSION['discountPerc'] = $discountPerc;
      }
    } 
}

if ($destroySession)
{
    session_unset();
    session_destroy();
    $_SESSION = array();
}
?>
