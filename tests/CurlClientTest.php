<?php

use \PHPUnit\Framework\TestCase;
use \Guolei\Php\Component\Common\CurlClient;

class CurlClientTest extends TestCase
{
    public function testMethods()
    {
        $this->assertTrue(true);
        $curlClient = new CurlClient();
        $requestUrl="https://www.baidu.com";
        $curlClient->init($requestUrl);
        $curlClient->setOpt(CURLOPT_CONNECTTIMEOUT, 30);
        //是否输出返回头部信息
//        $curlClient->setOpt(CURLOPT_HEADER, 1);
        $curlClient->setOpt(CURLOPT_RETURNTRANSFER, 1);
        $response = $curlClient->exec();
        print_r(sprintf("curl request %s successful \n",$requestUrl));
        print_r($response);
        print_r("\n");

        return true;
    }

}