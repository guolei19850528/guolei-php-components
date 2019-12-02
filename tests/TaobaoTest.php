<?php

use \PHPUnit\Framework\TestCase;
use Guolei\Php\Components\Taobao;

class TaobaoTest extends TestCase
{
    public function testMethods()
    {
        $this->assertTrue(true);
        $ipData = Taobao::getIpData();
        print_r($ipData);
        return true;
    }
}