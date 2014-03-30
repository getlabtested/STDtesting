<?php
require_once $_SERVER['APP_BASE'] . '/wp-load.php';
get_header();
?>
<!-- --------------------------------- throw info php start --------------------------------- -->
<?php
	
	function ListErrorMsg($errmgs){
		$mgstop = '<div class="post"><div class="post-bgtop"><div class="post-bgbtm">';
		foreach($errmgs as $k=>$v){
			if($k == 'DoneSuccessfully')
			$image = '<img src="images/ok.png" border="0" height="25" width="20">';
			else
			$image = 'images/error.gif';
			$emgs .= '<li style="list-style-image:url('.$image.');"><span style="color:red;">'.$v.'</span></li>';
		}
		$mgsbtm = '</div></div></div>';
        if (strlen($emgs) > 1) {
			$emgs = $mgstop.$emgs.$mgsbtm;
		}
		echo $emgs;
	}

	
	$errmgs = array();
	if($_REQUEST['Gender'] != '' || $_REQUEST['Age'] != '')
		{
			$_SESSION['Recommandation']['Gender'] = $_REQUEST['Gender'];
			$_SESSION['Recommandation']['Age'] = $_REQUEST['Age'];  
			if($_SESSION['Recommandation']['Zipcode'] == 'Zipcode' || $_SESSION['Recommandation']['Zipcode'] == '')
			$_SESSION['Recommandation']['Zipcode'] = 'Zipcode'; 
		}
  	$Recommandation = $_SESSION['Recommandation'];
	//
	if($_REQUEST['MyRecommandation'])
	{
	//pr($_REQUEST,1);
	if(empty($_REQUEST['Gender']))
		{
			$errmgs['Gender'] = 'Please select gender';
			$err = true;
		}
		if(empty($_REQUEST['Age']))
		{
			$errmgs['Age'] = 'Please select age';
			$err = true;
		}
		if(empty($_REQUEST['Zipcode']) || $_REQUEST['Zipcode'] == 'Zipcode')
		{
			$errmgs['Zipcode'] = 'Please enter zipcode';
			$err = true;
		}
		if(!is_numeric($_REQUEST['Zipcode']))
		{
			$errmgs['ValidZipCode'] = 'Please enter valid zipcode';
			$err = true;
		}
		if(empty($_REQUEST['SexualPartner']))
		{
			$errmgs['SexualPartner'] = 'Please choose sexual partner';
			$err = true;
		}
		if(empty($_REQUEST['LastSTDTest']))
		{
			$errmgs['LastSTDTest'] = 'When was your last STD test?';
			$err = true;
		}
		if(empty($_REQUEST['OneOrMoreSexualPartner']))
		{
			$errmgs['OneOrMoreSexualPartner'] = 'Have you had one or more new sexual partners since your last STD test, or within the last 6 months?';
			$err = true;
		}
		if(empty($_REQUEST['ConcernedWithOther']))
		{
			$errmgs['ConcernedWithOther'] = 'Are you concerned about your partner\'s sexual activity with others?';
			$err = true;
		}
		if(empty($_REQUEST['VaccinatedForHepatitisB']))
		{
			$errmgs['VaccinatedForHepatitisB'] = 'Have you been vaccinated for Hepatitis B?';
			$err = true;
		}
		if(empty($_REQUEST['RelationshipWithIVdrug']))
		{
			$errmgs['RelationshipWithIVdrug'] = 'Are you an intravenous (IV) drug user or have you had a relationship with an IV drug user?';
			$err = true;
		}
		if(empty($_REQUEST['InterestedWithGenitalHerpes']))
		{
			$errmgs['InterestedWithGenitalHerpes'] = 'Are you interested to know your genital herpes status?';
			$err = true;
		}
		$Recommandation = $_REQUEST;
		//if(true)
		if(!$err)
		{
			$_SESSION['addressInput'] = $_REQUEST['Zipcode']; 
			$Recommandation['Gender'] = $_REQUEST['Gender'];
			$Recommandation['Age'] = $_REQUEST['Age'];
			$Recommandation['Zipcode'] = $_REQUEST['Zipcode'];
			$Recommandation['SexualPartner'] = $_REQUEST['SexualPartner'];
			$Recommandation['VaccinatedForHepatitisB'] = $_REQUEST['VaccinatedForHepatitisB'];
			$Recommandation['RelationshipWithIVdrug'] = $_REQUEST['RelationshipWithIVdrug'];
			$Recommandation['chkIndi1'] = $_REQUEST['chkIndi1'];
			$Recommandation['SetValue'] = 'Yes';
			$_SESSION['Recommandation'] = $Recommandation;
			$_SESSION['testRec'] = 1;
		}
	} 
   
	if($err) {
		echo '<div class="container">';
			echo ListErrorMsg($errmgs);
		echo '</div>';
	}
?>

<!-- --------------------------------- throw info php end --------------------------------- -->


<!-- --------------------------------- catch info php start --------------------------------- -->
<?php

  $AddToCDC = array();
  $AddToCDC1 = array();
  $TotalPrice = 0;
  $cnt = 0;
  	
  $Recommandation = $_SESSION['Recommandation'];
    	
  if($Recommandation['Gender'] == 'Female' && ($Recommandation['Age'] == 'under18' || $Recommandation['Age'] == '18-24'))
    {      
      $AddToCDC['Chlamydia'] = "chlamydia";
  	}
  if($Recommandation['Gender'] == 'Male' && $Recommandation['SexualPartner'] != 'Female')
  	{
      $AddToCDC['Chlamydia'] = "chlamydia";
      $AddToCDC['Gonorrhea'] = "gonorrhea";
      $AddToCDC['HIV'] = "hiv";
      $AddToCDC['Syphilis'] = "syphilis";
  	}
  if($Recommandation['OneOrMoreSexualPartner'] == 'Yes' || $Recommandation['ConcernedWithOther'] == 'Yes')
  	{
      $AddToCDC['Chlamydia'] = "chlamydia";
      $AddToCDC['Gonorrhea'] = "gonorrhea";
      $AddToCDC['HIV'] = "hiv";
  	}
  if($Recommandation['VaccinatedForHepatitisB'] == 'No' || $Recommandation['VaccinatedForHepatitisB'] == 'DoNotKnow')
    $AddToCDC['Hepatitis B'] = "hepatitis-b";
  if($Recommandation['RelationshipWithIVdrug'] == 'Yes' || $Recommandation['RelationshipWithIVdrug'] == 'DoNotKnow')
    $AddToCDC['Hepatitis C'] = "hepatitis-c";
  if($Recommandation['InterestedWithGenitalHerpes'] == 'Yes')
    $AddToCDC['Herpes 2'] = "genital-herpes";
  	
  if(count($Recommandation['chkIndi1']) > 0)
    {
      foreach($AddToCDC as $key=>$val)
        {
          if(in_array($val,$Recommandation['chkIndi1']))
            {
              unset($AddToCDC[$key]);
  			}
  		}
  		
      foreach($AddToCDC1 as $key=>$val)
        {
          if(in_array($val,$Recommandation['chkIndi1']))
            {
              unset($AddToCDC1[$key]);
  			}
  		}	
  	}
  	
  $posResultString="";
  foreach ($Recommandation['chkIndi1'] as $key=>$value)
    {
      $posResultString.=",$value";
    }
    
  if ($Recommandation['Gender'] && $Recommandation['Age'])
    {
      $insertQ = "insert into testRecData set sessionID='".session_id()."',gender='".$Recommandation['Gender']."',ageGroup='".$Recommandation['Age']."',zipcode='".$Recommandation['Zipcode']."',partners='".$Recommandation['SexualPartner']."',lastTest='".$Recommandation['LastSTDTest']."',newPartners='".$Recommandation['OneOrMoreSexualPartner']."',concernedPartner='".$Recommandation['ConcernedWithOther']."',hepB='".$Recommandation['VaccinatedForHepatitisB']."',IV='".$Recommandation['RelationshipWithIVdrug']."',concernedHerpes='".$Recommandation['InterestedWithGenitalHerpes']."',posTests='$posResultString'";
      $result = mysql_query($insertQ);
    }
    
  $_SESSION['AddToCDC'] = $AddToCDC;
  $_SESSION['AddToCDC1'] = $AddToCDC1;
?>
  
<!-- --------------------------------- catch info php end --------------------------------- -->


<!-- --------------------------------- start css/js calls --------------------------------- -->

<link rel="stylesheet" href="test-recommender-css/demo.css" />
<!-- <script type="text/javascript" src="../lib/jquery.js"></script> -->
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript" src="test-recommender-js/chili-1.7.pack.js"></script>
<script type="text/javascript" src="test-recommender-js/jquery.easing.js"></script>
<script type="text/javascript" src="test-recommender-js/jquery.dimensions.js"></script>
<script type="text/javascript" src="test-recommender-js/jquery.accordion.js"></script>
<script type="text/javascript" src="test-recommender-js/std-testing-options.js"></script>
<script type="text/javascript">
 /** Do not alter the structure otherwise the marking of done ( check mark) will not function **/
 
<!-- --------------------------------- end css/js calls --------------------------------- --> 


<!-- --------------------------------- start jQuery set --------------------------------- --> 

jQuery().ready(function(){
			
var qno = 11; // enter the number of questions here for the progress bar
var wide = $("div.progresso").parent().width();
var increment = parseInt(wide/qno);

var wizard = $("#wizard").accordion({
	header: '.title',
	event: false
});
	
var wizardButtons = $([]);
$("div.title", wizard).each(function(index) {
	wizardButtons = wizardButtons.add($(this)
	.next()
	.children("#navigator")
	.filter(".next, .previous")
	.click(function() {
	wizard.accordion("activate", index + ($(this).is(".next") ? 1 : -1));
	if($(this).is(".next"))
	{
	((($(this).parent()).parent()).children(".title")).children(".uncheck").removeClass("uncheck").addClass("check");
	$("div.progresso").animate({    
		width: '+='+increment
	}, 800, function() {
	// Animation complete.
	});	
}
if($(this).is(".previous")){
	((($(this).parent()).parent()).children(".title")).children(".check").removeClass("check").addClass("uncheck");
	$("div.progresso").animate({    
		width: '-='+increment
	}, 800, function() {
	// Animation complete.
	});	
}
}));
});
	
// bind to change event of select to control first and seconds accordion
// similar to tab's plugin triggerTab(), without an extra method
var accordions = jQuery('#wizard');	
});
function showres(){
	$("div.progresso").width($("div.progresso").parent().width());
	$("div.uncheck").removeClass("uncheck").addClass("check");
	alert("test finished!! do something with the response");
}
function checkmenow()
{
if(document.frmRecommandation.Zipcode.value == '')
{
alert('Zipcode cannot be left empty..');
}
}
</script>

<!-- --------------------------------- end jQuery set --------------------------------- --> 
<?php if($_POST['Zipcode'] != '') { ?>
<style type="text/css">
#wizard .next 
{
display: inline;
}
</style>
<? } ?>


<div id="main" style="margin-top: 10px;">

<table width="960" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="500">
			<div style="float:left; width:38px; font-size:10px; font-weight:bold;">START</div>
			<div style="width:420px; height:10px; border:1px solid #008C8A; margin-bottom:10px; float:left;">
				<div class="progresso"></div>
			</div>		
			<div style="float:left; width:38px; font-size:10px; font-weight:bold; text-align:right;">FINISH</div>
			<div style="clear:both"></div>
    
			<div id="wizardOutterBorder">
            
        		<form id="frmRecommandation" name="frmRecommandation" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                
					<div id="wizard">
                    
						<div>
							<div class="title" style="border-top-color:#EDEDED">
								<div class="question_text"><b>START HERE:</b><br />Are you a man or a woman?</div>
								<div class="uncheck"></div>
							</div>
							<div class="qa_form_back">
                                <div class="content_area">
                                    <select name="Gender" id="Gender" style="width: 100px;" onchange="($(this).parent()).parent().children('.next').css('display','inline');">
                                        <option></option>
                                        <option value="Male" <?php if($_REQUEST['Gender'] == 'Male')echo'selected="selected"'; ?>>Male</option>
                                        <option value="Female" <?php if($_REQUEST['Gender'] == 'Female')echo'selected="selected"'; ?>>Female</option>
                                    </select>
                                </div>
                                <div id="navigator" class="next"></div>
							</div>	
						</div>
            
                        <div>
                            <div class="title">
                                <div class="question_text">How old are you?</div>
                                <div class="uncheck"></div>
                            </div>
                            <div class="qa_form_back">
                                <div class="content_area">
                                    <select name="Age" id="Age" style="width: 100px;" onchange="($(this).parent()).parent().children('.next').css('display','inline');">
                                        <option></option>
                                        <option value="under18" <?php if($_REQUEST['Age'] == 'under18')echo'selected="selected"'; ?>>Under 18</option>
                                        <option value="18-24" <?php if($_REQUEST['Age'] == '18-24')echo'selected="selected"'; ?>>18-24</option>
                                        <option value="25-29" <?php if($_REQUEST['Age'] == '25-29')echo'selected="selected"'; ?>>25-29</option>
                                        <option value="30-39" <?php if($_REQUEST['Age'] == '30-39')echo'selected="selected"'; ?>>30-39</option>
                                        <option value="40-49" <?php if($_REQUEST['Age'] == '40-49')echo'selected="selected"'; ?>>40-49</option>
                                        <option value="50-59" <?php if($_REQUEST['Age'] == '50-59')echo'selected="selected"'; ?>>50-59</option>
                                        <option value="60-69" <?php if($_REQUEST['Age'] == '60-69')echo'selected="selected"'; ?>>60-69</option>
                                        <option value="70+" <?php if($_REQUEST['Age'] == '70+')echo'selected="selected"'; ?>>70+</option>
                                    </select>
                            </div>
                                <div id="navigator" class="previous"></div>
                                <div id="navigator" class="next"></div>
                            </div>
                        </div>
            
                        <div>
                            <div class="title">
                                <div class="question_text">Where are you located?</div>
                                <div class="uncheck"></div>
                            </div>
                            <div class="qa_form_back">
                                <div class="content_area">
                                	Enter Your Zip Code:
                                    <input name="Zipcode" id="Zipcode" onclick="($(this).parent()).parent().children('.next').css('display','inline');" title="Zipcode" type="text" style="width:100px;" value="<?php echo $_REQUEST['Zipcode']?>"/>
                            </div>
                                <div id="navigator" class="previous"></div>
                                <div id="navigator" class="next" onmouseover="javascript:if(document.frmRecommandation.Zipcode.value == '')
{alert('Zipcode cannot be left empty..');this.style.display = 'none';}"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="title">
                                <div class="question_text">Are your sex partners generally...</div>
                                <div class="uncheck"></div>
                            </div>
                            <div class="qa_form_back">
                                <div class="content_area">
                                    <select name="SexualPartner" id="SexualPartner" style="width: 100px;"onchange="($(this).parent()).parent().children('.next').css('display','inline');">
                                        <option></option>
                                        <option value="Male" <?php if($_REQUEST['SexualPartner'] == 'Male')echo'selected="selected"'; ?>>Male</option>
                                        <option value="Female" <?php if($_REQUEST['SexualPartner'] == 'Female')echo'selected="selected"'; ?>>Female</option>
                                        <option value="Both" <?php if($_REQUEST['SexualPartner'] == 'Both')echo'selected="selected"'; ?>>Both</option>
                                    </select>
                            </div>
                                <div id="navigator" class="previous"></div>
                                <div id="navigator" class="next"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="title">
                                <div class="question_text">When was your last STD test?</div>
                                <div class="uncheck"></div>
                            </div>
                            <div class="qa_form_back">
                                <div class="content_area">
                                    <select name="LastSTDTest" id="LastSTDTest"  style="width: 100px;" onchange="($(this).parent()).parent().children('.next').css('display','inline');">
                                        <option></option>
                                        <option value="Never" <?php if($_REQUEST['LastSTDTest'] == 'Never')echo'selected="selected"'; ?>>Never</option>
                                        <option value="+1yr" <?php if($_REQUEST['LastSTDTest'] == '+1yr')echo'selected="selected"'; ?>>+1 Year</option>
                                        <option value="6months" <?php if($_REQUEST['LastSTDTest'] == '6months')echo'selected="selected"'; ?>>6 Months</option>
                                        <option value="3months" <?php if($_REQUEST['LastSTDTest'] == '3months')echo'selected="selected"'; ?>>3 Months</option>
                                    </select>
                            </div>
                                <div id="navigator" class="previous"></div>
                                <div id="navigator" class="next"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="title">
                                <div class="question_text">Have you had one or more new sexual partners since your last STD test, or within the last 6 months?</div>
                                <div class="uncheck"></div>
                            </div>
                            <div class="qa_form_back">
                                <div class="content_area">
                                    <select name="OneOrMoreSexualPartner" id="OneOrMoreSexualPartner" style="width: 100px;" onchange="($(this).parent()).parent().children('.next').css('display','inline');">
                                        <option></option>
                                        <option value="Yes" <?php if($_REQUEST['OneOrMoreSexualPartner'] == 'Yes')echo'selected="selected"'; ?>>Yes</option>
                                        <option value="No" <?php if($_REQUEST['OneOrMoreSexualPartner'] == 'No')echo'selected="selected"'; ?>>No</option>
                                    </select>
                            </div>
                                <div id="navigator" class="previous"></div>
                                <div id="navigator" class="next"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="title">
                                <div class="question_text">Are you concerned about your partner's sexual activity with others?</div>
                                <div class="uncheck"></div>
                            </div>
                            <div class="qa_form_back">
                                <div class="content_area">
                                    <select name="ConcernedWithOther" id="ConcernedWithOther" style="width: 100px;" onchange="($(this).parent()).parent().children('.next').css('display','inline');">
                                        <option></option>
                                        <option value="Yes" <?php if($_REQUEST['ConcernedWithOther'] == 'Yes')echo'selected="selected"'; ?>>Yes</option>
                                        <option value="No" <?php if($_REQUEST['ConcernedWithOther'] == 'No')echo'selected="selected"'; ?>>No</option>
                                    </select>
                            </div>
                                <div id="navigator" class="previous"></div>
                                <div id="navigator" class="next"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="title">
                                <div class="question_text">Have you been vaccinated for Hepatitis B?</div>
                                <div class="uncheck"></div>
                            </div>
                            <div class="qa_form_back">
                                <div class="content_area">
                                    <select name="VaccinatedForHepatitisB" id="VaccinatedForHepatitisB" style="width: 100px;" onchange="($(this).parent()).parent().children('.next').css('display','inline');">
                                        <option></option>
                                        <option value="Yes" <?php if($_REQUEST['VaccinatedForHepatitisB'] == 'Yes')echo'selected="selected"'; ?>>Yes</option>
                                        <option value="No" <?php if($_REQUEST['VaccinatedForHepatitisB'] == 'No')echo'selected="selected"'; ?>>No</option>
                                        <option value="DoNotKnow" <?php if($_REQUEST['VaccinatedForHepatitisB'] == 'DoNotKnow')echo'selected="selected"'; ?>>Don't Know</option>
                                    </select>
                            </div>
                                <div id="navigator" class="previous"></div>
                                <div id="navigator" class="next"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="title">
                                <div class="question_text">Have you ever used intravenous (IV) drugs or have had a relationship with an IV drug user?</div>
                                <div class="uncheck"></div>
                            </div>
                            <div class="qa_form_back">
                                <div class="content_area">
                                    <select name="RelationshipWithIVdrug" id="RelationshipWithIVdrug" style="width: 100px;" onchange="($(this).parent()).parent().children('.next').css('display','inline');">
                                        <option></option>
                                        <option value="Yes" <?php if($_REQUEST['RelationshipWithIVdrug'] == 'Yes')echo'selected="selected"'; ?>>Yes</option>
                                        <option value="No" <?php if($_REQUEST['RelationshipWithIVdrug'] == 'No')echo'selected="selected"'; ?>>No</option>
                                        <option value="DoNotKnow" <?php if($_REQUEST['RelationshipWithIVdrug'] == 'DoNotKnow')echo'selected="selected"'; ?>>Don't Know</option>
                                    </select>
                            </div>
                                <div id="navigator" class="previous"></div>
                                <div id="navigator" class="next"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="title">
                                <div class="question_text">Please check any of the following which you have tested positive for in the past:</div>
                                <div class="uncheck"></div>
                            </div>
                            <div class="qa_form_back">
                                <div class="content_area">
                                    <input type="checkbox" class="customCheckbox" name="chkIndi1[2]" id="chkIndi1" value="hepatitis-b" /> Hepatitis B
                                    <input type="checkbox" class="customCheckbox" name="chkIndi1[4]" id="chkIndi1" value="oral-herpes" /> Herpes 1
                                    <input type="checkbox" class="customCheckbox" name="chkIndi1[7]" id="chkIndi1" value="syphilis" /> Syphilis
                                    <br />
                                    <input type="checkbox" class="customCheckbox" name="chkIndi1[3]" id="chkIndi1" value="hepatitis-c" /> Hepatitis C
                                    <input type="checkbox" class="customCheckbox" name="chkIndi1[5]" id="chkIndi1" value="genital-herpes" /> Herpes 2
                                    <input type="checkbox" class="customCheckbox" name="chkIndi1[6]" id="chkIndi1" value="hiv" /> HIV
                            </div>
                                <div id="navigator" class="previous"></div>
                                <div id="navigator" class="next" style="display: inline;"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="title">
                                <div class="question_text">Would you like to know whether or not you have genital herpes?</div>
                                <div class="uncheck"></div>
                            </div>
                            <div class="qa_form_back">
                                <div class="content_area">
                                    <select name="InterestedWithGenitalHerpes" id="InterestedWithGenitalHerpes" style="width: 100px;" onchange="document.frmRecommandation.formSubmitTestRec.disabled = false;">                                        <option></option>
                                        <option value="Yes" <?php if($_REQUEST['InterestedWithGenitalHerpes'] == 'Yes')echo'selected="selected"'; ?>>Yes</option>
                                        <option value="No" <?php if($_REQUEST['InterestedWithGenitalHerpes'] == 'No')echo'selected="selected"'; ?>>No</option>
                                    </select>
                            </div>
                                <div id="navigator" class="previous"></div>
                            </div>
                        </div>
                        
					</div>
            
					<div style="height:60px; background-color:#008C8A;">
						<div style="padding:16px 15px 0 0; float:right;">
							<span style="color:#FFF; font-size:15px; font-weight:bold; font-style:oblique;">CLICK HERE FOR YOUR TEST RECOMMENDATION:</span> <input name="formSubmitTestRec" type="submit" disabled="true" class="submit" value="Submit"/>
						</div>
					</div>
        
				<input type="hidden" name="MyRecommandation" id="MyRecommandation" value="MyRecommandation" />
				</form>	

			</div>	

		</td>
		<td width="20">&nbsp;</td>
		<td align="left" style="vertical-align: top;" >
			<div id="wizard-rightSideTop">            


<? if (isset($_POST['MyRecommandation'])) { ?>
            	<div id="recommender-results-post">
					<form name="testOptionsL">
						<input type="hidden" name="indiTestString">
						<div style="font-size:22px; color:#006E6C; font-weight:bold;">Your STD Test Recommendation:</div>
                        <div style="font-size:16px;">Your recommended tests are selected below.</div>
                        <div style="font-size:12px;">Select more tests for additional savings.</div>
						<div style="float:left; margin-top:10px;">
							<li style="list-style:none;"><input type="checkbox" name="indiTests" value="chlamydia"<?php if (in_array("chlamydia", $_SESSION['AddToCDC'])) echo "checked";?> onclick="createTestString()"> Chlamydia</li>
							<li style="list-style:none;"><input type="checkbox" name="indiTests" value="gonorrhea" <?php if (in_array("gonorrhea", $_SESSION['AddToCDC'])) echo "checked";?> onclick="createTestString()"> Gonorrhea</li>
							<li style="list-style:none;"><input type="checkbox" name="indiTests" value="oral-herpes" <?php if (in_array("oral-herpes", $_SESSION['AddToCDC'])) echo "checked";?> onclick="createTestString()"> Herpes I</li>
							<li style="list-style:none;"><input type="checkbox" name="indiTests" value="genital-herpes" <?php if (in_array("genital-herpes", $_SESSION['AddToCDC'])) echo "checked";?> onclick="createTestString()"> Herpes II</li>
						</div>
						<div style="float:left; margin-left:20px; margin-top:10px;">
							<li style="list-style:none;"><input type="checkbox" name="indiTests" value="hiv" <?php if (in_array("hiv", $_SESSION['AddToCDC'])) echo "checked";?> onclick="createTestString()"> HIV</li>
							<li style="list-style:none;"><input type="checkbox" name="indiTests" value="syphilis" <?php if (in_array("syphilis", $_SESSION['AddToCDC'])) echo "checked";?> onclick="createTestString()"> Syphilis</li>
							<li style="list-style:none;"><input type="checkbox" name="indiTests" value="hepatitis-b" <?php if (in_array("hepatitis-b", $_SESSION['AddToCDC'])) echo "checked";?> onclick="createTestString()"> Hepatitis B</li>
							<li style="list-style:none;"><input type="checkbox" name="indiTests" value="hepatitis-c" <?php if (in_array("hepatitis-c", $_SESSION['AddToCDC'])) echo "checked";?> onclick="createTestString()"> Hepatitis C</li>
						</div>
						<div style="float:right">
							<div style="padding-bottom:10px; color:#3C748B; font-style:italic; font-size:28px; font-weight:bold;" align="center" id="indiPriceText"></div>
							<div id="btnStartSTDOrderOrange"><a href="#" onclick="orderIndiTests()" onMouseOver="this.style.cursor='pointer'">Click To Order Your STD Tests</a></div>
						</div>
					</form>
                </div>
                <div style="clear:both;"></div>
                <div>
                	<div style="font-size:18px; margin-top:20px;">Have Total Peace of Mind</div>
                    <div style="font-size:16px; margin-bottom:10px;">Order our <span style="color:#006E6C; font-weight:bold;">Total Testing Package</span> (8 tests)</div>
                	<div style="float:left; background-image:url(http://c189814.r14.cf1.rackcdn.com/checkmarks.gif); background-repeat:no-repeat; width:195px; height:85px;">
                    	<div style="float:left; margin-left:25px; line-height:19px;">Chlamydia<br />Gonorrhea<br />Herpes I<br />Herpes II</div>
                    	<div style="float:left; margin-left:30px; line-height:19px;">HIV<br />Syphilis<br />Hepatitis B<br />Hepatitis C</div>
                    </div>
                    <div style="float:right">
                        <div style="padding-bottom:10px; color:#3C748B; font-style:italic; font-size:28px; font-weight:bold;" align="center">Only $319</div>
                        <div id="btnStartSTDOrderBlue"><a href="select-testing-center?item=complete-std-package">Click To Order Your STD Tests</a></div>
                    </div>
                </div>
                <div style="clear:both;"></div>
<? } else { ?>
                <div id="recommender-results-pre">
                
                    <h1>STD Test Recommender</h1> 
                    <h2>YOUR ANSWERS ARE ANONYMOUS</h2>
                    <p>Developed by our Medical Advisory Board and based on guidelines from the CDC, AHRQ, and the USPSTF, this STD test recommendation tool was developed to help provide you with general testing guidelines.</p>
                    <p>It's important to understand that many STDs are asymptomatic, meaning you could be infected and show no signs and symptoms of STDs.</p>
                
                </div>
<? } ?>
                
			</div>
			<div id="wizard-rightSideBottom">
            	<div style="float:right; margin-left:20px;"><img src="http://c189814.r14.cf1.rackcdn.com/tr-privacy-protect.jpg" width="75" height="108" alt="secure std testing privacy protected"></div>Our test recommendation tool assesses your risk for having an STD based on advice from our Medical Advisory Board and national screening guidelines including the US Centers for Disease Control (CDC), US Agency for Healthcare Research and Quality (AHRQ), and the US Preventive Service Task Force (USPSTF). It is not a medical diagnostics tool and does not replace the advice of your doctor.  Your answers are completely anonymous.
            </div>
		</td>
	</tr>
</table>
</div>

<?php
  echo "<script>
    createTestString();
  </script>";
?>

<form name="optionsForm" action="<?php echo site_url('customer-checkout', 'https')?>" method="post">
<input type="hidden" name="action" value="checkout">
<input type="hidden" name="athome" value="1">
<input type="hidden" name="totalCost" value="">
<input type="hidden" name="codeString" value="">
<input type="hidden" name="nameString" value="">
<input type="hidden" name="priceString" value="">
<input type="hidden" name="packageType" value="">
<input type="hidden" name="packageName" value="">
<input type="hidden" name="a_aid" value="<?php echo $_SESSION['a_aid']; ?>">
<input type="hidden" name="http_referer" value="<?php echo $_SESSION['HTTP_REFERER']; ?>">
<input type="hidden" name="environment" value="<?php echo $_SERVER['HTTP_HOST']; ?>">
<input type="hidden" name="" value="">
</form>

<form name="optionsFormLab" action="select-testing-center" method="POST">
<input type="hidden" name="item" value="">
<input type="hidden" name="packName" value="">
</form>
<?php
get_footer();
