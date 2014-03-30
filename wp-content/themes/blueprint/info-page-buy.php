<?php
$package = "";
$disease = "";
if($post->post_parent == 0) {
    $disease = single_post_title('',false);
} else {
    $disease = get_the_title($post->post_parent); 
}
$product = strtolower($disease);
switch ($product) {
    case 'gonorrhea':
        $package = '<li><label><input name="packages" type="radio"
        value="chlamydia,gonorrhea" />Order a private Gonorrhea &amp;
        Chlamydia test today for only $178</label></li>';
    break;
    
    case 'chlamydia':
        $package = '<li><label><input name="packages" type="radio"
        value="chlamydia,gonorrhea" />Order a private Chlamydia &amp;
        Gonorrhea test today for only $178</label></li>';
    break;
    default:
        $package = "";
    break;
}
?>

<div id="infoPageBuy" class="span-10 last">
	<div style="padding: 15px;">
		<form id="testOptions" name="testOptions" action="">
			<ul style="margin-left: 0px;">
				<li><label><input checked="checked" name="packages" type="radio"
					value="<?php echo $product; ?>" />Order a private <?php echo $disease; ?> test today for only
					$89</label></li>
				<?php echo $package ?>
				<li><label><input name="packages" type="radio" value="expert-package" />Get
					our expert recommended STD Value Package for only $189</label></li>
				<li style="margin-left: 20px; font-size: 11px; font-style: italic;">The
					CDC and our panel of experts recommend testing for Chlamydia,
					Gonorrhea, HIV and Genital Herpes</li>
				<li><label><input name="packages" type="radio"
					value="complete-std-package" />Have total peace of mind - order
					our Total Testing Package (8 tests) for only $249</label></li>
				<li style="margin-left: 20px; font-size: 11px; font-style: italic;">Includes
					Chlamydia, Gonorrhea, HIV, Oral Herpes, Genital Herpes, Hepatitis
					B, Hepatitis C and Syphilis</li>
			</ul>
			<div id="btnOrderPageSmall" style="margin-left: 20px; float: left;">
				<a onclick="submitOrderForm();" href="javascript:void(0)">Get
					Your Test</a>
			</div>
			<div style="float: left; margin-top: 6px; margin-left: 20px;">Have
				Questions? We're here to talk: 866-749-6269</div>
		</form>
	</div>
</div>
<form name="optionsForm" action="select-testing-center" method="POST">
	<input name="item" type="hidden" />
</form>
<script type="text/javascript">
/* <![CDATA[ */
function submitOrderForm()
{
  radioGroupName = "packages";
  for (var i=0; i < eval("document.testOptions." + radioGroupName + ".length"); i++)
  {
        if (eval("document.testOptions."+radioGroupName+"[i].checked"))
        {
          var rad_val = eval("document.testOptions."+radioGroupName+"[i].value");
        }
  }
  document.optionsForm.item.value=rad_val;
  document.optionsForm.submit();
}
/* ]]> */
</script>
