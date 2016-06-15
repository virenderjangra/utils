<?php 
function locate($lat,$lng)
{
	$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
	$json = @file_get_contents($url);
	$data = json_decode($json);
	$status = $data->status;
	if($status=="OK")	return $data->results[0];
	else				return false;
}

function parseAddress($result, $level = "full"){	
	$area="";$city="";$state="";$country = "";
	if ($result->address_components) {		
		foreach($result->address_components as $cmp){
			foreach($cmp->types as $type){
				if($type == 'sublocality')	$area = $cmp->long_name;
				if($type == 'locality')		$city = $cmp->long_name;
				if($type == 'administrative_area_level_1')	$state = $cmp->long_name;
				if($type == 'country')		$country = $cmp->long_name;
				//echo $type . "<br/>";
			}
		}		
	}
	
	if($level == 'area') return "$area, $city, $state, $country";
	
	if($level == 'city') return "$city, $state, $country";
	
	if($level == 'state') return "$state, $country";
	
	if($level == 'country') return "$country";
	
	return $result->formatted_address;
}

if(isset($_REQUEST['latlng'])){
	$cords = $_REQUEST['latlng'];	
	list($lat,$lng) = explode(",",$cords);	
	$address= locate($lat,$lng);		
	if($address){
		if(isset($_REQUEST['level'])) $address = parseAddress($address,$_REQUEST['level']);
		else $address = parseAddress($address);				
	}else{
		$address = "Invalid-Latitude-Longitude";
	}
	
	if(isset($_REQUEST['encode']) && $_REQUEST['encode'] == true){
		$address = urlencode($address);
	}
	
}else{
	$address = "Missing-Latitude-Longitude.GPS-might-be-Off";
}

//var device_location = "";
//loadCommands("https://SERVER_LINK/location.php?latlng={{latlng_string}}",,onLoadComplete);
?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
	<array>
	    <dict>
	        <key>version</key>
	        <string>2</string>
	    </dict>
		<dict>
           <key>let</key>
           <string>device_location="<?= $address ?>" </string>
       </dict>
	</array>
</plist>
