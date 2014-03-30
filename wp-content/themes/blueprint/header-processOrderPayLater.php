<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<script type="text/javascript">
function hideMe(element)
{
  document.getElementById('processingMsg').innerHTML = '<br /><br /><br /><span style="font-size:14pt;font-weight:bold;">Processing Complete</span><br /><br /><a href="http://<?=$domainNameRedir?>/">Click Here</a> To Go Back To <?=$domainTitle?>';
}
function hideMe2(element)
{
  document.getElementById('processingMsg').innerHTML = '<br /><br /><br /><span style="font-size:14pt;font-weight:bold;">Processing Complete</span>';
}
</script>
	<title>GetSTDTested.com - Processing Order</title>
</head>
<body>
<!--Start Outline Table 1-->
<table width="804" id="status" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="center" style="border-bottom:2px solid #D75E2C;">
<!--Start Main Content Table-->
<table width="800" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
<td colspan=2 valign="top" align="center" style="font-family:arial;font-size:12pt" height="150">
<div id="processingMsg">
<br /><br /><b>One moment while we process your order...</b><br /><br /><img id="loadingImg" src="<?php bloginfo('template_directory'); ?>/images/ajax-loader.gif" /><br />
To avoid any issues, please do not press the back button or refresh this page.
</div>
</td>
</tr>
<!--End Main Content Table-->
</table>
<!--End Outline Table 1-->
</td>
</tr>
</table>
