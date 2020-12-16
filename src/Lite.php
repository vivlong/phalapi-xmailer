<?php

namespace PhalApi\Xmailer;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * 邮件工具类.
 */
class Lite
{
    protected $debug;

    protected $config;

    public function __construct($config = null)
    {
        $di = \PhalApi\DI();
        $this->debug = $di->debug;
        $this->config = $config;
        if (null == $this->config) {
            $this->config = $di->config->get('app.Xmailer.email');
        }
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
                $di->logger->info(__NAMESPACE__.DIRECTORY_SEPARATOR.__CLASS__.DIRECTORY_SEPARATOR.__FUNCTION__, ['Fail to send email' => $mail->ErrorInfo]);
            }

            return false;
        }
        if ($this->debug) {
            $di->logger->info(__NAMESPACE__.DIRECTORY_SEPARATOR.__CLASS__.DIRECTORY_SEPARATOR.__FUNCTION__, ['Succeed to send email' => ['addresses' => $addresses, 'title' => $title]]);
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
                $di->logger->info(__NAMESPACE__.DIRECTORY_SEPARATOR.__CLASS__.DIRECTORY_SEPARATOR.__FUNCTION__, ['Fail to send email' => $mail->ErrorInfo]);
            }

            return false;
        }
        if ($this->debug) {
            $di->logger->info(__NAMESPACE__.DIRECTORY_SEPARATOR.__CLASS__.DIRECTORY_SEPARATOR.__FUNCTION__, ['Succeed to send email' => ['addresses' => $addresses, 'title' => $title]]);
        }

        return true;
    }
}
