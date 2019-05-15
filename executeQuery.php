<?php

require_once 'config.php';

$ipv4 = "9.9.9.9";
$apikey = APIKEY;

$url = "https://api.shodan.io/shodan/host/$ipv4?key=$apikey&history=False&minify=True";

$ch = curl_init();
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
curl_setopt($ch, CURLOPT_URL, $url);

$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// Execute
$result = curl_exec($ch);
// Closing
curl_close($ch);

// Will dump a beauty json :3
$result_array = json_decode($result, true);
//var_dump($result_array);

if($http_status != "200") {
    exit("Wrong HTTP Status Code: $http_status");
}

if(isset($result_array['error'])) {
    // happens if "No information available for that IP."
    exit("An error was detected.");
}



$connection = new mysqli(DBHOST, DBUSER, DBPASS, DATABASE);
if ($connection->connect_error) {
    die("Secured");
}


// check if array key exist and than generate the query based on result
if (isset($result_array['region_code'])) {
    // can be null
    if ($result_array['region_code'] == "null") {

    }
}
if (isset($result_array['tags'])) {
    // array with key => value e.g. 0 => vpn  1 => dns
    foreach ($result_array['tags'] as $tag) {

        echo $tag;
    }
}
if (isset($result_array['ip'])) {
    // ip as integer 12345678
}
if (isset($result_array['ip_str'])) {
    // ip as 1.2.3.4
}
if (isset($result_array['os'])) {
    // null or string
}
if (isset($result_array['area_code'])) {

}
if (isset($result_array['latitude'])) {

}
if (isset($result_array['longitude'])) {

}
if (isset($result_array['hostnames'])) {
    // array  0 => "bla.example.net"
    foreach ($result_array['hostnames'] as $hostname) {
        echo $hostname;
    }
}
if (isset($result_array['postal_code'])) {

}
if (isset($result_array['dma_code'])) {

}
if (isset($result_array['country_code'])) {
    // 2 CHAR eg. DE
}
if (isset($result_array['org'])) {

}
if (isset($result_array['data'])) {
    // array is empty/null when minified

    // will not be saved
}
if (isset($result_array['asn'])) {

}
if (isset($result_array['city'])) {

}
if (isset($result_array['isp'])) {

}
if (isset($result_array['last_update'])) {
    // e.g 2019-05-08T11:43:18.823217
    $last_update = "";
}
if (isset($result_array['country_code3'])) {
    // 3 CHAR eg. DEU
}
if (isset($result_array['country_name'])) {
    // eg. Germany
}
if (isset($result_array['ports'])) {
    // array eg.  0 => 22, 1 => 80, 2 => 443
    foreach ($result_array['ports'] as $port) {
        echo $port;
    }
}
if (isset($result_array['vulns'])) {
    // array eg. 0 => "CVE-2011-4321", 1 => "CVE-2011-1234"
    foreach ($result_array['vulns'] as $vuln) {
        echo $vuln;
    }
}
if (isset($result_array[''])) {

}


$prepared = $connection->prepare("INSERT INTO `host` ( `ip` , `ip_str` , `last_update` ) VALUES ( ? , ? , ? ) ; ");
if ($prepared == false) {
    die("Secured");
}

$result_query_prepare = $prepared->bind_param("ss", $result_array['ip'], $result_array['ip_str'], $last_update);
if ($result_query_prepare == false) {
    die("Secured");
}

$result_query_execute = $prepared->execute();
if ($result_query_execute == false) {
    die("Secured");

}

$prepared->close();
$connection->close();
