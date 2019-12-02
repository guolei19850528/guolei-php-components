<?php
/**
 * @author guolei
 * @email 174000902@qq.com
 * @phone 15210720528
 */
namespace Guolei\Php\Components\Common;

use Ramsey\Uuid\Uuid;

/**
 * Class Util
 * @package Guolei\Php\Components\Common
 */
class Util
{
    /**
     * timestamp type today zero hour
     */
    const TIMESTAMP_TYPE_TODAY_ZERO_HOUR = 1;

    /**
     * timestamp type yesterday zero hour
     */
    const TIMESTAMP_TYPE_YESTERDAY_ZERO_HOUR = 2;

    /**
     * timestamp type tomorrow zero hour
     */
    const TIMESTAMP_TYPE_TOMORROW_ZERO_HOUR = 3;

    /**
     * get uuid str
     * @param int $option
     * if $option is 1 return uuid1
     * if $option is 3 return uuid3
     * if $option is 4 return uuid4
     * if $option is 5 return uuid5
     * @return string
     * @throws \Exception
     */
    public static function getUuidStr($option = 4)
    {
        if (!in_array(intval($option), [1, 3, 4, 5])) {
            $option = 4;
        }
        if ($option == 1) {
            $uuid = Uuid::uuid1();
            return $uuid->toString();
        }
        if ($option == 3) {
            $uuid = Uuid::uuid3(Uuid::NAMESPACE_DNS, "php.net");
            return $uuid->toString();
        }
        if ($option == 4) {
            $uuid = Uuid::uuid4();
            return $uuid->toString();
        }
        if ($option == 5) {
            $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, 'php.net');
            return $uuid->toString();
        }
    }

    /**
     * get remote ip addr
     * @return mixed
     */
    public static function getRemoteIpAddr()
    {
        $ip = "unknown";
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } else if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (!empty($_SERVER["REMOTE_ADDR"])) {
            $ip = $_SERVER["REMOTE_ADDR"];
        } else {
            $ip = "unknown";
        }
        $ips = explode(",", $ip);
        return $ips[0];
    }

    /**
     * get safe replace str
     * @param string $str
     * @return mixed|string
     */
    public static function getSafeReplaceStr($str = "")
    {
        $str = str_replace('%20', '', $str);
        $str = str_replace('%27', '', $str);
        $str = str_replace('%2527', '', $str);
        $str = str_replace('*', '', $str);
        $str = str_replace('"', '&quot;', $str);
        $str = str_replace("'", '', $str);
        $str = str_replace('"', '', $str);
        $str = str_replace(';', '', $str);
        $str = str_replace('<', '&lt;', $str);
        $str = str_replace('>', '&gt;', $str);
        $str = str_replace("{", '', $str);
        $str = str_replace('}', '', $str);
        $str = str_replace('\\', '', $str);
        return $str;
    }

    /**
     * get request url
     * @return string
     */
    public static function getRequestUrl()
    {
        $protocol = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $phpSelf = $_SERVER['PHP_SELF'] ? self::getSafeReplaceStr($_SERVER['PHP_SELF']) : self::SafeReplace($_SERVER['SCRIPT_NAME']);
        $pathInfo = isset($_SERVER['PATH_INFO']) ? self::getSafeReplaceStr($_SERVER['PATH_INFO']) : '';
        $queryStr = isset($_SERVER['REQUEST_URI']) ? self::getSafeReplaceStr($_SERVER['REQUEST_URI']) : $phpSelf . (isset($_SERVER['QUERY_STRING']) ? '?' . self::getSafeReplaceStr($_SERVER['QUERY_STRING']) : $pathInfo);
        return $protocol . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $queryStr;
    }

    /**
     * get json str
     * @param array $data
     * @return false|string
     */
    public static function getJsonStr($data = [])
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
    }

    /**
     * get suffix name
     * @param string $fileName
     * @return mixed
     */
    public static function getSuffixName($fileName = "")
    {
        return pathinfo($fileName, PATHINFO_EXTENSION);
    }

    /**
     * get children by recursion
     * @param int $parentId
     * @param array $data
     * @param string $parentKey
     * @param string $key
     * @param string $childrenKey
     * @return array
     */
    public static function getChildrenByRecursion($parentId = 0, $data = [], $parentKey = 'parentId', $key = 'id', $childrenKey = 'children')
    {
        $result = [];
        foreach ($data as $k => $v) {
            if ($v[$parentKey] == $parentId) {
                $temp = $v;
                unset($data[$k]);
                $temp[$childrenKey] = self::getChildrenByRecursion($temp[$key], $data, $parentKey, $key, $childrenKey);
                $result[] = $temp;
            }
        }
        return $result;
    }


    /**
     * get days seconds
     * @param int $days days
     * @return float|int
     */
    public static function getDaysSeconds($days = 1)
    {
        if (!is_numeric($days)) {
            $days = 1;
        }
        return abs(((60 * 60) * 24) * $days);
    }

    /**
     * get timestamp
     * @param null $option option
     * if $option is null return time()
     * if $option is string return strtotime($option)
     * if $options is TIMESTAMP_TYPE_TODAY_ZERO_HOUR return today zero hour timestamp
     * if $options is TIMESTAMP_TYPE_YESTERDAY_ZERO_HOUR return yesterday zero hour timestamp
     * if $options is TIMESTAMP_TYPE_TOMORROW_ZERO_HOUR return tomorrow zero hour timestamp
     * other throw GuoleiPhpUtilException
     * @return false|float|int
     */
    public static function getTimestamp($option = null)
    {
        if (empty($option) || is_null($option)) {
            return time();
        }
        if (is_string($option) && strlen($option) > 0) {
            return strtotime($option);
        }
        if (is_int($option) && $option > 0) {
            switch ($option) {
                case self::TIMESTAMP_TYPE_YESTERDAY_ZERO_HOUR:
                    return strtotime(date("Y-m-d", time())) - self::getDaysSeconds(1);
                case self::TIMESTAMP_TYPE_TODAY_ZERO_HOUR:
                    return strtotime(date("Y-m-d", time()));
                case self::TIMESTAMP_TYPE_TOMORROW_ZERO_HOUR:
                    return strtotime(date("Y-m-d", time())) + self::getDaysSeconds(1);
            }
        }
        throw new \InvalidArgumentException(sprintf("%s type error", $option));
    }

    /**
     * is leap year
     * @param null $year year
     * if $year is null $year is current year
     * @return bool
     */
    public static function isLeapYear($year = null)
    {
        if (empty($year) || is_null($year)) {
            $year = intval(date("Y", time()));
        }
        if (!is_int($year) || $year <= 0) {
            throw new \InvalidArgumentException(sprintf("year %s must integer and greater than 0", $year));
        }
        if (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0) {
            return true;
        }
        return false;
    }

    /**
     * get month max days
     * @param null $month month
     * @param null $year year
     * if $month is 2 $year must not empty
     * @return int
     */
    public static function getMonthMaxDays($month = null, $year = null)
    {
        if (!is_int($month) || $month <= 0) {
            throw new \InvalidArgumentException(sprintf("month must integer and greater than 0"));
        }
        if (in_array($month, [1, 3, 5, 7, 8, 10, 12])) {
            return 31;
        }
        if (in_array($month, [4, 6, 9, 11])) {
            return 30;
        }
        if ($month == 2) {
            if (!is_int($year) && $year <= 0) {
                throw new \InvalidArgumentException(sprintf("year must integer and greater than 0"));
            }
            if (self::isLeapYear($year)) {
                return 29;
            }
            return 28;
        }
    }

    /**
     * get random string
     * @param int $length length
     * @param string $strs strings
     * @return string
     */
    public static function getRandomStr($length = 32, $strs = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789")
    {
        if (!is_int($length)) {
            $length = 32;
        }
        if (!is_string($strs) || strlen($strs) <= 0) {
            $strs = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
        }
        $length = abs($length);
        $randomStr = "";
        for ($i = 0; $i < $length - 1; $i++) {
            $randomStr .= $strs[rand(0, strlen($strs) - 1)];
        }
        return $randomStr;
    }

    /**
     * xml to array
     * @param null $xml
     * @return mixed
     * @throws GuoleiPhpUtilException
     */
    public static function xmlToArray($xml = null)
    {
        if (!is_string($xml) || strlen($xml) <= 0) {
            throw new GuoleiPhpUtilException(sprintf("xml %s must string and not empty", $xml));
        }
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA)), true);
    }

    /**
     * xml to json
     * @param null $xml
     * @return false|string
     */
    public static function xmlToJson($xml = null)
    {
        if (!is_string($xml) || strlen($xml) <= 0) {
            throw new \InvalidArgumentException(sprintf("xml %s must string and not empty", $xml));
        }
        libxml_disable_entity_loader(true);
        return json_encode(simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA));
    }

    /**
     * array to xml
     * @param array $data
     * @param null $xmlHeader
     * @return string
     */
    public static function arrayToXml($data = [], $xmlHeader = null)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException(sprintf("data must array"));
        }
        $xml = "<xml>";
        if (!empty($xmlHeader) && !is_null($data) && is_string($xmlHeader) && strlen($xmlHeader) > 0) {
            $xml = $xmlHeader;
        }
        $xml .= self::recursionArrayToXml($data);
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * recursion array to xml
     * @param array $data
     * @return string
     */
    private static function recursionArrayToXml($data = [])
    {
        $xml = "";
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $xml .= "<" . $key . ">" . self::recursionArrayToXml($value) . "</" . $key . ">";
            } else {
                if (is_numeric($value)) {
                    $xml .= "<" . $key . ">" . $value . "</" . $key . ">";
                } else {
                    $xml .= "<" . $key . "><![CDATA[" . $value . "]]></" . $key . ">";
                }
            }

        }
        return $xml;
    }
}