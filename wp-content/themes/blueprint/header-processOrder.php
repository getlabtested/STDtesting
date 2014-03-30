<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php if ($approved || $payLaterVal || $go) : ?>
<!-- Google Website Optimizer Tracking Script -->
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['gwo._setAccount', 'UA-10462432-2']);
  _gaq.push(['gwo._trackPageview', '/4207066018/goal']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<script type="text/javascript">
  //wo-order.js
  var _gaq = _gaq || [];
  _gaq.push(['gwo._setAccount', 'UA-17357239-2']);
  _gaq.push(['gwo._trackPageview', '/2584219963/goal']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<!-- End of Google Website Optimizer Tracking Script -->
<?php endif; ?>

<script type="text/javascript">
var _kmq = _kmq || [];
function _kms(u){
  setTimeout(function(){
    var s = document.createElement('script'); var f = document.getElementsByTagName('script')[0]; s.type = 'text/javascript'; s.async = true;
    s.src = u; f.parentNode.insertBefore(s, f);
  }, 1);
}
_kms('//i.kissmetrics.com/i.js');_kms('//doug1izaerwt3.cloudfront.net/dba85967496cf921150f876356ed0193d9e2e23d.1.js');</script>
<script type="text/javascript">

 var _gaq = _gaq || [];
 _gaq.push(['_setAccount', 'UA-10462432-1']);
_gaq.push(['_setDomainName', '.<?php echo ppmdDomainName();?>']);
 _gaq.push(['_setAllowLinker', true]);
 _gaq.push(['_setAllowHash', false]);
 _gaq.push(['_trackPageview']);

 (function() {
   var ga = document.createElement('script'); ga.type =
'text/javascript'; ga.async = true;
   ga.src = ('https:' == document.location.protocol ? 'https://ssl' :
'http://www') + '.google-analytics.com/ga.js';
   var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(ga, s);
 })();

</script>

<script type="text/javascript">
function hideMe(element)
{
  document.getElementById('processingMsg').innerHTML = '<br /><br /><br /><span style="font-size:14pt;font-weight:bold">Processing Complete</span><br /><br/><a href="<?php echo site_url(); ?>">Click Here</a> To Go Back To <?=$domainTitle?>';
}
function hideMe2(element)
{
  document.getElementById('processingMsg').innerHTML = "<br /><br /><br /><span style='font-size:14pt;font-weight:bold'>Processing Complete</span>";
}
function hideMe3(element)
{
  document.getElementById('processingMsg').innerHTML = "<br /><br /><br /><span style='font-size:14pt;font-weight:bold'>Your Order Is ALMOST Complete. Please See Instructions Below</span>";
}
</script>
	<title><?=$domainTitle?> - Processing Order</title>
</head>

<body>
<?php
$testSession = 'mytest';
echo ppmdTestBorder($testSession);
