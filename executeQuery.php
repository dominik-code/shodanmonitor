<?php

require_once 'config.php';
require_once 'Host.class.php';
require_once 'Hostname.class.php';
require_once 'Port.class.php';
require_once 'Tag.class.php';
require_once 'Vuln.class.php';



$isValid = filter_var($_REQUEST['ipv4'], FILTER_VALIDATE_IP,FILTER_FLAG_IPV4);

if(§isValid == false) {
    die("not correct ipv4 format");
}

$ipv4 = $_REQUEST['ipv4'];

$apikey = APIKEY;

$url = "https://api.shodan.io/shodan/host/$ipv4?key=$apikey&history=False&minify=True";

$ch = curl_init();
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
curl_setopt($ch, CURLOPT_URL, $url);

// Execute
$result = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// Closing
curl_close($ch);

// Will dump a beauty json :3
$result_array = json_decode($result, true);

if ($http_status != "200") {
    if($http_status == "404") {
        exit("No data for this ip");
    }
    exit("Wrong HTTP Status Code: $http_status");
}

if (isset($result_array['error'])) {
    // happens if "No information available for that IP."
    exit("An error was detected.");
}


if (!isset($result_array['ip'])) {
    // ip as integer 12345678
    die("No IP given");
}


if (!Host::exist($result_array['ip'])) {
    // insert if not exist
    Host::create($result_array['ip'], $result_array['ip_str'], $result_array['last_update']);
}

$host = new Host($result_array['ip']);

if (isset($result_array['last_update'])) {
    $host->updateLastUpdate($result_array['last_update']);
}

if (isset($result_array['ports'])) {
    $host->updatePorts($result_array['ports']);
}

if (isset($result_array['vulns'])) {
    $host->updateVulns($result_array['vulns']);
}

if (isset($result_array['tags'])) {
    $host->updateTags($result_array['tags']);
}

if (isset($result_array['hostnames'])) {
    $host->updateHostnames($result_array['hostnames']);
}

if (isset($result_array['country_code']) || isset($result_array['country_code3']) || isset($result_array['country_name'])) {
    $host->updateCountry($result_array['country_code'], $result_array['country_code3'], $result_array['country_name']);
}

if (isset($result_array['os'])) {
    $host->updateOS($result_array['os']);
}

if (isset($result_array['org'])) {
    $host->updateORG($result_array['org']);
}

if (isset($result_array['asn'])) {
    $host->updateASN($result_array['asn']);
}

if (isset($result_array['isp'])) {
    $host->updateISP($result_array['isp']);
}

if (isset($result_array['latitude']) && isset($result_array['longitude'])) {
    $host->updateLocation($result_array['latitude'], $result_array['longitude']);
}


//var_dump($host);



// start by adding host to table host

// if host already exist take the following actions

//  - remove tags via host_id
//  - remove hostnames via host_id
//  - remove vulns via host_id
//  - remove ports via host_id

//  - update main host entry according to the JSON result
//  - add tags, hostnames, vulns, ports || use existing entries


// check if array key exist and than generate the query based on result


