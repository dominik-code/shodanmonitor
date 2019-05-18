<?php


class Hostname {

    private $id = -1;
    private $hostname = "";
    private $mysqli = null;


    public function __construct($hostname) {
        $this->hostname = $hostname;
        $this->mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DATABASE);
        if ($this->mysqli->connect_error) {
            die("Secured");
        }

        if (!($stmt = $this->mysqli->prepare("SELECT id FROM hostname where `hostname` = ?"))) {
            echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        }

        $stmt->bind_param("i", $this->hostname);

        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        if (!($res = $stmt->get_result())) {
            echo "Getting result set failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        $data = $res->fetch_assoc();

        $this->id = $data['id'];

        $res->close();


    }

    public static function exist($hostname) {
        $rows = 0;

        $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DATABASE);
        if ($mysqli->connect_error) {
            die("Secured");
        }


        $query = "SELECT id FROM hostname WHERE `hostname` = ?";
        if ($stmt = $mysqli->prepare($query)) {

            $result_query_prepare = $stmt->bind_param("s", $hostname);
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

    public static function create($hostname) {
        $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DATABASE);
        if ($mysqli->connect_error) {
            die("Secured");
        }

        $prepared = $mysqli->prepare("INSERT INTO `hostname` ( `hostname` ) VALUES ( ? ) ; ");
        if ($prepared == false) {
            die("Secured");
        }

        $result_query_prepare = $prepared->bind_param("s", $hostname);
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


    public function linkToHostId($host_id) {
        $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DATABASE);
        if ($mysqli->connect_error) {
            die("Secured");
        }

        $prepared = $mysqli->prepare("INSERT INTO `host_hostname` ( `host_id`, `hostname_id` ) VALUES ( ? , ? ) ; ");
        if ($prepared == false) {
            die("Secured1");
        }

        $result_query_prepare = $prepared->bind_param("ii", $host_id, $this->id);
        if ($result_query_prepare == false) {
            die("Secured2");
        }

        $result_query_execute = $prepared->execute();
        if ($result_query_execute == false) {
            die("Secured3");
        }

        $prepared->close();
        $mysqli->close();
    }

    public static function removeLinkToHost($host_id) {
        $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DATABASE);
        if ($mysqli->connect_error) {
            die("Secured");
        }

        $prepared = $mysqli->prepare("DELETE FROM `host_hostname` WHERE host_id = ? ; ");
        if ($prepared == false) {
            die("Secured");
        }

        $result_query_prepare = $prepared->bind_param("s", $host_id);
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