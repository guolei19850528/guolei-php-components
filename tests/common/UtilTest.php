<?php

use \PHPUnit\Framework\TestCase;
use \Guolei\Php\Components\Common\Util;

class UtilTest extends TestCase
{
    public function testMethods()
    {
        $this->assertIsBool(true);
        $uuid = Util::getUuidStr(1);
        print_r(sprintf("uuid1 is %s \n", $uuid));
        $uuid = Util::getUuidStr(3);
        print_r(sprintf("uuid3 is %s \n", $uuid));
        $uuid = Util::getUuidStr(4);
        print_r(sprintf("uuid4 is %s \n", $uuid));
        $uuid = Util::getUuidStr(5);
        print_r(sprintf("uuid5 is %s \n", $uuid));

        $randomStr = Util::getRandomStr(32);
        print_r(sprintf("random str is %s \n", $randomStr));

        $timestamp = Util::getTimestamp();
        print_r(sprintf("current timestamp is %s \n", $timestamp));

        $yesterdayZeroHourTimestamp = Util::getTimestamp(Util::TIMESTAMP_TYPE_YESTERDAY_ZERO_HOUR);
        print_r(sprintf("yesterday zero hour timestamp is %s \n", $yesterdayZeroHourTimestamp));

        $todayZeroHourTimestamp = Util::getTimestamp(Util::TIMESTAMP_TYPE_TODAY_ZERO_HOUR);
        print_r(sprintf("today zero hour timestamp is %s \n", $todayZeroHourTimestamp));

        $tomorrowZeroHourTimestamp = Util::getTimestamp(Util::TIMESTAMP_TYPE_TOMORROW_ZERO_HOUR);
        print_r(sprintf("tomorrow zero hour timestamp is %s \n", $tomorrowZeroHourTimestamp));

        $year = 2020;
        $isLeapYear = Util::isLeapYear($year);
        print_r(sprintf("year %s check leap year %d \n", $year, $isLeapYear));
        $month = 2;
        $monthMaxDays = Util::getMonthMaxDays($month, $year);
        print_r(sprintf("year %s month %s max days is %s \n", $year, $month, $monthMaxDays));

        $needToXmlArray = [
            "name" => "guolei",
            "email" => "174000902@qq.com",
            "phone" => "15210720528"
        ];
        $xml = Util::arrayToXml($needToXmlArray);
        print_r(sprintf("%s \n", $xml));

        $jsonStr = Util::getJsonStr($needToXmlArray);
        print_r(sprintf("%s \n", $jsonStr));

        $xmlArray = Util::xmlToArray($xml);
        print_r($xmlArray);
        print_r(sprintf("\n"));

        return true;
    }

}