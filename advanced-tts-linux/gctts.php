<?php

// show all errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// grab text in the clipboard
exec('xclip -o', $clipboard);

// remove new lines, formatting into one line
$JSON_request['input']['text'] = implode("\n", $clipboard);

// testing...
//var_dump($clipboard);

$GCP_TTS_Key = '<<PASTE_GCP_API_KEY_HERE>>';

// Google Cloud TTS Configuration
$JSON_request['voice']['languageCode'] ="en-GB";
// $JSON_request['voice']['name'] ="en-GB-Standard-C"; // cheaper female
$JSON_request['voice']['name'] ="en-GB-Wavenet-F"; // better female
$JSON_request['voice']['ssmlGender'] ="FEMALE"; // let it pick?
$JSON_request['audioConfig']['audioEncoding']="MP3";
$JSON_request['audioConfig']['speakingRate']="1.1";

// Quick API Function
function googletts_json_api($key = false, $method, $URI, $data) {
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
	curl_setopt($ch, CURLOPT_URL, $URI.'?key='.$key);

	// (array) will be converted to html form style, or send json string for body
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	// response should be JSON formatted
	$result = curl_exec($ch);

	var_dump($result);

	// is http error code in 4**/5** error range
	$status = (string) curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if( $status[0] == 4 || $status[0] == 5 ) {
		return false;	
	}
	// curl error
	elseif($result === false) {
		error_log("API ERROR: Connection failure: $URI", 0);
		return false;
	}
	else return json_decode($result, true);

	curl_close($ch);
}

// make the call...
$response = googletts_json_api($GCP_TTS_Key, 'POST', 'https://texttospeech.googleapis.com/v1/text:synthesize', json_encode($JSON_request));

// dump the audio to a temp location (temp is cleared on reboot etc)
file_put_contents('/tmp/gctts.mp3', base64_decode($response['audioContent']));

// play the audio
exec('play -q -t mp3 /tmp/gctts.mp3');

?>