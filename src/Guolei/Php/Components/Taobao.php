<?php


namespace Guolei\Php\Components;

use Guolei\Php\Components\Util;
use Guolei\Php\Components\CurlClient;

/**
 * Class Taobao
 * @package Guolei\Php\Components
 */
class Taobao
{
    public static function getIpData($ip = "")
    {
        if (strlen($ip) == 0) {
            $ip = Util::getRemoteIpAddr();
        }
        $_t = microtime(true);
        $requestUrl = sprintf("http://ip.taobao.com/service/getIpInfo.php?ip=%s&_t=%s", $ip, microtime(true));
        $curlClient = new CurlClient();
        $curlClient->init($requestUrl);

        $curlClient->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
        $curlClient->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
        $curlClient->setOpt(CURLOPT_ENCODING, 'gzip,deflate');// 解释gzip内容
        $curlClient->setOpt(CURLOPT_RETURNTRANSFER, 1);
        $curlClient->setOpt(CURLOPT_CONNECTTIMEOUT, 30);
        $curlClient->setOpt(CURLOPT_TIMEOUT, 30);
        $response = $curlClient->exec();
        if (is_array($response) && count($response) && isset($response["info"]) && $response["info"]["http_code"] == 200) {
            $content = isset($response["content"]) ? json_decode($response["content"], true) : [];
            return $content;
        }
        return [];
    }
}