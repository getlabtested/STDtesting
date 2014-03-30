// JavaScript Document

function get_radio_value(radioGroupName)
{
  if (!document.optionsForm.codeString.value)
  {
      alert('Please Select At Least One Test For Your Home Kit Order');
      return false;
  }
     
   document.optionsForm.submit();
}

function orderIndiTests()
{
    var testString = document.testOptionsL.indiTestString.value;
    if (!testString)
    {
      alert('Please select at least 1 test before proceeding');
      return false;
    }
    var winLoc = '/select-testing-center?item='+testString;
    window.location = winLoc;
}


function createTestString() 
{
	testpicked = "";
  testsFound = 0;
	add=document.testOptionsL.indiTests;
	
	//add total of selected values
	for (i = 0; i < add.length; i++){
		if(add[i].checked){
			testpicked = testpicked + add[i].value + ",";
			testsFound++;
		}
	}
	
	if (testsFound == 1) finalprice=90;
	if (testsFound == 2) finalprice=150;
	if (testsFound == 3 || testsFound == 4) finalprice=195;
	if (testsFound == 5 || testsFound == 6) finalprice=225;
	if (testsFound == 7 || testsFound == 8) finalprice=245;
	
	 
	
	//update tests selected
	document.testOptionsL.indiTestString.value = testpicked;
	
	//Update Price Text
	if (!testsFound) document.getElementById('indiPriceText').innerHTML = 'Select Tests For Pricing'; 
	else document.getElementById('indiPriceText').innerHTML = 'Only $' + finalprice.toFixed(0);
}

function createHomeTestString() 
{
	testpicked = "";
	nameString = "";
	priceString = "";
	var finalprice = 0;
	var discount = 0;
  testsFound = 0;
	add=document.testOptionsH.homekitSel;
	add2=document.testOptionsH.homekitSel2;
	
	//add total of selected values
	for (i = 0; i < add.length; i++)
  {
    if(add[i].checked)
    {
			var price = 90;
      
      if (add[i].value == '11361') var name = "Chlamydia";
      if (add[i].value == '11362') var name = "Gonorrhea";
      if (add[i].value == 'pp102') var name = "HIV Express";
      if (add[i].value == '19550') 
      {
        var name = "Trichomoniasis";
        var price = 195; 
      }
      
      if (testsFound)
      {
        testpicked = testpicked + "," + add[i].value;
        nameString = nameString + "|" + name;
        priceString = priceString + "|" + price;
      }
			else 
      {
        testpicked = add[i].value;
        nameString = name;
        priceString = price; 
      }
      
      testsFound++;
      
			finalprice = finalprice+price
		}
	}
	
	//Chlamydia
	if (testpicked == "11361") finalprice=90;
  if (testpicked == "11361,11362") finalprice=150;
  if (testpicked == "11361,19550") finalprice=225;
  if (testpicked == "11361,pp102") finalprice=150;
  if (testpicked == "11361,11362,19550") finalprice=265;
  if (testpicked == "11361,11362,pp102") finalprice=194;
  if (testpicked == "11361,19550,pp102") finalprice=265;
  if (testpicked == "11361,11362,19550,pp102") finalprice=309;
  
  //Gonorrhea
  if (testpicked == "11362") finalprice=90;
  if (testpicked == "11362,19550") finalprice=225;
  if (testpicked == "11362,pp102") finalprice=150;
  if (testpicked == "11362,19550,pp102") finalprice=265;
  
  //Trich
  if (testpicked == "19550") finalprice=195;
  if (testpicked == "19550,pp102") finalprice=225;
  
  //HIV
  if (testpicked == "pp102") finalprice=90;   
	
	  document.optionsForm.totalCost.value=finalprice;
    document.optionsForm.codeString.value=testpicked;
    document.optionsForm.nameString.value=nameString;
    document.optionsForm.priceString.value=priceString;
    document.optionsForm.packageType.value="indi";
    document.optionsForm.packageName.value="Home Test Package";
    
	
	if (finalprice) finalpriceWithShipping = finalprice+15;
	
	//Update Price Text
	if (!testsFound) document.getElementById('indiHomePriceText').innerHTML = 'Select Tests For Pricing';
  else document.getElementById('indiHomePriceText').innerHTML = '$' + finalpriceWithShipping.toFixed(0);
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
      	document.getElementById('indiHomePriceText').innerHTML = ''; 
}

function selectHIVTest(checkedVal)
{  
   if (checkedVal)
   {
     var hivChecked;
     for (i=0;i<document.testOptionsH.homekitSel2.length;i++)
     {
        if (document.testOptionsH.homekitSel2[i].checked) hivChecked = document.testOptionsH.homekitSel2[i].value;
     }
     if (!hivChecked) document.testOptionsH.homekitSel2[0].checked = true;
    }
    else
    {
       for (i=0;i<document.testOptionsH.homekitSel2.length;i++)
       {
          document.testOptionsH.homekitSel2[i].checked = false;
       } 
    }
    createHomeTestString();
}

function checkHIVRadio()
{
  if (!document.testOptionsH.homekitSelHIV.checked) document.testOptionsH.homekitSelHIV.checked=true;
  createHomeTestString();  
}
