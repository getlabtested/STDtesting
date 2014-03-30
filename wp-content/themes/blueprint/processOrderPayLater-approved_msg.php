<!--===================START APPROVED MESSAGE=====================================-->
<table width="804" id="sub_status" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
<td>
<table width="800" border="0" cellpadding="0" cellspacing="0" align="left" style="font-family:arial;font-size:10pt">
<tr>
<td colspan="3" align="center"><span style="color:blue;font-size:14pt"><br />Thank You. Your Order Was Approved.</span></td>
</tr>
<tr>
<td colspan="3" align="center"><br /><span style="color:red;font-size:12pt">* PLEASE CHECK YOUR EMAIL FOR DETAILED INSTRUCTIONS *</span><br /><br /></td>
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
		<td><?echo getTestNames($tests);?></td>
		</tr>
		</table>
	</td>
	<td valign="top">

		<table>
		<tr>
		<td style="color:#566E1F">Testing Location:</td>
		</tr>
		<tr>
		<td><?=$locationString?></td>
		</tr>
		</table>

	</td>
	<td valign="top">
		<table>
		<tr>
		<td style="color:#566E1F">Total Charges:</td>
		</tr>
		<tr>
		<td><b>$<?=$totalcharge?></b> will be required in order to view your test results</td>
		</tr>
		</table>
	</td>
	</tr>
	</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
<script type="text/javascript">hideMe('processingMsg');</script>
<!--===================END APPROVED MESSAGE=====================================-->
