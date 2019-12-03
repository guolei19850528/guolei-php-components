# guolei-php-components
a php components by guolei
### Util Class
common util class  
```php
use \Guolei\Php\Components\Util;
Util::getUuidStr(1)
// use other Util methods ...
```
### DbFactory Class
common DbFactory class  
```php
use \Guolei\Php\Components\DbFactory;
// dbal mysql
$config = [
    "dbname" => "",
    "user" => "",
    "password" => "password",
    "host" => "host",
    "driver" => "pdo_mysql",
];
$dbFactory=new DbFactory();
$conn=$dbFactory->getDBALConnection($config);

// predis redis
$config = [
    "scheme" => "tcp",
    "host" => "121.42.166.250",
    "password" =>"abcd@123",
    "timeout" =>50,
    "port" => 6379,
];
$dbFactory=new DbFactory();
$conn=$dbFactory->getPredisConnection($config);
```

### Wechat Class
wechat operation class
```php
use \Guolei\Php\Components\Wechat;
$appId="wx41bd6621e194c939";
$appSecret="a3186dd5f20f045496fa49962d0df994";
$wechat=new Wechat($appId,$appSecret);
$accessToken=$wechat->getAccessToken();
$jsApiTicket=$wechat->getJsApiTicket($accessToken);
```
### Taobao Class
taobao operation class
```php
use \Guolei\Php\Components\Taobao;
Taobao::getIpData();
```
### other components
other components developing