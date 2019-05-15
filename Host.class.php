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

        for ($row_no = ($res->num_rows - 1); $row_no >= 0; $row_no--) {
            $res->data_seek($row_no);
            var_dump($res->fetch_assoc());
        }
        $res->close();

    }

    public static function exist($ip) {
        return false;
    }


}