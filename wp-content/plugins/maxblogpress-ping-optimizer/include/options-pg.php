<script type="text/javascript" src="<?php echo $this->mbpo_incpath;?>tooltip.js"></script>
<link href="<?php echo $this->mbpo_incpath;?>tooltip.css" rel="stylesheet" type="text/css">
<script><!--//
function mbpoShowHide(Div,Img) {
	var divCtrl = document.getElementById(Div);
	if ( Img != '' ) var theImg = document.getElementById(Img);
	if ( divCtrl.style == "" || divCtrl.style.display == "none" ) {
		divCtrl.style.display = "block";
		 if ( Img != '' ) theImg.src = '<?php echo $this->mbpo_incpath;?>images/arr_green2.gif';
	} else if ( divCtrl.style != "" || divCtrl.style.display == "block" ) {
		divCtrl.style.display = "none";
		 if ( Img != '' ) theImg.src = '<?php echo $this->mbpo_incpath;?>images/arr_green1.gif';
	}
}//--></script>
<div class="wrap">
 <?php $this->mbpoHeader();?>
 <h3><?php _e('URIs to Ping', 'mbpo'); ?></h3>
 <p>The following services will automatically be pinged when you publish new posts or drafts.  
 <strong>Not</strong> when you publish future posts or edit previously published posts, as WordPress does by default.</p>
 <p><strong>NB:</strong> This list is synchronized with the <a href="options-writing.php" target="_blank">original update services list</a>.</p>
 <form method="post">
 <p><?php _e('Separate multiple service URIs with line breaks:', 'mbpo'); ?><br />
 <textarea name="mbpo[uris]" cols="60" rows="10"><?php echo $this->mbpo_ping_sites;?></textarea></p>
 <p><input type="checkbox" name="mbpo[ping]" id="ping_checkbox" value="1" <?php echo $ping_enable_chk;?> /> Enable pinging</p>
 <p>
 <input type="checkbox" name="mbpo[limit_ping]" id="limit_ping" value="1" <?php echo $limit_ping_chk;?> onclick="mbpoShowHide('limit_ping_dv','')" /> Limit excessive pinging in short time
 <span id="limit_ping_dv" style="display:<?php echo $limit_ping_display;?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Ping at most <input type="text" name="mbpo[limit_number]" value="<?php echo $this->mpo_options['limit_number'];?>" style="width:25px" maxlength="5" /> time(s) within 
 <input type="text" name="mbpo[limit_time]" value="<?php echo $this->mpo_options['limit_time'];?>" style="width:25px" maxlength="5" /> minute(s)</span>
 </p>
 <p>
 <input type="submit" name="mbpo[save]" value="<?php _e('Save Settings', 'mbpo'); ?>" class="button" />
 <input type="submit" name="mbpo[pingnow]" value="<?php _e('Ping Now', 'mbpo'); ?>" class="button" onclick="return confirm('Are you sure you want to ping these services now? Pinging too often could get you banned for spamming.');" />
 </p>
 </form><br />
 <?php if ( MBPO_LOG == true ) { ?>
 <h3><?php _e('Ping Log', 'mbpo'); ?></h3>
 <p><strong><?php _e('Following are the lastest actions performed by the plugin:', 'mbpo'); ?></strong>
 <?php 
 list($pinglog,$exists) = $this->mbpoGetLogData();
 echo $pinglog;
 ?>
 </p>
 <?php if($exists){?><p><a href="?page=<?php echo $this->mbpo_path;?>&d=yes" onclick="return confirm('All ping log data will be deleted. Are you sure?')" style="color:#FF0000;font-weight:bold"><img src="<?php echo $this->mbpo_incpath;?>images/delete.gif" border="0" align="absmiddle"> Clear Log</a></p><?php }?>
 <?php } ?>
 <?php $this->mbpoFooter();?>
</div>