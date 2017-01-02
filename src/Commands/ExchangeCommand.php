<?php

namespace Stingus\Crawler\Commands;

use GuzzleHttp\Client;
use Stingus\Crawler\Exchange\Exchange;
use Stingus\Crawler\Storage\Exchange\ExchangeStorage;
use Stingus\Crawler\Storage\MySqlConnection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class ExchangeCommand.
 * Crawls exchange rate sources and saves the results
 *
 * @package Stingus\Crawler\Commands
 */
class ExchangeCommand extends CrawlCommand
{
    /**
     * @inheritdoc
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('crawl:exchange')
            ->setDescription('Crawls for exchange rates')
        ;
    }

    /**
     * @inheritdoc
     * @throws \Symfony\Component\Console\Exception\CommandNotFoundException
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @throws \InvalidArgumentException
     * @throws \PDOException
     * @throws \RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Execute migrations first
        $this->checkMigrations($output);

        $config = $this->getConfig();
        $output->writeln(sprintf('Starting the exchange crawl on %s', date('d/m/y H:i:s')));
        $exchange = new Exchange(new Crawler(), new Client());

        /** @var array $sources */
        $sources = $config['exchange']['sources'];
        foreach ($sources as $source) {
            $exchange->registerCrawler(new $source['class']($source['url']));
        }
        $result = $exchange->crawl();

        $output->writeln(sprintf('Fetched %s rates', count($result)));

        $mysql = new MySqlConnection($config['storage']['mysql']);

        $exchangeStorage = new ExchangeStorage($exchange->getDate(), $mysql);
        $output->write('Saving... ');
        $saveResult = $exchangeStorage->save($result);
        if ($saveResult) {
            $output->writeln('<info>done</info>');
        }
        if (!$saveResult) {
            $output->writeln('<comment>date already saved</comment>');
        }
    }
}
