<?php

namespace Stingus\Crawler\Commands;

use GuzzleHttp\Client;
use Stingus\Crawler\Storage\MySqlConnection;
use Stingus\Crawler\Storage\Weather\WeatherStorage;
use Stingus\Crawler\Weather\Weather;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class WeatherCommand.
 * Crawls weather sources and saves the results
 *
 * @package Stingus\Crawler\Commands
 */
class WeatherCommand extends CrawlCommand
{
    /**
     * @inheritdoc
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('crawl:weather')
            ->setDescription('Crawls for weather stations')
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
        $output->writeln(sprintf('Starting the weather crawl on %s', date('d/m/y H:i:s')));
        $weather = new Weather(new Crawler(), new Client());

        /** @var array $sources */
        $sources = $config['weather']['sources'];
        foreach ($sources as $source) {
            $weather->registerCrawler(
                new $source['class']($source['url'], $config['weather']['unit'], $source['stations'])
            );
        }

        $result = $weather->crawl();
        $statusSuccess = $weather->getCrawlerStatus(true);
        $statusFail = $weather->getCrawlerStatus(false);

        if (count($statusSuccess) > 0) {
            $output->writeln('<info>Successful stations:</info>');
            foreach ($statusSuccess as $successCrawler => $successStations) {
                $output->writeln("\t" . $successCrawler . ': ' . implode(', ', $successStations));
            }
        }

        if (count($statusFail) > 0) {
            $output->writeln('<error>Failed stations:</error>');
            foreach ($statusFail as $failCrawler => $failStations) {
                $output->writeln("\t" . $failCrawler . ': ' . implode(', ', $failStations));
            }
        }

        $output->writeln(sprintf('Fetched %s weather stations', count($result)));

        if (count($result) > 0) {
            $mysql = new MySqlConnection($config['storage']['mysql']);

            $weatherStorage = new WeatherStorage($mysql);
            $output->write('Saving... ');
            $weatherStorage->save($result);
            $output->writeln('<info>done</info>');
        }
    }
}
