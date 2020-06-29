<?php
include_once "../vendor/autoload.php";
use \PHPUnit\Framework\TestCase;
use Guolei\Php\Components\DbFactory;
class DbTest extends TestCase
{
    public function testMethods(){
        $this->assertTrue(true);
        $connectionParams = array(
            'dbname' => 'my_db1',
            'user' => 'root',
            'password' => '',
            'host' => '127.0.0.1',
            "port"=>3306,

            'driver' => 'pdo_mysql',
        );
        $dbFactory=new DbFactory();
        $conn=$dbFactory->getDBALConnection($connectionParams);

        $sql = "SELECT * FROM t1";
        $stmt = $conn->prepare($sql); // Simple, but has several drawbacks
        $conn->executeQuery("insert into t1(name)values(:name);",[":name"=>"33333"]);
        $a=$conn->fetchAllAssociative($sql);
        echo "<pre>";print_r($a);

        return true;
    }
}