<?php

require_once 'config.php';
require_once 'Host.class.php';
require_once 'Hostname.class.php';
require_once 'Port.class.php';
require_once 'Tag.class.php';
require_once 'Vuln.class.php';
require_once 'CIDR.class.php';


function scanIP($starttime, $ip) {
    $runtime = microtime(true) - $starttime;
    if($runtime > 45.0) {
        exit("Runtime limit reached aborting scan for now <br>");
    }


    if (Host::exist(ip2long($ip))) {
        echo "Host: $ip already exist skipping scan in favor of new hosts. <br>";
        return;
    }


    echo "Scanning: ". $ip . "<br>";
    usleep(500000);

    $ipv4 = $ip;

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
        if ($http_status == "404") {
            Host::create(ip2long($ip), $ip, null);
            echo "No data for this ip: $ipv4 <br>";
            return;
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
}



$starttime = microtime(true);


$randomip = "";
$randomip .= mt_rand(1,254);
$randomip .= ".";
$randomip .= mt_rand(0,254);
$randomip .= ".";
$randomip .= mt_rand(0,254);
$randomip .= ".";
$randomip .= mt_rand(1,254);

scanIP($starttime, $randomip);



$mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DATABASE);
if ($mysqli->connect_error) {
    die("Secured");
}

if (!($stmt = $mysqli->prepare("SELECT * FROM scan"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}


if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

if (!($res = $stmt->get_result())) {
    echo "Getting result set failed: (" . $stmt->errno . ") " . $stmt->error;
}

while ($row = $res->fetch_assoc()) {
    $id = $row['id'];
    $cidr = $row['cidr'];
    $name = $row['name'];
    $user_id = $row['user_id'];

    $ips = CIDR::cidrToRange($cidr);
    foreach ($ips as $ip) {
        scanIP($starttime, $ip);
    }
}

$res->close();
$mysqli->close();


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


