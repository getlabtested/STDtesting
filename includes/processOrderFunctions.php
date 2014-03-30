<?php
require_once ABSPATH . '/wp-load.php';
require_once TEMPLATEPATH . '/functions-ppmd.php';
global $wpdb;

$affiliate = "getstdtested";
$athome = $_SESSION['athome'];
$serverName = $_SERVER["SERVER_NAME"];
$hostName = $_SERVER['HTTP_HOST'];
$hostNameA = explode(".",$hostName);
$domainName = $hostNameA[1];
$domainName = strtolower($domainName);
$domainNameRedir = $domainName.".com";
$filepathloc = "/var/www/vhosts/secure.".$domainName.".com/httpdocs/";  

$doNotReplyMsg = "<span style=\"font-style:italic;font-size:9pt\">* This message is being delivered from an unmonitored email box so please do not reply - Send all questions to help@dtcmd.com</span><br><br>";

function chargeCard($custID,$ccNum,$expDate,$cvv=0,$script=0,$amount)
{
    global $wpdb;
  echo"\n<!-- Starting Charge Process-->\n";
  
  //global  $environment;
  $IPaddr = $_SERVER['REMOTE_ADDR'];
  
  //$query = "select * from user_test_results where id='$custID'";
  $query = "SELECT * FROM user_test_results WHERE id=%d"; //@todo Use function here
  $row = $wpdb->get_row($wpdb->prepare($query, $custID), ARRAY_A);
  //if (!$result = mysql_query($query)) die ("Query failed: $query ");
  //$row = mysql_fetch_array($result);
	$fname = $row['firstname'];
	$lname = $row['lastname'];
	$email = $row['email'];
	$ecom = $row['ecom'];
	
	if ($script==1) {
     //$query = "select * from prescriptionData where custID='$custID' limit 1";
     $query = 'SELECT * FROM prescriptionData WHERE custID=%d LIMIT 1';
     //if (!$result = mysql_query($query)) die ("Query failed: $query ");
     //$row = mysql_fetch_array($result);
     $row = $wpdb->get_row($wpdb->prepare($query, $custID), ARRAY_A);
     if(null === $row) {
         error_log('Could not find customer in prescriptionData - CustID: ' . $custID);
         ppmdFatalError('Sorry a critical error occured. Please try agagin later.');
     }
	   $name  = $row['name'];
	   $nameArray = explode(" ",$name);
     $fname = $nameArray[0];
	   $lname = $nameArray[1];
	   $email = $row['email'];
	}
	
	$ccNum1 = substr($ccNum, 0,4);
  $ccNum2 = substr($ccNum ,-4);
  $ccNumP = $ccNum1.$ccNum2;
  
  $today = date('Y-m-d');

	$auth_net_login_id = "5X7yDQygCr6g";
	$auth_net_tran_key = "6QKqU44q3Zhz77K8";
	$description = "DTC MD";
	
	$transType = "AUTH_CAPTURE";
	
	$auth_net_url = "https://secure.authorize.net/gateway/transact.dll";
	
	$authnet_values	= array
	(
		"x_login"				=> $auth_net_login_id,
		"x_version"				=> "3.1",
		"x_delim_char"			=> "|",
		"x_delim_data"			=> "TRUE",
		"x_url"					=> "FALSE",
		"x_type"				=> $transType,
		"x_method"				=> "CC",
	 	"x_tran_key"			=> $auth_net_tran_key,
	 	"x_relay_response"		=> "FALSE",
		"x_card_num"		    => $ccNum,
		"x_exp_date"			=> $expDate,
		"x_description"			=> $description,
		"x_amount"				=> $amount,
		"x_first_name"			=> $fname,
		"x_last_name"			=> $lname,
		"custID"				=> $custID,
		"x_email"				=> $email
	);
	
	if ($cvv) $authnet_values['x_card_code'] = $cvv;
	if (isInTestMode()) {
		$authnet_values['x_test_request'] = 'TRUE';    // Just a test transaction
		$testTrans=1;
	} else {
		$authnet_values['x_test_request'] = 'FALSE';
		$testTrans=0;
	}
	
	$fields = "";
	foreach( $authnet_values as $key => $value ) $fields .= "$key=" . urlencode( $value ) . "&";
	echo"\n<!-- Past URL Encoding-->\n";
	
	$ch = curl_init($auth_net_url);
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $fields, "& " )); 
	$resp = curl_exec($ch); 
	curl_close ($ch);
	
	echo"\n<!-- $resp -->\n";
	$respArray = explode("|",$resp);
	$requestID = trim($respArray[6]);
	$replyCode = trim($respArray[2]);
	
	$approved=0;
	
	if( substr( $resp,0,1 )=="1" )	$approved=1;
	else
	{
		foreach ($respArray as $key => $value)
		{
			echo"\n<!-- $key : $value -->\n";
		}
	}
	
	$returnArray = array();
	$returnArray['approved'] = $approved;
	$returnArray['requestID'] = $requestID;
	$returnArray['replyCode'] = $replyCode;
	
	//$query = "insert into ccTransactions set custID='$custID',transType='Sale',requestID='$requestID',amount='$amount',approved='$approved',replyCode='$replyCode',testTrans='$testTrans',script='$script',ecom='$ecom'";
    $query = array(
       'custID'=>$custID,
    'transType'=>'Sale',
    'requestID'=>$requestID,
       'amount'=>$amount,
     'approved'=>$approved,
    'replyCode'=>$replyCode,
    'testTrans'=>$testTrans,
       'script'=>$script,
         'ecom'=>$ecom
    );
	//	echo"\n<!-- $query-->\n";
    //if (!$result = mysql_query($query)) die ("Query failed: $query ");
    $ccStatus = $wpdb->insert('ccTransactions', $query, array('%d', '%s', '%s', '%f', '%d', '%s', '%d', '%d', '%s'));
    //$transID = mysql_insert_id();
    $transID = $wpdb->insert_id;
    if(false == $transID) {
        ppLog('Could not insert cc transaction -- CustID ' . $custID);
        ppmdFatalError('Sorry could not finish transaction, please try agagin later.');
    }
	if ($approved) {
	    //$query = "update user_test_results set cc_validation = 'success',ccNum='$ccNumP',expDate='$expDate',IP=INET_ATON('$IPaddr') where id='$custID'";
        $query = array('cc_validation' => 'success','ccNum'=>$ccNumP,'expDate'=>$expDate,'IP'=>ip2long($IPaddr));
        $defineQuery = array('%s', '%s', '%s', '%d');
	} else {
	    //$query = "update user_test_results set ccNum='$ccNumP',expDate='$expDate',IP=INET_ATON('$IPaddr') where id='$custID'";
        $query = array('ccNum'=>$ccNumP,'expDate'=>$expDate,'IP'=> ip2long($IPaddr));
        $defineQuery = array('%s', '%s', '%d');
	}
	//if (!$result = mysql_query($query)) die ("Query failed: $query");
	$wpdb->update('user_test_results', $query, array('id' => $custID), $defineQuery, array('%d'));
	return $returnArray;
}

function chargeCheck($custID,$routingNum,$accountNum,$amount,$script=0)
{
  echo"\n<!-- Starting Charge Process-->\n";
  
  //global  $environment;
  $IPaddr = $_SERVER['REMOTE_ADDR'];
  
  if ($script==1)
  {
     //$query = "select * from prescriptionData where custID='$custID' limit 1";
     $query = 'SELECT * FROM prescriptionData WHERE custID=%d LIMIT 1';
     //if (!$result = mysql_query($query)) die ("Query failed: $query ");
     //$row = mysql_fetch_array($result);
     $row = $wpdb->get_row($wpdb->prepare($query, $custID). ARRAY_A);
	   $name  = $row['name'];
	   $nameArray = explode(" ",$name);
     $fname = $nameArray[0];
	   $lname = $nameArray[1];
	   $email = $row['email'];
  }
  else
  {
    //$query = "select * from user_test_results where id='$custID'";
    $query = 'SELECT * FROM user_test_results WHERE id=%d';
    //if (!$result = mysql_query($query)) die ("Query failed: $query ");
    //$row = mysql_fetch_array($result);
    $row = $wpdb->get_row($wpdb->prepare($query, $custID). ARRAY_A);
	  $fname = $row['firstname'];
	  $lname = $row['lastname'];
	  $email = $row['email'];
  }
  
  $today = date('Y-m-d');

	$auth_net_login_id = "5X7yDQygCr6g";
	$auth_net_tran_key = "6QKqU44q3Zhz77K8";
	$description = "DTC MD";
	
	$auth_net_url = "https://secure.authorize.net/gateway/transact.dll";
	$acctName = $fname." ".$lname;
	$authnet_values	= array
	(
		"x_login"				=> $auth_net_login_id,
		"x_tran_key"			=> $auth_net_tran_key,
		"x_version"				=> "3.1",
		"x_delim_char"			=> "|",
		"x_delim_data"			=> "TRUE",
		"x_url"					=> "FALSE",
		"x_method"				=> "ECHECK",
	 	"x_relay_response"		=> "FALSE",
	 	"x_amount"		    => $amount,
		"x_bank_aba_code"		    => $routingNum,
		"x_bank_acct_num"			=> $accountNum,
		"x_bank_acct_type"			=> "CHECKING",
		"x_bank_name"				=> "My Bank",
		"x_first_name"			=> $fname,
		"x_last_name"			=> $lname,
		"x_bank_acct_name"	=> $acctName,
		"x_echeck_type"			=> "WEB",
		"x_recurring_billing"				=> "FALSE",
		"x_email"				=> $email
	);
	
	if (isInTestMode()) {
		$authnet_values['x_test_request'] = 'TRUE';    // Just a test transaction
		$testTrans=1;
	} else {
		$authnet_values['x_test_request'] = 'FALSE';
		$testTrans=0;
	}
	
	$fields = "";
	foreach( $authnet_values as $key => $value ) $fields .= "$key=" . urlencode( $value ) . "&";
	echo"\n<!-- Past URL Encoding-->\n";
	
	$ch = curl_init($auth_net_url);
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $fields, "& " )); 
	$resp = curl_exec($ch); 
	curl_close ($ch);
	
	echo"\n<!-- $resp -->\n";
	$respArray = explode("|",$resp);
	$requestID = trim($respArray[6]);
	$replyCode = trim($respArray[2]);
	
	$approved=0;
	
	if( substr( $resp,0,1 )=="1" )	$approved=1;
	else
	{
		foreach ($respArray as $key => $value)
		{
			echo"\n<!-- $key : $value -->\n";
		}
	}
	
	$returnArray = array();
	$returnArray[approved] = $approved;
	$returnArray[requestID] = $requestID;
	$returnArray[replyCode] = $replyCode;
	
	//$query = "insert into ccTransactions set custID='$custID',transType='Sale',requestID='$requestID',amount='$amount',approved='$approved',replyCode='$replyCode',testTrans='$testTrans',script='$script'";
    $query = array(
       'custID'=>$custID,
    'transType'=>'Sale',
    'requestID'=>$requestID,
       'amount'=>$amount,
     'approved'=>$approved,
    'replyCode'=>$replyCode,
    'testTrans'=>$testTrans,
       'script'=>$script
    );
    $defineQuery = array('%d', '%s', '%s', '%f', '%d','%s', '%d', '%d');
    //if (!$result = mysql_query($query)) die ("Query failed: $query ");
	$wpdb->insert('ccTransactions', $query, $defineQuery);
    //$transID = mysql_insert_id();
    if(false == $wpdb->insert_id) {
        ppLog('Could not insert cc transaction -- CustID: ' . $custID);
        ppmdFatalError('Sorry could not complete transaction. Please try again later.');
    }
    $transID = $wpdb->insert_id;
	if ($approved) {
	    //$query = "update user_test_results set cc_validation = 'success',ccNum='$ccNumP',expDate='$expDate',IP=INET_ATON('$IPaddr'),eCheck=1 where id='$custID'";
        $query = array('cc_validation' => 'success','ccNum'=>$ccNumP,'expDate'=>$expDate,'IP'=>ip2long($IPaddr),'eCheck'=>1);
        $defineQuery = array('%s','%s', '%s', '%d', '%d');
	} else {
        //$query = "update user_test_results set ccNum='$ccNumP',expDate='$expDate',IP=INET_ATON('$IPaddr'),eCheck=1 where id='$custID'";
        $query = array('ccNum'=>$ccNumP,'expDate'=>$expDate,'IP'=>ip2long($IPaddr),'eCheck'=>1);
        $defineQuery = array('%s', '%s', '%d', '%d');
	}
	//if (!$result = mysql_query($query)) die ("Query failed: $query");
	$wpdb->update('user_test_results', $query, array('id' => $custID),$defineQuery, array('%d'));
	return $returnArray;
}


function createPWNOrder($custID)
{	
	global $affiliate, $filepathloc, $environment, $wpdb;
	
	//$query = "select * from user_test_results where id='$custID'";
	//if (!$result = mysql_query($query)) die ("Query failed: $query");
	//$row = mysql_fetch_array($result);
	$row = getUserTestResults($custID);
	$pwn_creation = $row['pwn_creation'];
    
	if ($pwn_creation == "success") return 0;
	
  $fname = $row['firstname'];
  $privacyid = $row['privacyid'];
  //if (!$privacyid) $privacyid = 'dtc '.substr(time(),3,10);
	$lname = $row['lastname'];
	$email = $row['email'];
  $gender = $row['gender'];
	$custID = $row['id'];
	$phoneContact = $row['phoneContact'];
  $dob = $row['dob'];
	$labzip = $row['labzip'];
	$labid = $row['labid'];
	$labtype = $row['labtype'];
	$system = $row['system'];
	$SOURCE_ID = $row['SOURCE_ID'];
	$dobArray = explode("-",$dob);
	$dobYear = $dobArray[0];
	$dobMonth = $dobArray[1];
	$dobDay = $dobArray[2];
	$dobF = $dobYear . $dobMonth . $dobDay;
	
	if ($labtype == "l") $privacyid=""; 
	
	$custPhone = $row['custPhone'];
	$shipaddr1 = $row['mailing_address1'];
	$shipaddr2 = $row['mailing_address2'];
	$shipName = $row['mailing_name'];
	$shipcity = $row['mailing_city'];
	$shipstate = $row['mailing_state'];
	$shipzip = $row['mailing_zip'];
	
	$athome = $row['athome'];
	
	if (!$custPhone || strlen($custPhone<10)) $custPhone="866-749-6269";
	if (!$phoneContact) $custPhone="866-749-6269";
	
	$pwnVars = getPWNVars($system,$athome,$labtype);
    $weblabendpoint = $pwnVars['weblabendpoint'];
    $weblabpassword = $pwnVars['weblabpassword'];
    $weblabusername = $pwnVars['weblabusername'];
	
	if ($athome === 'true')
	{
		$address = "$shipaddr1 $shipaddr2";
		$zip = $shipzip;
		$city = $shipcity;
		$state = $shipstate;
	}
	else 
	{
		$address = "Address Withheld";
		$zip = $labzip;
		$state = $row['labstate'];
		if ($SOURCE_ID == "USALab") 
    {
      $state = $shipstate;
      $zip = $shipzip;
    }
	}
	
	//$zipArray = getZipData($zip);
	//$state = $zipArray['state'];
	$reference = $affiliate;
	$take_tests_same_day = "true";
	
	$tests = $row['tests_chosen'];
	$testString = getTestCodes($labtype,$tests);
	if (strlen($testString) < 4)
  {
    if ($athome === 'true') {
       //$query = "insert into comments set custID='$custID',user='System',comment='Mail Home Kit',followUp=1";
       //$result = mysql_query($query);
       setComment($custID, 'Mail Home Kit', true);
    }
    
    //$query = "update user_test_results set pwn_creation='success' where id='$custID'";
		//if (!$result = mysql_query($query)) die ("Query failed: $query");
    setPwnCreationSuccess($custID);
    return 1;
  }
		
 if ($labtype == "l") $acctNum = "12004405";
 else $acctNum = "97508147";

	//Build up the request to PNW
	$req = "<customer><account_number>$acctNum</account_number><first_name>$fname</first_name><last_name>$lname</last_name><gender>$gender</gender><dob>$dobF</dob>";
	$req .= "<address>$address</address><city>$city</city><state>$state</state><test_types>$testString</test_types><home_phone>$custPhone</home_phone><zip>$zip</zip>";
	$req .= "<email>$email</email><draw_location>PSC</draw_location><psc>$labid</psc><take_tests_same_day>$take_tests_same_day</take_tests_same_day>";
	$req .= "<privacy_id>$privacyid</privacy_id><reference>$reference</reference></customer>";
	
	$url = $weblabendpoint ."/customers";
	$headers[] = "Accept: application/xml";
	$headers[] = "Content-Type: application/xml";
	$ch = curl_init(); 
	//comment out for prod
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch,CURLOPT_USERPWD, $weblabusername . ":" . $weblabpassword);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	global $pwnResponseString;
	//$response = curl_exec($ch);
	$pwnResponseString = curl_exec($ch);
	if(false === $pwnResponseString) {
	    $message = 'Call failed to: ' . $url . ' ';
	    error_log($message . curl_error($ch));
	    ppmdFatalError('Sorry, an unexpected error happen, please check back later');
	}
	/*$dateVar = date('Ymdis');
	$randNum = mt_rand(1,1000);
	$fileName = $filepathloc."temp/tempFileReq".$dateVar.$randNum.".xml";
	$fp = fopen($fileName, 'w');
  sleep(1);
  fwrite($fp, $req);
	
	$fileName = $filepathloc."temp/tempFile".$dateVar.$randNum.".xml";
	$fp = fopen($fileName, 'w');
  sleep(1);
  fwrite($fp, $response);
	*/

	global $confirmation_code;
	//$pwnResponseString = $response;
	$approved=0;
	
	//$xml = simplexml_load_file($fileName);
	$xml = simplexml_load_string($pwnResponseString);
    if(isset($xml->error)) {
        ppmdCritical('Test was not found: ' . $xml->error);
        error_log($xml->asXML);
        error_log($weblabusername . ":" . $weblabpassword);
        error_log($req);
        fatalBailOut();
        exit();
    }
	$id = (int) $xml->id;
	$requisition_number = (int) $xml->requisition_number;
	$reference = (string) $xml->reference;
	$confirmation_code = (string) $xml->confirmation_code;
	$psc = (string) $xml->psc;
	$physicians_name = (string) $xml->physicians_name;
	$physicians_upin = (string) $xml->physicians_upin;
	$physicians_npi = (int) $xml->physicians_npi;
	$physicians_license = (string) $xml->physicians_license;

	if ($confirmation_code) {
		$followUpQ="";
		//if ($athome == "true") $followUpQ=",followUp=1";
		if ($athome === 'true') {
		    $followUpQ = true;
		} else {
		    $followUpQ = false;
		}
    
        //$query = "update user_test_results set pwn_creation='success',customernumber='$id',status='approved',privacyid='$privacyid',physicianName='$physicians_name',physicianUPIN='$physicians_upin',physicianNPI='$physicians_npi',physicianLicense='$physicians_license' $followUpQ where id='$custID'";
        $user_test_results_table = 'user_test_results';
        $dataPWN = array(
        'pwn_creation'=>'success',
      'customernumber'=>$id,
              'status'=>'approved',
           'privacyid'=>$privacyid,
       'physicianName'=>$physicians_name,
       'physicianUPIN'=>$physicians_upin,
        'physicianNPI'=>$physicians_npi,
    'physicianLicense'=>$physicians_license,
           'followUp' => $followUpQ
        );
        $wpdb->update($user_test_results_table, $dataPWN, array('id' => $custID), array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d'), array('%d'));
        //if (!$result = mysql_query($query)) die ("Query failed: $query");
        $approved=1;

        if ($athome === 'true') {
            //$query = "insert into comments set custID='$custID',user='System',comment='Mail Home Kit',followUp=1";
            //$result = mysql_query($query);
            $comment = 'Mail Home Kit';
            setComment($custID, $comment, true);
        }
    //echo "\r\n<!--PWN SUCCESS-->\r\n";
    } else {
		//$query = "update user_test_results set disposition='Order - PWN Creation' where id='$custID'";
		//if (!$result = mysql_query($query)) die ("Query failed: $query");
		//echo "\r\n<!--PWN FAIL-->\r\n";
	}
	return $approved;
}


function sendPayLaterEmail($custID,$email,$IP)
{
	
	global $serverName;
	global $doNotReplyMsg;
	
  $headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	$headers .= 'From: DTC MD <orders@dtcmd.com>' . "\r\n";
	$subject = "Order Activation";
	
	$activationLink = site_url('/processorderPL.php?orderID=' . $custID . '&code=' . $IP, 'https');	
	$emailmessage = "<html><body>".$doNotReplyMsg."Thank you for your order.<br><br>Please click this activation link to complete the order process and recieve your lab order form.<br><br>Activation Link: <a href=\"" . $activationLink . "\">" . $activationLink . "</a></body></html>";
	
	sendCustomerEmail($custID, $email, $subject, $emailmessage, $headers);
}

function getTests($tests){
	if($tests == "8-pack"){
	    // KCK 6/22 removed 11361 & 11362 since they are in 11363
		$tests = "17305,498,8472,3636,3640,37694,36126";
	} 
	if(preg_match('/11361/',$tests) && preg_match('/11362/',$tests)) {
		$tests = preg_replace('/11361,/', '', $tests);
		$tests = preg_replace('/11361/', '', $tests);
		$tests = preg_replace('/11362,/', '', $tests);
		$tests = preg_replace('/11362/', '', $tests);
		//print "$tests ". strlen($tests);
		if (strlen($tests) == 0){
			$tests = "11363";
		}else{
			$tests .=",11363";
		}
	}
	if(preg_match('/17303/',$tests) && preg_match('/17304/',$tests)) {
		$tests = preg_replace('/17303,/', '', $tests);
		$tests = preg_replace('/17303/', '', $tests);
		$tests = preg_replace('/17304,/', '', $tests);
		$tests = preg_replace('/17304/', '', $tests);
		//print "$tests ". strlen($tests);
		if (strlen($tests) == 0){
			$tests = "17305";
		}else{
			$tests .=",17305";
		}
	}
	$tests = preg_replace('/,,/', ',', $tests);
	
	return $tests;
}

/**
 *
 * Returns zip code data
 * @param string $zipcode
 * @todo Not used needs to be deleted
 * @deprecated
 */
function getZipData($zipcode)
{
	$zipDataArray = array();
	
	$query = "select * from zipcodeData where zipcode='$zipcode' limit 1";
    if (!$result = mysql_query($query)) die ("Query failed: $query");
	$rowcount = mysql_num_rows($result);
	if ($rowcount)
	{
		$row = mysql_fetch_array($result);
		$zipDataArray['city'] = $row['city'];
		$zipDataArray['state'] = $row['state'];
		$zipDataArray['county'] = $row['county'];
		
		return $zipDataArray;
	}
	else
	{
		$zipcodeSub = substr($zipcode,0,4);
		$query = "select * from zipcodeData where zipcode like '$zipcodeSub%' limit 1";
    	if (!$result = mysql_query($query)) die ("Query failed: $query");
		$rowcount = mysql_num_rows($result);
		if ($rowcount)
		{
			$row = mysql_fetch_array($result);
			$zipDataArray['city'] = $row['city'];
			$zipDataArray['state'] = $row['state'];
			$zipDataArray['county'] = $row['county'];
			
			return $zipDataArray;
		}
		else
		{
			$zipcodeSub = substr($zipcode,0,3);
			$query = "select * from zipcodeData where zipcode like '$zipcodeSub%' limit 1";
	    	if (!$result = mysql_query($query)) die ("Query failed: $query");
			$rowcount = mysql_num_rows($result);
			
			$row = mysql_fetch_array($result);
			$zipDataArray['city'] = $row['city'];
			$zipDataArray['state'] = $row['state'];
			$zipDataArray['county'] = $row['county'];
			
			return $zipDataArray;
		}
	}
}


function sendPWNErrorEmail($custID)
{
	//$query = "select * from user_test_results where id='$custID'";
    //if (!$result = mysql_query($query)) die ("Query failed: $query");
	//$row = mysql_fetch_array($result);
	$row = getUserTestResults($custID, ARRAY_A);
	$firstname = $row['firstname'];
	$lastname = $row['lastname'];
	$totalcost = $row['totalcost'];
	$dob = $row['dob'];
	$gender = $row['gender'];
	$tests = $row['tests_chosen'];
	$labname = $row['labname'];
	$labaddress = $row['labaddress'];
	$labcity = $row['labcity'];
	$labstate = $row['labstate'];
	$labzip = $row['labzip'];
	$labphone = $row['labphone'];
	$labid = $row['labid'];
	$athome = $row['athome'];
	$mailing_name = $row['mailing_name'];
	$mailing_address1 = $row['mailing_address1'];
	$mailing_address2 = $row['mailing_address2'];
	$mailing_city = $row['mailing_city'];
	$mailing_state = $row['mailing_state'];
	$mailing_zip = $row['mailing_zip'];
	$confirmationnumber = $row['confirmationnumber'];
	$customernumber = $row['customernumber'];
	$custPhone = $row['custPhone'];
	$email = $row['email'];
	
	//===================COPY CONFIRMATION TO KEITH===============================
	$emailmessages = "Possible Error Creating PWN Order:\n";
	$emailmessages .= "first_name: $firstname \n";
	$emailmessages .= "last_name: $lastname \n";
	$emailmessages .= "Charge: $totalcost \n";
	$emailmessages .= "PWNID: $customernumber \n";
	$emailmessages .= "ROW_NUM: $num_rows \n";
	$emailmessages .= "gender: $gender \n";
	$emailmessages .= "dob: $dob \n";
	$emailmessages .= "address: $mailing_address1 \n";
	$emailmessages .= "city: $mailing_city \n";
	$emailmessages .= "state: $mailing_state \n";
	$emailmessages .= "zipcode: $mailing_zip \n";
	$emailmessages .= "testtypes: $tests \n";
	$emailmessages .= "homephone: $custPhone \n";
	$emailmessages .= "email: $email \n";
	$emailmessages .= "drawlocation: PSC \n";
	$emailmessages .= "psc: $labid \n";
	$emailmessages .= "lab name: $labname \n" ;
	$emailmessages .= "lab address: $labaddress \n";
	$emailmessages .= "lab city: $labcity \n";
	$emailmessages .= "lab state: $labstate \n";
	$emailmessages .= "lab zip: $labzip \n";
	$emailmessages .= "lab phone: $labphone \n";
	
	//$headers = "MIME-Version: 1.0" . "\r\n";
	//$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	$headers .= 'From: DTC MD <orders@dtcmd.com>' . "\r\n";
	$headers .= 'BCC: orders@dtcmd.com' . "\r\n";
	
	global $pwnResponseString;
	
	//need to pass the response?
	$message = $emailmessages . "\n\n" . "Response from PWN: \n" . $pwnResponseString ."\n" ;
	$subject = "Possible Error Creating PWN Record";
	
	//mail('orders@dtcmd.com,help@dtcmd.com', $subject, $message);		
	//mail('kknohl@dtcmd.com', $subject, $message, $headers);
	ppmdSendAdminEmail($subject, $message, $headers);
}

/**
 *
 * Send confirmation email to customer.
 * @param int $custID
 */
function sendConfirmationEmail($custID) {
	global $serverName, $doNotReplyMsg, $wpdb;
  
  //$query = "select * from user_test_results where id='$custID'";
  $query = 'SELECT * FROM user_test_results WHERE id=%d';
  //if (!$result = mysql_query($query)) die ("Query failed: $query");
	//$row = mysql_fetch_array($result);
	$row = $wpdb->get_row($wpdb->prepare($query, $custID), ARRAY_A);
	if(false === $row) {
	    error_log('Could not find customer in user_test_results -- CustID: ' . $custID);
	    ppmdFatalError('Sorry could not continue transaction, please try again later.');
	}
	$firstname = $row['firstname'];
	$lastname = $row['lastname'];
	$totalcost = $row['totalcost'];
	$dob = $row['dob'];
	$gender = $row['gender'];
	$tests = $row['tests_chosen'];
	$labname = $row['labname'];
	$labaddress = $row['labaddress'];
	$labcity = $row['labcity'];
	$labstate = $row['labstate'];
	$labzip = $row['labzip'];
	$labphone = $row['labphone'];
	$labhours = $row['labhours'];
	$labid = $row['labid'];
	$labtype = $row['labtype'];
	$athome = $row['athome'];
	$mailing_name = $row['mailing_name'];
	$mailing_address1 = $row['mailing_address1'];
	$mailing_address2 = $row['mailing_address2'];
	$mailing_city = $row['mailing_city'];
	$mailing_state = $row['mailing_state'];
	$mailing_zip = $row['mailing_zip'];
	$confirmationnumber = $row['confirmationnumber'];
	$customernumber = $row['customernumber'];
	$custPhone = $row['custPhone'];
	$email = $row['email'];
	$paymentMethod = $row['paymentMethod'];
	$source = $row['SOURCE_ID'];
	$ecom = $row['ecom'];
	
	if ($ecom === "gst" || 'std' == $ecom) {
	    $orderAttchedTxt = "YOUR LAB ORDER AND LAB INSTRUCTIONS ARE ATTACHED TO THIS EMAIL. YOU MUST OPEN AND PRINT BOTH ATTACHMENTS AND TAKE THEM WITH YOU TO THE LAB.";
	}
	else {
	    $orderAttchedTxt = "YOUR LAB ORDER IS ATTACHED TO THIS EMAIL. YOU MUST OPEN AND PRINT THE ATTACHMENT AND TAKE IT WITH YOU TO THE LAB.";
	}
	
	$cashOptionTxt = "";
  
  if ($paymentMethod == "pnm") {
     //$query2 = "select slip_url from pnm_data where custID='$custID'";
     $query2 = 'SELECT slip_url FROM pnm_data WHERE custID=%d';
     //$result2 = mysql_query($query2);
     //$row2 = mysql_fetch_array($result2);
     $row2 = $wpdb->get_row($wpdb->prepare($query2,$custID), ARRAY_N);
     $slip_url = $row2[0];
     
     $cashOptionTxt = "<span style=\"color:blue\">CASH PAYMENT OPTION:</span> You have chosen to pay cash for testing. Please go to this link ".$slip_url." to retrieve your payment slip and to see the closest locations. You may test immediately, however payment is required before results can be viewed.<br><br>";
  }
  
  $resultsText = "";
	
	$locationString = $labname."<br>".$labaddress."<br>".$labcity.",".$labstate." ".$labzip."<br>Hours: ".$labhours."<br>Phone: ".$labphone;
	
	//===================COPY CONFIRMATION TO KEITH===============================
	$subject;	
	$emailmessages = "A New customer order was created with the following:\n";
	if ($athome === 'true')
	{
		$emailmessages .= "This was an at home kit.";
		$subject = "Home - "; 
	}
	$emailmessages .= "first_name: $firstname \n";
	$emailmessages .= "last_name: $lastname \n";
	$emailmessages .= "Charge: $totalcost \n";
	$emailmessages .= "PWNID: $customernumber \n";
	$emailmessages .= "ROW_NUM: $num_rows \n";
	$emailmessages .= "gender: $gender \n";
	$emailmessages .= "dob: $dob \n";
	$emailmessages .= "address: $mailing_address1 \n";
	$emailmessages .= "city: $mailing_city \n";
	$emailmessages .= "state: $mailing_state \n";
	$emailmessages .= "zipcode: $mailing_zip \n";
	$emailmessages .= "testtypes: $tests \n";
	$emailmessages .= "homephone: $custPhone \n";
	$emailmessages .= "email: $email \n";
	$emailmessages .= "drawlocation: PSC \n";
	$emailmessages .= "psc: $labid \n";
	$emailmessages .= "lab name: $labname \n" ;
	$emailmessages .= "lab address: $labaddress \n";
	$emailmessages .= "lab city: $labcity \n";
	$emailmessages .= "lab state: $labstate \n";
	$emailmessages .= "lab zip: $labzip \n";
	$emailmessages .= "lab phone: $labphone \n";
	
	$headers .= 'From: DTC MD <orders@dtcmd.com>' . "\r\n";
	$headers .= 'BCC: orders@dtcmd.com' . "\r\n";
	
	global $pwnResponseString, $filepathloc;
	
	//need to pass the response?
	$message = "New customer record created.\n" . $emailmessages . "\n\n" . "Response from PWN: \n" . $pwnResponseString ."\n" ;
	$subject .= "New customer record created";
	
	$windowPeriodText="";
	
	if ($ecom == "gst" || 'std' == $ecom) {
  $windowPeriodText = "IMPORTANT: Please read the following information:<br><br>
		
		<b>Chlamydia & Gonorrhea Tests:</b><br>
		In order to ensure accurate results do not urinate 1 hour before getting tested.<br><br>
		
		<b>Window Period:</b><br>
		When testing for STDs, as with any infection including cold and flu, there is a period of time between your exposure and when the infection is detectable.  This time period is called the \"window period\".  The window period for STD tests are:
		<ul><li>Urine-based tests (Chlamydia & Gonorrhea) are usually 3-5 days (7 at most)</li>
		<li>Blood-based tests (Herpes, Syphilis, & Hepatitis) are 3-6 weeks</li>
		<li>HIV can be between 3-6 months</li></ul>
		<br />
		If you receive a negative result and your time since exposure is within the window period, please understand there is a possibility that the infection currently is undetectable.<br/>
		<br />";
	}
	
	ppmdSendAdminEmail($subject, $message, $headers);
	echo "\r\n<!--Sent Copy Email-->\r\n";
	//===================END COPY CONFIRMATION TO KEITH===============================
	
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	$headers .= 'From: DTC MD <orders@dtcmd.com>' . "\r\n";
	$headers .= 'BCC: orders@dtcmd.com' . "\r\n";
	$subject = "Order Confirmation";
	
	//===============AT HOME EMAIL CONFIRMATION=============================================================
	if ($athome === 'true')
	{
		$urine=0;
		$blood=0;
    
    if(preg_match('/11361/',$tests) || preg_match('/11362/',$tests) || preg_match('/19550/',$tests)) $urine=1;
    if(preg_match('/pp102/',$tests) || preg_match('/pp101/',$tests)) $blood=1; 
    if ($urine)
		{
        if ($blood) $subject = "Order Confirmation - 1 of 2"; 
        $htmlmsg = "<html>
    		<head>
    		<title>Order Confirmation</title>
    		</head>
    		<body>
    		".$doNotReplyMsg."
    		<p><b>CONFIRMATION EMAIL - HOME BASED STD TESTING URINE/SWAB TEST</b></p>
        <p>Dear " . $firstname .":<br /><br />
    		Thank you for your order. Your confirmation number is: " . $confirmationnumber .". Please keep this number because you will need it to access your results.
    		<br><br>
    		To complete the testing process:<br><br>
    		<ol>
    		<li><b>Collect Sample when your testing kit arrives, keeping in mind:</b>
    		    <ul>
    		    <li>Men provide a urine sample. Women provide a swab sample.</li>
    		    <li>When testing for STDs - as with any infection - there is a window period of time between exposure and when the infection is detectable. If you receive a negative result and exposure is within the window period, understand there is a possibility the infection is currently undetectable. Estimated window periods are 3-7 days for urine or swab-based tests.</li>
    		    <li>For urine-based tests, do not urinate 1 hour before testing. Do NOT clean your genital area prior to providing the sample.</li>
    		    <li>See package insert for specific instructions.</li>
            </ul>
            <br>
        </li>
        <li><b>Mail Sample in enclosed pre-paid box</b><br><br></li>
        <li><b>Get results:</b><br>Approximately 3 days after we receive your sample you will receive a notification by email or phone (your choice during the ordering process). Simply log into your getSTDtested.com account using your confirmation name to view results online. If at anytime you have a question regarding the status of your test, e-mail or call us directly.<br><br></li>
    		<li><b>Get answers:</b><br>With every test ordered through getSTDtested.com, access our in-house Physician network free-of-charge through a free phone consultation. E-mail or call our counselors anytime to talk live to our counselors or to schedule a physician appointment.<br><br></li>
        </ol>
    		      	
    		Thank you,<br><br>
    		DTC MD<br />
    		866-749-6269<br />
    		help@dtcmd.com
    		</body>
    		</html>";
        sendCustomerEmail($custID, $email, $subject, $htmlmsg, $headers);
    }
		
		if ($blood)
		{
        if ($urine) $subject = "Order Confirmation - 2 of 2";
        $htmlmsg = "<html>
    		<head>
    		<title>Order Confirmation</title>
    		</head>
    		<body>
    		".$doNotReplyMsg."
    		<p><b>CONFIRMATION EMAIL - HOME BASED STD TESTING HIV TEST</b></p>
        <p>Dear " . $firstname .":<br><br>
    		Thank you for your order. To complete the testing process:<br><br>
    		<ol>
    		<li><b>Collect Sample when your testing kit arrives, keeping in mind:</b>
    		    <ul>
    		    <li>When testing for STDs - as with any infection - there is a window period of time between exposure and when the infection is detectable. If you receive a negative result and exposure is within the window period, understand there is a possibility the infection is currently undetectable. The window period for HIV can be as little as 3 months and as many as 6 months.</li>
    		    <li>See package insert for specific instructions.</li>
            </ul>
            <br>
        </li>
        <li><b>Mail Sample in enclosed pre-paid envelope back to our lab.</b><br><br></li>
        <li><b>Get results:</b><br>See package insert for specific instructions for obtaining your result. This test is 100% anonymous so no one receives your results but you, not getstdtested.com and no public health department. Only you can access your results by calling in with the secure code included in your package.<br><br></li>
    		<li><b>Get answers:</b><br>The home HIV also has a dedicated 800-number that you can use to discuss results when you obtain them.<br><br></li>
        </ol>
    		      	
    		Thank you,<br><br>
    		DTC MD<br />
    		866-749-6269<br />
    		help@dtcmd.com
    		</body>
    		</html>";
        sendCustomerEmail($custID, $email, $subject, $htmlmsg, $headers);
    }
	}
	//===============END AT HOME EMAIL CONFIRMATION=============================================================
	else
	{ 
    //Lab Order Email Confirmation
		$htmlmsg = "<html>
		<head>
		<title>Order Confirmation</title>
		</head>
		<body>
		".$doNotReplyMsg."
		<p>".$firstname.",<br/>
		<br/>
		
		Thank you for your order.<br/><br/>
		
		Your confirmation number is <b>" . $confirmationnumber ."</b>. Please keep this confirmation number as you will need it to access your results.<br/><br/>
		
		<span style=\"color:red\">".$orderAttchedTxt."</span><br/><br/>
		
		".$cashOptionTxt."
    
    <b>Your Lab Information:<br></b>".
		$locationString."<br><br>
		
		".$windowPeriodText."
		
		<b>Please find all your lab information within your attached requisition form. Open and print the attached requisition and bring it with you to the lab.</b><br />
		<br />
		For your added convenience, we recommend you visit the testing center in the afternoon and avoid the morning rush or call the lab directly to make an appointment.  
    <br/><br/>
		If you would like to talk to someone before you get tested, need assistance finding an alternative Patient Service Center, retrieving your lab requisition form, or have any other questions - please email or call us at the number below and we will be glad to assist.<br/>
		<br/>
		<b>Results:</b> Your results will be ready approximately 3 days after you provide your sample. We will notify you when they are available and how to view them. Occasionally results can take longer or may not be automatically delivered. If you do not receive a notification from us within 3 business days, please contact us.<br/>
		<br/>
		Thank you,<br/>
		<br/>
		DTC MD<br/>
		866-749-6269<br/>
		help@dtcmd.com</p>
		</body>
		</html>";
		
		$to      =  $email;
		$from    = "DTC MD <orders@dtcmd.com>";
		$subject = "Order Confirmation";
		$message = $htmlmsg;
		
        $pdfReqForm = downloadReqForm($custID);
		
        //$filepath = $filepathloc."temp/Requisition-".$confirmationnumber.".pdf";
        $filename = "Requisition-".$confirmationnumber.".pdf";
    
    if ($labtype == "l") {
        $filepath2 = ABSPATH . "pdfs/LabCorp-Instructions.pdf";
        $filename2 = "LabCorp-Instructions.pdf";
    } else {
        $filepath2 = ABSPATH . "pdfs/DTC_QuestLab_Instructions.pdf";
        $filename2 = "DTC_QuestLab_Instructions.pdf";
    }
		
		// Obtain file upload vars     
		$fileatt_name = $filename;
		$fileatt_name2 = $filename2;
		
		$headers = "From: $from";
		
        // Read the file to be attached ('rb' = read binary)
        //$file = fopen($filepath,'rb');
        //$data = fread($file,filesize($filepath));
        //fclose($file);
		 
		 $file2 = fopen($filepath2,'rb');
        if(false === $file2) {
            ppmdCritical('Could not open pdf file: ' . $filepath2);
        }
		 $data2 = fread($file2,filesize($filepath2));     
		 fclose($file2);
		 
		 // Generate a boundary string     
		 $semi_rand = md5(time());     
		 $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";     
		      
		 // Add the headers for a file attachment     
		 $headers .= "\nMIME-Version: 1.0\n" .     
		             "Content-Type: multipart/mixed;\n" .     
		             " boundary=\"{$mime_boundary}\"";
		
		$headers .= "\nBCC: orders@dtcmd.com";
		
		 // Add a multipart boundary above the plain message     
		 $message = "This is a multi-part message in MIME format.\n\n" .     
		            "--{$mime_boundary}\n" .     
		            "Content-Type: text/html; charset=\"iso-8859-1\"\n" .     
		            "Content-Transfer-Encoding: 7bit\n\n" .     
		            $message . "\n\n";
		
		 // Base64 encode the file data     
        //$data = chunk_split(base64_encode($data));
        $data = chunk_split(base64_encode($pdfReqForm));
		 $data2 = chunk_split(base64_encode($data2));
		
		 // Add file attachment to the message
     if ($ecom == "gst" || 'std' == $ecom) 
     {     
        $message .= "--{$mime_boundary}\n" .     
		             "Content-Type: {application/pdf};\n" .     
		             " name=\"{$fileatt_name}\"\n" .     
		             "Content-Disposition: attachment;\n" .     
		             " filename=\"{$fileatt_name}\"\n" .     
		             "Content-Transfer-Encoding: base64\n\n" .     
		             $data . "\n\n" .    
		             "--{$mime_boundary}\n" .
		             "Content-Type: {application/pdf};\n" .     
		             " name=\"{$fileatt_name2}\"\n" .     
		             "Content-Disposition: attachment;\n" .     
		             " filename=\"{$fileatt_name2}\"\n" .     
		             "Content-Transfer-Encoding: base64\n\n" .     
		             $data2 . "\n\n" .   
		             "--{$mime_boundary}--\n";
		}
		else
		{
       $message .= "--{$mime_boundary}\n" .     
		             "Content-Type: {application/pdf};\n" .     
		             " name=\"{$fileatt_name}\"\n" .     
		             "Content-Disposition: attachment;\n" .     
		             " filename=\"{$fileatt_name}\"\n" .     
		             "Content-Transfer-Encoding: base64\n\n" .     
		             $data . "\n\n" .  
		             "--{$mime_boundary}--\n";
    }
		
		// Send the message     
		sendCustomerEmail($custID, $to, $subject, $message, $headers);
		echo "\r\n<!--Sent Conf Email-->\r\n";
	}
}

/**
 * get PDF request form
 *
 * @param int $custID
 * @return string PDF as string and base 64 decoded
 */

function downloadReqForm($custID)
{
	global $filepathloc;
	
	//$query = "select * from user_test_results where id='$custID'";
    //if (!$result = mysql_query($query)) die ("Query failed: $query");
	//$row = mysql_fetch_array($result);
	$row = getUserTestResults($custID, ARRAY_A);
	
	$privacyid = $row['privacyid'];
	$labtype = $row['labtype'];
	$system = $row['system'];
	$athome = $row['athome'];
	$confCode = $row['confirmationnumber']; 
	$tempArray = explode(" ",$privacyid);
	$privacyid = trim($tempArray[1]);
	
	$pwnVars = getPWNVars($system,$athome,$labtype);
    $weblabendpoint = $pwnVars['weblabendpoint'];
    $weblabpassword = $pwnVars['weblabpassword'];
    $weblabusername = $pwnVars['weblabusername'];
	
	//Get the PWN record for the user 		
	$url = $weblabendpoint ."/customers/". $row['customernumber'] ."?include=everything";
	//print $url ."<br>";
	
	//echo "\r\n<!--DOWNLOAD URL: $url-->\r\n";
	
	$headers[] = "Accept: application/xml";
	$headers[] = "Content-Type: application/xml";
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_POST,0);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch,CURLOPT_USERPWD, $weblabusername . ":" . $weblabpassword);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	//curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	$response = curl_exec($ch);
    if(false === $response) {
        ppmdCritical('Error in response from: ' . $url . ' error: ' . curl_error($ch));
        return false;
    }
    $xml = new SimpleXMLElement($response);
    //$filename = $filepathloc."temp/Requisition-".$confCode.'.pdf';
    if(isset($xml->sameday_requisition)) {
        $file = base64_decode($xml->sameday_requisition);
    } else {
        ppmdCritical('Did not find sameday_requisition in XML that holds PDF requisition form. cust#: ' . $row['customernumber']);
        return false;
    }
    //$fh = fopen ($filename, "a+");
    //fwrite ($fh,$file);
    //fclose($fh);
    return $file;
}


function outputBufferText()
{
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
	echo"<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->\n";
}

function getTestNames($testselected){
	$texttests='';
	
	if (preg_match('/11361/',$testselected) ||	preg_match('/17303/',$testselected)){
	  $texttests.="<li>Chlamydia</li>";
	}
	if (preg_match('/498/',$testselected)){
	  $texttests.="<li>Hepatitis B</li>";
	}
	if (preg_match('/37677/',$testselected) || preg_match('/8472/',$testselected)){
	  $texttests.="<li>Hepatitis C</li>";
	}
	if (preg_match('/11362/',$testselected) ||	preg_match('/17304/',$testselected)){
	  $texttests.="<li>Gonorrhea</li>";
	}
	if (preg_match('/37694/',$testselected)){
	  $texttests.="<li>HIV</li>";
	}
	if (preg_match('/799/',$testselected)){
	  $texttests.="<li>Syphilis</li>";
	}
	if (preg_match('/36126/',$testselected)){
	  $texttests.="<li>Syphilis</li>";
	}
	if (preg_match('/3636/',$testselected)){
	  $texttests.="<li>Herpes 1</li>";
	}
	if (preg_match('/3640/',$testselected)){
	  $texttests.="<li>Herpes 2</li>";
	}
	if (preg_match('/11363/',$testselected) || preg_match('/17305/',$testselected)){
      $texttests.="<li>Chlamydia & Gonorrhea</li>";
	}
	if (preg_match('/19550/',$testselected)){
	  $texttests.="<li>Trichomoniasis</li>";
	}
	if (preg_match('/7600,496,484/',$testselected)){
	  $texttests.="<li>Complete Heart Panel</li>";
	}
	if (preg_match('/pp101/',$testselected)){
	  $texttests.="<li>HIV</li>";
	}
	if (preg_match('/pp102/',$testselected)){
	  $texttests.="<li>HIV (Expedited)</li>";
	}
	if (preg_match('/8401/',$testselected)){
	  $texttests.="<li>HIV (PCR)</li>";
	}
	
		
	return $texttests;
}

function sendPrescriptionRequestEmail()
{
	global $serverName;
  $headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	$headers .= 'From: DTC MD <orders@dtcmd.com>' . "\r\n";
	//$headers .= 'BCC: help@dtcmd.com' . "\r\n";
	$subject = "New Prescription Request";
	
	$emailmessage = "<html><body>A new prescription request has been entered in our system.<br><br>Please login to <a href=\"https://$serverName/admin\">https://$serverName/admin</a> to view all pending requests. Thank you.</body></html>";
	
	$subject = "New Prescription Request";
	
	$email = "chiberg1@aol.com";
	
	sendCustomerEmail(null, $email, $subject, $emailmessage, $headers);
}

function sendPrescriptionConfEmail($email,$patientPhone)
{
	global $doNotReplyMsg;
  
  $headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	$headers .= 'From: DTC MD <orders@dtcmd.com>' . "\r\n";
	//$headers .= 'BCC: help@dtcmd.com' . "\r\n";
	$subject = "Prescription Service Confirmation";
  
	$emailmessage = "<html><body>".$doNotReplyMsg."Thank you for your prescription order.<br><br>DTC MD takes pride in delivering affordable and quick prescription service.<br><br>One of our physicians will call in your prescription and you will be notified by email at $email when completed.<br><br>Please allow up to 1 business day for the prescription to be called in.<br><br>Please call us at 866-749-6269 if you have any questions or concerns.</body></html>";
	
	sendCustomerEmail(null, $email, $subject, $emailmessage, $headers);
}

/**
 *
 * Create Pay Near Me order
 * @param int $custID
 */
function createPNMOrder($custID) {
    global $wpdb;
    //$query = "select totalcost,email,labzip from user_test_results where id='$custID'";
    $query = "SELECT totalcost,email,labzip FROM user_test_results WHERE id=%d";
    //if (!$result = mysql_query($query)) die ("Query failed: $query");
    //$row = mysql_fetch_array($result);
    $row = $wpdb->get_row($wpdb->prepare($query, $custID), ARRAY_N);
    if(null === $row) {
        error_log('Could not find user in user_test_results - CustID: ' . $custID);
        ppmdFatalError('Sorry but a critical failure happened try again later.');
    }
	$totalcost = $row[0];
	$email = $row[1];
	$labzip = $row[2];

  $curTimestamp = time();
   
  $pnm_values	= array 
	(                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
		"order_amount"  => $totalcost,
		"order_currency"	=> "USD",
		"order_type"	=> "exact",
		"site_customer_email"	=> $email,
    "site_customer_name" => "DTCMD Customer",                                                           
		"site_customer_identifier"	=> $custID,
		"site_customer_postal_code" => $labzip,
		"timestamp"	=> $curTimestamp,
		"version"	=> "v1.2",
		"site_identifier"	=> "S1209288194"
	);
  ksort($pnm_values);
  
  $sigStr="";
  foreach ($pnm_values as $key => $value)
  {
     $sigStr.=$key.$value; 
  }
  $sigStr .= "44a31bcf1f41bf39";
  
  $signature = md5($sigStr);
  
  $pnm_values['signature'] = $signature;
  //print_r($pnm_values);
	$fields = "";
	foreach( $pnm_values as $key => $value ) $fields .= "$key=" . urlencode( $value ) . "&";
	
	if(empty($_SERVER['PNM_URL'])) {
		$paynearme_host = 'https://paynearme.com/api/order/create';
	} else {
		$paynearme_host = $_SERVER['PNM_URL'];
	}

	$ch = curl_init($paynearme_host);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $fields, "& " )); 
    $resp = curl_exec($ch);
    if(false === $resp) {
        $message = "Customer ID: " . $custID . " Curl response to " . $paynearme_host . " failed. Curl error: " . curl_error($ch);
        ppmdCritical($message, true);
        $message = "System unavailable at this moment, please try again later.";
        ppmdFatalError($message);
        exit();
    }
    if(empty($resp)) {
        $message = "Customer ID: " . $custID . " Curl response to " . $paynearme_host . " failed. No content. Curl error: " . curl_error($ch);
        ppmdCritical($message, true);
        $message = "System unavailable at this moment, please try again later.";
        ppmdFatalError($message);
        exit();
    }
	curl_close ($ch);
	
	//echo "\r\n<!--$resp-->\r\n";
	global $pnmResponseString; 
	$pnmResponseString = $resp;
	$xml = simplexml_load_string($resp);
	
	$resultStatus = $xml->attributes();
  
  if ($resultStatus == "ok") {
    $pnm_order_identifier = $xml->order[0]->attributes()->pnm_order_identifier;
    $pnm_customer_identifier = $xml->order[0]->customer->attributes()->pnm_customer_identifier;
    $slip_pdf_url = $xml->order[0]->slip->attributes()->slip_pdf_url;
    $slip_url = $xml->order[0]->slip->attributes()->slip_url;
    
    //$query = "insert into pnm_data set custID='$custID',pnm_order_identifier='$pnm_order_identifier',pnm_customer_identifier='$pnm_customer_identifier',slip_pdf_url='$slip_pdf_url',slip_url='$slip_url'";
    $query = array(
                     'custID'=>$custID,
       'pnm_order_identifier'=>$pnm_order_identifier,
    'pnm_customer_identifier'=>$pnm_customer_identifier,
               'slip_pdf_url'=>$slip_pdf_url,
                   'slip_url'=>$slip_url);
    //mysql_query($query);
    //@todo MCH Everythings is saved to a string but there are some pure ints in the data, the table is defined wrong.
    //@todo MCH This table could also use a primary key id 
    $pnmStatus = $wpdb->insert('pnm_data', $query,
    	    array('%s', '%s', '%s', '%s', '%s'));
    if(!$pnmStatus) {
        ppmdCritical('Pay near me order could not be saved. ' . $custID .
       ' pnm_order_identifier: ' . $pnm_order_identifier .
    ' pnm_customer_identifier: ' . $pnm_customer_identifier .
               ' slip_pdf_url: ' . $slip_pdf_url .
                   ' slip_url: ' . $slip_url);
        ppmdSendAdminEmail('Pay near me order failed.', 'Could not insert data into pnm_data. CustID:' . $custID );
        sendPMNErrorEmail($custID);
    }
  } else {
      sendPMNErrorEmail($custID);
  }
}

function sendPMNErrorEmail($custID)
{
	$emailmessages = "Possible Error Creating PayNearMe Order For CustID: $custID\n";

	$headers = 'From: DTC MD <orders@dtcmd.com>' . "\r\n";
	
	global $pnmResponseString;
	
	$message = $emailmessages . "\n\n" . "Response from PNM: \n" . $pnmResponseString ."\n" ;
	$subject = "Possible Error Creating PayNearMe Order";
	
	ppmdSendAdminEmail($subject, $message, $headers);
}


function getTestCodes($labType,$testString)
{
   $testString = preg_replace('/pp101,/', '', $testString);
   $testString = preg_replace('/pp101/', '', $testString);
   $testString = preg_replace('/pp102,/', '', $testString);
   $testString = preg_replace('/pp102/', '', $testString);
   
   if ($labType == "q")
   {
        if(preg_match('/11361/',$testString) && preg_match('/11362/',$testString)) 
        {
      		$testString = preg_replace('/11361,/', '', $testString);
      		$testString = preg_replace('/11361/', '', $testString);
      		$testString = preg_replace('/11362,/', '', $testString);
      		$testString = preg_replace('/11362/', '', $testString);
      		//print "$tests ". strlen($tests);
      		if (strlen($testString) == 0) $testString = "11363";
      		else $testString .=",11363";
	     }
	     
    	if(preg_match('/17303/',$testString) && preg_match('/17304/',$testString)) 
      {
    		$testString = preg_replace('/17303,/', '', $testString);
    		$testString = preg_replace('/17303/', '', $testString);
    		$testString = preg_replace('/17304,/', '', $testString);
    		$testString = preg_replace('/17304/', '', $testString);
    		//print "$testString ". strlen($testString);
    		if (strlen($testString) == 0) $testString = "17305";
    		else $testString .=",17305";
    	}
    	
    	//$testString = preg_replace('/37677/', '34024', $testString);
    	
	    $testString = preg_replace('/,,/', ',', $testString);
	    return $testString;
   }
   else
   {  
      $testString = str_replace("17303", "188078", $testString); //Chlamydia
      $testString = str_replace("498", "006510", $testString);  //Hep B
      $testString = str_replace("8472", "143991", $testString); //Hep C
      $testString = str_replace("17304", "188086", $testString); //Gonnorhea
      $testString = str_replace("37694", "083824", $testString); //HIV
      $testString = str_replace("799", "012005", $testString); //Syphillis
      $testString = str_replace("36126", "012005", $testString);//Syphillis
      $testString = str_replace("3636", "164897", $testString); //Herpes 1
      $testString = str_replace("3640", "163147", $testString); //Herpes 2
      $testString = str_replace("8401", "550430", $testString); //HIV PCR
      //$testString = str_replace("17171", "163147", $testString); //Herpes Inhibition
      
      $hivPCR=0;
      if (preg_match('/550430/',$testString)) $hivPCR=1;
      
        if(preg_match('/188078/',$testString) && preg_match('/188086/',$testString) && preg_match('/006510/',$testString) && preg_match('/143991/',$testString) && preg_match('/083824/',$testString) && preg_match('/012005/',$testString) && preg_match('/164897/',$testString) && preg_match('/163147/',$testString)) 
        {
      		 $testString = "343575"; //8 Pack
           if ($hivPCR) $testString=$testString.",550430"; //with HIV PCR
      		 return $testString;
    		}
    		else if (preg_match('/188078/',$testString) && preg_match('/188086/',$testString) && preg_match('/083824/',$testString) && preg_match('/163147/',$testString))
        {
           $testString = preg_replace('/188078,/', '', $testString);
        	 $testString = preg_replace('/188078/', '', $testString);
        	 $testString = preg_replace('/188086,/', '', $testString);
        	 $testString = preg_replace('/188086/', '', $testString);
        	 $testString = preg_replace('/083824,/', '', $testString);
        	 $testString = preg_replace('/083824/', '', $testString);
        	 $testString = preg_replace('/163147,/', '', $testString);
        	 $testString = preg_replace('/163147/', '', $testString);
        	 if (strlen($testString) == 0) $testString = "365235";
      		 else $testString .=",365235";
      		 $testString = preg_replace('/,,/', ',', $testString);
      		 if ($hivPCR) $testString=$testString.",550430"; //with HIV PCR
           return $testString; //4 Pack
        }
        else if (preg_match('/188078/',$testString) && preg_match('/188086/',$testString))
        {
           $testString = preg_replace('/188078,/', '', $testString);
        	 $testString = preg_replace('/188078/', '', $testString);
        	 $testString = preg_replace('/188086,/', '', $testString);
        	 $testString = preg_replace('/188086/', '', $testString);
        	 if (strlen($testString) == 0) $testString = "183194";
      		 else $testString .=",183194";
      		 $testString = preg_replace('/,,/', ',', $testString);
      		 if ($hivPCR) $testString=$testString.",550430"; //with HIV PCR
           return $testString; //Chlamydia and Gonnorhea
        }
        else return $testString;
    
   }   
}

function createRandomPassword() 
{      
  $chars = "abcdefghijkmnopqrstuvwxyz023456789";     
  srand((double)microtime()*1000000);     
  $i = 0;     
  $pass = '' ;      
  while ($i <= 7) 
  {         
  $num = rand() % 33;         
  $tmp = substr($chars, $num, 1);         
  $pass = $pass . $tmp;         $i++;     
  }      
  return $pass;  
}

function getPWNVars($env,$athome,$labtype)
  {
      $pwnVars = array();
      
      if ($env == "testing")
      {
         $weblabendpoint = "https://labs-staging.medivo.com";
         if ($athome === 'true') $weblabusername = "gst2";
         else
         {
           if ($labtype == "l") $weblabusername = "dtcmdlc";
           else $weblabusername = "gst"; 
         }  
      }
      else
      {
         $weblabendpoint = "https://labs.medivo.com";
         $weblabusername = "getstdtest";
/*
         if ($athome === 'true') $weblabusername = "getstdtest2";
         else
         {
           if ($labtype == "l") $weblabusername = "dtcmdlc";
           else $weblabusername = "getstdtest"; 
         }  
*/
      }
      
      $pwnVars['weblabendpoint'] = $weblabendpoint;
      $pwnVars['weblabpassword'] = "pwn";
      $pwnVars['weblabusername'] = $weblabusername;
      
      return $pwnVars;
  }
  /**
   * For ProcessOrder.
   * Pull user data from user_test_results, checks for numeric ID.
   * @param int $id
   * @param constant $type ARRAY_N, ARRAY_A, OBJECT
   * @return array
   */
  function getUserTestResults($id, $type = ARRAY_A) {
      global $wpdb;
      $userResults = false;
      if(is_numeric($id)) {
          $query = $wpdb->prepare('SELECT * FROM user_test_results WHERE id=%d', $id);
          $userResults = $wpdb->get_row($query, $type);
          if(null === $userResults) {
              error_log('Could not find user in user_test_results. : ' . $id);
          }
      } else {
          error_log('The user id for user_test_results was not numeric. Data could be corrupted: ' . $id);
      }
      return $userResults;
  }
  
  /**
   * For ProcessorderPL.
   * Pull user data from user_test_results, checks for numeric ID.
   * @param int $id
   * @param int $code
   * @param string $type OBJECT, ARRAY_A, ARRAY_N
   * @return array
   */
  function getUserTestResultsWithIP($id, $code, $type = ARRAY_A) {
      global $wpdb;
      $userResults = false;
      if(is_numeric($id) && is_numeric($code)) {
          $query = $wpdb->prepare('SELECT * FROM user_test_results WHERE id=%d AND IP=%d', $id, $code);
          $userResults = $wpdb->get_row($query, $type);
          if(null === $userResults) {
              error_log('Could not find user in user_test_results. -- custid: ' . $id . 'code: ' . $code);
              $userResults = false;
          }
      } else {
          error_log('The user id or IP/code for user_test_results was not numeric. Data could be corrupted: ' . $id . ' ' . $code);
      }
      return $userResults;
  }
  
  /**
   * Saves string to a temporay file and encrypted if available.
   * @param string $response
   * @return FILE_HANDLE
   * @todo needs testing
   */
  function saveReponse($response) {
      global $mySalt, $initializationVector;
      $mySalt = 'mysalt';
      $initializationVector = 'abababab';
      $fh = tmpfile();
      if(function_exists('openssl_encrypt') && function_exists('openssl_decrypt')) {
          if(version_compare(PHP_VERSION, '5.3.3') >= 0) {
              $encrypted = openssl_encrypt($response, 'des-cbc', $mySalt, false, $initializationVector);
          } else {
              $encrypted = openssl_encrypt($response, 'des-cbc', $mySalt);
          }
          fwrite($fh, $encrypted);
      } else {
          fwrite($fh, $response);
      }
      return $fh;
  }
  
  /**
   * Gets a string from a given file handle
   * 
   * @param FILE_HANDLE $tempFH
   * @return string;
   * @todo needs testing
   */
  function retrieveResponse($tempFH) {
      global $mySalt, $initializationVector;
      $response = fread($tempFH, filesize($tempFH));
      if(function_exists('openssl_encrypt') && function_exists('openssl_decrypt')) {
          if(version_compare(PHP_VERSION, '5.3.3') >= 0) {
            $response = openssl_decrypt($response, 'des-cbc', $mySalt, false, $in);
          } else {
            $response = openssl_decrypt($response, 'des-cbc', $mySalt);
          }
      }
      return $response;
  }
  /**
   * Set a comment
   * Sets comment for customer with a followup switch.
   * @param int $custID customer id
   * @param string $comment A comment string
   * @param bool $followUp Should agent follow up with customer.
   * @return int|bool insert id
   */
  function setComment($custID, $comment, $followUp = false) {
      global $wpdb;
      //$query = "insert into comments set custID='$custID',user='System',comment='Mail Home Kit',followUp=1";
       //$result = mysql_query($query);
      $table = 'comments';
      $commentData = array('custID'=> $custID, 'user'=>'System', 'comment'=>$comment, 'followUp'=>$followUp); 
      $status = $wpdb->insert($table, $commentData, array('%d','%s', '%s', '%d'));
      if(!$status) {
          ppLog('Could not set comment for customer. -- ' . $custID);
          return;
      }
      return $wpdb->insert_id;
  }
  
  /**
   * Set PWN creation
   * 
   * @param int $custID customer id
   * @return void
   */
  function setPwnCreationSuccess($custID) {
      global $wpdb;
      //$query = "update user_test_results set pwn_creation='success' where id='$custID'";
      $table = 'user_test_results';
      $data = array('pwn_creation'=>'success');
      $where = array('id'=>$custID);
      $wpdb->update($table, $data, $where, array('%s'),array('%d'));
  }
  /**
   * Handle errors
   * Call because of a fatal error
   * @param string $message
   * @todo This should call a global generic error page. With the message.
   */
  function ppmdFatalError($message) {
      $refresh = 15;
      $redirect = '';
      $head = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . PHP_EOL;
      $head .= '<html><head><title>Fatal Error Page</title></head><body>' . PHP_EOL;
      $foot = '</body></html>' . PHP_EOL;
      if(!headers_sent()) {
          header('HTTP/1.1 400 Bad request');
          header('Refresh: '. $refresh .'; url=' . site_url());
          $redirect = 'You will be redirected to our homepage in ' . $refresh . ' seconds.<br />';
      }
      echo $head;
      echo $redirect;
      echo $message;
      echo $foot;
      die();
  }

  /**
   *
   * Send email to customer.
   * @param int $custID
   * @param string $emailToAddress
   * @param string $emailSubject
   * @param string $emailMessage
   * @param string $emailHeaders
   * @param string $parameters
   */
  function sendCustomerEmail($custID, $emailToAddress, $emailSubject, $emailMessage, $emailHeaders = null, $parameters = null) {
    if(mail($emailToAddress, $emailSubject, $emailMessage, $emailHeaders, $parameters)) {
        ppmdNotice('eMail sent - CustID: ' . $custID . ' ' . $emailSubject);
        return true;
    } else {
        ppmdCritical('eMail not sent - CustID: ' . $custID . ' ' . $emailToAddress . ' ' . $emailSubject);
        return false;
    }
  }

  /**
   * Check to see if form was submitted twice.
   * @return bool
   */
  function isDoubleSubmit() {
      if(isset($_POST['verify']) && $_POST['verify'] == $_SESSION['verify']) {
          unset($_SESSION['verify']);
          return false;
      }
      ppmdNotice('INFO-- Somebody double submitted.');
      return true;
  }
  
  /**
   * Verify Nonce
   * @param string $nonce
   * @param string $action
   * @return bool
   */
  function isNonce($nonce, $action) {
      if (! wp_verify_nonce($nonce, $action)) {
          ppmdNotice('Security check failed for: ' . $action);
          return false;
      }
      return true;
  }
  
  /**
   * Bail out function
   * @param string $message
   * @return void
   */
  function fatalBailOut($message = '') {
      $http_status = 500;
      if(headers_sent()) {
          getErrorPage($http_status, $message);
      } else {
          status_header($http_status);
          nocache_headers();
          getErrorPage($http_status, $message);
      }
  }
  
  /**
   * Retrieve error template
   * @param string $http_status
   * @param string $message
   * @return bool
   */
  function getErrorPage($http_status = 500, $message = '') {
      if('' != get_query_template($http_status)) {
          if('' != $message) {
              $errMessage = $message;
          }
          require_once get_query_template($http_status);
      } else {
          require_once get_query_template('404');
          ppmdCritical('The ' . $http_status . ' error page template is missing.');
          return false;
      }
      return true;
  }
?>
