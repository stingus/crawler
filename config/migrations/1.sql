CREATE TABLE `weather` (
  `station` int(10) unsigned NOT NULL,
  `build_date` datetime NOT NULL,
  `city` varchar(200) NOT NULL,
  `geo_lat` decimal(9,6) NOT NULL,
  `geo_long` decimal(9,6) NOT NULL,
  `current_temp` smallint(6) NOT NULL,
  `current_code` smallint(5) unsigned NOT NULL,
  `current_wind_chill` smallint(6) NOT NULL,
  `current_wind_direction` smallint(5) unsigned NOT NULL,
  `current_wind_speed` smallint(5) unsigned NOT NULL,
  `current_atm_humidity` tinyint(3) unsigned NOT NULL,
  `current_atm_pressure` decimal(10,2) NOT NULL,
  `current_atm_visibility` tinyint(4) NOT NULL,
  `current_astro_sunrise` varchar(7) NOT NULL,
  `current_astro_sunset` varchar(7) NOT NULL,
  `forecast_date_0` date NOT NULL,
  `forecast_code_0` smallint(5) unsigned NOT NULL,
  `forecast_high_0` smallint(5) NOT NULL,
  `forecast_low_0` smallint(5) NOT NULL,
  `forecast_date_1` date NOT NULL,
  `forecast_code_1` smallint(5) unsigned NOT NULL,
  `forecast_high_1` smallint(5) NOT NULL,
  `forecast_low_1` smallint(5) NOT NULL,
  `forecast_date_2` date NOT NULL,
  `forecast_code_2` smallint(5) unsigned NOT NULL,
  `forecast_high_2` smallint(5) NOT NULL,
  `forecast_low_2` smallint(5) NOT NULL,
  `forecast_date_3` date NOT NULL,
  `forecast_code_3` smallint(5) unsigned NOT NULL,
  `forecast_high_3` smallint(5) NOT NULL,
  `forecast_low_3` smallint(5) NOT NULL,
  `forecast_date_4` date NOT NULL,
  `forecast_code_4` smallint(5) unsigned NOT NULL,
  `forecast_high_4` smallint(5) NOT NULL,
  `forecast_low_4` smallint(5) NOT NULL,
  `forecast_date_5` date NOT NULL,
  `forecast_code_5` smallint(5) unsigned NOT NULL,
  `forecast_high_5` smallint(5) NOT NULL,
  `forecast_low_5` smallint(5) NOT NULL,
  `forecast_date_6` date NOT NULL,
  `forecast_code_6` smallint(5) unsigned NOT NULL,
  `forecast_high_6` smallint(5) NOT NULL,
  `forecast_low_6` smallint(5) NOT NULL,
  `forecast_date_7` date NOT NULL,
  `forecast_code_7` smallint(5) unsigned NOT NULL,
  `forecast_high_7` smallint(5) NOT NULL,
  `forecast_low_7` smallint(5) NOT NULL,
  `forecast_date_8` date NOT NULL,
  `forecast_code_8` smallint(5) unsigned NOT NULL,
  `forecast_high_8` smallint(5) NOT NULL,
  `forecast_low_8` smallint(5) NOT NULL,
  `forecast_date_9` date NOT NULL,
  `forecast_code_9` smallint(5) unsigned NOT NULL,
  `forecast_high_9` smallint(5) NOT NULL,
  `forecast_low_9` smallint(5) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`station`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
