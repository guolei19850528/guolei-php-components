<?php
include_once "../vendor/autoload.php";
use \PHPUnit\Framework\TestCase;
use Guolei\Php\Components\Wechat;
class WechatTest extends \PHPUnit\Framework\TestCase
{
    public function testMethods(){
        $this->assertTrue(true);
        $appId="wx41bd6621e194c939";
        $appSecret="a3186dd5f20f045496fa49962d0df994";
        $wechat=new Wechat($appId,$appSecret);
        $accessToken=$wechat->getAccessToken();
//        print_r($accessToken);
        $jsApiTicket=$wechat->getJsApiTicket($accessToken);
        print_r($jsApiTicket);
        print_r($wechat->getSignatures($jsApiTicket));
        return true;
    }
}