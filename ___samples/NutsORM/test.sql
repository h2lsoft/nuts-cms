/**
 * NutsORM Sample
 *
 * @version 1.0
 * @date 2012/02/23
 * @author H2lsoft - www.h2lsoft.com
 */
CREATE TABLE `Author` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `Deleted` enum('YES','NO') NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`ID`),
  KEY `Deleted` (`Deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Book` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `AuthorID` int(11) DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Deleted` enum('YES','NO') NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`ID`),
  KEY `Deleted` (`Deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

