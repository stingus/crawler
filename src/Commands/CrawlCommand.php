<?php

namespace Stingus\Crawler\Commands;

use Stingus\Crawler\Configuration\CrawlConfiguration;
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
    /**
     * @return array
     * @throws \Symfony\Component\Yaml\Exception\ParseException
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
     *
     * @throws \Symfony\Component\Console\Exception\CommandNotFoundException
     */
    protected function checkMigrations(OutputInterface $output)
    {
        $migrationCommand = $this->getApplication()->find('crawl:migrations');
        $migrationCommand->run(new ArrayInput([]), $output);
    }
}
