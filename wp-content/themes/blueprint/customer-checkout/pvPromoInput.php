<tr>
  <td colspan="2" style="font-weight:bold;text-align:left;padding-top:20px">
  <form name="promoForm" id="promoForm" method="post" action="<?=esc_url($_SERVER['PHP_SELF']) ?>" onsubmit="return submitPromoCode();">
  <label style="color: #999;">Promo Code: <input id="promo-code" type="text" name="promoCode" value="<?=$promoCode?>" style="display:inline;font-size:10px;width:75px" maxlength="12" /></label> 
  <input type="hidden" name="promoAction" value="applyCode" />
  <input style="display:inline;font-size:10px" type="submit" value="Apply" />
  </form>
  </td>
</tr>
