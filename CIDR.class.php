<?php


class CIDR {

    public static function cidrToRange($value) {
        $range = array();
        $split = explode('/', $value);
        if (!empty($split[0]) && is_scalar($split[1]) && filter_var($split[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $rangeStart = ip2long($split[0]) & ((-1 << (32 - (int)$split[1])));
            $rangeStartIP = long2ip($rangeStart);
            $rangeEnd = ip2long($rangeStartIP) + pow(2, (32 - (int)$split[1])) - 1;
            for ($i = $rangeStart; $i <= $rangeEnd; $i++) {
                $range[] = long2ip($i);
            }
            return $range;
        } else {
            return $value;
        }
    }
}