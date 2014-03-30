// JavaScript Document
function orderIndiTests()
{
    var testString = document.testOptionsL.indiTestString.value;
    if (!testString)
    {
      alert('Please select at least 1 test before proceeding');
      return false;
    }
    document.optionsFormLab.packName.value = "";
    document.optionsFormLab.item.value = testString;
    document.optionsFormLab.submit();
}


function createTestString() 
{
	testpicked = "";
	document.optionsFormLab.packName.value = "";
  testsFound = 0;
	add=document.testOptionsL.indiTests;
	
	//add total of selected values
	for (i = 0; i < add.length; i++){
		if(add[i].checked){
			testpicked = testpicked + add[i].value + ",";
			testsFound++;
		}
	}
	
	if (testsFound == 1) finalprice=89;
	if (testsFound == 2) finalprice=178;
	if (testsFound == 3 || testsFound == 4) finalprice=189;
	if (testsFound == 5 || testsFound == 6 || testsFound == 7 || testsFound == 8) finalprice=249;
	
	 
	
	//update tests selected
	document.testOptionsL.indiTestString.value = testpicked;
	
	//Update Price Text
	if (!testsFound) document.getElementById('indiPriceText').innerHTML = '$0'; 
	else document.getElementById('indiPriceText').innerHTML = '$' + finalprice.toFixed(0);
}

function createTestStringValPack() 
{
  var valuePicSel = document.form1.bonusMenu;
  
  var getSelectedIndex = document.form1.bonusMenu.selectedIndex;
	var testChoice = document.form1.bonusMenu[getSelectedIndex].value;
  testpicked = "chlamydia,gonorrhea,hiv,"+testChoice;
	//update tests selected
	document.optionsFormLab.item.value = testpicked;
  document.optionsFormLab.packName.value = "valPack";
}

function createTestStringMain()
{
  var stdName = document.specificTestForm.stdName.value;
  var itemString = stdName;
  if (document.specificTestForm.upgrade)
  {
    if (document.specificTestForm.upgrade.checked)
    {
      itemString = itemString+','+document.specificTestForm.upgrade.value;
    }
  }
  document.optionsFormLab.item.value = itemString;
  document.optionsFormLab.submit();
}

function createHomeTestString() 
{
	testpicked = "";
  testsFound = 0;
	add=document.testOptionsH.homekitSel;
	
	//add total of selected values
	for (i = 0; i < add.length; i++){
		if(add[i].checked)
    {
			if (testsFound) testpicked = testpicked + "," + add[i].value;
			else testpicked = add[i].value;
			testsFound++;
		}
	}
	
	if (testpicked == "11361")
  {
    finalprice=90;
    document.optionsForm.totalCost.value=finalprice;
    document.optionsForm.codeString.value=testpicked;
    document.optionsForm.nameString.value="Chlamydia";
    document.optionsForm.priceString.value="90";
    document.optionsForm.packageType.value="indi";
    document.optionsForm.packageName.value=""; 
  }
  if (testpicked == "11362")
  {
    finalprice=90;
    document.optionsForm.totalCost.value=finalprice;
    document.optionsForm.codeString.value=testpicked;
    document.optionsForm.nameString.value="Gonorrhea";
    document.optionsForm.priceString.value="90";
    document.optionsForm.packageType.value="indi";
    document.optionsForm.packageName.value=""; 
  }
	if (testpicked == "11361,11362")
  {
    finalprice=150;
    document.optionsForm.totalCost.value=finalprice;
    document.optionsForm.codeString.value=testpicked;
    document.optionsForm.nameString.value="Chlamydia|Gonorrhea";
    document.optionsForm.priceString.value="90|90";
    document.optionsForm.packageType.value="indi";
    document.optionsForm.packageName.value=""; 
  }
	if (testpicked == "19550")
	{
    finalprice=195;
    document.optionsForm.totalCost.value=finalprice;
    document.optionsForm.codeString.value=testpicked;
    document.optionsForm.nameString.value="Trichomoniasis";
    document.optionsForm.priceString.value="195";
    document.optionsForm.packageType.value="indi";
    document.optionsForm.packageName.value=""; 
  }
	if (testpicked == "11361,11362,19550")
	{
    finalprice=255;
    document.optionsForm.totalCost.value=finalprice;
    document.optionsForm.codeString.value=testpicked;
    document.optionsForm.nameString.value="Chlamydia|Gonorrhea|Trichomoniasis";
    document.optionsForm.priceString.value="90|90|195";
    document.optionsForm.packageType.value="group";
    document.optionsForm.packageName.value="Home Test Package"; 
  }
	if (testpicked == "11361,19550")
	{
    finalprice=215;
    document.optionsForm.totalCost.value=finalprice;
    document.optionsForm.codeString.value=testpicked;
    document.optionsForm.nameString.value="Chlamydia|Trichomoniasis";
    document.optionsForm.priceString.value="90|195";
    document.optionsForm.packageType.value="indi";
    document.optionsForm.packageName.value=""; 
  }
  if (testpicked == "11362,19550")
  {
    finalprice=215;
    document.optionsForm.totalCost.value=finalprice;
    document.optionsForm.codeString.value=testpicked;
    document.optionsForm.nameString.value="Gonorrhea|Trichomoniasis";
    document.optionsForm.priceString.value="90|195";
    document.optionsForm.packageType.value="indi";
    document.optionsForm.packageName.value=""; 
  }
	
	//Update Price Text
	if (!testsFound)
  {
    document.getElementById('indiHomePriceText').innerHTML = 'Select Tests For Pricing';
    document.testOptionsH.homeTest[1].checked = false;
  } 
	else
  {
    document.testOptionsH.homeTest[1].checked = true;
    document.getElementById('indiHomePriceText').innerHTML = '$' + finalprice.toFixed(0);
  }
}

function clearIndiHomeTests()
{
   for (i=0;i<document.testOptionsH.homekitSel.length;i++)
        {
            document.testOptionsH.homekitSel[i].checked = false;
        }
        document.optionsForm.totalCost.value="";
        document.optionsForm.codeString.value="";
        document.optionsForm.nameString.value="";
        document.optionsForm.priceString.value="";
        document.optionsForm.packageType.value="";
        document.optionsForm.packageName.value="";
      	document.getElementById('indiHomePriceText').innerHTML = 'Select Tests For Pricing'; 
}
