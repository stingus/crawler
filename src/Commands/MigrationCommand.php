<?php

namespace Stingus\Crawler\Commands;

use Stingus\Crawler\Migrations\Migration;
use Stingus\Crawler\Storage\MySqlConnection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MigrationCommand.
 * Check the DB schema and execute migrations
 *
 * @package Stingus\Crawler\Commands
 */
class MigrationCommand extends CrawlCommand
{
    /**
     * @inheritdoc
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('crawl:migrations')
            ->setDescription('Keeps the DB schema valid by running necessary migrations')
        ;
    }

    /**
     * @inheritdoc
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @throws \RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getConfig();
        $migration = new Migration(new MySqlConnection($config['storage']['mysql']));
        $output->write('Checking migration... ');
        $fromVersion = $migration->migrate();
        if (-1 === $fromVersion) {
            $output->writeln('<info>created schema.</info> ');
        } elseif ($fromVersion < Migration::LAST_VERSION) {
            $output->writeln(
                sprintf(
                    '<info>successfully executed migrations from %s to %s</info>',
                    $fromVersion,
                    Migration::LAST_VERSION
                )
            );
        } else {
            $output->writeln('<info>already up to date</info>');
        }
    }
}
