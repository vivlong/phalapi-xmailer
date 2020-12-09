<?php

namespace PhalApi\XMailer;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * 邮件工具类.
 *
 * - 基于PHPMailer的邮件发送
 *
 *  配置
 *
 * 'XMailer' => array(
 *   'email' => array(
 *       'host' => 'smtp.gmail.com',
 *       'username' => 'XXX@gmail.com',
 *       'password' => '******',
 *       'from' => 'XXX@gmail.com',
 *       'fromName' => 'PhalApi团队',
 *       'sign' => '<br/><br/>请不要回复此邮件，谢谢！<br/><br/>-- PhalApi团队敬上 ',
 *   ),
 * ),
 *
 * 示例
 *
 * $mailer = new PHPMailer_Lite(true);
 * $mailer->send('chanzonghuang@gmail.com', 'Test PHPMailer Lite', 'something here ...');
 *
 * @author dogstar <chanzonghuang@gmail.com> 2015-2-14
 */
class Lite
{
    protected $debug;

    protected $config;

    public function __construct($debug = false)
    {
        $di = \PhalApi\DI();
        $this->debug = $debug;
        $this->config = $di->config->get('app.XMailer.email');
    }

    /**
     * 发送邮件.
     *
     * @param array/string $addresses 待发送的邮箱地址
     * @param sting        $title     标题
     * @param string       $content   内容
     * @param bool         $isHtml    是否使用HTML格式，默认是
     *
     * @return bool 是否成功
     */
    public function send($addresses, $title, $content, $isHtml = true)
    {
        $di = \PhalApi\DI();
        $cfg = $this->config;
        $mail = new PHPMailer();
        //Server settings
        $mail->isSMTP();
        $mail->Host = $cfg['host'];
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = $cfg['SMTPSecure'];
        $mail->Port = $cfg['port'];
        $mail->Username = $cfg['username'];
        $mail->Password = $cfg['password'];
        //Recipients
        $mail->CharSet = 'utf-8';
        $mail->setFrom($cfg['username'], $cfg['fromName']);
        $addresses = is_array($addresses) ? $addresses : [$addresses];
        foreach ($addresses as $address) {
            if (!empty($address)) {
                $mail->addAddress($address);
            }
        }
        $mail->WordWrap = 50;
        $mail->isHTML($isHtml);
        $mail->Subject = trim($title);
        $mail->Body = $content.$cfg['sign'];
        if (!$mail->send()) {
            if ($this->debug) {
                $di->logger->debug('Fail to send email with error: '.$mail->ErrorInfo);
            }

            return false;
        }
        if ($this->debug) {
            $di->logger->debug('Succeed to send email', ['addresses' => $addresses, 'title' => $title]);
        }

        return true;
    }

    /**
     * 发送邮件.
     *
     * @param array/string $addresses 待发送的邮箱地址
     * @param sting        $title     标题
     * @param string       $content   内容
     * @param string       $filePath  附件路径
     *
     * @return bool 是否成功
     */
    public function sendWithAttachment($addresses, $title, $content, $filePath, $isHtml = true)
    {
        $di = \PhalApi\DI();
        $cfg = $this->config;
        $mail = new PHPMailer();
        //Server settings
        $mail->isSMTP();
        $mail->Host = $cfg['host'];
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = $cfg['SMTPSecure'];
        $mail->Port = $cfg['port'];
        $mail->Username = $cfg['username'];
        $mail->Password = $cfg['password'];
        //Recipients
        $mail->CharSet = 'utf-8';
        $mail->setFrom($cfg['username'], $cfg['fromName']);
        $addresses = is_array($addresses) ? $addresses : [$addresses];
        foreach ($addresses as $address) {
            if (!empty($address)) {
                $mail->addAddress($address);
            }
        }
        $mail->WordWrap = 50;
        $mail->AddAttachment($filePath);
        $mail->isHTML($isHtml);
        $mail->Subject = trim($title);
        $mail->Body = $content;
        if (!$mail->send()) {
            if ($this->debug) {
                $di->logger->debug('Fail to send email with error: '.$mail->ErrorInfo);
            }

            return false;
        }
        if ($this->debug) {
            $di->logger->debug('Succeed to send email', ['addresses' => $addresses, 'title' => $title]);
        }

        return true;
    }
}
