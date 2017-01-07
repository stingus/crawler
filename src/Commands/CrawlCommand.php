<?php

namespace Stingus\Crawler\Commands;

use Stingus\Crawler\Configuration\CrawlConfiguration;
use Stingus\Crawler\Notification\EmailProvider;
use Stingus\Crawler\Notification\Notification;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class CrawlCommand.
 * Base crawl command, parses the configuration
 *
 * @package Stingus\Crawler\Commands
 */
abstract class CrawlCommand extends Command
{
    /** @var EmailProvider */
    private $emailProvider;

    /**
     * @return array
     */
    public function getConfig()
    {
        $config = Yaml::parse(file_get_contents(__DIR__ . '/../../config/crawl.yml'));
        $processor = new Processor();
        return $processor->processConfiguration(new CrawlConfiguration(), $config);
    }

    /**
     * Check migrations command
     *
     * @param OutputInterface $output
     */
    protected function checkMigrations(OutputInterface $output)
    {
        $migrationCommand = $this->getApplication()->find('crawl:migrations');
        $migrationCommand->run(new ArrayInput([]), $output);
    }

    /**
     * Send an error notification
     *
     * @param string $crawler Crawler config key
     * @param string $message Error message
     */
    protected function sendErrorNotification($crawler, $message)
    {
        $config = $this->getConfig();

        if (array_key_exists('notification', $config[$crawler])
            && true === $config[$crawler]['notification']
            && (($emailProvider = $this->getEmailProvider()) instanceof EmailProvider)
        ) {
            $notification = new Notification($emailProvider);
            $notification
                ->setSubject(sprintf('%s crawler exception', ucfirst($crawler)))
                ->setBody($message)
                ->send();
        }
    }

    /**
     * Setup the email provider
     *
     * @return EmailProvider|null
     */
    private function getEmailProvider()
    {
        if ($this->emailProvider instanceof EmailProvider) {
            return $this->emailProvider;
        }
        $config = $this->getConfig();
        if (array_key_exists('notification', $config)) {
            $notificationConfig = $config['notification'];
            $transport = new \Swift_SmtpTransport($notificationConfig['smtp_host'], $notificationConfig['smtp_port']);
            $transport
                ->setUsername($notificationConfig['smtp_user'])
                ->setPassword($notificationConfig['smtp_password']);
            $mailer = new \Swift_Mailer($transport);
            $this->emailProvider = $mailer;

            return new EmailProvider($mailer, $notificationConfig['email'], $notificationConfig['smtp_from']);
        }

        return null;
    }
}
