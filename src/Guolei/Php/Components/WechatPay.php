<?php


namespace Guolei\Php\Components;

use Guolei\Php\Components\Util;
use Guolei\Php\Components\CurlClient;


/**
 * 微信支付操作类
 * Class WeChatPay
 * @package Guolei\Php\Components
 */
class WechatPay
{
    protected $appId = "";
    protected $mchId = "";
    protected $key = "";
    protected $sslCertPath = "";
    protected $sslKeyPath = "";
    protected $notifyUrl = "";

    /**
     * @return mixed
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param mixed $appId
     */
    public function setAppId($appId): void
    {
        $this->appId = $appId;
    }

    /**
     * @return mixed
     */
    public function getMchId()
    {
        return $this->mchId;
    }

    /**
     * @param mixed $mchId
     */
    public function setMchId($mchId): void
    {
        $this->mchId = $mchId;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key): void
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getSslCertPath()
    {
        return $this->sslCertPath;
    }

    /**
     * @param mixed $sslCertPath
     */
    public function setSslCertPath($sslCertPath): void
    {
        $this->sslCertPath = $sslCertPath;
    }

    /**
     * @return mixed
     */
    public function getSslKeyPath()
    {
        return $this->sslKeyPath;
    }

    /**
     * @param mixed $sslKeyPath
     */
    public function setSslKeyPath($sslKeyPath): void
    {
        $this->sslKeyPath = $sslKeyPath;
    }

    /**
     * @return mixed
     */
    public function getNotifyUrl()
    {
        return $this->notifyUrl;
    }

    /**
     * @param mixed $notifyUrl
     */
    public function setNotifyUrl($notifyUrl): void
    {
        $this->notifyUrl = $notifyUrl;
    }

    public function __construct()
    {
    }

    /**
     * 统一下单接口
     * https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_1
     * @param array $data
     * @return bool|mixed
     * @throws \Exception
     */
    public function unifiedorder($data = [])
    {
        if (!is_string($this->getAppId()) || strlen($this->getAppId()) <= 0) {
            throw new \InvalidArgumentException("appId must not empty");
        }
        if (!is_string($this->getMchId()) || strlen($this->getMchId()) <= 0) {
            throw new \InvalidArgumentException("mchId must not empty");
        }
        if (!is_string($this->getKey()) || strlen($this->getKey()) <= 0) {
            throw new \InvalidArgumentException("key must not empty");
        }
        if (!is_string($this->getSslCertPath()) || strlen($this->getSslCertPath()) <= 0) {
            throw new \InvalidArgumentException("sslCertPath must not empty");
        }
        if (!is_string($this->getSslKeyPath()) || strlen($this->getSslKeyPath()) <= 0) {
            throw new \InvalidArgumentException("sslKeyPath must not empty");
        }
        if (!is_string($this->getNotifyUrl()) || strlen($this->getNotifyUrl()) <= 0) {
            throw new \InvalidArgumentException("notifyUrl must not empty");
        }
        if (!is_array($data) || count($data) <= 0) {
            throw new \InvalidArgumentException("data is not array or empty");
        }
        if (!isset($data["openid"]) || empty($data["openid"]) || is_null($data["openid"])) {
            throw new \InvalidArgumentException("openid error");
        }
        if (!isset($data["out_trade_no"]) || empty($data["out_trade_no"]) || is_null($data["out_trade_no"])) {
            throw new \InvalidArgumentException("out_trade_no error");
        }
        if (!isset($data["body"]) || empty($data["body"]) || is_null($data["body"])) {
            throw new \InvalidArgumentException("body error");
        }
        if (!isset($data["trade_type"]) || empty($data["trade_type"]) || is_null($data["trade_type"])) {
            $data["trade_type"] = "JSAPI";
        }
        if (!isset($data["total_fee"]) || !is_numeric($data["total_fee"]) || $data["total_fee"] <= 0) {
            throw new \InvalidArgumentException("total_fee error");
        }

        $data["appid"] = $this->getAppId();
        $data["mch_id"] = $this->getMchId();
        $data["nonce_str"] = Random::generate(32);
        $data["spbill_create_ip"] = Util::clientIp();
        $data["trade_type"] = "JSAPI";
        $data["notify_url"] = $this->getNotifyUrl();
        $data["sign_type"] = "MD5";
        $data["total_fee"] = $data["total_fee"] * 100;
        ksort($data);
        $dataStr = $this->toUrlParams($data);
        $signTempStr = $dataStr . "&key=" . $this->getKey();
        $sign = md5($signTempStr);
        $data["sign"] = strtoupper($sign);
        $curlClient = new CurlClient();
        $requestUrl = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $userAgent = "WXPaySDK/3.0.9 (" . PHP_OS . ") PHP/" . PHP_VERSION . " CURL/" . $curlClient->curlVersion("version") . " "
            . $this->wechatPayAccount->getMchId();
        $curlClient->init($requestUrl);
        $curlClient->setOpt(CURLOPT_POST, 1);
        $curlClient->setOpt(CURLOPT_SSL_VERIFYPEER, 1);
        $curlClient->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
        $curlClient->setOpt(CURLOPT_ENCODING, 'gzip,deflate');// 解释gzip内容
        $curlClient->setOpt(CURLOPT_RETURNTRANSFER, 1);
        $curlClient->setOpt(CURLOPT_CONNECTTIMEOUT, 30);
        $curlClient->setOpt(CURLOPT_TIMEOUT, 30);
        $curlClient->setOpt(CURLOPT_USERAGENT, $userAgent);
        $curlClient->setOpt(CURLOPT_SSLCERTTYPE, "PEM");
        $curlClient->setOpt(CURLOPT_SSLCERT, $this->getSetSslCertPath());
        $curlClient->setOpt(CURLOPT_SSLKEY, $this->getSetSslKeyPath());
        $curlClient->setOpt(CURLOPT_POST, 1);
        $curlClient->setOpt(CURLOPT_POSTFIELDS, Util::arrayToXml($data));
        $response = $curlClient->exec();
        if (is_array($response) && count($response) > 0 && isset($response["info"]["http_code"]) && $response["info"]["http_code"] == 200) {
            $content = isset($response["content"]) ? $response["content"] : "";
            return Util::xmlToArray($content);
        }
        return false;

    }

    /**
     * 订单查询
     * https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_2
     * @param string $outTradeNo
     * @return bool|mixed
     * @throws \Exception
     */
    public function orderQuery($outTradeNo = "")
    {
        if (!is_string($this->getAppId()) || strlen($this->getAppId()) <= 0) {
            throw new \InvalidArgumentException("appId must not empty");
        }
        if (!is_string($this->getMchId()) || strlen($this->getMchId()) <= 0) {
            throw new \InvalidArgumentException("mchId must not empty");
        }
        if (!is_string($this->getKey()) || strlen($this->getKey()) <= 0) {
            throw new \InvalidArgumentException("key must not empty");
        }
        if (!is_string($this->getSslCertPath()) || strlen($this->getSslCertPath()) <= 0) {
            throw new \InvalidArgumentException("sslCertPath must not empty");
        }
        if (!is_string($this->getSslKeyPath()) || strlen($this->getSslKeyPath()) <= 0) {
            throw new \InvalidArgumentException("sslKeyPath must not empty");
        }
        if (empty($outTradeNo) || is_null($outTradeNo)) {
            throw new \InvalidArgumentException("outTradeNo is empty");
        }
        $data = [];
        $data["appid"] = $this->getAppId();
        $data["mch_id"] = $this->getMchId();
        $data["nonce_str"] = Util::getRandomStr(32);
        $data["out_trade_no"] = $outTradeNo;
        $data["sign_type"] = "MD5";
        ksort($data);
        $dataStr = $this->toUrlParams($data);
        $signTempStr = $dataStr . "&key=" . $this->getKey();
        $sign = md5($signTempStr);
        $data["sign"] = strtoupper($sign);
        $curlClient = new CurlClient();
        $requestUrl = "https://api.mch.weixin.qq.com/pay/orderquery";
        $userAgent = "WXPaySDK/3.0.9 (" . PHP_OS . ") PHP/" . PHP_VERSION . " CURL/" . $curlClient->curlVersion("version") . " "
            . $this->wechatPayAccount->getMchId();
        $curlClient->init($requestUrl);
        $curlClient->setOpt(CURLOPT_POST, 1);
        $curlClient->setOpt(CURLOPT_SSL_VERIFYPEER, 1);
        $curlClient->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
        $curlClient->setOpt(CURLOPT_ENCODING, 'gzip,deflate');// 解释gzip内容
        $curlClient->setOpt(CURLOPT_RETURNTRANSFER, 1);
        $curlClient->setOpt(CURLOPT_CONNECTTIMEOUT, 30);
        $curlClient->setOpt(CURLOPT_TIMEOUT, 30);
        $curlClient->setOpt(CURLOPT_USERAGENT, $userAgent);
        $curlClient->setOpt(CURLOPT_SSLCERTTYPE, "PEM");
        $curlClient->setOpt(CURLOPT_SSLCERT, $this->getSetSslCertPath());
        $curlClient->setOpt(CURLOPT_SSLKEY, $this->getSetSslKeyPath());
        $curlClient->setOpt(CURLOPT_POST, 1);
        $curlClient->setOpt(CURLOPT_POSTFIELDS, Util::arrayToXml($data));
        $response = $curlClient->exec();
        if (is_array($response) && count($response) > 0 && isset($response["info"]["http_code"]) && $response["info"]["http_code"] == 200) {
            $content = isset($response["content"]) ? $response["content"] : "";
            return Util::xmlToArray($content);
        }
        return false;
    }

    /**
     * https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_4
     * @param string $outTradeNo
     * @param int $totalFee
     * @param string $outRefundNo
     * @param int $refundFee
     * @return bool|mixed
     * @throws \Exception
     */
    public function refund($outTradeNo = "", $totalFee = 0, $outRefundNo = "", $refundFee = 0)
    {
        if (!is_string($this->getAppId()) || strlen($this->getAppId()) <= 0) {
            throw new \InvalidArgumentException("appId must not empty");
        }
        if (!is_string($this->getMchId()) || strlen($this->getMchId()) <= 0) {
            throw new \InvalidArgumentException("mchId must not empty");
        }
        if (!is_string($this->getKey()) || strlen($this->getKey()) <= 0) {
            throw new \InvalidArgumentException("key must not empty");
        }
        if (!is_string($this->getSslCertPath()) || strlen($this->getSslCertPath()) <= 0) {
            throw new \InvalidArgumentException("sslCertPath must not empty");
        }
        if (!is_string($this->getSslKeyPath()) || strlen($this->getSslKeyPath()) <= 0) {
            throw new \InvalidArgumentException("sslKeyPath must not empty");
        }

        if (empty($outTradeNo) || is_null($outTradeNo)) {
            throw new \InvalidArgumentException("outTradeNo is empty");
        }
        if (empty($outRefundNo) || is_null($outRefundNo)) {
            throw new \InvalidArgumentException("outRefundNo is empty");
        }

        if (!is_numeric($totalFee) || $totalFee <= 0) {
            throw new \InvalidArgumentException("totalFee is nor number or totalFee Less than or equal to 0");
        }

        if (!is_numeric($refundFee) || $refundFee <= 0 || $refundFee > $totalFee) {
            throw new \InvalidArgumentException("refundFee is nor number or refundFee Less than or equal to 0 or refundFee greater than totalFee");
        }

        $data = [];
        $data["appid"] = $this->getAppId();
        $data["mch_id"] = $this->getMchId();
        $data["nonce_str"] = Random::generate(32);
        $data["out_trade_no"] = $outTradeNo;
        $data["out_refund_no"] = $outRefundNo;
        $data["sign_type"] = "MD5";
        $data["total_fee"] = $totalFee * 100;
        $data["refund_fee"] = $refundFee * 100;
        ksort($data);
        $dataStr = $this->toUrlParams($data);
        $signTempStr = $dataStr . "&key=" . $this->getKey();
        $sign = md5($signTempStr);
        $data["sign"] = strtoupper($sign);
        $curlClient = new CurlClient();
        $requestUrl = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        $userAgent = "WXPaySDK/3.0.9 (" . PHP_OS . ") PHP/" . PHP_VERSION . " CURL/" . $curlClient->curlVersion("version") . " "
            . $this->wechatPayAccount->getMchId();
        $curlClient->init($requestUrl);
        $curlClient->setOpt(CURLOPT_POST, 1);
        $curlClient->setOpt(CURLOPT_SSL_VERIFYPEER, 1);
        $curlClient->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
        $curlClient->setOpt(CURLOPT_ENCODING, 'gzip,deflate');// 解释gzip内容
        $curlClient->setOpt(CURLOPT_RETURNTRANSFER, 1);
        $curlClient->setOpt(CURLOPT_CONNECTTIMEOUT, 30);
        $curlClient->setOpt(CURLOPT_TIMEOUT, 30);
        $curlClient->setOpt(CURLOPT_USERAGENT, $userAgent);
        $curlClient->setOpt(CURLOPT_SSLCERTTYPE, "PEM");
        $curlClient->setOpt(CURLOPT_SSLCERT, $this->getSetSslCertPath());
        $curlClient->setOpt(CURLOPT_SSLKEY, $this->getSetSslKeyPath());
        $curlClient->setOpt(CURLOPT_POST, 1);
        $curlClient->setOpt(CURLOPT_POSTFIELDS, Util::arrayToXml($data));
        $response = $curlClient->exec();
        if (is_array($response) && count($response) > 0 && isset($response["info"]["http_code"]) && $response["info"]["http_code"] == 200) {
            $content = isset($response["content"]) ? $response["content"] : "";
            return Util::xmlToArray($content);
        }
        return false;
    }

    /**
     * 退款查询
     * https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_5
     * @param string $outTradeNo
     * @param int $totalFee
     * @param string $outRefundNo
     * @param int $refundFee
     * @return bool|mixed
     * @throws \Exception
     */
    public function refundQuery($outTradeNo = "", $outRefundNo = "")
    {
        if (!is_string($this->getAppId()) || strlen($this->getAppId()) <= 0) {
            throw new \InvalidArgumentException("appId must not empty");
        }
        if (!is_string($this->getMchId()) || strlen($this->getMchId()) <= 0) {
            throw new \InvalidArgumentException("mchId must not empty");
        }
        if (!is_string($this->getKey()) || strlen($this->getKey()) <= 0) {
            throw new \InvalidArgumentException("key must not empty");
        }
        if (!is_string($this->getSslCertPath()) || strlen($this->getSslCertPath()) <= 0) {
            throw new \InvalidArgumentException("sslCertPath must not empty");
        }
        if (!is_string($this->getSslKeyPath()) || strlen($this->getSslKeyPath()) <= 0) {
            throw new \InvalidArgumentException("sslKeyPath must not empty");
        }
        if (empty($outTradeNo) || is_null($outTradeNo)) {
            throw new \InvalidArgumentException("outTradeNo is empty");
        }
        if (empty($outRefundNo) || is_null($outRefundNo)) {
            throw new \InvalidArgumentException("outRefundNo is empty");
        }

        $data = [];
        $data["appid"] = $this->getAppId();
        $data["mch_id"] = $this->getMchId();
        $data["nonce_str"] = Util::getRandomStr(32);
        $data["out_trade_no"] = $outTradeNo;
        $data["out_refund_no"] = $outRefundNo;
        $data["sign_type"] = "MD5";
        ksort($data);
        $dataStr = $this->toUrlParams($data);
        $signTempStr = $dataStr . "&key=" . $this->getKey();
        $sign = md5($signTempStr);
        $data["sign"] = strtoupper($sign);
        $curlClient = new CurlClient();
        $requestUrl = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        $userAgent = "WXPaySDK/3.0.9 (" . PHP_OS . ") PHP/" . PHP_VERSION . " CURL/" . $curlClient->curlVersion("version") . " "
            . $this->wechatPayAccount->getMchId();
        $curlClient->init($requestUrl);
        $curlClient->setOpt(CURLOPT_POST, 1);
        $curlClient->setOpt(CURLOPT_SSL_VERIFYPEER, 1);
        $curlClient->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
        $curlClient->setOpt(CURLOPT_ENCODING, 'gzip,deflate');// 解释gzip内容
        $curlClient->setOpt(CURLOPT_RETURNTRANSFER, 1);
        $curlClient->setOpt(CURLOPT_CONNECTTIMEOUT, 30);
        $curlClient->setOpt(CURLOPT_TIMEOUT, 30);
        $curlClient->setOpt(CURLOPT_USERAGENT, $userAgent);
        $curlClient->setOpt(CURLOPT_SSLCERTTYPE, "PEM");
        $curlClient->setOpt(CURLOPT_SSLCERT, $this->getSetSslCertPath());
        $curlClient->setOpt(CURLOPT_SSLKEY, $this->getSetSslKeyPath());
        $curlClient->setOpt(CURLOPT_POST, 1);
        $curlClient->setOpt(CURLOPT_POSTFIELDS, Util::arrayToXml($data));
        $response = $curlClient->exec();
        if (is_array($response) && count($response) > 0 && isset($response["info"]["http_code"]) && $response["info"]["http_code"] == 200) {
            $content = isset($response["content"]) ? $response["content"] : "";
            return Util::xmlToArray($content);
        }
        return false;
    }

    /**
     * 数组转字符串
     * @param array $data
     * @return string
     */
    public function toUrlParams($data = [])
    {
        $buff = "";
        foreach ($data as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }
}