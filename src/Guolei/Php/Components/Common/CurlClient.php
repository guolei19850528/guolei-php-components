<?php
/**
 * @author guolei
 * @email 174000902@qq.com
 * @phone 15210720528
 */

namespace Guolei\Php\Components\Common;


/**
 * Class CurlClient
 * curl operation class
 * use curl
 * @see https://www.php.net/manual/zh/book.curl.php
 * @package Guolei\Php\Components\Common
 */
class CurlClient
{
    /**
     * curl 对象
     * @var null
     */
    protected $client = null;

    /**
     * @return null
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * CurlClient constructor.
     * @param string $url request url
     * if $url not empty call method $this->init($url)
     * @throws \HttpInvalidParamException
     */
    public function __construct($url = null)
    {
        if (is_string($url) && strlen($url) > 0) {
            $this->init($url);
        }
    }

    /**
     * call method curl_init
     * @param string $url request url
     * $url must not empty
     * @throws \HttpInvalidParamException
     */
    public function init($url = null)
    {
        if (!is_string($url) || strlen($url) == 0) {
            throw new \HttpInvalidParamException(sprintf("url %s type error", $url));
        }
        $this->client = curl_init($url);
    }

    /**
     * call method curl_setopt
     * @see https://www.php.net/manual/zh/function.curl-setopt.php
     * @param null $option CURLOPT_HEADER_XXX
     * $option must integer
     * @param null $value value
     * @return bool
     * @throws \HttpException
     */
    public function setOpt($option = null, $value = null)
    {
        if (!is_resource($this->client)) {
            throw new \HttpException(sprintf("curl open error"));
        }
        if (!is_int($option)) {
            throw new \HttpInvalidParamException(sprintf("option %s must integer", $option));
        }
        return curl_setopt($this->client, $option, $value);
    }

    /**
     * call method curl_setopt_array
     * @see https://www.php.net/manual/zh/function.curl-setopt-array.php
     * @param null $options
     * $options must array
     * @return bool
     * @throws \HttpException
     * @throws \HttpInvalidParamException
     */
    public function setOptArray($options = null)
    {
        if (!is_resource($this->client)) {
            throw new \HttpException(sprintf("curl open error"));
        }
        if (!is_array($options) || count($options) == 0) {
            throw new \HttpInvalidParamException(sprintf("options must array and not empty"));
        }
        return curl_setopt_array($this->client, $options);
    }

    /**
     * call method curl_version
     * @param null $key curl_version key
     * if $key not empty and curl_version array key exists $key return curl_version[$key]
     * if $key empty return curl_version
     * @return array
     * @throws \InvalidArgumentException
     */
    public function curlVersion($key = null)
    {
        $curlVersion = curl_version();
        if (!is_null($key)) {
            if (!array_key_exists($key, $curlVersion)) {
                throw new \InvalidArgumentException(sprintf("key %s not exists", $key));
            }
            return $curlVersion[$key];
        }
        return $curlVersion;
    }

    /**
     * call method curl_exec
     * @return array
     * [
     *   "error" => curl_error(),
     *   "info" => curl_getinfo(),
     *   "content" => curl_exec()
     * ]
     * @throws \HttpException
     */
    public function exec()
    {
        if (!is_resource($this->client)) {
            throw new \HttpException(sprintf("curl open error"));
        }
        ob_start();
        $content = curl_exec($this->client);
        ob_end_clean();
        $info = curl_getinfo($this->client);
        $error = curl_error($this->client);
        curl_close($this->client);
        return [
            "error" => $error,
            "info" => $info,
            "content" => $content,
        ];
    }
}