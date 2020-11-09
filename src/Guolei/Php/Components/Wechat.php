<?php
/**
 * @author guolei
 * @email 174000902@qq.com
 * @phone 15210720528
 */

namespace Guolei\Php\Components;

use Guolei\Php\Components\CurlClient;
use Guolei\Php\Components\Util;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

/**
 * Class Wechat
 * wechat operation class
 * @package Guolei\Php\Components
 */
class Wechat
{
    protected $appId = "";
    protected $appSecret = "";

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
    public function getAppSecret()
    {
        return $this->appSecret;
    }

    /**
     * @param mixed $appSecret
     */
    public function setAppSecret($appSecret): void
    {
        $this->appSecret = $appSecret;
    }


    public function __construct($appId = null, $appSecret = null)
    {
        if (is_string($appId) && strlen($appId) > 0) {
            $this->setAppId($appId);
        }
        if (is_string($appSecret) && strlen($appSecret) > 0) {
            $this->setAppSecret($appSecret);
        }
    }

    /**
     * get access token
     * @return string
     * @throws \HttpException
     * @throws \HttpInvalidParamException
     */
    public function getAccessToken()
    {
        if (!is_string($this->getAppId()) || strlen($this->getAppId()) <= 0) {
            throw new \InvalidArgumentException(sprintf("appId %s must string and not empty"), $this->getAppId());
        }
        if (!is_string($this->getAppSecret()) || strlen($this->getAppSecret()) <= 0) {
            throw new \InvalidArgumentException(sprintf("appSecret %s must string and not empty"), $this->getAppSecret());
        }
        $client=new Client(["base_uri"=>"https://api.weixin.qq.com/cgi-bin/token","timeout"=>30]);
        $response=$client->request("GET","",[
            RequestOptions::QUERY=>[
                "grant_type"=>"client_credential",
                "appid"=>$this->appId,
                "secret"=>$this->appSecret
            ]
        ]);
        if($response->getStatusCode()==200){
            $content=json_decode($response->getBody()->getContents(),true);
            if (is_array($content) && count($content)) {
                $accessToken = isset($content["access_token"]) ? strval($content["access_token"]) : "";
                return $accessToken;
            }
        }
        return "";
    }

    public function getJsApiTicket($accessToken = null)
    {
        if (!is_string($this->getAppId()) || strlen($this->getAppId()) <= 0) {
            throw new \InvalidArgumentException(sprintf("appId %s must string and not empty"), $this->getAppId());
        }
        if (!is_string($this->getAppSecret()) || strlen($this->getAppSecret()) <= 0) {
            throw new \InvalidArgumentException(sprintf("appSecret %s must string and not empty"), $this->getAppSecret());
        }
        if (!is_string($accessToken) || strlen($accessToken) <= 0) {
            throw new \InvalidArgumentException(sprintf("accessToken %s must string and not empty"), $this->getAppId());
        }
        $client=new Client(["base_uri"=>"https://api.weixin.qq.com/cgi-bin/ticket/getticket","timeout"=>30]);
        $response=$client->request("POST","",[
            RequestOptions::FORM_PARAMS=>[
                "access_token"=>$accessToken,
                "type"=>"jsapi",
            ]
        ]);
        if($response->getStatusCode()==200){
            $content=json_decode($response->getBody()->getContents(),true);
            if (is_array($content) && count($content)) {
                $jsApiTicket = isset($content["ticket"]) ? strval($content["ticket"]) : "";
                return $jsApiTicket;
            }
        }
        return "";
    }

    /**
     * get signatures
     * @param null $jsApiTicket
     * @param string $url
     * @param string $type
     * @return array
     */
    public function getSignatures($jsApiTicket = null, $url = "", $type = "shar1")
    {
        if (!is_string($jsApiTicket) || strlen($jsApiTicket) <= 0) {
            throw new \InvalidArgumentException(sprintf("jsApiTicket %s must string and not empty"), $this->getAppId());
        }
        $nonceStr = Util::getRandomStr(64);
        $timestamp = time();
        $string1 = 'jsapi_ticket=' . $jsApiTicket . '&noncestr=' . $nonceStr . '&timestamp=' . $timestamp . '&url=' . $url;
        $signature = sha1($string1);
        if ($type == 'md5') {
            $signature = md5($string1);
        }
        return [
            'nonceStr' => $nonceStr,
            'signature' => $signature,
            'timestamp' => $timestamp,
        ];
    }

    /**
     * get code url
     * @param null $url
     * @return string
     */
    public function getCodeUrl($url = null)
    {
        if (!is_string($this->getAppId()) || strlen($this->getAppId()) <= 0) {
            throw new \InvalidArgumentException(sprintf("appId %s must string and not empty"), $this->getAppId());
        }
        $codeUrl = sprintf("https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base&state=1#wechat_redirect", $this->getAppId(), urlencode($url));
        return $codeUrl;
    }

    /**
     * get open id
     * @param string $code
     * @return string
     * @throws \HttpException
     * @throws \HttpInvalidParamException
     */
    public function getOpenId($code = "")
    {
        if (!is_string($this->getAppId()) || strlen($this->getAppId()) <= 0) {
            throw new \InvalidArgumentException(sprintf("appId %s must string and not empty"), $this->getAppId());
        }
        if (!is_string($this->getAppSecret()) || strlen($this->getAppSecret()) <= 0) {
            throw new \InvalidArgumentException(sprintf("appSecret %s must string and not empty"), $this->getAppSecret());
        }
        if (!is_string($code) || strlen($code) <= 0) {
            throw new \InvalidArgumentException(sprintf("code %s must string and not empty"), $this->getAppId());
        }

        $client=new Client(["base_uri"=>"https://api.weixin.qq.com/sns/oauth2/access_token","timeout"=>30]);
        $response=$client->request("POST","",[
            RequestOptions::FORM_PARAMS=>[
                "grant_type"=>"authorization_code",
                "appid"=>$this->appId,
                "secret"=>$this->appSecret,
                "code"=>$code
            ]
        ]);
        if($response->getStatusCode()==200){
            $content=json_decode($response->getBody()->getContents(),true);
            if (is_array($content) && count($content)) {
                $content = isset($response["content"]) ? json_decode($response["content"], true) : [];
                $openId = isset($content["openid"]) ? strval($content["openid"]) : "";
                return $openId;
            }
        }
        return "";
    }
}