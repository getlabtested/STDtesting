<?php
require_once './wp-load.php';

global $wpdb;

require_once 'includes/sessionInclude.php';
require_once 'includes/processOrderFunctions.php';
require_once ABSPATH . 'includes/classes/PPMDPostAffiliatePro.php';
require_once ABSPATH . 'includes/classes/Transaction.php';

//IF THERE IS NO UUID PASSED INTO THIS PAGE - REDIRECT BACK TO HOMEPAGE
$custID = $_GET['orderID'];
$code = $_GET['code'];
$serverName = $_SERVER["SERVER_NAME"];

if (!$custID || !$code) {
    //die("Activation link is not valid. Please call 866-749-6269 for support.");
    error_log('[PL] Did not find customer id and/or code -- custID: ' . $custID . ' code:' . $code);
    $message = 'Activation link is not valid. Please call 866-749-6269 for support.';
    ppmdFatalError($message);
    exit();
} else {
    //Get All Cust Info From DB
    //$query = "select * from user_test_results where id='$custID' and IP='$code'";
    //if (!$result = mysql_query($query)) die ("Query failed: $query");
    //$row = mysql_fetch_array($result);
    //$rowcount = mysql_num_rows($result);
    $row = getUserTestResultsWithIP($custID, $code);
    //if (!$rowcount) die("Activation link is not valid. Please call 866-749-6269 for support.");
    if(false === $row) {
        error_log('[PL] Did not find user test results using -- custID: ' . $custID . ' code: ' . $code);
        $message = 'Activation link is not valid. Please call 866-749-6269 for support.';
        ppmdFatalError($message);
        exit();
    }
    $cc_validation = $row['cc_validation'];
    $pwn_creation = $row['pwn_creation'];
    $tests = $row['tests_chosen'];
    $athome = $row['athome'];
    $system = $row['system'];
    $_SESSION['environment'] = $system;
    $paymentMethod = $row['paymentMethod'];
    
    $IPaddr = $_SERVER['REMOTE_ADDR'];
    
    $payType="later";
    
    if (($row['payLater'] != 1 && $paymentMethod != "pnm") || $pwn_creation == "success" || $cc_validation == "success" || !$tests) {
        //die("Activation link is not valid. Please call 866-749-6269 for support.");
        error_log('[PL] Failed execution of customers pay later email link -- Customer ID:' .$custID. ' payLater:' . $row['payLater'] . ' Paymethod: ' . $paymentMethod . ' PWN Creation: ' . $pwn_creation . ' CC Validation:' . $cc_validation);
        $message = 'Activation link is not valid or has been used previously. Please call 866-749-6269 for support.';
        ppmdFatalError($message);
        exit();
    }
}
require_once TEMPLATEPATH . '/header-processOrderPayLater.php';
//Create The PWN Order
$pwnApproved = createPWNOrder($custID);

if ($pwnApproved) {
  if ($paymentMethod == "pnm") {
      createPNMOrder($custID);
  }
  sendConfirmationEmail($custID); 
} else {
    sendPWNErrorEmail($custID);
}

//Get All Cust Info From DB Again
//$query = "select * from user_test_results where id='$custID'";
//if (!$result = mysql_query($query)) die ("Query failed: $query");
//$row = mysql_fetch_array($result);
$row = getUserTestResults($custID, ARRAY_A);
if(false === $row) {
    error_log('[PL] Did not find user test results code:' . $custID);
    $message = 'Activation link is not valid. Please call 866-749-6269 for support.';
    ppmdFatalError($message);
    exit();
}
$cc_validation = $row['cc_validation'];
$pwn_creation = $row['pwn_creation'];
$confirmationnumber = $row['confirmationnumber'];
$customernumber = $row['customernumber'];
$labname = $row['labname'];
$labaddress = $row['labaddress'];
$labcity = $row['labcity'];
$labstate = $row['labstate'];
$labzip = $row['labzip'];
$labphone = $row['labphone'];
$labid = $row['labid'];
$IP = $row['IP'];
$mailing_name = $row['mailing_name'];
$mailing_address1 = $row['mailing_address1'];
$mailing_address2 = $row['mailing_address2'];
$mailing_city = $row['mailing_city'];
$mailing_state = $row['mailing_state'];
$mailing_zip = $row['mailing_zip'];
$totalcharge = $row['totalcost'];
$lastname = $row['lastname'];

$locationString = "$labname<br />$labaddress<br />$labcity, $labstate $labzip";

ob_flush();
flush();
sleep(3);
if ($pwnApproved) {
    require_once TEMPLATEPATH . '/processOrderPayLater-approved_msg.php';
} else {
    require_once TEMPLATEPATH . '/processOrderPayLater-decline_msg.php';
}
$totalchargeP = $totalcharge;
if ($pwnApproved && $system == "production") { ?>
    <img alt="" src="https://www.emjcd.com/u?AMOUNT=<?php echo $totalcharge; ?>P&CID=1515507&OID=<?php echo $customernumber; ?>&TYPE=332501&DISCOUNT=0&CURRENCY=USD&METHOD=IMG" height="1" width="20" />
    <script type="text/javascript">
    microsoft_adcenterconversion_domainid = 550265;
    microsoft_adcenterconversion_cp = 5050;
    microsoft_adcenterconversionparams = new Array();
    microsoft_adcenterconversionparams[0] = "dedup=1";
    </script>
    <script type="text/javascript" src="https://0.r.msn.com/scripts/microsoft_adcenterconversion.js"></script>
    <noscript><img width="1" height="1" src="https://550265.r.msn.com/?type=1&cp=1&dedup=1" /></noscript>
    <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-10462432-1']);
    _gaq.push(['_setDomainName', '.getstdtested.com']);
    _gaq.push(['_trackPageview']);
  
    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
  </script>
		<script type="text/javascript">
			var gaJsHost = (("https:" == document.location.protocol ) ? "https://ssl." : "http://www.");
			document.write(unescape("%3Cscript src=" + gaJsHost + "google-analytics.com/ga.js type=text/javascript%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
		try{
			var pageTracker = _gat._getTracker("UA-10462432-1");
			pageTracker._trackPageview();
			pageTracker._addTrans(
					"<?php echo $customernumber ?>", // order ID - required
					"", // affiliation or store name
					"<?php echo $totalcharge; ?>", // total - required
					"0.00", // tax
					"0.00", // shipping
					"", // city
					"", // state or province
					"" // country
				);
			 // add item might be called for every item in the shopping cart
			 // where your ecommerce engine loops through each item in the cart and
			 // prints out _addItem for each 
			 pageTracker._addItem(
					"<?php echo $customernumber ?>", // order ID - necessary to associate item with transaction
					"", // SKU/code - required
					"Tests", // product name
					"", // category or variation
					"<?php echo $totalcharge ?>", // unit price - required
					"1" // quantity - required
			 );

			 pageTracker._trackTrans(); //submits transaction to the Analytics servers
		} catch(err) {}
		</script>

		<!-- Google Code for Registration Conversion Page -->
		<script type="text/javascript">
		<!--
		var google_conversion_id = 1047469408;
		var google_conversion_language = "en";
		var google_conversion_format = "2";
		var google_conversion_color = "ffffff";
		var google_conversion_label = "plpoCPL5zQEQ4Lq88wM";
		var google_conversion_value = '.$totalcharge.';
		//-->
		</script>
		<script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js"></script>
		<noscript>
		<div style="display:inline;">
		<img height="1" width="1" style="border-style:none;" alt="" src="https://www.googleadservices.com/pagead/conversion/1047469408/?label=plpoCPL5zQEQ4Lq88wM&amp;guid=ON&amp;script=0" />
		</div>
		</noscript>
		
        <script type="text/javascript" src="//ah8.facebook.com/js/conversions/tracking.js"></script>
        <script type="text/javascript">
			try {
				FB.Insights.impression({
					'id' : 6002403919074,
					'h' : '66e83d8088',
					'value' : 180 // you can change this dynamically
				});
			} catch (e) {}
		</script>
		
		<!--Begin Code -->
		<img src="https://shareasale.com/sale.cfm?amount=<?php echo $totalcharge; ?>&tracking=<?php echo $customernumber; ?>&transtype=sale&merchantID=24878" width="1" height="1">
		<!--End Code -->
    <?php
        $pap = new PPMDPostAffiliatePro();
        $tr = new Transaction();
        $tr->setCustomerID($customernumber);
        $tr->setProducts(explode(',', $tests));
        $tr->setTotal($totalchargeP);
        $pap->setTransaction($tr);
        $pap->setPending();
        echo $pap->getScript();
    }
    if ($pwnApproved) {
        session_destroy();
        $_SESSION = array();
    }
?>
</body>
</html>

