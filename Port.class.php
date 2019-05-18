<?php


class Port {

    private $id = -1;
    private $port = -1;
    private $mysqli = null;


    public function __construct($port) {
        $this->port = $port;
        $this->mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DATABASE);
        if ($this->mysqli->connect_error) {
            die("Secured");
        }

        if (!($stmt = $this->mysqli->prepare("SELECT id, port FROM port where `port` = ?"))) {
            echo "Prepare failed: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        }

        $stmt->bind_param("s", $this->port);

        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        if (!($res = $stmt->get_result())) {
            echo "Getting result set failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        while ($row = $res->fetch_assoc()) {
            $this->id = $row['id'];
            $this->port = $row['port'];
        }

        $res->close();


    }

    public static function exist($port) {
        $rows = 0;

        $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DATABASE);
        if ($mysqli->connect_error) {
            die("Secured");
        }


        $query = "SELECT id FROM port WHERE `port` = ?";
        if ($stmt = $mysqli->prepare($query)) {

            $result_query_prepare = $stmt->bind_param("s", $port);
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

    public static function create($port) {
        $mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DATABASE);
        if ($mysqli->connect_error) {
            die("Secured");
        }

        $prepared = $mysqli->prepare("INSERT INTO `port` ( `port` ) VALUES ( ? ) ; ");
        if ($prepared == false) {
            die("Secured");
        }

        $result_query_prepare = $prepared->bind_param("s", $port);
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

        $prepared = $mysqli->prepare("INSERT INTO `host_port` ( `host_id`, `port_id` ) VALUES ( ? , ? ) ; ");
        if ($prepared == false) {
            die("Secured1");
        }

        $result_query_prepare = $prepared->bind_param("ii", $host_id, $this->id);
        if ($result_query_prepare == false) {
            echo $mysqli->error;
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

        $prepared = $mysqli->prepare("DELETE FROM `host_port` WHERE host_id = ? ; ");
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