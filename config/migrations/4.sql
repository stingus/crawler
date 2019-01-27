ALTER TABLE `weather`
  DROP COLUMN `build_date`,
  DROP COLUMN `current_wind_chill`,
  DROP COLUMN `forecast_date_5`,
  DROP COLUMN `forecast_code_5`,
  DROP COLUMN `forecast_high_5`,
  DROP COLUMN `forecast_low_5`,
  DROP COLUMN `forecast_date_6`,
  DROP COLUMN `forecast_code_6`,
  DROP COLUMN `forecast_high_6`,
  DROP COLUMN `forecast_low_6`,
  DROP COLUMN `forecast_date_7`,
  DROP COLUMN `forecast_code_7`,
  DROP COLUMN `forecast_high_7`,
  DROP COLUMN `forecast_low_7`,
  DROP COLUMN `forecast_date_8`,
  DROP COLUMN `forecast_code_8`,
  DROP COLUMN `forecast_high_8`,
  DROP COLUMN `forecast_low_8`,
  DROP COLUMN `forecast_date_9`,
  DROP COLUMN `forecast_code_9`,
  DROP COLUMN `forecast_high_9`,
  DROP COLUMN `forecast_low_9`,
  MODIFY `current_atm_visibility` SMALLINT NOT NULL;

ALTER TABLE `weather`
  ADD COLUMN `current_icon` VARCHAR(15) NOT NULL AFTER `current_code`,
  ADD COLUMN `current_descr` VARCHAR(255) NOT NULL AFTER `current_icon`,
  ADD COLUMN `forecast_icon_0` VARCHAR(15) NOT NULL AFTER `forecast_code_0`,
  ADD COLUMN `forecast_descr_0` VARCHAR(255) NOT NULL AFTER `forecast_icon_0`,
  ADD COLUMN `forecast_icon_1` VARCHAR(15) NOT NULL AFTER `forecast_code_1`,
  ADD COLUMN `forecast_descr_1` VARCHAR(255) NOT NULL AFTER `forecast_icon_1`,
  ADD COLUMN `forecast_icon_2` VARCHAR(15) NOT NULL AFTER `forecast_code_2`,
  ADD COLUMN `forecast_descr_2` VARCHAR(255) NOT NULL AFTER `forecast_icon_2`,
  ADD COLUMN `forecast_icon_3` VARCHAR(15) NOT NULL AFTER `forecast_code_3`,
  ADD COLUMN `forecast_descr_3` VARCHAR(255) NOT NULL AFTER `forecast_icon_3`,
  ADD COLUMN `forecast_icon_4` VARCHAR(15) NOT NULL AFTER `forecast_code_4`,
  ADD COLUMN `forecast_descr_4` VARCHAR(255) NOT NULL AFTER `forecast_icon_4`;


DROP TABLE `weather_codes`;
