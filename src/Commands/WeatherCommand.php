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
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getConfig();
        try {
            // Execute migrations first
            $this->checkMigrations($output);

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
                $errorMessage = 'The following weather stations failed to provide data:' . PHP_EOL;
                $output->writeln('<error>Failed stations:</error>');
                foreach ($statusFail as $failCrawler => $failStations) {
                    $errorLine = $failCrawler . ': ' . implode(', ', $failStations);
                    $output->writeln("\t" . $errorLine);
                    $errorMessage .= $errorLine . PHP_EOL;
                }
                $this->sendErrorNotification('weather', $errorMessage);
            }

            $output->writeln(sprintf('Fetched %s weather stations', count($result)));

            if (count($result) > 0) {
                $mysql = new MySqlConnection($config['storage']['mysql']);

                $weatherStorage = new WeatherStorage($mysql);
                $output->write('Saving... ');
                $weatherStorage->save($result);
                $output->writeln('<info>done</info>');
            }
        } catch (\Exception $e) {
            $this->sendErrorNotification('weather', $e->getMessage());

            throw $e;
        }
    }
}
