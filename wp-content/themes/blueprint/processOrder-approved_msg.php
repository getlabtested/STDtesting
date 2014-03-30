<!-- Outer table of Results -->
<table width="804" id="sub_status" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
<td>
<!--===================START APPROVED MESSAGE=====================================-->
<table width="800" border="0" cellpadding="0" cellspacing="0" align="left" style="font-family:arial;font-size:10pt">
<tr>
<td colspan="3" align="center"><span style="color:blue;font-size:14pt"><br />Thank You. Your Order Was Approved.</span></td>
</tr>
<tr>
<td colspan="3" align="center"><span style="color:red;font-size:12pt"><br />* PLEASE CHECK YOUR EMAIL FOR DETAILED INSTRUCTIONS *<br /></span><br /></td>
</tr>
<tr>
<td colspan="2">If you have any questions, please call us at <?=$defaultPhone?><br /><br /></td>
</tr>
<tr>
<td colspan="2">
	<table style="font-family:arial;font-size:10pt" width="800">
	<tr>
	<td style="font-weight:bold;font-size:12pt">Your Receipt</td>
	</tr>
	<tr>
	<td valign="top">
		<table>
		<tr>
		<td style="color:#566E1F">Tests Ordered:</td>
		</tr>
		<tr>
		<td><?php echo getTestNames($tests_chosen);?></td>
		</tr>
		</table>
	</td>
	<td valign="top">
	<?php if (!$athome):?>
		<table>
		<tr>
		<td style="color:#566E1F">Testing Location:</td>
		</tr>
		<tr>
		<td><?=$locationString?></td>
		</tr>
		</table>
	<?php else: ?>
		<table>
		<tr>
		<td style="color:#566E1F">Shipping Address:</td>
		</tr>
		<tr>
		<td><?=$mailing_name?><br /><?=$address?><br /><?=$city?>, <?=$state?> <?=$zipcode?></td>
		</tr>
		</table>
	<?php endif; ?>
	</td>
	<td valign="top">
		<table>
		<tr>
		<td style="color:#566E1F">Total Charges:</td>
		</tr>
		<tr>
		<?
     if ($payType == "now") $chargeText = "was charged to your card ending in ".$ccNum2;
     if ($payType == "eCheck") $chargeText = "was charged to your account ending in ".$accountNum2;
    ?>
    <td><b>$<?=$totalcharge?></b> <?=$chargeText?></td>
		</tr>
		</table>
	</td>
	</tr>
	</table>
</td>
</tr>
</table>
<!--===================END APPROVED MESSAGE=====================================-->
</td>
</tr>
</table>
<!-- End Outer table of Results -->
