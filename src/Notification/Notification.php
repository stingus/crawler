<?php

namespace Stingus\Crawler\Notification;

/**
 * Class Notification
 *
 * @package Stingus\Crawler\Notification
 */
class Notification
{
    /** @var EmailProvider */
    private $emailProvider;

    /** @var string */
    private $subject;

    /** @var string */
    private $body;

    /**
     * Notification constructor.
     *
     * @param EmailProvider $emailProvider
     */
    public function __construct(EmailProvider $emailProvider)
    {
        $this->emailProvider = $emailProvider;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     *
     * @return Notification
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     *
     * @return Notification
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Send the notification
     */
    public function send()
    {
        $this->emailProvider->send($this);
    }
}
