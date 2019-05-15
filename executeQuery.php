<?php

require_once 'config.php';

$ipv4 = "9.9.9.9";
$apikey = APIKEY;

$url = "https://api.shodan.io/shodan/host/$ipv4?key=$apikey&history=False&minify=True";

$ch = curl_init();
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
curl_setopt($ch, CURLOPT_URL,$url);
// Execute
$result=curl_exec($ch);
// Closing
curl_close($ch);

// Will dump a beauty json :3
var_dump(json_decode($result, true));