<?php

function fnSetParamDB($host, $username, $password, $dbname, $port){
    $params['host']        = $host;
    $params['username']    = $username;
    $params['password']    = $password;
    $params['dbname']      = $dbname;
    $params['port']        = $port;
   
    return $params;
}
function fnVerifyDate($DateFrom, $DateTo){
    $diff       = abs(strtotime($DateTo) - strtotime($DateFrom));
    $days 		= round($diff / 86400);
    return $days;
}
function validateDate($attribute, $value)
{
    if ($value instanceof DateTimeInterface) {
        return true;
    }

    try {
        if ((! is_string($value) && ! is_numeric($value)) || strtotime($value) === false) {
            return false;
        }
    } catch (Exception $e) {
            return false;
    }

    $date = date_parse($value);

    return checkdate($date['month'], $date['day'], $date['year']);
}
