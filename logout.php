<?php
	$baseURL = 'http://apcr.hol.es/attendance/';
	$id = 'b4319422268043312f2b98daad5e7040';
	$token = $_COOKIE['wotm8']['token'];
	
	$url = 'https://api.worldoftanks.eu/wot/auth/logout/';
	$fields = array(
							'application_id' => urlencode($id),
							'access_token' => urlencode($token),
					);
	
	//url-ify the data for the POST
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');
	
	//open connection
	$ch = curl_init();
	
	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
	
	//execute post
	$result = curl_exec($ch);
	
	//close connection
	curl_close($ch);
	setcookie("wotm8[token]", '', time()-3600);
	setcookie("wotm8[id]", '', time()-3600);
	setcookie("wotm8[nick]", '', time()-3600);
	header("Location: $baseURL");

?>