// JavaScript Document
    var map;
    var geocoder;
    function load()
	{
		if (GBrowserIsCompatible()) 
		{
			geocoder = new GClientGeocoder();
			map = new GMap2(document.getElementById('map'));
			map.addControl(new GSmallMapControl());
			map.addControl(new GMapTypeControl());
			map.setCenter(new GLatLng(40, -100), 4);
		}
	}

	function searchLocations() 
	{
		var address = $('#addressInput').val();
		var zipEntry = document.getElementById('addressInput').value;
		if(!zipEntry)
		{
			alert('Please enter valid Zipcode');
			$('#addressInput').focus();
			return false;
		}
		else
		{
			
			geocoder.getLatLng(address, function(latlng) 
			{
				if (!latlng)
				{
					alert(address + ' not found');
				}
				else
				{
					$("#sidebar").html('<div id="LoadLocation" align="center"><img src="http://c0001470.cdn1.cloudfiles.rackspacecloud.com/ajaxloading.gif" alt="loading" /></div>');
					$("div[id^=location]").remove();
					searchLocationsNear();
				}
			});
		}
	}
	
	function searchLocationsNear() 
	{
		var searchUrl = 'phpsqlsearch_genxml2.php?zip='+$('#addressInput').val();
		//var searchUrl = 'phpsqlsearch_genxml.php?lat=' + center.lat() + '&lng=' + center.lng()+'&locid='+selectedlocid;
		
		GDownloadUrl(searchUrl, function(data, responseCode) 
		{
			if(responseCode == 200) 
			{
				var xml = GXml.parse(data);
				var markers = xml.documentElement.getElementsByTagName('marker');
				map.clearOverlays();
				
				var sidebar = document.getElementById('sidebar');
				
				sidebar.innerHTML = '';
				if (markers.length == 0) 
				{
					$("#sidebar").html('<div align="center" style="font-weight:bold; padding-top:10px; font-size:15px;">No results found.</div>');
					map.setCenter(new GLatLng(40, -100), 4);
					return;
				}
				
				var bounds = new GLatLngBounds();
				for (var i = 0; i < markers.length; i++) 
				{
					var locid = markers[i].getAttribute('id');
					var name = markers[i].getAttribute('name');
					var labAddr = markers[i].getAttribute('address');
					var labCity = markers[i].getAttribute('city');
					var labState = markers[i].getAttribute('state');
					var labZipcode = markers[i].getAttribute('zip');
					var labHours = markers[i].getAttribute('hours');
					var labPhone = markers[i].getAttribute('telephone');
					var labType = markers[i].getAttribute('lab-id');
					
					var showLC = 1;
					
					if (labType == '129' && labState.toUpperCase()=='MA') showLC=0;
					
					if (labPhone && showLC) 
          {
  					var address = markers[i].getAttribute('address')+'<br>'+markers[i].getAttribute('city')+','+markers[i].getAttribute('state')+' '+markers[i].getAttribute('zip');
  					var distance = parseFloat(markers[i].getAttribute('distance'));
  					var point = new GLatLng(parseFloat(markers[i].getAttribute('lat')),
  											parseFloat(markers[i].getAttribute('lng')));
				
					
            var marker = createMarker(locid,point, name, address, labAddr, labCity, labState, labZipcode, labHours, labPhone, labType);
  					map.addOverlay(marker);
  					var sidebarEntry = createSidebarEntry(marker, locid, name, address, distance, labHours);
  					sidebar.appendChild(sidebarEntry);
  					bounds.extend(point);
  				}
				}
				map.setCenter(bounds.getCenter(), map.getBoundsZoomLevel(bounds));
			}
			else if(responseCode == -1)
			{
				alert("Data request timed out. Please try later.");
			}
			else
			{ 
				alert("Request resulted in error. Check XML file is retrievable.");
			}	
		});
	}

    function createMarker(locid, point, name, address, labAddr, labCity, labState, labZipcode, labHours, labPhone, labType) 
    {
          var selectedlocation = '';
    	    var marker = new GMarker(point);
    	    var idname = '';
          var html = address+'';
          var zipinput = $('#addressInput').val();
    	 
    	 
      	  GEvent.addListener(marker, 'click', function() 
          {
              document.locationForm.labPhone.value = labPhone;
              document.locationForm.labHours.value = labHours;
              document.locationForm.labName.value = name;
              document.locationForm.labAddr.value = labAddr;
              document.locationForm.labCity.value = labCity;
              document.locationForm.labState.value = labState;
              document.locationForm.labZipcode.value = labZipcode;
              document.locationForm.labID.value = locid;
              document.locationForm.zipinput.value = zipinput;
              document.locationForm.labType.value = labType;
      		    document.locationForm.submit();
          });
      	  return marker;
    }
	
	function createSidebarEntry(marker, locid, name, address, distance, labHours) 
	{
      var div = document.createElement('div');
  		if (labHours) var html = name + '<br>' + address + '<br>' + labHours + '<br><b>1-866-749-6269</b><br><img src=\'http://c0001470.cdn1.cloudfiles.rackspacecloud.com/chooseLoc2.jpg\'>';
  		else  var html = name + '<br>' + address + '<br><b>1-866-749-6269</b><br><img src=\'http://c0001470.cdn1.cloudfiles.rackspacecloud.com/chooseLoc2.jpg\'>';
  		div.innerHTML = html;
  		div.id = 'location'+locid;
		
			div.className = 'clickout';

  		GEvent.addDomListener(div, 'click', function() 
  		{
  			GEvent.trigger(marker, 'click');
  		});
  		GEvent.addDomListener(div, 'mouseover', function() 
  		{
  			if(div.className == 'clickover SelectedLocation' || div.className == 'clickout SelectedLocation' || div.className == 'SelectedLocation')
  				div.className == 'SelectedLocation'
  			else
  				div.className = 'clickover';
  		});
  		GEvent.addDomListener(div, 'mouseout', function() 
  		{
  		if(div.className == 'clickout SelectedLocation' || div.className == 'clickover SelectedLocation' || div.className == 'SelectedLocation')
  			div.className == 'SelectedLocation'
  		else
  			div.className = 'clickout';
  		});
  		return div;
	}