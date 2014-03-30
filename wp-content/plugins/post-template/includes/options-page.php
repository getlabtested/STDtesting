<?php
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
The license is also available at http://www.gnu.org/copyleft/gpl.html
*/

//############################################################################
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('You are not allowed to call this page directly.'); 
}
//############################################################################

//############################################################################
// Stop unauthorised users 
if (!current_user_can('manage_options')) {
	wp_die(__("You are not allowed to change the options of this plugin.", POST_TEMPLATES_I18N_DOMAIN));
}
//############################################################################
?>

<div class="wrap">

<?php 	
global $post_templates_plugin, $post_templates_dao;	
if (isset($_POST['post_templates_options_submit'])) {
	$post_templates_plugin->options['allow_rich_editor'] = $_POST['allow_rich_editor'];
	$post_templates_plugin->save_options();
?>
<div id="message" class="updated fade">
	<p><?php _e('Options set successfully.', POST_TEMPLATES_I18N_DOMAIN); ?></p>
</div>
<?php
}
?>

<h2><?php _e("Post Templates Plugin", POST_TEMPLATES_I18N_DOMAIN); ?> <?php echo $post_templates_plugin->options['active_version']; ?> - <?php _e("Options", POST_TEMPLATES_I18N_DOMAIN); ?></h2>

<ul class="subsubsub">
	<li><?php _e("Options", POST_TEMPLATES_I18N_DOMAIN); ?> | </li>
	<li><a href="options-general.php?page=post-template/includes/import-page.php" ><?php _e("Importers", POST_TEMPLATES_I18N_DOMAIN); ?></a> | </li>
	<li><a href="options-general.php?page=post-template/includes/help-page.php" ><?php _e("Help", POST_TEMPLATES_I18N_DOMAIN); ?></a> | </li>
	<li><a href="http://post-templates.vincentprat.info" target="_blank"><?php _e("Plugin's home page", POST_TEMPLATES_I18N_DOMAIN); ?></a></li>
</ul>
<br class="clear"/>

<div align="center">
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_xclick" />
		<input type="hidden" name="business" value="vpratfr@yahoo.fr" />
		<input type="hidden" name="item_name" value="Vincent Prat - WordPress Plugin" />
		<input type="hidden" name="no_shipping" value="1" />
		<input type="hidden" name="no_note" value="1" />
		<input type="hidden" name="currency_code" value="EUR" />
		<input type="hidden" name="tax" value="0" />
		<input type="hidden" name="lc" value="<?php _e('EN', POST_TEMPLATES_I18N_DOMAIN); ?>" />
		<input type="hidden" name="bn" value="PP-DonationsBF" />
		<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" border="0" name="submit" alt="PayPal" />
		<img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1" />
	</form>
</div>

<br class="clear"/>

<form name="SetOptions" action="options-general.php?page=post-template/includes/options-page.php" method="post">
	<input type="hidden" name="post_templates_options_submit" value="post_templates_options_submit" />

	<table class="form-table" width="100%" cellspacing="2" cellpadding="5">
		<tr>
			<th scope="row" valign="top">
				<label></label>
			</th>
			<td>
				<input name="allow_rich_editor" type="checkbox" <?php echo $post_templates_plugin->options['allow_rich_editor'] ? ' checked="checked" ' : ''; ?> value="allow_rich_editor"> <?php echo __('Allow the use of the rich editor in the template edit pages.', POST_TEMPLATES_I18N_DOMAIN); ?></input>
			</td>
		</tr>
	</table>
	<p class="submit">
		<input type="submit" name="Submit" value="<?php echo __('Set Options', POST_TEMPLATES_I18N_DOMAIN) . ' &raquo;'; ?>" />
	</p>
</form>

</div>
	
