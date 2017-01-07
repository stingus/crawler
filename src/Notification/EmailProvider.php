<?php

namespace Stingus\Crawler\Notification;

/**
 * Class EmailProvider
 *
 * @package Stingus\Crawler\Notification
 */
class EmailProvider
{
    /** @var \Swift_Message */
    private $message;

    /** @var \Swift_Mailer */
    private $mailer;

    /**
     * EmailProvider constructor.
     *
     * @param \Swift_Mailer $mailer
     * @param string        $mailTo
     * @param string        $mailFrom
     */
    public function __construct(\Swift_Mailer $mailer, $mailTo, $mailFrom)
    {
        $this->mailer = $mailer;
        $this->message = $mailer->createMessage('message');
        $this->message->setTo($mailTo);
        $this->message->setFrom($mailFrom);
    }

    /**
     * @param Notification $notification
     */
    public function send(Notification $notification)
    {
        $this->message
            ->setSubject($notification->getSubject())
            ->setBody($notification->getBody());
        $this->mailer->send($this->message);
    }
}
