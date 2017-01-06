CREATE TABLE `weather_codes` (
  `code` smallint(11) unsigned NOT NULL,
  `description_en` varchar(200) NOT NULL DEFAULT '',
  `description_ro` varchar(200) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `weather_codes` (`code`, `description_en`, `description_ro`)
VALUES
  (0, 'tornado', 'tornada'),
  (1, 'tropical storm', 'furtuna tropicala'),
  (2, 'hurricane', 'uragan'),
  (3, 'severe thunderstorms', 'furtuna puternica insotita de descarcari electrice'),
  (4, 'thunderstorms', 'furtuna insotita de descarcari electrice'),
  (5, 'mixed rain and snow', 'averse de ploaie si zapada'),
  (6, 'mixed rain and sleet', 'lapovita si ploaie'),
  (7, 'mixed snow and sleet', 'lapovita si zapada'),
  (8, 'freezing drizzle', 'burnita si ger'),
  (9, 'drizzle', 'burnita'),
  (10, 'freezing rain', 'ploaie si ger'),
  (11, 'showers', 'ploaie'),
  (12, 'showers', 'averse de ploaie'),
  (13, 'snow flurries', 'fulgi de zapada'),
  (14, 'light snow showers', 'ninsoare usoara'),
  (15, 'blowing snow', 'ninsoare viscolita'),
  (16, 'snow', 'ninsoare'),
  (17, 'hail', 'grindina'),
  (18, 'sleet', 'lapovita'),
  (19, 'dust', 'praf'),
  (20, 'foggy', 'ceata'),
  (21, 'haze', 'smog'),
  (22, 'smoky', 'fum'),
  (23, 'blustery', 'rafale de vant'),
  (24, 'windy', 'vant'),
  (25, 'cold', 'ger'),
  (26, 'cloudy', 'innorat'),
  (27, 'mostly cloudy', 'innorat in cea mai mare parte a noptii'),
  (28, 'mostly cloudy', 'innorat in cea mai mare parte a zilei'),
  (29, 'partly cloudy', 'partial innorat'),
  (30, 'partly cloudy', 'partial innorat'),
  (31, 'clear', 'senin'),
  (32, 'sunny', 'insorit'),
  (33, 'fair', 'vreme frumoasa'),
  (34, 'fair', 'vreme frumoasa'),
  (35, 'mixed rain and hail', 'averse de ploaie si grindina'),
  (36, 'hot', 'arsita'),
  (37, 'isolated thunderstorms', 'furtuni izolate'),
  (38, 'scattered thunderstorms', 'furtuna pe arii restranse'),
  (39, 'scattered thunderstorms', 'furtuna pe arii restranse'),
  (40, 'scattered showers', 'averse restranse de ploaie'),
  (41, 'heavy snow', 'ninsori puternice'),
  (42, 'scattered snow showers', 'averse restranse de ninsoare'),
  (43, 'heavy snow', 'ninsori puternice'),
  (44, 'partly cloudy', 'partial innorat'),
  (45, 'thundershowers', 'ploi insotite de descarcari electrice'),
  (46, 'snow showers', 'averse de ninsoare'),
  (47, 'isolated thundershowers', 'ploi izolate insotite de descarcari electrice'),
  (3200, 'not available', 'stare necunoscuta');