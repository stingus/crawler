<?php

namespace Stingus\Crawler\Notification;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

/**
 * Class EmailProvider
 *
 * @package Stingus\Crawler\Notification
 */
class EmailProvider
{
    /** @var Mailer */
    private $mailer;

    /** @var string */
    private $mailTo;

    /** @var string */
    private $mailFrom;

    /**
     * EmailProvider constructor.
     *
     * @param Mailer $mailer
     * @param string $mailTo
     * @param string $mailFrom
     */
    public function __construct(Mailer $mailer, $mailTo, $mailFrom)
    {
        $this->mailer = $mailer;
        $this->mailTo = $mailTo;
        $this->mailFrom = $mailFrom;
    }

    /**
     * @param Notification $notification
     */
    public function send(Notification $notification)
    {
        $email = (new Email())
            ->from($this->mailFrom)
            ->to($this->mailTo)
            ->subject($notification->getSubject())
            ->text($notification->getBody());

        $this->mailer->send($email);
    }
}

