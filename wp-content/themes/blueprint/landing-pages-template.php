<?php
/*
Template Name: Landing Pages
*/
?>

<?php get_header(); ?>

<?php
  $stdName = get_post_custom_values('STD Name');
	if (empty($stdName))
	{
	  $stdName = "STDs";
	  $stdNameProper = "STDs";
	  $stdNameSingle = "STD";
  }
  else
  {
	  $stdName = $stdName[0];
	  $stdName = strtolower($stdName);
	  $stdNameSingle = $stdName;
	  $stdNameProper =  str_replace("-"," ",$stdName);
	  $stdNameProper = ucwords($stdNameProper);
  }
  
	//SET STD Variables
	$infoArray['chlamydia']['price'] = "89";
	
	
	if ($stdName == "hiv") $stdNameProper="HIV";
	
	$h1Title = "Get Your ".ucfirst($stdNameSingle)." Test For \$89";
	
	$stdNameProper =  str_replace("-"," ",$stdName);
	$stdNameProper = ucwords($stdNameProper);
	if ($stdName == "hiv") $stdNameProper="HIV";
	
	$h1Title = "Get Your ".$stdNameProper." Test For \$89";
	
	$treatedText = "All STDs are better managed when diagnosed early";
	$treatedHeader = "Take Action";
	
	if ($stdName == "chlamydia" || $stdName == "gonorrhea" || $stdName == "trichomoniasis")
	{
      $treatedHeader = "Get Treated";
      $treatedText = "Cure ".$stdNameProper." infection in 7 to 10 days with a prescription written by our in-house physician network";
  }
  
  if ($stdName == "oral-herpes" || $stdName == "genetial-herpes")
  {
      $treatedHeader = "Get Treated";
      $treatedText = "Get a prescription to help manage and control ".$stdNameProper;
  }
  
  //========Determine Upgrade Offer=================================
  $upgrade = 0;
  if ($stdName == "chlamydia" || $stdName == "gonorrhea" || $stdName == "oral-herpes" || $stdName == "gential-herpes" || $stdName == "hepatitis-b" || $stdName == "hepatitis-c")
  {
     $upgrade=1;
     if ($stdName == "chlamydia")
     {
        $upgradeText = "The symptoms of Gonorrhea and Chlamydia are similar, but the treatments are different. One simple urine test detects both.";
        $upgradeVal = "gonorrhea";
        $upgradeName = "Gonorrhea";     
     }
     if ($stdName == "gonorrhea")
     {
        $upgradeText = "The symptoms of Chlamydia and Gonorrhea are similar, but the treatments are different. One simple urine test detects both.";
        $upgradeVal = "chlamydia";
        $upgradeName = "Chlamydia";     
     }
     if ($stdName == "oral-herpes")
     {
        $upgradeText = "The symptoms of Gential Herpes and Oral Herpes are similar, but the treatments are different. One simple blood test detects both.";
        $upgradeVal = "gential-herpes";
        $upgradeName = "Gential Herpes";     
     }
     if ($stdName == "genital-herpes")
     {
        $upgradeText = "The symptoms of Oral Herpes and Gential Herpes are similar, but the treatments are different. One simple blood test detects both.";
        $upgradeVal = "oral-herpes";
        $upgradeName = "Oral Herpes";     
     }
     if ($stdName == "hepatitis-b")
     {
        $upgradeText = "The symptoms of Hepatitis C and Hepatitis B are similar, but the treatments are different. One simple blood test detects both.";
        $upgradeVal = "hepatitis-c";
        $upgradeName = "Hepatitis C";     
     }
     if ($stdName == "hepatitis-c")
     {
        $upgradeText = "The symptoms of Hepatitis B and Hepatitis C are similar, but the treatments are different. One simple blood test detects both.";
        $upgradeTest = "hepatitis-b";
        $upgradeTest = "Hepatitis B";     
     } 
  }
?>

<style>
table,td,tr {margin:0px;padding:0px;font-size:11px;font-family:arial;vertical-align:text-top;border:1px;vertical-align:top;}
</style>

<div id="page">

	<div class="column span-10 first" id="maincontent">

		<div class="content2">
		
		<?php
     $h1Val = get_post_custom_values('h1Tag');
     if (empty($h1Val)) $h1Title = "Get Your ".ucfirst($stdNameSingle)." Test For \$119";
     else $h1Title = $h1Val[0];	
			?>
     
    <h1 style="padding-bottom:15px"><?php echo $h1Title?></h1>           

     <?php
     $h2Val = get_post_custom_values('h2Tag');	
			?>
            
      <h2 style="color:#0B776A;font-size:16px;font-weight:bold;padding-bottom:0px;margin-bottom:0px;border:0px"><?php echo $h2Val[0]?></h2>      
      <h3 style="color:black;padding-top:0px;margin-top:0px">Join the nearly 1 million people who undergo private <?php echo $stdName?> testing through STDTesting.com</h3> 
      
      <table style="width:673px;margin-top:20px">
      <tr>
      <td valign="middle" width="207px" height="300px" style="width:207px;height:300px;vertical-align:middle">
          <table style="background-image:url('http://c189814.r14.cf1.rackcdn.com/std-test-package-bg1.jpg');background-repeat:no-repeat;width:207px;height:300px;vertical-align:middle;">
          <tr>
          <td style="padding-left:5px;padding-right:5px;padding-top:10px;text-align:center;font-size:17px">CDC Standard Package</td>
          </tr>
          <tr>
          <td style="padding-left:5px;padding-right:5px;text-align:center;font-size:32px;color:#008B88">$249</td>
          </tr>
          <tr>
          <td style="font-weight:bold;font-size:13px;padding-left:50px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"> Chlamydia</td>
          </tr>
          <tr>
          <td style="font-weight:bold;font-size:13px;padding-left:50px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"> Gonorrhea</td>
          </tr>
          <tr>
          <td style="font-weight:bold;font-size:13px;padding-left:50px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"> HIV</td>
          </tr>
          <tr>
          <td style="font-weight:bold;font-size:11px;padding-left:15px;">Add additional test free of charge</td>
          </tr>
          <tr>
          <td style="font-weight:bold;font-size:13px;padding-left:55px">
              <form id="form1" name="form1" method="post" action="">
                <select name="bonusMenu" id="bonusMenu" style="font-size:12px">
                    <?php
                    if ($stdName == "chlamydia" || $stdName == "gonorrhea" || $stdName == "gential-herpes" || $stdName == "hiv") echo "<option selected value=\"genital-herpes\">Herpes II</option>";
                    else echo "<option selected value=\"genital-herpes\">Herpes II</option>";  
                    if ($stdName == "oral-herpes") echo "<option selected value=\"oral-herpes\">Herpes I</option>";
                    else echo "<option value=\"oral-herpes\">Herpes I</option>";
                    if ($stdName == "syphilis") echo "<option selected value=\"syphilis\">Syphillis</option>";
                    else echo "<option value=\"syphilis\">Syphillis</option>";
                    if ($stdName == "hepatitis-b") echo "<option selected value=\"hepatitis-b\">Hepatitis B</option>";
                    else echo "<option value=\"hepatitis-b\">Hepatitis B</option>";
                    if ($stdName == "hepatitis-c") echo "<option selected value=\"hepatitis-c\">Hepatitis C</option>";
                    else echo "<option value=\"hepatitis-c\">Hepatitis C</option>";   
                    ?>  
                </select>
            </form>
          </td>
          </tr>
          <tr>
        	<td height="30" style="height:30px;text-align:center;padding-left:20px;padding-bottom:20px;padding-top:20px"><div id="btnOrderPageSmall"><a href="javascript:void(0)" onclick="createTestStringValPack();document.optionsFormLab.submit();" onMouseOver="this.style.cursor='pointer'">Order Now</a></div></td>
          </tr>
          </table>
      </td>
      <td style="background-image:url('http://c189814.r14.cf1.rackcdn.com/std-test-package-bg2.jpg');background-repeat:no-repeat;width:259px;height:335px;">
          
          <?php
          if ($stdName == "STDs") {
          ?>
          <table style="padding-top:<?php echo $paddingTop?>;width:100%" align="center">
          <tr>
          <td colspan=2 style="text-align:center;text-align:center;font-size:28px;line-height:1.1em;padding-top:10px">Comprehensive Package</td>
          </tr>
          <tr>
          <td style="padding-left:5px;padding-right:5px;text-align:center;font-size:32px;color:#008B88">$249</td>
          </tr>
          <tr>
          <td style="text-align:center;font-weight:bold;font-style:italic;font-size:18px">Best Value!</td>
          </tr>
          <tr>
          <td style="text-align:center;padding-right:10px;padding-left:10px;font-style:italic;font-weight:bold;padding-bottom:10px">Complete peace of mind; amazing price</td>
          </tr>
          <tr>
          <td align="center" style="text-align:center;padding-left:40px">
              <table>
              <tr>
              <td style="padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"></td><td style="font-weight:bold;font-size:12px;padding-right:12px">Chlamydia</td><td style="padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"></td><td style="font-weight:bold;font-size:12px;">Herpes I</td>
              </tr>
              <tr>
              <td style="padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"></td><td style="font-weight:bold;font-size:12px;padding-right:12px">Gonorrhea</td><td style="padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"></td><td style="font-weight:bold;font-size:12px;">Syphilis</td>
              </tr>
              <tr>
              <td style="padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"></td><td style="font-weight:bold;font-size:12px;padding-right:12px">Herpes II</td><td style="padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"></td><td style="font-weight:bold;font-size:12px;">Hepatitis B</td>
              </tr>
              <tr>
              <td style="padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"></td><td style="font-weight:bold;font-size:12px;padding-right:12px">HIV</td><td style="padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"></td><td style="font-weight:bold;font-size:12px;">Hepatitis C</td>
              </tr>
              </table>
          </td>
          </tr>
          <tr>
        	<td height="30" style="height:30px;text-align:center;padding-left:20px;padding-bottom:20px;padding-top:20px"><div id="btnOrderPageLarge"><a href="javascript:void(0)" onclick="document.optionsFormLab.packName.value='';document.optionsFormLab.item.value='complete-std-package';document.optionsFormLab.submit();" onMouseOver="this.style.cursor='pointer'">Order Now</a></div></td>
          </tr>
          </table>
          <?php
          } else {
          $paddingTop = "10px";
          if (!$upgrade) $paddingTop = "15px";
          echo "<form name=\"specificTestForm\" style=\"margin:0px;padding:0px\">\r\n";
          echo "<input type=\"hidden\" name=\"stdName\" value=\"$stdName\">\r\n";
          ?>
          <table style="padding-top:<?php echo $paddingTop?>;width:100%" align="center">
          <tr>
          <td colspan=2 style="text-align:center;text-align:center;font-size:28px"><?php echo $stdNameProper?> Test</td>
          </tr>
          
          <?php 
          if (!$upgrade) {
          echo "<tr>\r\n";
          echo "<td style=\"text-align:center;text-align:center;font-size:16px;padding-top:10px\"><span style=\"color:#008B88\">~</span> 100% Confidential <span style=\"color:#008B88\">~</span></td>\r\n";
          echo "</tr>\r\n";
          echo "<tr>\r\n";
          echo "<td style=\"text-align:center;text-align:center;font-size:16px\"><span style=\"color:#008B88\">~</span> Fast, Accurate Results <span style=\"color:#008B88\">~</span></td>\r\n";
          echo "</tr>\r\n";
          echo "<tr>\r\n";
          echo "<td style=\"text-align:center;text-align:center;font-size:16px;padding-bottom:20px\"><span style=\"color:#008B88\">~</span> 4000+ Walk-In Test Centers <span style=\"color:#008B88\">~</span></td>\r\n";
          echo "</tr>\r\n";
          }
          ?>
          <tr>
          <td colspan=2 style="padding-left:5px;padding-right:5px;text-align:center;font-size:32px;color:#008B88"><span style="color:black;font-size:16px">only</span> $119</td>
          </tr>
          
          <?php
          if ($upgrade) {
          echo "<tr>\r\n";
          echo "<td style=\"padding-left:15px;padding-right:10px;padding-top:15px\">\r\n";
          echo "<table>\r\n";
          echo "<tr>\r\n";
          echo "<td colspan=2>$upgradeText</td>\r\n";
          echo "</tr>\r\n";
          echo "<tr>\r\n";
          echo "<td style=\"text-align:center;padding-top:15px;padding-bottom:15px\">\r\n";
             echo "<input type=\"checkbox\" name=\"upgrade\" value=\"$upgradeVal\">\r\n"; 
          echo "</td>\r\n";
          echo "<td style=\"font-weight:bold;padding-top:15px;padding-bottom:15px\">Add $upgradeName for only \$80</td>\r\n";
          echo "</tr>\r\n";
          echo "</table>\r\n";
          echo "</td>\r\n";
          echo "</tr>\r\n";
          }
          ?>
          
          <tr>
        	<td height="50" style="height:50px;padding-left:20px;padding-bottom:20px;padding-top:20px"><div id="btnOrderPageLarge"><a href="javascript:void(0)" onclick="createTestStringMain();" onMouseOver="this.style.cursor='pointer'">Order Now</a></div></td>
          </tr>
          </table>
          <?php
          echo "</form>\r\n";
          }
          ?>       
      </td>
      <td valign="middle" style="width:207px;height:300px;vertical-align:middle">
          <?php
          if ($stdName == "STDs") {
          ?>
          <table align="center" style="background-image:url('http://c189814.r14.cf1.rackcdn.com/std-test-package-bg1.jpg');background-repeat:no-repeat;vertical-align:middle;width:207px;height:300px;">
          <tr>
            <td>
                <div style="font-size:16px; line-height:16px; text-align:center;margin-top:15px">Individual Tests</div>
                <div id="indiPriceText" style="font-size:32px; line-height:75px; text-align:center;color:#008B88">$0</div>
                <div style="font-size:10px; line-height:12px; margin-top:10px; text-align:center;">$119 ea | Discounts Apply For +2</div>
        		      <div style="margin-top:10px;width:150px;margin-left:30px">
                    <form name="testOptionsL">
                        <input type="hidden" name="indiTestString">
                        <div style="float:left">
                            <div><input type="checkbox" name="indiTests" value="chlamydia" onclick="createTestString()">Chlamydia</div>
                            <div><input type="checkbox" name="indiTests" value="gonorrhea"  onclick="createTestString()">Gonorrhea</div>
                            <div><input type="checkbox" name="indiTests" value="hepatitis-b"  onclick="createTestString()">Hepatitis B</div>
                            <div><input type="checkbox" name="indiTests" value="hepatitis-c"  onclick="createTestString()">Hepatitis C</div>
                        </div>
                        <div style="float:right">
                            <div><input type="checkbox" name="indiTests" value="oral-herpes"  onclick="createTestString()">Herpes I</div>
                            <div><input type="checkbox" name="indiTests" value="genital-herpes"  onclick="createTestString()">Herpes II</div>
                            <div><input type="checkbox" name="indiTests" value="hiv"  onclick="createTestString()">HIV</div>
                            <div><input type="checkbox" name="indiTests" value="syphilis"  onclick="createTestString()">Syphilis</div>
                        </div>
                    </form>
                </div>
            </td>
          </tr>
          <tr>
          	<td height="30" style="height:30px;padding-left:20px"><div id="btnOrderPageSmall"><a href="javascript:void(0)" onclick="document.optionsForm.packageName.value='';orderIndiTests()" onMouseOver="this.style.cursor='pointer'">Order Now</a></div></td>
          </tr>
        </table>
        <?php
        } else {
        ?>
        <table align="center" style="background-image:url('http://c189814.r14.cf1.rackcdn.com/std-test-package-bg1.jpg');background-repeat:no-repeat;vertical-align:middle;width:207px;height:300px;">
          <tr>
          <td style="padding-left:5px;padding-right:5px;padding-top:10px;text-align:center;font-size:17px">Comprehensive Package</td>
          </tr>
          <tr>
          <td style="padding-left:5px;padding-right:5px;text-align:center;font-size:32px;color:#008B88">$249</td>
          </tr>
          <tr>
          <td style="text-align:center;font-weight:bold;font-style:italic;font-size:18px">Best Value!</td>
          </tr>
          <tr>
          <td style="text-align:center;padding-right:10px;padding-left:10px;font-style:italic;font-weight:bold">Complete peace of mind at an amazing price</td>
          </tr>
          <tr>
          <td align="center" style="text-align:center;padding-left:15px">
              <table>
              <tr>
              <td style="padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"></td><td style="font-weight:bold;font-size:12px;padding-right:12px">Chlamydia</td><td style="padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"></td><td style="font-weight:bold;font-size:12px;">Herpes I</td>
              </tr>
              <tr>
              <td style="padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"></td><td style="font-weight:bold;font-size:12px;padding-right:12px">Gonorrhea</td><td style="padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"></td><td style="font-weight:bold;font-size:12px;">Syphilis</td>
              </tr>
              <tr>
              <td style="padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"></td><td style="font-weight:bold;font-size:12px;padding-right:12px">Herpes II</td><td style="padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"></td><td style="font-weight:bold;font-size:12px;">Hepatitis B</td>
              </tr>
              <tr>
              <td style="padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"></td><td style="font-weight:bold;font-size:12px;padding-right:12px">HIV</td><td style="padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/std-checked.jpg"></td><td style="font-weight:bold;font-size:12px;">Hepatitis C</td>
              </tr>
              </table>
          </td>
          </tr>
          <tr>
        	<td height="30" style="height:30px;text-align:center;padding-left:20px;padding-bottom:20px;padding-top:20px"><div id="btnOrderPageSmall"><a href="javascript:void(0)" onclick="document.optionsFormLab.packName.value='';document.optionsFormLab.item.value='complete-std-package';document.optionsFormLab.submit();" onMouseOver="this.style.cursor='pointer'">Order Now</a></div></td>
          </tr>
          </table>
        <?php
        }
        ?>
      </td>
      </tr>
      </table>
      
      <h3 style="color:#3F3F3F;font-size:22px;padding-top:15px;padding-bottom:10px;font-weight:normal">Benefits of Private STD Testing</h3>
      
      <table style="font-weight:bold;width:100%">
      <tr>
      <td style="width:70px;padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/benefits-test-today.jpg"></td>
      <td style="font-weight:bold;width:210px;padding-right:30px"><span style="color:#047269;font-size:14px">Test Today</span><br>No wait. Order and test for <?php echo $stdName?> today. Get results within 3 days via private, online portal.</td>
      <td style="width:70px;padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/benefits-physician-consult.jpg"></td>
      <td style="font-weight:bold;width:210px"><span style="color:#047269;font-size:14px">Free Physician Consult</span><br>Get the facts about <?php echo $stdName?> through a complimentary consultation with our in-house physician network.</td>
      </tr>
      <tr>
      <td>&nbsp;</td>
      </tr>
      <tr>
      <td style="width:70px;padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/benefits-get-treated.jpg"></td>
      <td style="font-weight:bold;width:210px;padding-right:30px"><span style="color:#047269;font-size:14px"><?php echo $treatedHeader?></span><br><?php echo $treatedText?></td>
      <td style="width:70px;padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/benefits-convenient.jpg"></td>
      <td style="font-weight:bold;width:210px"><span style="color:#047269;font-size:14px">Convenient</span><br><?php echo ucfirst($stdNameSingle)?> testing available at 4000+ locations nationwide. No appointment necessary.</td>
      </tr>
      <tr>
      <td>&nbsp;</td>
      </tr>
      <tr>
      <td style="width:70px;padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/benefits-accurate.jpg"></td>
      <td style="font-weight:bold;width:210px;padding-right:30px"><span style="color:#047269;font-size:14px">Highest Accuracy Available</span><br>We use the same trusted labs and tests administered by doctors and hospitals nationwide.</td>
      <td style="width:70px;padding-right:5px"><img src="http://c189814.r14.cf1.rackcdn.com/benefits-confidential.jpg"></td>
      <td style="font-weight:bold;width:210px"><span style="color:#047269;font-size:14px">100% Confidential</span><br>No insurance records. <?php echo $stdNameSingle?> test results are completley private.</td>
      </tr>
      </table>
      
      
      <table style="padding-top:30px;width:100%">
      <tr>
      <td width="600px" style="padding-right:20px;font-si">
          <?php
          while (have_posts()) : the_post();      
    			getStaticPage($post);
    			endwhile;
          ?>
      </td>
      <td>
          <table bgcolor="#F9FBD6" style="width:260px;height:200px;border-top:1px solid #DFE5A5;border-bottom:1px solid #DFE5A5;border-right:1px solid #DFE5A5;border-left:1px solid #DFE5A5">
          <tr>
          <td style="padding-left:5px;padding-top:5px;padding-right:5px"><h3><a href="/the-institute-of-sexual-health">Institute of Sexual Health</a></h3><br>
          Backed by nationwide medical experts, the Institute of Sexual Health is a leading source of STD information, research and education.<br>STDTesting.com is committed to online access to inform, test and treat.<br><a href="/the-institute-of-sexual-health">LEARN MORE</a>
          </td>
          <td style="vertical-align:bottom;padding-right:5px;padding-bottom:5px"><img src="http://c189814.r14.cf1.rackcdn.com/std-bbb-and-asha.jpg" alt="Healthplace, LLC is a BBB Accredited Medical Testing Company in Schaumburg, IL" usemap="#bbbMap"></td>
          </tr>
          </table>
          
          <map name="bbbMap">
          <area shape="rect" coords="0,0,60,100" href="http://www.bbb.org/chicago/business-reviews/medical-testing-companies/healthplace-in-schaumburg-il-88387750#bbblogo" alt="Healthplace, LLC is a BBB Accredited Medical Testing Company in Schaumburg, IL" target="_blank"/>
          </map>
      </td>
      </tr>
      </table>
      
      

		</div> <!-- /content -->

	</div> <!-- /maincontent-->

<?php include ('sidebar-hiw.php'); ?>

</div> <!-- /page -->

<form name="optionsForm" action="<?php echo site_url('/customer-checkout', 'https'); ?>" method="post">
<input type="hidden" name="action" value="checkout">
<input type="hidden" name="athome" value="1">
<input type="hidden" name="totalCost" value="">
<input type="hidden" name="codeString" value="">
<input type="hidden" name="nameString" value="">
<input type="hidden" name="priceString" value="">
<input type="hidden" name="packageType" value="">
<input type="hidden" name="packageName" value="">
<input type="hidden" name="a_aid" value="[php] echo $_SESSION['a_aid']; [/php]">
<input type="hidden" name="http_referer" value="[php] echo $_SESSION['HTTP_REFERER']; [/php]">
<input type="hidden" name="environment" value="[php] echo $_SERVER['HTTP_HOST']; [/php]">
<input type="hidden" name="" value="">
</form>

<form name="optionsFormLab" action="select-testing-center" method="POST">
<input type="hidden" name="item" value="">
<input type="hidden" name="packName" value="">
</form>

<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/js/std-testing-options.js"></script>

<?php get_footer(); ?>
