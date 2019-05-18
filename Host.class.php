<?php


class Host {

    private $id = -1;
    private $ip = -1;
    private $ip_str = "";

    private $mysqli = null;

    public function __construct($ip) {
        $this->ip = $ip;
        $this->mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DATABASE);
        if ($this->mysqli->connect_error) {
            die("Secured");
        }

        if (!($stmt = $this->mysqli->prepare("SELECT * FROM host where ip = ?"))) {
            echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
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

    public function __destruct() {
        $this->mysqli->close();
    }

    // multiple values

    public function updateHostnames($hostnames) {
        // delete all existing links to the ip
        Hostname::removeLinkToHost($this->id);
        // add all hostnames if not already exist then add the link to host_id
        foreach ($hostnames as $hostname_str) {
            if(!Hostname::exist($hostname_str)) {
                Hostname::create($hostname_str);
            }
            $hostname = new Hostname($hostname_str);
            $hostname->linkToHostId($this->id);
        }
    }

    public function updatePorts($ports) {

    }

    public function updateVulns($vulns) {

    }

    public function updateTags($tags) {

    }

    // single values

    public function updateLastUpdate($lastUpdate) {
        $query = "UPDATE host SET `last_update_miner` = NOW() WHERE ip = ?";
        if ($stmt = $this->mysqli->prepare($query)) {

            $result_query_prepare = $stmt->bind_param("s", $this->ip);
            if ($result_query_prepare == false) {
                die("Secured");
            }

            /* execute query */
            $stmt->execute();

            /* store result */
            $stmt->store_result();

            /* close statement */
            $stmt->close();
        }



        $query = "UPDATE host SET `last_update` = ? WHERE ip = ?";
        if ($stmt = $this->mysqli->prepare($query)) {

            $result_query_prepare = $stmt->bind_param("ss", $lastUpdate, $this->ip);
            if ($result_query_prepare == false) {
                die("Secured");
            }

            /* execute query */
            $stmt->execute();

            /* store result */
            $stmt->store_result();

            /* close statement */
            $stmt->close();
        }
    }

    public function updateCountry($country_code, $country_code3, $country_name) {
        $country_id = null;

        $query = "SELECT id FROM country WHERE country_code = ?";

        if($stmt = $this->mysqli->prepare($query)){
            /*
                 Binds variables to prepared statement

                 i    corresponding variable has type integer
                 d    corresponding variable has type double
                 s    corresponding variable has type string
                 b    corresponding variable is a blob and will be sent in packets
            */
            $stmt->bind_param('s',$country_code);

            /* execute query */
            $stmt->execute();

            /* Get the result */
            $result = $stmt->get_result();

            /* Get the number of rows */
            $num_of_rows = $result->num_rows;



            while ($row = $result->fetch_assoc()) {
                $country_id = $row['id'];
            }

            /* free results */
            $stmt->free_result();

            /* close statement */
            $stmt->close();
        }

        if ($country_id == null) {

            $prepared = $this->mysqli->prepare("INSERT INTO `country` ( `country_code` , `country_code3` , `country_name` ) VALUES ( ? , ? , ? ) ; ");
            if ($prepared == false) {
                die("Secured");
            }

            $result_query_prepare = $prepared->bind_param("sss", $country_code, $country_code3, $country_name);
            if ($result_query_prepare == false) {
                die("Secured");
            }

            $result_query_execute = $prepared->execute();
            if ($result_query_execute == false) {
                die("Secured");
            }

            $country_id = $this->mysqli->insert_id;

            $prepared->close();
        }

        $query = "UPDATE host SET `country_id` = ? WHERE ip = ?";
        if ($stmt = $this->mysqli->prepare($query)) {

            $result_query_prepare = $stmt->bind_param("ss", $country_id, $this->ip);
            if ($result_query_prepare == false) {
                die("Secured");
            }

            /* execute query */
            $stmt->execute();

            /* store result */
            $stmt->store_result();

            /* close statement */
            $stmt->close();
        }

    }

    public function updateLocation($latitude, $longitude) {

        $location_id = null;

        $query = "SELECT id FROM location WHERE latitude = ? AND longitude = ? ";

        if($stmt = $this->mysqli->prepare($query)){
            /*
                 Binds variables to prepared statement

                 i    corresponding variable has type integer
                 d    corresponding variable has type double
                 s    corresponding variable has type string
                 b    corresponding variable is a blob and will be sent in packets
            */
            $stmt->bind_param('dd',$latitude, $longitude);

            /* execute query */
            $stmt->execute();

            /* Get the result */
            $result = $stmt->get_result();

            /* Get the number of rows */
            $num_of_rows = $result->num_rows;



            while ($row = $result->fetch_assoc()) {
                $location_id = $row['id'];
            }

            /* free results */
            $stmt->free_result();

            /* close statement */
            $stmt->close();
        }

        if ($location_id == null) {

            $prepared = $this->mysqli->prepare("INSERT INTO `location` ( `latitude` , `longitude`) VALUES ( ? , ? ) ; ");
            if ($prepared == false) {
                die("Secured");
            }

            $result_query_prepare = $prepared->bind_param("dd", $latitude, $longitude);
            if ($result_query_prepare == false) {
                die("Secured");
            }

            $result_query_execute = $prepared->execute();
            if ($result_query_execute == false) {
                die("Secured");
            }

            $location_id = $this->mysqli->insert_id;

            $prepared->close();
        }

        $query = "UPDATE host SET `location_id` = ? WHERE ip = ?";
        if ($stmt = $this->mysqli->prepare($query)) {

            $result_query_prepare = $stmt->bind_param("ss", $location_id, $this->ip);
            if ($result_query_prepare == false) {
                die("Secured");
            }

            /* execute query */
            $stmt->execute();

            /* store result */
            $stmt->store_result();

            /* close statement */
            $stmt->close();
        }
    }

    public function updateASN($asn) {

        $asn_id = null;

        $query = "SELECT id FROM asn WHERE `name` = ? ";

        if($stmt = $this->mysqli->prepare($query)){
            /*
                 Binds variables to prepared statement

                 i    corresponding variable has type integer
                 d    corresponding variable has type double
                 s    corresponding variable has type string
                 b    corresponding variable is a blob and will be sent in packets
            */
            $stmt->bind_param('s',$asn);

            /* execute query */
            $stmt->execute();

            /* Get the result */
            $result = $stmt->get_result();

            /* Get the number of rows */
            $num_of_rows = $result->num_rows;



            while ($row = $result->fetch_assoc()) {
                $asn_id = $row['id'];
            }

            /* free results */
            $stmt->free_result();

            /* close statement */
            $stmt->close();
        }

        if ($asn_id == null) {

            $prepared = $this->mysqli->prepare("INSERT INTO `asn` ( `name` ) VALUES ( ? ) ; ");
            if ($prepared == false) {
                die("Secured");
            }

            $result_query_prepare = $prepared->bind_param("s", $asn );
            if ($result_query_prepare == false) {
                die("Secured");
            }

            $result_query_execute = $prepared->execute();
            if ($result_query_execute == false) {
                die("Secured");
            }

            $asn_id = $this->mysqli->insert_id;

            $prepared->close();
        }

        $query = "UPDATE host SET `asn_id` = ? WHERE ip = ?";
        if ($stmt = $this->mysqli->prepare($query)) {

            $result_query_prepare = $stmt->bind_param("ss", $asn_id, $this->ip);
            if ($result_query_prepare == false) {
                die("Secured");
            }

            /* execute query */
            $stmt->execute();

            /* store result */
            $stmt->store_result();

            /* close statement */
            $stmt->close();
        }

    }

    public function updateISP($isp) {
        $isp_id = null;

        $query = "SELECT id FROM isp WHERE `name` = ? ";

        if($stmt = $this->mysqli->prepare($query)){
            /*
                 Binds variables to prepared statement

                 i    corresponding variable has type integer
                 d    corresponding variable has type double
                 s    corresponding variable has type string
                 b    corresponding variable is a blob and will be sent in packets
            */
            $stmt->bind_param('s',$isp);

            /* execute query */
            $stmt->execute();

            /* Get the result */
            $result = $stmt->get_result();

            /* Get the number of rows */
            $num_of_rows = $result->num_rows;



            while ($row = $result->fetch_assoc()) {
                $isp_id = $row['id'];
            }

            /* free results */
            $stmt->free_result();

            /* close statement */
            $stmt->close();
        }

        if ($isp_id == null) {

            $prepared = $this->mysqli->prepare("INSERT INTO `isp` ( `name` ) VALUES ( ? ) ; ");
            if ($prepared == false) {
                die("Secured");
            }

            $result_query_prepare = $prepared->bind_param("s", $isp );
            if ($result_query_prepare == false) {
                die("Secured");
            }

            $result_query_execute = $prepared->execute();
            if ($result_query_execute == false) {
                die("Secured");
            }

            $isp_id = $this->mysqli->insert_id;

            $prepared->close();
        }

        $query = "UPDATE host SET `isp_id` = ? WHERE ip = ?";
        if ($stmt = $this->mysqli->prepare($query)) {

            $result_query_prepare = $stmt->bind_param("ss", $isp_id, $this->ip);
            if ($result_query_prepare == false) {
                die("Secured");
            }

            /* execute query */
            $stmt->execute();

            /* store result */
            $stmt->store_result();

            /* close statement */
            $stmt->close();
        }
    }

    public function updateOS($os) {

        $os_id = null;

        $query = "SELECT id FROM os WHERE `name` = ? ";

        if($stmt = $this->mysqli->prepare($query)){
            /*
                 Binds variables to prepared statement

                 i    corresponding variable has type integer
                 d    corresponding variable has type double
                 s    corresponding variable has type string
                 b    corresponding variable is a blob and will be sent in packets
            */
            $stmt->bind_param('s',$os);

            /* execute query */
            $stmt->execute();

            /* Get the result */
            $result = $stmt->get_result();

            /* Get the number of rows */
            $num_of_rows = $result->num_rows;



            while ($row = $result->fetch_assoc()) {
                $os_id = $row['id'];
            }

            /* free results */
            $stmt->free_result();

            /* close statement */
            $stmt->close();
        }

        if ($os_id == null) {

            $prepared = $this->mysqli->prepare("INSERT INTO `os` ( `name` ) VALUES ( ? ) ; ");
            if ($prepared == false) {
                die("Secured");
            }

            $result_query_prepare = $prepared->bind_param("s", $os );
            if ($result_query_prepare == false) {
                die("Secured");
            }

            $result_query_execute = $prepared->execute();
            if ($result_query_execute == false) {
                die("Secured");
            }

            $os_id = $this->mysqli->insert_id;

            $prepared->close();
        }

        $query = "UPDATE host SET `os_id` = ? WHERE ip = ?";
        if ($stmt = $this->mysqli->prepare($query)) {

            $result_query_prepare = $stmt->bind_param("ss", $os_id, $this->ip);
            if ($result_query_prepare == false) {
                die("Secured");
            }

            /* execute query */
            $stmt->execute();

            /* store result */
            $stmt->store_result();

            /* close statement */
            $stmt->close();
        }

    }

    public function updateORG($org) {
        $org_id = null;

        $query = "SELECT id FROM org WHERE `name` = ? ";

        if($stmt = $this->mysqli->prepare($query)){
            /*
                 Binds variables to prepared statement

                 i    corresponding variable has type integer
                 d    corresponding variable has type double
                 s    corresponding variable has type string
                 b    corresponding variable is a blob and will be sent in packets
            */
            $stmt->bind_param('s',$org);

            /* execute query */
            $stmt->execute();

            /* Get the result */
            $result = $stmt->get_result();

            /* Get the number of rows */
            $num_of_rows = $result->num_rows;



            while ($row = $result->fetch_assoc()) {
                $org_id = $row['id'];
            }

            /* free results */
            $stmt->free_result();

            /* close statement */
            $stmt->close();
        }

        if ($org_id == null) {

            $prepared = $this->mysqli->prepare("INSERT INTO `org` ( `name` ) VALUES ( ? ) ; ");
            if ($prepared == false) {
                die("Secured");
            }

            $result_query_prepare = $prepared->bind_param("s", $org );
            if ($result_query_prepare == false) {
                die("Secured");
            }

            $result_query_execute = $prepared->execute();
            if ($result_query_execute == false) {
                die("Secured");
            }

            $org_id = $this->mysqli->insert_id;

            $prepared->close();
        }

        $query = "UPDATE host SET `org_id` = ? WHERE ip = ?";
        if ($stmt = $this->mysqli->prepare($query)) {

            $result_query_prepare = $stmt->bind_param("ss", $org_id, $this->ip);
            if ($result_query_prepare == false) {
                die("Secured");
            }

            /* execute query */
            $stmt->execute();

            /* store result */
            $stmt->store_result();

            /* close statement */
            $stmt->close();
        }
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