<?php

namespace PhalApi\Xmailer;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * 邮件工具类.
 */
class Lite
{
    protected $debug;

    protected $config;

    public function __construct($debug = true, $config = null)
    {
        $di = \PhalApi\DI();
        $this->debug = $debug || $di->debug;
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
     * @param array        $replyto   回复
     * @param array/string $ccs       抄送
     * @param array/string $bccs      暗送
     * @param array        $attachs   附件
     *
     * @return bool 是否成功
     */
    public function send(
        $addresses,
        $title,
        $content,
        $isHtml = true,
        $replyto = [],
        $ccs = [],
        $bccs = [],
        $attachs = []
    ) {
        $di = \PhalApi\DI();
        $cfg = $this->config;
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer($this->debug);
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; //Enable verbose debug output
            $mail->isSMTP(); //Send using SMTP
            $mail->Host = $cfg['host']; //Set the SMTP server to send through
            $mail->Port = $cfg['port']; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            $mail->SMTPAuth = true; //Enable SMTP authentication
            $mail->SMTPSecure = $cfg['SMTPSecure']; //Enable encryption
            $mail->Username = $cfg['username']; //SMTP username
            $mail->Password = $cfg['password']; //SMTP password
            //Recipients
            $mail->CharSet = 'utf-8';
            $mail->setFrom($cfg['username'], $cfg['fromName']);
            $addses = is_array($addresses) ? $addresses : [$addresses];
            foreach ($addses as $address) {
                if (!empty($address)) {
                    $mail->addAddress($address); //Name is optional
                }
            }
            if (!empty($replyto)) {
                $mail->addReplyTo($replyto['address'], $replyto['title']);
            }
            if (!empty($ccs)) {
                $ccAdds = is_array($ccs) ? $ccs : [$ccs];
                foreach ($ccAdds as $ccAdd) {
                    if (!empty($ccAdd)) {
                        $mail->addCC($ccAdd);
                    }
                }
            }
            if (!empty($bccs)) {
                $bccAdds = is_array($bccs) ? $bccs : [$bccs];
                foreach ($bccAdds as $bccAdd) {
                    if (!empty($bccAdd)) {
                        $mail->addBCC($bccAdd);
                    }
                }
            }
            if (!empty($attachs) && is_array($attachs)) {
                foreach ($attachs as $attach) {
                    if (!empty($attach)) {
                        if (
                            !$mail->addAttachment(
                                $attach['path'],
                                $attach['name']
                            )
                        ) {
                            $di->logger->info(
                                __CLASS__ . DIRECTORY_SEPARATOR . __FUNCTION__,
                                ['Failed to attach file' => $attach['path']]
                            );
                        }
                    }
                }
            }
            $mail->WordWrap = 50; // Wraps the message body to the number of chars set in the WordWrap property. You should only do this to plain-text bodies as wrapping HTML tags may break them.
            $mail->isHTML($isHtml); //Set email format to HTML
            $mail->Subject = trim($title);
            $mail->Body = $content . $cfg['sign'];
            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            //$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
            if (!$mail->send()) {
                if ($this->debug) {
                    $di->logger->error(
                        __CLASS__ . DIRECTORY_SEPARATOR . __FUNCTION__,
                        ['Fail to send email' => $mail->ErrorInfo]
                    );
                }
                // TODO
                // allow using IMAP to save mail

                return false;
            }
            if ($this->debug) {
                $di->logger->debug(
                    __CLASS__ . DIRECTORY_SEPARATOR . __FUNCTION__,
                    [
                        'Succeed to send email' => [
                            'addresses' => $addresses,
                            'title' => $title,
                        ],
                    ]
                );
            }

            return true;
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            $di->logger->error(__CLASS__ . DIRECTORY_SEPARATOR . __FUNCTION__, [
                'Mailer Exception' => $e->errorMessage(),
            ]);

            return false;
        }
    }
}
