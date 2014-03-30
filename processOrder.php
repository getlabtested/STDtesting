<?php
require_once './wp-load.php';

global $wpdb;

require_once ABSPATH . 'includes/sessionInclude.php';
require_once ABSPATH . 'includes/processOrderFunctions.php';
require_once ABSPATH . 'includes/classes/PPMDPostAffiliatePro.php';
require_once ABSPATH . 'includes/classes/Transaction.php';
require_once ABSPATH . 'includes/classes/Lab.php';

$testSession = 'mytest';
ppmdTestServerSwitch($testSession);

$landingPage = $_SESSION['landingPage'];
$testRec = $_POST['testRec'];
$testCode = $_POST['testCode'];
$go = $_REQUEST['go'];

$redirPage = "customer-checkout";
if ($testCode == "p2") $redirPage = "customer-checkout-b";
else $testCode = "p1"; 
 

$athome = $_POST['athome'];
if ($athome) $athomeVal = "true";
else $athomeVal = "false";
	
$custID = $_SESSION['custID'];
$serverName = $_SERVER["SERVER_NAME"];

$_SESSION['termsChecked'] = 1;

$packType = $_SESSION['packageType'];
if ($_SESSION['packageType'] == "group") $packType = "g";
else $packType = "i";

//Get All Posted Data

if (isInTestMode()) {
	$environment = 'testing';
} else {
	$environment = 'production';
}
error_log('In:' . $environment . ' mode');

$tests_chosen = $_POST['codeString'];
$totalcost = $_POST['totalCost'];
$action = $_POST['action'];
if ($action != "processOrder" ||
    !$tests_chosen ||
    !$totalcost ||
    !isNonce($_POST['nonce'], $_POST['action'])
   ) {
error_log('action: ' . $action . ' test_chosen: ' . $tests_chosen . ' totalcost: ' . $totalcost . ' ' .  isDoubleSubmit());
  if (!$go)
  {
    echo "<script>window.location='http://$serverName'</script>\r\n";
	  die();
  }
}

$fname = $_POST['custFname'];
$_SESSION['fname'] = $fname; 
$lname = $_POST['custLname'];
$_SESSION['lname'] = $lname;

$fname = str_ireplace("'", "", $fname);
$lname = str_ireplace("'", "", $lname);

$mailing_name = $fname." ".$lname;

$gender = $_POST['gender'];
if ($gender == "Gender") $gender="Male";
$_SESSION['gender'] = $gender;
$email = $_POST['custEmail'];
$email =  trim($email);
$email = str_ireplace(" ", "", $email);
$email = strtolower($email);
$_SESSION['email'] = $email;
$dobmonth = $_POST['dobMon'];
$_SESSION['dobmonth'] = $dobmonth;
$dobday = $_POST['dobDay'];
$_SESSION['dobday'] = $dobday;
$dobyear = $_POST['dobYear'];
$_SESSION['dobyear'] = $dobyear;


// $areaCode = $_POST['areacode'];
// $_SESSION['areacode'] = $areaCode;
// $phone1 = $_POST['phone1'];
// $_SESSION['phone1'] = $phone1;
// $phone2 = $_POST['phone2'];
// $_SESSION['phone2'] = $phone2;

// $custPhone = $_POST['custPhone']."-".$areaCode."-".$phone1."-".$phone2;

$custPhone = $_POST['custPhone'];

$custPhone = str_replace("-","",$custPhone);
$custPhone = str_replace(".","",$custPhone);
$custPhone = str_replace("|","",$custPhone);
$custPhone = str_replace("(","",$custPhone);
$custPhone = str_replace(")","",$custPhone);
$custPhone = trim($custPhone);
if (strlen($custPhone) != 10) $custPhone="a";
else
{
   $areaCode = substr($custPhone, 0, 3);
   $_SESSION['areacode'] = $areaCode;
   $phone1 = substr($custPhone, 3, 3);
   $_SESSION['phone1'] = $phone1;
   $phone2 = substr($custPhone, 6, 4);
   $_SESSION['phone2'] = $phone2;
   
   $custPhone = $areaCode."-".$phone1."-".$phone2;
}


$address = $_POST['custAddress'];
$_SESSION['address'] = $address;
$city = $_POST['custCity'];
$_SESSION['city'] = $city;
$state = $_POST['custState'];
$_SESSION['state'] = $state;
$zipcode = $_POST['custZipcode'];
$_SESSION['zipcode'] = $zipcode;


$labID = $_POST['labID'];
$labName = $_POST['labName'];
$labAddr = $_POST['labAddr'];
$labCity = $_POST['labCity'];
$labState = $_POST['labState'];
$labZipcode = $_POST['labZipcode'];
$labPhone = $_POST['labPhone'];
$labHours = $_POST['labHours'];
$labType = $_POST['labType'];
if ($labType == "129") $labTypeT="l"; 
else $labTypeT="q";

$resultPref = $_POST['resultPref'];
$_SESSION['posRslt'] = $resultPref;
$resultPrefVal=0;
if ($resultPref == "Phone") $resultPrefVal=1;

$locationString = $labName."<br />".$labAddr."<br />".$labCity.",".$labState." ".$labZipcode;


$payType = $_POST['payOptVal'];
$_SESSION['payOptVal'] = $payType;

$ccnumber = $_POST['ccNum'];
$_SESSION['ccnumber'] = $ccnumber;
if ($ccnumber) 
{
    $ccNum1 = substr($ccnumber, 0,4);
    $ccNum2 = substr($ccnumber ,-4);
    $ccNumP = $ccNum1.$ccNum2;   
}

$cvv2 = $_POST['cvv2'];
$_SESSION['cvv2'] = $cvv2;

$_SESSION['expYear'] = $expYear;

$expMonth = $_POST['expMon'];
$_SESSION['expMonth'] = $expMonth;

$expYear = $_POST['expYear'];
$_SESSION['expYear'] = $expYear;

$routingNum = $_POST['routingNum'];
$_SESSION['routingNum'] = $routingNum;

$accountNum = $_POST['accountNum'];
$_SESSION['accountNum'] = $accountNum;
if ($accountNum) 
{
  $accountNum1 = substr($accountNum ,0,4);
  $accountNum2 = substr($accountNum ,-4);
  $ccNumP = $accountNum1.$accountNum2; 
}

$sessionID = $_SESSION['sessionID'];

$dob = $dobyear."-".$dobmonth."-".$dobday;
$expDate = $expMonth."/".$expYear;

if ($payType == "later" || $payType == "PNM") $payLaterVal = 1;
else $payLaterVal = 0;

$eCheckVal = 0;
if ($payType == "eCheck") $eCheckVal = 1;

$anon = $_POST['anon'];
if ($anon == "anonYes") 
{
    $anonVal = 1;
    $privacyid = 'dtc '.substr(time(),3,10);
    $_SESSION['anon'] = "anonYes";
}
else
{
   $anonVal=0;
   $privacyid = "";
   $_SESSION['anon'] = "anonNo";
}

//Determine Payment Mehtod Abbr
$paymentMethod="cc";
if ($payType == "later") $paymentMethod="pl";
if ($payType == "eCheck") $paymentMethod="ec";
if ($payType == "googleCheckout") $paymentMethod="gc";
if ($payType == "PNM") $paymentMethod="pnm";

//Handle Promo Codes
$promoCode = $_POST['promoCode'];
$_SESSION['promoCode'] = $promoCode;
$promoID = $_POST['promoID'];
$promoDiscount = $_POST['promoDiscount'];
$originalCustID = $_POST['originalCustID'];
$totalcostTemp = $totalcost;
if ($promoDiscount>=1) $totalcostTemp = $totalcostTemp-$promoDiscount;  

if (strlen($custID>4))
{
    //Get All Cust Info From DB
    //$query = "select * from user_test_results where id='$custID'";
    $query = "SELECT * FROM user_test_results WHERE id=%d";
    //if (!$result = mysql_query($query)) die ("Query failed: $query");
    $row = $wpdb->get_row($wpdb->prepare($query, $custID), ARRAY_A);//@todo Confirm that there's data
    //$row = mysql_fetch_array($result);
    $cc_validation = $row['cc_validation'];
    $pwn_creation = $row['pwn_creation'];
    
    //$query = "update user_test_results set firstname='$fname',lastname='$lname',gender='$gender',email='$email',dob='$dob',custPhone='$custPhone',athome='$athomeVal',ccNum='$ccNumP',expDate='$expDate',payLater='$payLaterVal',labname='$labName',labid='$labID',labzip='$labZipcode',labaddress='$labAddr',labcity='$labCity',labstate='$labState',labphone='$labPhone',labhours='$labHours',labtype='$labTypeT',tests_chosen='$tests_chosen',totalcost='$totalcostTemp',phoneContact='$resultPrefVal',mailing_address1='$address',mailing_city='$city',mailing_state='$state',mailing_zip='$zipcode',mailing_name='$mailing_name',eCheck='$eCheckVal',privacyid='$privacyid',paymentMethod='$paymentMethod',iflairTest='$testCode',testRec='$testRec',packType='$packType',landing_page='$landingPage',anon='$anonVal' where id='$custID'";
    //if (!$result = mysql_query($query)) die ("Query failed: $query");
    //@FIXME This is where you left off
    $currentUserData = array(
    	           'firstname'=>$fname,
    	            'lastname'=>$lname,
    	              'gender'=>$gender,
    	               'email'=>$email,
    	                 'dob'=>$dob,
    	           'custPhone'=>$custPhone,
    	              'athome'=>$athomeVal,
    	               'ccNum'=>$ccNumP,
    	             'expDate'=>$expDate,
    	            'payLater'=>$payLaterVal,
    	             'labname'=>$labName,
    	               'labid'=>$labID,
    	              'labzip'=>$labZipcode,
    	          'labaddress'=>$labAddr,
    	             'labcity'=>$labCity,
    	            'labstate'=>$labState,
    	            'labphone'=>$labPhone,
    	            'labhours'=>$labHours,
    	             'labtype'=>$labTypeT,
    	        'tests_chosen'=>$tests_chosen,
    	           'totalcost'=>$totalcostTemp,
    	        'phoneContact'=>$resultPrefVal,
    	    'mailing_address1'=>$address,
    	        'mailing_city'=>$city,
    	       'mailing_state'=>$state,
    	         'mailing_zip'=>$zipcode,
    	        'mailing_name'=>$mailing_name,
    	      'originalCustID'=>$originalCustID,
    	              'eCheck'=>$eCheckVal,
    	           'privacyid'=>$privacyid,
    	       'paymentMethod'=>$paymentMethod,
    	          'iflairTest'=>$testCode,
    	             'testRec'=>$testRec,
    	            'packType'=>$packType,
    	        'landing_page'=>$landingPage,
    	                'anon'=>$anonVal
    	    );
    $wpdb->update( 'user_test_results', $currentUserData, array( 'id' => $custID ), null, array( '%d' ));
} else {
    $referer = $_SESSION['http_referer'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $lastdate = date("Y-m-d H:i:s");
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $followUp = false;
    if ($athome) {
      $payType="now";
      $followUp = true;
    }
    
    $sourceID = $_SESSION['a_aid'];
    
    $password = createRandomPassword();
    
	$newUserInfo = array(
		         'firstname'=>$fname,
		          'lastname'=>$lname,
		         'sessionID'=>$sessionID,
		            'gender'=>$gender,
		             'email'=>$email,
		               'dob'=>$dob,
		'confirmationnumber'=>$password,
		         'custPhone'=>$custPhone,
		            'athome'=>$athomeVal,
		             'ccNum'=>$ccNumP,
		           'expDate'=>$expDate,
		                'IP'=>ip2long($ip),
		          'payLater'=>$payLaterVal,
		           'labname'=>$labName,
		             'labid'=>$labID,
		            'labzip'=>$labZipcode,
		        'labaddress'=>$labAddr,
		           'labcity'=>$labCity,
		          'labstate'=>$labState,
		          'labphone'=>$labPhone,
		          'labhours'=>$labHours,
		           'labtype'=>$labTypeT,
	 	      'tests_chosen'=>$tests_chosen,
		         'totalcost'=>$totalcostTemp,
		      'phoneContact'=>$resultPrefVal,
		  'mailing_address1'=>$address,
		      'mailing_city'=>$city,
		     'mailing_state'=>$state,
		       'mailing_zip'=>$zipcode,
		      'mailing_name'=>$mailing_name,
		    'originalCustID'=>$originalCustID,
		            'system'=>$environment,
		         'SOURCE_ID'=>$sourceID,
		         'LAST_DATE'=>$lastdate,
		           'promoID'=>$promoID,
		            'eCheck'=>$eCheckVal,
		         'privacyid'=>$privacyid,
		     'paymentMethod'=>$paymentMethod,
		      'HTTP_Referer'=>$referer,
		        'http_agent'=>$userAgent,
		              'ecom'=>$ecom,
                  'followUp'=>$followUp,
		        'iflairTest'=>$testCode,
		           'testRec'=>$testRec,
		          'packType'=>$packType,
		      'landing_page'=>$landingPage,
		              'anon'=>$anonVal
	);
	$wpdb->insert('user_test_results', $newUserInfo);
	$custID = $wpdb->insert_id;
	//echo "<!--INSERT Q: $queryInsert-->\r\n";
    $_SESSION['custID'] = $custID;
}

if ($payType=="now")
{
		//Attempt to charge card
		$responseArray = chargeCard($custID,$ccnumber,$expDate,$cvv2,0,$totalcostTemp);
		$approved = $responseArray['approved'];
		$requestID = $responseArray['requestID'];
		$replyCode = $responseArray['replyCode'];
	
	  //If Credit Card Was Approved - Attempt To Create The PWN Order
    if ($approved) {
        $pwnApproved = createPWNOrder($custID);
        //If PWN Successful Then Send Confirmation Email
        if ($pwnApproved) {
            sendConfirmationEmail($custID);
        } else {
            sendPWNErrorEmail($custID);
        }
    }
}

if ($payType=="eCheck")
{
		//Attempt to charge card
		$responseArray = chargeCheck($custID,$routingNum,$accountNum,$totalcostTemp,0);
		$approved = $responseArray['approved'];
		$requestID = $responseArray['requestID'];
		$replyCode = $responseArray['replyCode'];
	
	  //If Credit Card Was Approved - Attempt To Create The PWN Order
    if ($approved) {
        $pwnApproved = createPWNOrder($custID);
        //If PWN Successful Then Send Confirmation Email
        if ($pwnApproved) {
            sendConfirmationEmail($custID);
        } else {
            sendPWNErrorEmail($custID);
        }
    }
}
include_once TEMPLATEPATH . '/header-processOrder.php';
if ($payType == "googleCheckout") {
    include_once TEMPLATEPATH . '/processOrder-google_checkout.php';
    die();
}
include_once TEMPLATEPATH . '/processOrder-processing.php';
//=======================================START PROCESSING ORDER==========================================-->
//Get All Cust Info From DB Again
//$query = "select * from user_test_results where id='$custID'";
$query = "select * from user_test_results where id=%d";
//if (!$result = mysql_query($query)) die ("Query failed: $query");
//$row = mysql_fetch_array($result);
$row = $wpdb->get_row($wpdb->prepare($query, $custID),ARRAY_A);
$cc_validation = $row['cc_validation'];
$pwn_creation = $row['pwn_creation'];
$email = $row['email'];
$confirmationnumber = $row['confirmationnumber'];
$customernumber = $row['customernumber'];
$labname = $row['labname'];
$labaddress = $row['labaddress'];
$labcity = $row['labcity'];
$labstate = $row['labstate'];
$labzip = $row['labzip'];
$labphone = $row['labphone'];
$labid = $row['labid'];
$lastname = $row['lastname'];
$IP = $row['IP'];
$mailing_name = $row['mailing_name'];
$mailing_address1 = $row['mailing_address1'];
$mailing_address2 = $row['mailing_address2'];
$mailing_city = $row['mailing_city'];
$mailing_state = $row['mailing_state'];
$mailing_zip = $row['mailing_zip'];
$totalcharge = $row['totalcost'];

ob_flush();
flush();
sleep(3);
if ($payType == "later")
{
    sendPayLaterEmail($custID,$email,$IP);
    include_once TEMPLATEPATH . '/processOrder-pay_later_confirmation.php';
} elseif ($payType == "PNM")  {
    sendPayLaterEmail($custID,$email,$IP);
    include_once TEMPLATEPATH . '/processOrder-pay_with_cash_confirmation.php';
} else  {
    if ($cc_validation == "success") {
        include_once TEMPLATEPATH . '/processOrder-approved_msg.php';
    } else {
        $_SERVER["DECLINED"] = 1;
        include_once TEMPLATEPATH . '/processOrder-decline_msg.php';
    }
}
include_once TEMPLATEPATH . '/footer-processOrder.php';
