<?php


class Vuln {

    private $id = -1;
    private $cve = null;
    private $year = null;

    public function __construct() {


    }

    public function create() {
        // insert new cve
    }

    public function getId() {
        return $this->id;
    }

    public function getCVE() {
        return $this->cve;
    }

    public function getYear()  {
        return $this->year;
    }

    public static function exist($cve) {
        return false;
    }

}