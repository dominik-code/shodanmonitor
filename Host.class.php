<?php


class Host {

    private $id = -1;
    private $ip = -1;
    private $ip_str = "";

    public function __construct($ip) {
        $this->ip = $ip;
        $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DATABASE);
        if ($mysqli->connect_error) {
            die("Secured");
        }

        if (!($stmt = $mysqli->prepare("SELECT * FROM host where ip = ?"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }

        $stmt->bind_param("i", $this->id);

        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        if (!($res = $stmt->get_result())) {
            echo "Getting result set failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        $data = $res->fetch_assoc();

        $this->id = $data['id'];
        $this->ip_str = $data['ip_str'];

        $res->close();

    }

    // multiple values

    public function updateHostnames($hostnames) {

    }

    public function updatePorts($ports) {

    }

    public function updateVulns($vulns) {

    }

    public function updateTags($tags) {

    }

    // single values

    public function updateLastUpdate($lastUpdate) {

    }

    public function updateCountry($country_code, $country_code3, $country_name) {

    }

    public function updateLocation($latitude, $longitude) {

    }

    public function updateASN($asn) {

    }

    public function updateISP($isp) {

    }

    public function updateOS($os) {

    }

    public function updateORG($org) {

    }



    public static function exist($ip) {
        $rows = 0;

        $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DATABASE);
        if ($mysqli->connect_error) {
            die("Secured");
        }


        $query = "SELECT id FROM host WHERE ip = ?";
        if ($stmt = $mysqli->prepare($query)) {

            $result_query_prepare = $stmt->bind_param("s", $ip);
            if ($result_query_prepare == false) {
                die("Secured");
            }

            /* execute query */
            $stmt->execute();

            /* store result */
            $stmt->store_result();

            $rows = $stmt->num_rows;


            /* close statement */
            $stmt->close();
        }

        var_dump($rows);
        if ($rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function create($ip, $ip_str, $last_update) {
        $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DATABASE);
        if ($mysqli->connect_error) {
            die("Secured");
        }

        $prepared = $mysqli->prepare("INSERT INTO `host` ( `ip` , `ip_str` , `last_update` ) VALUES ( ? , ? , ? ) ; ");
        if ($prepared == false) {
            die("Secured");
        }

        $result_query_prepare = $prepared->bind_param("sss", $ip, $ip_str, $last_update);
        if ($result_query_prepare == false) {
            die("Secured");
        }

        $result_query_execute = $prepared->execute();
        if ($result_query_execute == false) {
            die("Secured");
        }

        $prepared->close();
        $mysqli->close();
    }


}