<?php
/**
 * Created by PhpStorm.
 * User: koko
 * Date: 16/03/2017
 * Time: 21:14
 *
 * TODO: wrapper for encrypted messages (possibly will never be implemented :D)
 *
 */

class Mail {

    /**
     * @var array
     */
    private $__config = [
        'transport' => 'sendmail', // smtp|sendmail
        'sendmail_command' => '/usr/sbin/sendmail -bs',
        'smtp_host' => null,
        'smtp_port' => 25,
        'smtp_username' => null,
        'smtp_password' => null,
        'smtp_security' => null,
    ];
    /**
     * @var $this Swift_Message|null
     */
    private $message = null;

    /**
     * @var null|Swift_SmtpTransport|Swift_SendmailTransport
     */
    private $transport = null;

    /**
     * @var null|Swift_Mailer
     */
    private $mailer = null;

    /**
     * @var array
     */
    private $failedRecipients = [];

    /**
     * Mail constructor.
     *
     * @param array $config
     *
     * @throws Exception
     */
    public function __construct($config = []) {
        if(!class_exists('Swift_Message') || !class_exists('Swift_Mailer')) {
            throw new Exception("Swift mailer is not properly installed on system! Use 'composer require swiftmailer/swiftmailer @stable'");
        }

        $this->__config = array_merge($this->__config, Config::get('mail', []), $config);

        switch($this->__config['transport']) {
            case 'smtp':
                $this->transport = Swift_SmtpTransport::newInstance($this->__config['smtp_host'], $this->__config['smtp_port']);
                if(!is_null($this->__config['smtp_security'])) {
                    $this->transport->setEncryption($this->__config['smtp_security']);
                }

                if(!is_null($this->__config['smtp_password']) && !is_null($this->__config['smtp_username'])) {
                    $this->transport->setUsername($this->__config['smtp_username']);
                    $this->transport->setPassword($this->__config['smtp_password']);
                }
                break;
            case 'sendmail':
                $this->transport = Swift_SendmailTransport::newInstance($this->__config['sendmail_command']);
                break;
            default:
                throw new Exception('No legal mailer transport provided! Use sendmail or smtp');
        }

        $this->mailer = Swift_Mailer::newInstance($this->transport);

        $this->message = Swift_Message::newInstance();
    }

    /**
     * @param $text string
     *
     * @return $this
     */
    public function setSubject($text) {
        $this->message->setSubject($text);
        return $this;
    }

    /**
     * @param $addresses string|array
     * @param null|string $name
     *
     * @return $this
     */
    public function setFrom($addresses, $name = null) {
        $this->message->setFrom($addresses, $name);
        return $this;
    }

    /**
     * @param string|array $addresses
     * @param null|string $name
     *
     * @return $this
     */
    public function setTo($addresses, $name = null) {
        $this->message->setTo($addresses, $name);
        return $this;
    }

    /**
     * @param string|array $address
     * @param null|string $name
     *
     * @return $this
     */
    public function addTo($address, $name = null) {
        $this->message->addTo($address, $name);
        return $this;
    }

    /**
     * @param string|array $addresses
     * @param null|string $name
     *
     * @return $this
     */
    public function setCc($addresses, $name = null) {
        $this->message->setCc($addresses, $name);
        return $this;
    }

    /**
     * @param string|array $address
     * @param null|string $name
     *
     * @return $this
     */
    public function addCc($address, $name = null) {
        $this->message->addCc($address, $name);
        return $this;
    }

    /**
     * @param string|array $addresses
     * @param null|string $name
     *
     * @return $this
     */
    public function setBcc($addresses, $name = null) {
        $this->message->setBcc($addresses, $name);
        return $this;
    }

    /**
     * @param string|array $address
     * @param null|string $name
     *
     * @return $this
     */
    public function addBcc($address, $name = null) {
        $this->message->addBcc($address, $name);
        return $this;
    }

    /**
     * @param string $address
     * @param null|string $name
     *
     * @return $this
     */
    public function setSender($address, $name = null) {
        $this->message->setSender($address, $name);
        return $this;
    }

    /**
     * @param string $address
     *
     * @return $this
     */
    public function setReturnPath($address) {
        $this->message->setReturnPath($address);
        return $this;
    }

    /**
     * @param string $charset
     *
     * @return $this
     */
    public function setCharset($charset) {
        $this->message->setCharset($charset);
        return $this;
    }

    /**
     * @param string $body
     * @param null|string $contentType
     * @param null|string $charset
     *
     * @return $this
     */
    public function setBody($body, $contentType = null, $charset = null) {
        $this->message->setBody($body, $contentType, $charset);
        return $this;
    }

    /**
     * @param string $body
     * @param null|string $contentType
     * @param null|string $charset
     *
     * @return $this
     */
    public function addPart($body, $contentType = null, $charset = null) {
        $this->message->addPart($body, $contentType, $charset);
        return $this;
    }

    /**
     * @param string|array $file path or path with name
     * @param null|string $contentType
     * @param null|string $disposition use 'inline' for inline attachments.
     *
     * @return $this
     */
    public function attach($file, $contentType = null, $disposition = null) {
        $fileName = null;
        if(is_array($file)) {
            reset($file);
            $filePath = key($file);
            $fileName = $file[$filePath];
        } else {
            $filePath = $file;
        }
        if(!is_null($contentType)) {
            $attachment = Swift_Attachment::fromPath($filePath, $contentType);
        } else {
            $attachment = Swift_Attachment::fromPath($filePath);
        }

        if(!is_null($fileName)) {
            $attachment->setFilename($fileName);
        }

        if(!is_null($disposition)) {
            $attachment->setDisposition($disposition);
        }
        $this->message->attach($attachment);
        return $this;
    }

    /**
     * Usage: in message body use as img src
     * <img src="' . $mail->prepareImgForInline($imagePath) . '" alt="">
     *
     * @param $imagePath string
     *
     * @return string
     */
    public function prepareImgForInline($imagePath) {
        return $this->message->embed(Swift_Image::fromPath($imagePath));
    }

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority($priority = 3) {
        $this->message->setPriority($priority);
        return $this;
    }

    /**
     * use as original Swift_Message object
     * @return Swift_Message
     */
    public function messageObject() {
        return $this->message;
    }

    /**
     * Use, if made message object out of Mail class.
     * Overrides private $this->message
     *
     * @param Swift_Message $message
     *
     * @return $this
     */
    public function setMessageObject(Swift_Message $message) {
        $this->message = $message;
        return $this;
    }

    /**
     * @return int
     */
    public function send() {
        return $this->mailer->send($this->message, $this->failedRecipients);
    }

}