[![Build Status](https://travis-ci.org/stingus/crawler.svg?branch=master)](https://travis-ci.org/stingus/crawler)
[![Code Climate](https://codeclimate.com/github/stingus/crawler/badges/gpa.svg)](https://codeclimate.com/github/stingus/crawler)
[![Test Coverage](https://codeclimate.com/github/stingus/crawler/badges/coverage.svg)](https://codeclimate.com/github/stingus/crawler/coverage)

# Crawler
This application crawls the Romanian National Bank for RON (Romanian Leu) exchange rates and weather conditions from OpenWeatherMaps.

## Installation
`git clone` this repository and run `./composer install` afterwards.

## Configuration
In the `config` directory, copy / paste the contents from `crawl.yml.dist` into `crawl.yml` and adjust them accordingly:

### Storage section
For now, data can be persisted in a MySQL database. Make sure to fill in the configuration the appropriate values for
your environment:
```yaml
crawl:
  storage:
    mysql:
      host: 127.0.0.1
      db: crawler
      user: crawler
      password: ~
```
You just need to create the database, the tables are automatically maintained by the application (see Migrations below).

### Exchange section
Data sources for exchange rates are configured through this section:
```yaml
crawl:
  exchange:
    notification: false
    sources:
    -
      class: 'Stingus\Crawler\Exchange\NbrCrawler'
      url: 'http://www.bnro.ro/nbrfxrates.xml'
    -
      class: 'Stingus\Crawler\Exchange\InforeuroCrawler'
      url: 'http://ec.europa.eu/budg/inforeuro/api/public/monthly-rates'
```
If you'd like to skip the Inforeuro exchange rate from crawling, remove the entry from the config. The NBR crawler
MUST be left in place, because it provides the reference date for each crawl.

### Weather section
Data sources for weather are configured through this section:
```yaml
crawl:
  weather:
    notification: false
    unit: 'C'
    sources:
    -
      class: 'Stingus\Crawler\Weather\OpenWeatherCrawler'
      url: 'http://api.openweathermap.org/data/2.5'
      stations: [683506]
      lang: 'en'
      apiKey: 'abcdef'
```
[OpenWeatherMaps](https://openweathermap.org) (OWM) is already built-in, but other sources could be easily added.
It provides geolocation for the selected station IDs, sunset, sunrise, atmospheric pressure, humidity and a 5-day forecast.

- For units you can use 'C' for Celsius or 'F' for Fahrenheit
- You don't need to change the `url` value, it's already set to use the OWM APIs
- Station IDs can be found [here](http://bulk.openweathermap.org/sample/)
- You can customize the `lang` parameter with any of the supported [languages](https://openweathermap.org/forecast5#multi).
This setting will get the weather conditions in the desired language
- The `apikey` can be obtained from your OWM [account](https://home.openweathermap.org/api_keys)

### Notification section
If you'd like to receive error notifications when running the crawlers, you can setup the system in this config section:
```yaml
crawl:
  notification:
    email: <your_email>
    smtp_host: <your_smtp_server>
    smtp_port: <your_smtp_port>
    smtp_user: <optional_smtp_username>
    smtp_password: <optional_smtp_password>
    smtp_from: <your_from_email>
```
You'll also need to enable the notifications on each crawler section:
```yaml
crawl:
  ...
  exchange:
    notification: true
    ...
  weather:
    notification: true
```
You can disable the notification per crawler section or entirely, by removing the whole `notification ` section. 

## Usage
For exchange rates, run the `bin/exchange` command and for weather run `bin/weather`.
The application checks if the DB schema is in place and it creates it if required.
The data is stored in the `exchange` and `weather` tables.

You might want to use a cron to run the scripts. For the exchange rates, it's recommended to run the crawler after
11am UTC, when the NBR updates the numbers. The weather crawler can be ran on an hourly basis.

## Schema migration
The application maintains the schema automatically, by checking if the schema is valid before each run.
In case a new exchange rate is crawled and a new column is needed, you'll need to update the code repository
with the latest version and that's it :)

If you'd like to check for schema updates, independent of the crawling process, run the `bin/migration` command.

*Don't alter or change in any way the `version` table! Doing so will render the schema migration system useless!*

## Tests
You can run the tests using `vendor/bin/phpunit` command.

#happycrawling!
