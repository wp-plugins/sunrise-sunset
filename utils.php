<?php

require_once "city.php";


class SSUtils
{

    static public function convertTimezone($timestamp, $city)
    {
        $newTimestamp = 0;
        $time_format = 'Y-m-d';
        $currentTimezone = $city->getTimezone();

        $dstStart = '2012-03-11';
        $dstEnd = '2012-11-04';
        $today = date($time_format, $timestamp);

        if ((($today >= $dstStart) && ($today <= $dstEnd))) {
            $currentTimezone = $city->getSummerTimezone();
        }

        switch ($currentTimezone) {
            case 'EST':
                $newTimestamp = $timestamp - (5 * 60 * 60);
                break;
            case 'EDT':
                $newTimestamp = $timestamp - (4 * 60 * 60);
                break;
            case 'MST':
                $newTimestamp = $timestamp - (7 * 60 * 60);
                break;
            case 'MDT':
                $newTimestamp = $timestamp - (6 * 60 * 60);
                break;
            case 'CST':
                $newTimestamp = $timestamp - (6 * 60 * 60);
                break;
            case 'CDT':
                $newTimestamp = $timestamp - (5 * 60 * 60);
                break;
            case 'PST':
                $newTimestamp = $timestamp - (8 * 60 * 60);
                break;
            case 'PDT':
                $newTimestamp = $timestamp - (7 * 60 * 60);
                break;
            case 'AKST':
                $newTimestamp = $timestamp - (9 * 60 * 60);
                break;
            case 'AKDT':
                $newTimestamp = $timestamp - (8 * 60 * 60);
                break;
            case 'HAST':
                $newTimestamp = $timestamp - (10 * 60 * 60);
                break;
        }
        return array("timestamp" => $newTimestamp, "timezone" => $currentTimezone);
    }

    static public function getTimes($instance, $cities)
    {
        $time_format = 'h:i A';
        $targetCity = $cities[$instance['city']];

        $lat = $targetCity->getLatitude();
        $long = $targetCity->getLongitude();

        $suninfo = date_sun_info(time(), $lat, $long);

        $convertedSunriseTime = self::convertTimezone($suninfo['sunrise'], $targetCity);
        $convertedSunsetTime = self::convertTimezone($suninfo['sunset'], $targetCity);

        $sunrise_time = date($time_format, $convertedSunriseTime['timestamp']) . ' ' . $convertedSunriseTime['timezone'];
        $sunset_time = date($time_format, $convertedSunsetTime['timestamp']) . ' ' . $convertedSunsetTime['timezone'];

        return array('sunrise' => $sunrise_time, 'sunset' => $sunset_time);
    }

    static public function getCities()
    {
        $cities = array();
        $lines = file(dirname(__FILE__) . '/cities.txt');
        foreach ($lines as $line) {
            $pieces = explode(":", $line);
            if ($pieces[0] == 'city') continue;
            $cities[$pieces[0]] = new City($pieces[0], $pieces[1], $pieces[2], $pieces[3], $pieces[4]);
        }
        return $cities;
    }

}
