<?php if ($cc_validation == "success"): ?>
<script type="text/javascript">hideMe('processingMsg');</script>
<?php elseif ($payType == "later" || $payType == "PNM"): ?>
<script type="text/javascript">hideMe3('processingMsg');</script>
<?php else: ?>
<script type="text/javascript">hideMe2('processingMsg');</script>
<?php endif; ?>

<?php
$totalchargeP = $totalcharge;
if ($athome) $totalchargeP = $totalcharge-15;

if ($cc_validation == "success") {
    session_destroy();
    $_SESSION = array();
}

if ($cc_validation == "success" && $environment == "production" || $go) { ?>
    <img alt="" src="https://www.emjcd.com/u?AMOUNT=<?php echo $totalcharge; ?>P&CID=1515507&OID=<?php echo $customernumber; ?>&TYPE=332501&DISCOUNT=0&CURRENCY=USD&METHOD=IMG" height="1" width="20">
		
		<script type="text/javascript">
        <!-- Yahoo! Inc. 
		window.ysm_customData = new Object();
		window.ysm_customData.conversion = "transId=,currency=,amount=";
		var ysm_accountid = "1JFNHCEI3DSJ4KK1VJUEC5FSNH0";
		document.write("<SCR" + "IPT language='JavaScript' type='text/javascript' " + "SRC=//" + "srv1.wa.marketingsolutions.yahoo.com" + "/script/ScriptServlet" + "?aid=" + ysm_accountid + "></SCR" + "IPT>");
		// -->
		</script>

    <script type="text/javascript">
    microsoft_adcenterconversion_domainid = 550265;
    microsoft_adcenterconversion_cp = 5050;
    microsoft_adcenterconversionparams = new Array();
    microsoft_adcenterconversionparams[0] = "dedup=1";
    </script>
    <script type="text/javascript" src="https://0.r.msn.com/scripts/microsoft_adcenterconversion.js"></script>
    <noscript><img width="1" height="1" src="https://550265.r.msn.com/?type=1&cp=1&dedup=1" /></noscript>
    <!-- Google Analytics tracking -->
    <script type="text/javascript">
      var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
      document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript">
		try{
			var pageTracker = _gat._getTracker("UA-10462432-1");
			pageTracker._trackPageview();
			pageTracker._addTrans(
					"<?php echo $customernumber; ?>", // order ID - required
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
					"<?php echo $customernumber; ?>", // order ID - necessary to associate item with transaction
					"", // SKU/code - required
					"Tests", // product name
					"", // category or variation
					"<?php echo $totalcharge; ?>", // unit price - required
					"1" // quantity - required
			 );

			 pageTracker._trackTrans(); //submits transaction to the Analytics servers
		} catch(err) {}
		</script>

		
		<!-- Google Code for Purchases Conversion Page -->
		<script type="text/javascript">
		<!--
		var google_conversion_id = 1047469408;
		var google_conversion_language = "en_US";
		var google_conversion_format = "2";
		var google_conversion_color = "ffffff";
		var google_conversion_label = "dbHnCJ6xkQEQ4Lq88wM";
		var google_conversion_value = <?php echo $totalcharge; ?>;
		//-->
		</script>
		<script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js">
		</script>
		<noscript>
		<div style="display:inline;">
		<img height="1" width="1" style="border-style:none;" alt="" src="https://www.googleadservices.com/pagead/conversion/1047469408/?label=dbHnCJ6xkQEQ4Lq88wM&amp;guid=ON&amp;script=0"/>
		</div>
		</noscript>
		
		<script type="text/javascript" src="//ah8.facebook.com/js/conversions/tracking.js"></script><script type="text/javascript">
			try {
				FB.Insights.impression({
					'id' : 6002403919074,
					'h' : '66e83d8088',
					'value' : 180 // you can change this dynamically
				});
			} catch (e) {}
		</script>
		
		<!--Begin Code -->
		<img alt="" src="https://shareasale.com/sale.cfm?amount=<?php echo $totalcharge; ?>&tracking=<?php echo $customernumber; ?>&transtype=sale&merchantID=24878" width="1" height="1">
		<!--End Code -->
    <?php
	$pap = new PPMDPostAffiliatePro();
	$tr = new Transaction();
	$tr->setCustomerID($customernumber);
	$tr->setProducts(explode(',', $tests_chosen));
	$tr->setTotal($totalchargeP);
	$pap->setTransaction($tr);
	echo $pap->getScript();
}

if ($cc_validation == "success" || $payType=="later" || $go) : ?>
      <!-- begin adBrite, Purchases/sales tracking -->
      <img alt="" border="0" hspace="0" vspace="0" width="1" height="1" src="https://stats.adbrite.com/stats/stats.gif?_uid=845986&_pid=0" />
      <!-- end adBrite, Purchases/sales tracking -->
      
<?php endif; ?>
</body>
</html>
