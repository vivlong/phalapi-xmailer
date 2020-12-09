# XMailer
PhalApi 2.x扩展类库，基于PHPMailer的邮件发送，增加自定义端口与发送附件支持。

## 安装和配置
修改项目下的composer.json文件，并添加：  
```
    "vivlong/phalapi-xmailer":"dev-master"
```
然后执行```composer update```，如果PHP版本过低，可使用```composer update --ignore-platform-reqs```。  

安装成功后，添加以下配置到./config/app.php文件：  
```php
    'XMailer' => array(
        'email' => array(
            'host' => 'smtp.gmail.com',
            'username' => 'XXX@gmail.com',
            'password' => '******',
            'from' => 'XXX@gmail.com',
            'fromName' => 'PhalApi团队',
            'sign' => '<br/><br/>请不要回复此邮件，谢谢！<br/><br/>-- PhalApi团队敬上 ',
            'SMTPSecure' => 'SSL',
            'port' => 465,
        ),
    ),
```

## 注册
在./config/di.php文件中，注册邮件服务：  
```php
$di->mailer = function() {
    return new \PhalApi\XMailer\Lite(true);
};
```

## 使用
如下代码示例：
```php
\PhalApi\DI()->$mailer->send('abc@gmail.com', 'Test PHPMailer Lite', 'something here ...');
```

如果需要发送邮件给多个邮箱时，可以使用数组，如：  
```php
$addresses = array('abc@gmail.com', 'test@phalapi.com');
\PhalApi\DI()->mailer->send($addresses, 'Test PHPMailer Lite', 'something here ...');
```
