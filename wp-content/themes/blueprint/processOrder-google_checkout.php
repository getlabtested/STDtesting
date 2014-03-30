<!--====================GOOGLE CHECK OUT===================================-->
  <form name="googleForm" id="googleForm" method="post" action="https://checkout.google.com/api/checkout/v2/checkoutForm/Merchant/860563308726424" accept-charset="utf-8"> <!-- No product -->
      <?php
      //Reg merchant ID 860563308726424
      //Sandbox merchant ID 975074554601553
       $packtype = array_key_exists('group',$_SESSION['PackageDetail']) ? 'group' : 'indi' ;
       
       $total = $_SESSION['PackageDetail']['total'];
       $i=1;
       foreach($_SESSION['PackageDetail'][$packtype] as $key=>$val)
       {?>
          <input type="hidden" name="item_name_<?=$i?>" value="<?=$key?>" />
          <input type="hidden" name="item_description_<?=$i?>" value="<?=$key?>" />
          <input type="hidden" name="item_quantity_<?=$i?>" value="1" />
          <input type="hidden" name="item_price_<?=$i?>" value="<?=$val?>" />
          <input type="hidden" name="item_currency_<?=$i?>" value="USD" />
        <?php
        $i++;
       }
       
       if ($_SESSION['PackageDetail']['savings'])
       {
          if ($packtype == "indi")
          {
            $tempDiscount = $_SESSION['PackageDetail']['savings'];
             ?>
                <input type="hidden" name="item_name_<?=$i?>" value="Multi-Test Discount" />
                <input type="hidden" name="item_description_<?=$i?>" value="Multi-Test Discount" />
                <input type="hidden" name="item_quantity_<?=$i?>" value="1" />
                <input type="hidden" name="item_price_<?=$i?>" value="-<?=$tempDiscount?>" />
                <input type="hidden" name="item_currency_<?=$i?>" value="USD" />
              <? 
          }
       }
      ?>
  
  <input type="hidden" name="shopping-cart.items.item-1.merchant-item-id" value="<?=$_SESSION['custID']?>"/> 
  <input type="hidden" name="_charset_" />
  </form>
  <script>document.googleForm.submit();</script>
<!--====================END GOOGLE CHECK OUT===================================-->