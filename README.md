[![Build Status](https://travis-ci.org/stingus/crawler.svg?branch=master)](https://travis-ci.org/stingus/crawler)
[![Code Climate](https://codeclimate.com/github/stingus/crawler/badges/gpa.svg)](https://codeclimate.com/github/stingus/crawler)
[![Test Coverage](https://codeclimate.com/github/stingus/crawler/badges/coverage.svg)](https://codeclimate.com/github/stingus/crawler/coverage)

# Crawler
This application crawls for RON (New Romanian Leu) exchange rates from National Bank of Romania (NBR) and UE's Inforeuro.

Coming soon: weather station crawling!

## Installation
`git clone` this repository and run `./composer install` afterwards.

## Configuration
In the `config` directory, copy / paste the contents from `crawl.yml.dist` into `crawl.yml` and adjust them accordingly:

### Storage section
For now, data can be persisted in a MySQL database. Make sure to fill in the configuration the appropriate values for
your environment:
```yaml
storage:
  mysql:
    host: 127.0.0.1
    db: crawler
    user: crawler
    password: ~
```
You just need to create the database, the tables are automatically maintained by the application (see Migrations below).

### Exchange section
Data sources are configured through this section:
```yaml
exchange:
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

## Usage
Run the `bin/exchange` command. The application checks if the DB schema is in place and it creates it if required.
The data is stored in the `exchange` table. You might want to use a cron to run the script daily.

## Schema migration
The application maintains the schema automatically, by checking if the schema is valid before each run.
In case a new exchange rate is crawled and a new column is needed, you'll need to update the code repository
with the latest version and that's it :)

If you'd like to check for schema updates, independent of the crawling process, run the `bin/migration` command.

*Don't alter or change in any way the `version` table! Doing so will render the schema migration system useless!*

## Tests
You can run the tests using `vendor/bin/phpunit` command. More tests are on the way (unit and functional).

#Happy crawling!
