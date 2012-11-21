##############################################################
##				CONFIGURATION								##
##############################################################

-- Warning! change your database name MyNuts
ALTER DATABASE MyNuts DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE MyNuts;
-- end of changing name

-- administrator properties
SET @ADMIN_LOGIN := 'admin';
SET @ADMIN_FIRST_NAME := 'First name';
SET @ADMIN_LAST_NAME := 'Last name';
SET @ADMIN_EMAIL := 'admin@domain.com';

-- language en, fr, es, it, ru, de
SET @ADMIN_LANG := 'en';
SET @ADMIN_TIMEZONE = '1';

##############################################################
##				FINISH CONFIGURATION						##
##############################################################





/*
NutsUser
*/
CREATE TABLE `NutsUser` (

  `ID` int(10) unsigned NOT NULL auto_increment,
  `NutsGroupID` int(10) unsigned NOT NULL default '0',
  `FirstName` varchar(50) default NULL,
  `LastName` varchar(50) default NULL,
  `Email` varchar(150) NOT NULL default '',
  `Login` varchar(15) NOT NULL default '',
  `Password` varchar(15) NOT NULL default '',
  `Language` varchar(4) NOT NULL default '',
  `Timezone` varchar(50) NOT NULL default '',
  `Active` enum('YES','NO') NOT NULL default 'YES',
  `Deleted` enum('YES','NO') NOT NULL default 'NO',
  
  PRIMARY KEY  (`ID`,`NutsGroupID`),
  KEY `Deleted` (`Deleted`),
  KEY `Active` (`Active`)
) ENGINE=MyISAM;

INSERT INTO `NutsUser` VALUES
('1','1',@ADMIN_FIRST_NAME, @ADMIN_LAST_NAME, @ADMIN_EMAIL, @ADMIN_LOGIN, 'admin', @ADMIN_LANG, @ADMIN_TIMEZONE,'YES','NO');




/*
NutsGroup
*/
CREATE TABLE `NutsGroup` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(50) NOT NULL default '',
  `Description` text,
  `TinyMceConfig` varchar(15) NOT NULL default '',
  `Priority` smallint(5) unsigned NOT NULL default '0',
  `Deleted` enum('YES','NO') NOT NULL default 'NO',
  `LogoImage` varchar(255) default NULL,
  PRIMARY KEY  (`ID`),
  KEY `Deleted` (`Deleted`),
  KEY `Name` (`Name`)
) ENGINE=MyISAM;

INSERT INTO `NutsGroup` VALUES
('1','SuperAdmin','User with maximum rights','Full','1','NO','');




/*
NutsTemplateConfiguration
*/
CREATE TABLE `NutsTemplateConfiguration` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `LanguageDefault` varchar(255) default NULL,
  `Languages` varchar(255) default NULL,
  `Theme` varchar(255) default NULL,
  `Description` text,
  `Deleted` enum('YES','NO') NOT NULL default 'NO',
  PRIMARY KEY  (`ID`),
  KEY `Deleted` (`Deleted`)
) ENGINE=MyISAM;

INSERT INTO `NutsTemplateConfiguration` VALUES
('1','fr','','default','This is the default theme','NO');


/*
NutsZone
*/
CREATE TABLE `NutsZone` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `Type` enum('MENU','UNIVERSAL TEXT','TEXT') NOT NULL default 'MENU',
  `Name` varchar(50) default NULL,
  `CssName` varchar(50) default NULL,
  `Description` text,
  `Visible` enum('YES','NO') NOT NULL default 'YES',
  `Deleted` enum('YES','NO') NOT NULL default 'NO',
  PRIMARY KEY  (`ID`),
  KEY `Deleted` (`Deleted`),
  KEY `Type` (`Type`),
  KEY `Visible` (`Visible`)
) ENGINE=MyISAM;



/*
NutsMenu
*/
CREATE TABLE `NutsMenu` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `Categorie` int(11) default NULL,
  `Name` varchar(50) default NULL,
  `ExternalUrl` varchar(255) default NULL,
  `BreakAfter` smallint(5) unsigned NOT NULL default '0',
  `Position` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  KEY `Group` (`Categorie`),
  KEY `Position` (`Position`)
) ENGINE=MyISAM;

INSERT INTO `NutsMenu` VALUES
('1','1','_configuration','','1','1'),
('2','2','_zone-manager','','0','2'),
('3','2','_template_settings','','0','1'),
('4','1','_user-manager','','0','3'),
('5','1','_group-manager','','0','3'),
('6','2','_page-manager','','1','4'),
('7','4','_logs','','0','2'),
('8','1','_right-manager','','0','4'),
('9','2','_news','','0','3'),
('10','4','_updater','','0','2'),
('11','4','_phpinfo','','0','1'),
('12','2','_region-manager','','1','3'),
('13','2','_gallery','','0','6'),
('14','2','_gallery_image','','1','7'),
('15','2','_pattern','','0','8'),
('17','4','_documentation','','0','4'),
('18','2','_block_builder','','0','2');


/*
NutsMenuRight
*/
CREATE TABLE `NutsMenuRight` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `NutsMenuID` int(10) unsigned NOT NULL default '0',
  `NutsGroupID` int(10) unsigned NOT NULL default '0',
  `Name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`ID`,`NutsMenuID`,`NutsGroupID`)
) ENGINE=MyISAM;

INSERT INTO `NutsMenuRight` VALUES
('','8','1','edit');


/*
NutsNews
*/
CREATE TABLE `NutsNews` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `NutsUserID` int(10) unsigned NOT NULL,
  `Language` varchar(5) default NULL,
  `DateGMT` datetime NOT NULL,
  `DateGMTExpiration` datetime default NULL,
  `Title` varchar(255) default NULL,
  `Resume` text,
  `Text` text,
  `Tags` text,
  `Event` enum('YES','NO') NOT NULL default 'YES',
  `Comment` enum('YES','NO') NOT NULL default 'NO',
  `Active` enum('YES','NO') NOT NULL default 'YES',
  `VirtualPageName` varchar(255) default NULL,
  `Filter1` varchar(255) default NULL,
  `Filter2` varchar(255) default NULL,
  `Filter3` varchar(255) default NULL,
  `Archived` enum('YES','NO') NOT NULL default 'NO',
  `Deleted` enum('YES','NO') NOT NULL default 'NO',
  PRIMARY KEY  (`ID`,`NutsUserID`),
  KEY `Date` (`DateGMT`),
  KEY `Deleted` (`Deleted`),
  KEY `Event` (`Event`),
  KEY `Active` (`Active`),
  KEY `Language` (`Language`),
  KEY `DateGMTExpiration` (`DateGMTExpiration`),
  KEY `Filter1` (`Filter1`),
  KEY `Filter2` (`Filter2`),
  KEY `Filter3` (`Filter3`),
  KEY `Archived` (`Archived`),
  FULLTEXT KEY `Tags` (`Tags`)
) ENGINE=MyISAM;


/*
NutsPage
*/
CREATE TABLE `NutsPage` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `NutsPageID` int(10) unsigned NOT NULL default '0',
  `NutsUserID` int(10) unsigned NOT NULL default '0',
  `ContentType` varchar(255) default 'TEXT',
  `ZoneID` int(10) unsigned NOT NULL default '0',
  `HeaderImage` varchar(255) default NULL,
  `Language` varchar(5) default NULL,
  `DateCreation` datetime NOT NULL default '0000-00-00 00:00:00',
  `DateUpdate` datetime default NULL,
  `Template` varchar(255) default NULL,
  `MetaTitle` varchar(255) default NULL,
  `MetaDescription` varchar(255) default NULL,
  `MetaKeywords` varchar(255) default NULL,
  `H1` varchar(255) default NULL,
  `VirtualPagename` varchar(255) default NULL,
  `MenuName` varchar(255) default NULL,
  `ContentResume` text,
  `Content` text,
  `State` enum('DRAFT','PUBLISHED','WAITING MODERATION','REVISION','ARCHIVED') NOT NULL default 'PUBLISHED',
  `MenuVisible` enum('YES','NO') NOT NULL default 'YES',
  `TopBar` enum('YES','NO') NOT NULL default 'YES',
  `BottomBar` enum('YES','NO') NOT NULL default 'YES',
  `Comments` enum('YES','NO') NOT NULL default 'YES',
  `AccessRestricted` enum('YES','NO') NOT NULL default 'NO',
  `Position` int(10) unsigned NOT NULL default '0',
  `_HasChildren` enum('YES','NO') NOT NULL default 'NO',
  `Deleted` enum('YES','NO') NOT NULL default 'NO',
  `Event` enum('YES','NO') NOT NULL default 'YES',
  `CustomVars` text,
  `CustomBlock` text,
  `Tags` text,
  PRIMARY KEY  (`ID`,`NutsPageID`,`NutsUserID`),
  KEY `MenuVisible` (`MenuVisible`),
  KEY `ContentType` (`ContentType`),
  KEY `State` (`State`),
  KEY `Deleted` (`Deleted`),
  KEY `AccessRestricted` (`AccessRestricted`),
  KEY `Language` (`Language`),
  KEY `Zone` (`ZoneID`),
  KEY `Comments` (`Comments`),
  FULLTEXT KEY `Tags` (`Tags`)
) ENGINE=MyISAM;


/*
NutsLog
*/
CREATE TABLE `NutsLog` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `NutsGroupID` int(10) unsigned NOT NULL default '0',
  `NutsUserID` int(10) unsigned NOT NULL default '0',
  `DateGMT` datetime default NULL,
  `Application` varchar(40) default NULL,
  `Action` varchar(255) default NULL,
  `Resume` text,
  `IP` varchar(255) default NULL,
  `RecordID` int(10) unsigned NOT NULL default '0',
  `Deleted` enum('YES','NO') NOT NULL default 'NO',
  PRIMARY KEY  (`ID`,`NutsGroupID`,`NutsUserID`),
  KEY `Application` (`Application`),
  KEY `Deleted` (`Deleted`),
  KEY `Date` (`DateGMT`)
) ENGINE=MyISAM;

/*
NutsBlock
*/
CREATE TABLE `NutsBlock` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `GroupName` varchar(255) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Text` text,
  `Visible` enum('YES','NO') NOT NULL default 'YES',
  `Deleted` enum('YES','NO') NOT NULL default 'NO',
  PRIMARY KEY  (`ID`),
  KEY `Deleted` (`Deleted`),
  KEY `Visible` (`Visible`),
  KEY `GroupName` (`GroupName`)
) ENGINE=MyISAM;

/*
NutsContentType
*/
CREATE TABLE `NutsContentType` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `NutsPageID` int(10) unsigned NOT NULL default '0',
  `Type` varchar(255) default NULL,
  `Option` text,
  PRIMARY KEY  (`ID`,`NutsPageID`)
) ENGINE=MyISAM;

INSERT INTO `NutsContentType` VALUES ('1','0','TEXT','');

/*
NutsGallery
*/
CREATE TABLE `NutsGallery` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL,
  `Description` text,
  `LogoImage` varchar(255) default NULL,
  `Position` int(10) unsigned NOT NULL,
  `Active` enum('YES','NO') NOT NULL default 'YES',
  `Deleted` enum('YES','NO') NOT NULL default 'NO',
  PRIMARY KEY  (`ID`),
  KEY `Active` (`Active`),
  KEY `Deleted` (`Deleted`),
  KEY `Position` (`Position`)
) ENGINE=MyISAM;

/*
NutsGalleryImage
*/
CREATE TABLE `NutsGalleryImage` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `NutsGalleryID` int(10) unsigned NOT NULL,
  `MainImage` varchar(255) default NULL,
  `BigImage` varchar(255) default NULL,
  `HDImage` varchar(255) default NULL,
  `Legend` varchar(255) default NULL,
  `Description` text,
  `Active` enum('YES','NO') NOT NULL default 'YES',
  `Position` int(10) unsigned NOT NULL,
  `Deleted` enum('YES','NO') NOT NULL default 'NO',
  PRIMARY KEY  (`ID`,`NutsGalleryID`),
  KEY `Deleted` (`Deleted`),
  KEY `Active` (`Active`),
  KEY `Position` (`Position`)
) ENGINE=MyISAM;


/*
NutsPageAccess
*/
CREATE TABLE `NutsPageAccess` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `NutsGroupID` int(10) unsigned NOT NULL default '0',
  `NutsPageID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ID`,`NutsGroupID`,`NutsPageID`)
) ENGINE=MyISAM;


/*
NutsPageComment
*/
CREATE TABLE `NutsPageComment` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `NutsPageID` int(10) unsigned NOT NULL,
  `NutsNewsID` int(10) unsigned NOT NULL,
  `NutsUserID` int(10) unsigned NOT NULL,
  `Name` varchar(150) default NULL,
  `Email` varchar(200) default NULL,
  `Website` varchar(255) default NULL,
  `Message` varchar(255) default NULL,
  `Suscribe` enum('YES','NO') NOT NULL default 'NO',
  `Deleted` enum('YES','NO') NOT NULL default 'NO',
  PRIMARY KEY  (`ID`,`NutsPageID`,`NutsNewsID`),
  KEY `Suscribe` (`Suscribe`),
  KEY `Deleted` (`Deleted`),
  KEY `NutsUserID` (`NutsUserID`)
) ENGINE=MyISAM;


/*
NutsPattern
*/
CREATE TABLE `NutsPattern` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(255) default NULL,
  `Description` text,
  `Type` enum('PHP','HTML','REGEX') NOT NULL default 'PHP',
  `Pattern` text,
  `Code` text,
  `Deleted` enum('YES','NO') NOT NULL default 'NO',
  PRIMARY KEY  (`ID`),
  KEY `Deleted` (`Deleted`)
) ENGINE=MyISAM;


/*
NutsRegion
*/
CREATE TABLE `NutsRegion` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(255) character set latin1 NOT NULL,
  `Description` text,
  `PhpCode` text,
  `Query` text character set latin1,
  `Html` text character set latin1,
  `HtmlNoRecord` text character set latin1,
  `Caption` text,
  `Result` int(11) unsigned NOT NULL,
  `HookData` text character set latin1,
  `PreviousNextVisible` enum('YES','NO') character set latin1 NOT NULL default 'YES',
  `Pager` enum('YES','NO') character set latin1 default 'YES',
  `Deleted` enum('YES','NO') character set latin1 default 'NO',
  PRIMARY KEY  (`ID`),
  KEY `Deleted` (`Deleted`)
) ENGINE=MyISAM;



/* update v.0.85 */
ALTER TABLE `NutsMenu` CHANGE COLUMN `Categorie` `Category` INT(11) NULL ;
ALTER TABLE `NutsMenu` DROP INDEX `Group`;
ALTER TABLE `NutsMenu` ADD INDEX `Group` (`Category`);
ALTER TABLE `NutsMenu` ADD COLUMN `Visible` ENUM('YES','NO') NOT NULL DEFAULT 'YES' AFTER `Position`;




/* update v.0.86 */
CREATE TABLE `NutsEmail`(`ID` INT UNSIGNED NOT NULL AUTO_INCREMENT, `Language` VARCHAR(5) NULL, `Expeditor` VARCHAR(255) NULL, `Subject` VARCHAR(255) NULL, `Body` TEXT NULL, `Deleted` ENUM('YES','NO') NOT NULL DEFAULT 'NO', PRIMARY KEY (`ID`), INDEX `Language` (`Language`), INDEX `Deleted` (`Deleted`)) TYPE= MYISAM;
INSERT INTO NutsMenu (Category, Name, Position) VALUES (1, '_email', 10);


/* update v.0.87 */
ALTER TABLE `NutsRegion` ADD COLUMN `HtmlBefore` TEXT NULL AFTER `Query` ;
ALTER TABLE `NutsRegion` ADD COLUMN `HtmlAfter` TEXT NULL AFTER `Html` ;
ALTER TABLE `NutsNews` CHANGE COLUMN `DateGMT` `DateGMT` DATE NOT NULL ;
ALTER TABLE `NutsNews` CHANGE COLUMN `DateGMTExpiration` `DateGMTExpiration` DATE NULL ;
ALTER TABLE `NutsEmail` ADD COLUMN `GroupName` VARCHAR(255) NULL AFTER Language;
ALTER TABLE `NutsEmail` ADD COLUMN `Description` TEXT NULL  AFTER GroupName;
ALTER TABLE `NutsEmail` ADD INDEX `GroupName` (`GroupName`);
CREATE TABLE `NutsMedia` (`ID` int(10) unsigned NOT NULL auto_increment,`Type` enum('AUDIO','VIDEO') NOT NULL default 'AUDIO',`Name` varchar(255) default NULL,`Description` text,`Url` text,`Parameters` text,`Deleted` enum('YES','NO') NOT NULL default 'NO', PRIMARY KEY  (`ID`), KEY `Type` (`Type`),KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `NutsRegion`  ADD COLUMN `PagerPreviousText` VARCHAR(255) NULL AFTER `Pager`, ADD COLUMN `PagerNextText` VARCHAR(255) NULL AFTER `PagerPreviousText`;
UPDATE NutsMenu SET Category = 2, Position = 5 WHERE  Name = '_media';

/* update v.0.88 */
CREATE TABLE `NutsI18n` (`ID` int(10) unsigned NOT NULL AUTO_INCREMENT, `Pattern` text, `Replacement` text, `Deleted` enum('YES','NO') NOT NULL DEFAULT 'NO', PRIMARY KEY (`ID`), KEY `Deleted` (`Deleted`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO NutsMenu (Category,Position,Name)  VALUES (2, 2, '_i18n');
ALTER TABLE `NutsMenu` ADD COLUMN `Deleted` ENUM('YES','NO') NOT NULL DEFAULT 'NO' AFTER `Visible`;
ALTER TABLE `NutsMenu` ADD INDEX `Deleted` (`Deleted`);
INSERT INTO NutsMenu (Category,Position,Name)  VALUES (4, 0, '_plugins-list');

/* update v.0.90 */
ALTER TABLE `NutsUser` CHANGE COLUMN `Password` `Password` VARCHAR(15) BINARY NOT NULL ;

/* update v.0.92 */


/* update v.0.94 */


/* update v.0.95 */
ALTER TABLE `NutsUser` CHANGE COLUMN `Password` `Password` BLOB NOT NULL;
UPDATE NutsUser SET Password = ENCODE('admin', '');
INSERT INTO NutsMenu (Category, Name, Position) VALUES (1, '_plugins', 2);

/* update v.0.96 */


/* update v.0.97 */
CREATE TABLE `NutsRteTemplate` (`ID` int(10) unsigned NOT NULL auto_increment, `Name` varchar(255) default NULL, `Description` varchar(255) default NULL, `Content` text, `Deleted` enum('YES','NO') NOT NULL default 'NO',  PRIMARY KEY  (`ID`), KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO NutsMenu (Category, Name, Position) VALUES (1, '_rte_template', 5);
ALTER TABLE `NutsMenu` ADD COLUMN `BreakBefore` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' AFTER `ExternalUrl`;
CREATE TABLE `NutsPageVersion` (`ID` int(10) unsigned NOT NULL auto_increment, `NutsUserID` int(10) unsigned NOT NULL,`NutsPageID` int(10) unsigned NOT NULL, `Date` datetime NOT NULL, `H1` varchar(255) default NULL, `ContentResume` text, `Content` text, `Deleted` enum('YES','NO') NOT NULL default 'NO', PRIMARY KEY  (`ID`,`NutsUserID`,`NutsPageID`), KEY `Date` (`Date`), KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `NutsLog` CHANGE COLUMN `IP` `IP` INT UNSIGNED NOT NULL ;
ALTER TABLE `NutsLog` ADD INDEX `IP` (`IP`);
ALTER TABLE `NutsContentType` CHANGE COLUMN `NutsPageID` `NutsPageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ;
ALTER TABLE `NutsContentType` DROP PRIMARY KEY, ADD PRIMARY KEY (`ID`);
ALTER TABLE `NutsContentType` ADD INDEX `NutsPageID` (`NutsPageID`);
ALTER TABLE `NutsGalleryImage` CHANGE COLUMN `NutsGalleryID` `NutsGalleryID` INT(10) UNSIGNED NOT NULL ;
ALTER TABLE `NutsGalleryImage` DROP PRIMARY KEY, ADD PRIMARY KEY (`ID`);
ALTER TABLE `NutsGalleryImage` ADD INDEX `NutsGalleryID` (`NutsGalleryID`);
ALTER TABLE `NutsLog` CHANGE COLUMN `NutsGroupID` `NutsGroupID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ;
ALTER TABLE `NutsLog` CHANGE COLUMN `NutsUserID` `NutsUserID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ;
ALTER TABLE `NutsLog` DROP PRIMARY KEY, ADD PRIMARY KEY (`ID`);
ALTER TABLE `NutsLog` ADD INDEX `NutsGroupID` (`NutsGroupID`);
ALTER TABLE `NutsLog` ADD INDEX `NutsUserID` (`NutsUserID`);
ALTER TABLE `NutsMenuRight` CHANGE COLUMN `NutsMenuID` `NutsMenuID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ;
ALTER TABLE `NutsMenuRight` CHANGE COLUMN `NutsGroupID` `NutsGroupID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ;
ALTER TABLE `NutsMenuRight` DROP PRIMARY KEY, ADD PRIMARY KEY (`ID`);
ALTER TABLE `NutsMenuRight` ADD INDEX `NutsMenuID` (`NutsMenuID`);
ALTER TABLE `NutsMenuRight` ADD INDEX `NutsGroupID` (`NutsGroupID`);
ALTER TABLE `NutsNews` CHANGE COLUMN `NutsUserID` `NutsUserID` INT(10) UNSIGNED NOT NULL ;
ALTER TABLE `NutsNews` DROP PRIMARY KEY, ADD PRIMARY KEY (`ID`);
ALTER TABLE `NutsNews` ADD INDEX `NutsUserID` (`NutsUserID`);
ALTER TABLE `NutsPage` CHANGE COLUMN `NutsPageID` `NutsPageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ;
ALTER TABLE `NutsPage` CHANGE COLUMN `NutsUserID` `NutsUserID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ;
ALTER TABLE `NutsPage` DROP PRIMARY KEY, ADD PRIMARY KEY (`ID`);
ALTER TABLE `NutsPage` ADD INDEX `NutsPageID` (`NutsPageID`);
ALTER TABLE `NutsPage` ADD INDEX `NutsUserID` (`NutsUserID`);
ALTER TABLE `NutsPageAccess` CHANGE COLUMN `NutsGroupID` `NutsGroupID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ;
ALTER TABLE `NutsPageAccess` CHANGE COLUMN `NutsPageID` `NutsPageID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ;
ALTER TABLE `NutsPageAccess` DROP PRIMARY KEY, ADD PRIMARY KEY (`ID`);
ALTER TABLE `NutsPageAccess` ADD INDEX `NutsGroupID` (`NutsGroupID`);
ALTER TABLE `NutsPageAccess` ADD INDEX `NutsPageID` (`NutsPageID`);
ALTER TABLE `NutsPageComment` CHANGE COLUMN `NutsPageID` `NutsPageID` INT(10) UNSIGNED NOT NULL ;
ALTER TABLE `NutsPageComment` CHANGE COLUMN `NutsNewsID` `NutsNewsID` INT(10) UNSIGNED NOT NULL ;
ALTER TABLE `NutsPageComment` DROP PRIMARY KEY, ADD PRIMARY KEY (`ID`);
ALTER TABLE `NutsPageComment` ADD INDEX `NutsPageID` (`NutsPageID`);
ALTER TABLE `NutsPageComment` ADD INDEX `NutsNewsID` (`NutsNewsID`);
ALTER TABLE `NutsPageVersion` CHANGE COLUMN `NutsUserID` `NutsUserID` INT(10) UNSIGNED NOT NULL ;
ALTER TABLE `NutsPageVersion` CHANGE COLUMN `NutsPageID` `NutsPageID` INT(10) UNSIGNED NOT NULL ;
ALTER TABLE `NutsPageVersion` DROP PRIMARY KEY, ADD PRIMARY KEY (`ID`);
ALTER TABLE `NutsPageVersion` ADD INDEX `NutsUserID` (`NutsUserID`);
ALTER TABLE `NutsPageVersion` ADD INDEX `NutsPageID` (`NutsPageID`);
ALTER TABLE `NutsUser` CHANGE COLUMN `NutsGroupID` `NutsGroupID` INT(10) UNSIGNED NOT NULL DEFAULT '0' ;
ALTER TABLE `NutsUser` DROP PRIMARY KEY, ADD PRIMARY KEY (`ID`);
ALTER TABLE `NutsUser` ADD INDEX `NutsGroupID` (`NutsGroupID`);

/* update v.0.98 */
ALTER TABLE `NutsZone` ADD COLUMN `Url` VARCHAR(255) NULL AFTER `Description` ;
ALTER TABLE `NutsPage` ADD FULLTEXT INDEX `MetaKeywords` (`MetaKeywords`);

/* update v.0.99 */
ALTER TABLE `NutsLog` CHANGE COLUMN `IP` `IP` INT(10) NOT NULL ;
ALTER TABLE `NutsGroup` ADD COLUMN `AllowUpload` ENUM('YES','NO') NOT NULL DEFAULT 'YES' AFTER `Priority` ;
ALTER TABLE `NutsGroup` ADD COLUMN `AllowEdit` ENUM('YES','NO') NOT NULL DEFAULT 'YES' AFTER `AllowUpload` ;
ALTER TABLE `NutsGroup` ADD COLUMN `AllowDelete` ENUM('YES','NO') NOT NULL DEFAULT 'YES' AFTER `AllowEdit` ;
ALTER TABLE `NutsGroup` ADD COLUMN `AllowFolders` ENUM('YES','NO') NOT NULL DEFAULT 'YES' AFTER `AllowDelete` ;
ALTER TABLE `NutsPage` ADD INDEX `DateCreation` (`DateCreation`);
ALTER TABLE `NutsPage` ADD INDEX `DateUpdate` (`DateUpdate`);
ALTER TABLE `NutsPage` ADD INDEX `Position` (`Position`);
ALTER TABLE `NutsLog` ADD INDEX `Action` (`Action`);
ALTER TABLE `NutsRegion` ADD INDEX `Name` (`Name`);
ALTER TABLE `NutsUser` ADD INDEX `Login` (`Login`);

/* update v.1.0 */
ALTER TABLE `NutsLog` CHANGE COLUMN `IP` `IP` INT(10) UNSIGNED NOT NULL ;
UPDATE NutsMenu SET ExternalUrl = 'http://www.nuts-cms.com/en/2-documentation.html' WHERE Name = '_documentation';
UPDATE NutsMenu SET BreakBefore = 1 WHERE Name = '_rte_template';
UPDATE NutsMenu SET BreakBefore = 1 WHERE Name = '_group-manager';
INSERT INTO NutsMenu (Category, Name, Position, BreakBefore) VALUES (1, '_page-versionning', 11, 1);

INSERT INTO NutsMenu (Category, Name, Position, BreakBefore) VALUES (2, '_media', 9, 1);



/* update v.1.1 */
ALTER TABLE `NutsLog` CHANGE COLUMN `IP` `IP` INT(10) NOT NULL;
ALTER TABLE `NutsMenu` CHANGE COLUMN `Category` `Category` INT(11) UNSIGNED NOT NULL DEFAULT '1' ;
INSERT INTO `NutsMenu`(Category,Name,BreakBefore,Position) VALUES('1','_file_browser', 1, 12);
ALTER TABLE `NutsZone` ADD COLUMN `Navbar` ENUM('YES','NO') NOT NULL DEFAULT 'YES' AFTER `Url`;
ALTER TABLE `NutsZone` ADD INDEX `Navbar` (`Navbar`);
ALTER TABLE `NutsPage` ADD COLUMN `CacheTime` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `_HasChildren` ;
ALTER TABLE `NutsPage` ADD COLUMN `Note` TEXT NULL AFTER `Tags` ;
ALTER TABLE `NutsPageVersion` ADD COLUMN `Note` TEXT NULL AFTER `Content` ;
CREATE TABLE `NutsForm` (  `ID` int(10) unsigned NOT NULL auto_increment,  `Language` char(4) default NULL,  `Name` varchar(255) default NULL,  `Description` varchar(255) default NULL,  `CssId` varchar(255) default NULL,  `JsCode` longtext,  `Captcha` enum('YES','NO') NOT NULL default 'NO',  `FormCustomError` longtext,  `FormValidPhpCode` longtext,  `FormValidHtmlCode` longtext,  `FormValidMailer` enum('YES','NO') NOT NULL default 'NO',  `FormValidMailerFrom` varchar(255) default NULL,  `FormValidMailerTo` varchar(255) default NULL,  `FormValidMailerSubject` varchar(255) default NULL,  `Deleted` enum('YES','NO') NOT NULL default 'NO',  `LogActionCreateNutsUserID` int(11) NOT NULL,  `LogActionCreateNutsGroupID` int(11) NOT NULL,  `LogActionCreateDateGMT` datetime NOT NULL,  PRIMARY KEY  (`ID`),  KEY `Language` (`Language`),  KEY `Deleted` (`Deleted`),  KEY `LogActionCreateNutsUserID` (`LogActionCreateNutsUserID`),  KEY `LogActionCreateNutsGroupID` (`LogActionCreateNutsGroupID`),  KEY `LogActionCreateDateGMT` (`LogActionCreateDateGMT`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `NutsFormField` (  `ID` int(10) unsigned NOT NULL auto_increment,  `NutsFormID` int(10) unsigned NOT NULL,  `Name` varchar(255) default NULL,  `Label` varchar(255) default NULL,  `Type` varchar(50) default NULL,  `Required` enum('YES','NO') NOT NULL default 'NO',  `Attributes` varchar(255) default NULL,  `Email` enum('YES','NO') NOT NULL default 'NO',  `OtherValidation` text,  `I18N` enum('YES','NO') NOT NULL default 'NO',  `Value` text,  `Position` int(10) unsigned NOT NULL,  `Deleted` enum('YES','NO') NOT NULL default 'NO',  PRIMARY KEY  (`ID`,`NutsFormID`),  KEY `Deleted` (`Deleted`),  KEY `Position` (`Position`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `NutsFormData` (`ID` int(10) unsigned NOT NULL auto_increment,`NutsFormID` int(10) unsigned NOT NULL,`Date` datetime default NULL,`Data` longtext,`Deleted` enum('YES','NO') NOT NULL default 'NO',PRIMARY KEY  (`ID`),KEY `NutsFormID` (`NutsFormID`),KEY `Date` (`Date`),KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `NutsMenu`(Category,Name,BreakBefore,Position) VALUES('2','_form-builder', 1, 10);
INSERT INTO `NutsMenu`(Category,Name,BreakBefore,Position,Visible) VALUES('2','_form-builder-fields', 0, 11, 'NO');
UPDATE NutsMenu SET Visible = 'NO' WHERE Name = '_gallery_image';

/* update v.1.2 */
ALTER TABLE `NutsBlock` ADD COLUMN `Type` ENUM('TEXT', 'HTML') NOT NULL DEFAULT 'HTML' AFTER `Name` ;

/* update v.1.3 */
ALTER TABLE `NutsForm` ADD COLUMN `Caption` TEXT NULL AFTER `Description` ;
UPDATE NutsMenu SET Category = Category + 1 WHERE Category >= 3;
CREATE TABLE `NutsNewsletterMailingList` (  `ID` int(10) unsigned NOT NULL auto_increment,  `Name` varchar(255) default NULL,  `Description` varchar(255) default NULL,  `Deleted` enum('YES','NO') NOT NULL default 'NO',  PRIMARY KEY  (`ID`),  KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `NutsNewsletterMailingList` (Name,Description) VALUES('Default','Mailing list by default');
INSERT INTO `NutsMenu`(Category,Name,BreakBefore,Position) VALUES('3','_newsletter-mailing-list', 0, 1);
CREATE TABLE `NutsNewsletterMailingListSuscriber` (  `ID` int(10) unsigned NOT NULL auto_increment,  `NutsNewsletterMailingListID` int(10) unsigned NOT NULL,  `Language` char(4) NOT NULL,  `Date` datetime NOT NULL,  `Email` varchar(255) default NULL,  `UnsuscribeNewletterID` int(10) unsigned NOT NULL default '0',  `UnsuscribeDate` datetime default NULL,  `Deleted` enum('YES','NO') NOT NULL default 'NO',  PRIMARY KEY  (`ID`,`Date`),  KEY `Language` (`Language`),  KEY `Email` (`Email`),  KEY `Deleted` (`Deleted`),  KEY `Unsuscribe` (`UnsuscribeNewletterID`),  KEY `NutsMailingListID` (`NutsNewsletterMailingListID`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `NutsMenu`(Category,Name,BreakBefore,Position) VALUES('3','_newsletter-mailing-list-suscriber', 0, 2);
CREATE TABLE `NutsNewsletter` (  `ID` int(10) unsigned NOT NULL auto_increment,  `uFrom` varchar(255) default NULL,  `Subject` varchar(255) default NULL,  `Body` longtext,  `Deleted` enum('YES','NO') NOT NULL default 'NO',  `TotalSend` bigint(20) unsigned NOT NULL,  PRIMARY KEY  (`ID`),  KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `NutsMenu`(Category,Name,BreakBefore,Position) VALUES('3','_newsletter', 0, 3);
CREATE TABLE `NutsNewsletterData` (  `NutsNewsletterMailingListSuscriberID` int(10) unsigned NOT NULL,  `NutsNewsletterID` int(10) unsigned NOT NULL,  `Date` datetime default NULL,  KEY `NutsNewsletterMailingListID` (`NutsNewsletterMailingListSuscriberID`),  KEY `NutsNewsletterID` (`NutsNewsletterID`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `NutsSurvey` (  `ID` int(10) unsigned NOT NULL auto_increment,  `Title` text,  `I18N` enum('YES','NO') NOT NULL default 'NO',  `Deleted` enum('YES','NO') NOT NULL default 'NO',  PRIMARY KEY  (`ID`),  KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `NutsSurveyOption` (  `ID` int(10) unsigned NOT NULL auto_increment,  `NutsSurveyID` int(10) unsigned NOT NULL,  `Title` varchar(255) NOT NULL,  `I18N` enum('YES','NO') NOT NULL default 'NO',  `Position` int(10) unsigned NOT NULL,  `Deleted` enum('YES','NO') NOT NULL default 'NO',  PRIMARY KEY  (`ID`),  KEY `NutsSurveyID` (`NutsSurveyID`),  KEY `Position` (`Position`),  KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `NutsSurveyData` (  `NutsSurveyID` int(10) unsigned NOT NULL,  `NutsSurveyOptionID` int(10) unsigned NOT NULL,  `IP` int(11) NOT NULL,  `Date` datetime NOT NULL,  KEY `NutsSurveyID` (`NutsSurveyID`),  KEY `IP` (`IP`),  KEY `NutsSurveyOptionID` (`NutsSurveyOptionID`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `NutsMenu`(Category,Name,BreakBefore,Position) VALUES('3','_survey', 1, 3);
INSERT INTO `NutsMenu`(Category,Name,BreakBefore,Position,Visible) VALUES('4','_survey-option', 0, 4, 'NO');

/* update v.1.4 */
UPDATE NutsMenu SET Category = 3 , Position = 5 WHERE Name = '_form-builder';
UPDATE NutsMenu SET Category = 3 , Position = 5 WHERE Name = '_form-builder-fields';
ALTER TABLE `NutsFormData` ADD COLUMN `DataSerialize` LONGTEXT NULL AFTER `Data` ;
ALTER TABLE `NutsPageComment` ADD COLUMN `Visible` ENUM('YES','NO') NOT NULL DEFAULT 'NO' AFTER `Suscribe` ;
ALTER TABLE `NutsPageComment` ADD INDEX `Visible` (`Visible`);
ALTER TABLE `NutsPageComment` CHANGE COLUMN `Message` `Message` TEXT NULL ;
ALTER TABLE `NutsPageComment` ADD COLUMN `IP` INT NULL AFTER `Deleted` ;
ALTER TABLE `NutsPage` ADD COLUMN `Sitemap` ENUM('YES','NO') NOT NULL DEFAULT 'YES'  ;
ALTER TABLE `NutsPage` ADD COLUMN `SitemapChangefreq`  ENUM('hourly', 'daily', 'weekly', 'monthly', 'always', 'never') NOT NULL DEFAULT 'weekly' ;
ALTER TABLE `NutsPage` ADD COLUMN `SitemapPriority` DECIMAL(2,1) UNSIGNED NOT NULL DEFAULT '0.5';
ALTER TABLE `NutsPage` ADD INDEX `Sitemap` (`Sitemap`);
INSERT INTO `NutsMenu`(Category,Name,BreakBefore,Position) VALUES('3','_sitemap', 1, 7);
ALTER TABLE `NutsMedia` CHANGE COLUMN `Type` `Type` ENUM('AUDIO','VIDEO','EMBED CODE') NOT NULL DEFAULT 'VIDEO' ;
ALTER TABLE `NutsMedia` ADD COLUMN `EmbedCode` TEXT NULL AFTER `Description` ;
ALTER TABLE `NutsMedia` ADD COLUMN `EmbedCodePreviewUrl` VARCHAR(255) NULL AFTER `EmbedCode` ;
DROP TABLE NutsPageComment;
CREATE TABLE `NutsPageComment` (`ID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, `NutsPageID` INT(10) UNSIGNED NOT NULL, `NutsUserID` INT(10) UNSIGNED NOT NULL, `Date` DATETIME DEFAULT NULL, `Name` VARCHAR(150) DEFAULT NULL, `Email` VARCHAR(200) DEFAULT NULL, `Website` VARCHAR(255) DEFAULT NULL, `Message` TEXT, `Suscribe` ENUM('YES','NO') NOT NULL DEFAULT 'NO', `Visible` ENUM('YES','NO') NOT NULL DEFAULT 'NO', `Deleted` ENUM('YES','NO') NOT NULL DEFAULT 'NO', `IP` INT(11) DEFAULT NULL, PRIMARY KEY  (`ID`), KEY `Suscribe` (`Suscribe`), KEY `Deleted` (`Deleted`), KEY `NutsUserID` (`NutsUserID`), KEY `NutsPageID` (`NutsPageID`), KEY `Visible` (`Visible`), KEY `Date` (`Date`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;


/* update v.1.5 */
ALTER TABLE `NutsBlock` ADD COLUMN `Preview` VARCHAR(255) NULL AFTER `Text` ;
ALTER TABLE `NutsSurvey` ADD COLUMN `ViewResult` ENUM('YES','NO') NOT NULL DEFAULT 'NO' AFTER `I18N` ;
CREATE TABLE `NutsRss` (  `ID` int(10) unsigned NOT NULL auto_increment,  `RssTitle` varchar(255) default NULL,  `RssLink` varchar(255) default NULL,  `RssDescription` varchar(255) default NULL,  `RssCopyright` varchar(255) default NULL,  `RssImage` varchar(255) default NULL,  `PhpCode` text,  `Query` text,  `HookFunction` text,  `RssLimit` smallint(5) unsigned NOT NULL,  `Deleted` enum('YES','NO') NOT NULL default 'NO',  PRIMARY KEY  (`ID`),  KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `NutsMenu`(Category,Name,BreakBefore,Position) VALUES('3','_rss', 1, 8);

/* update v.1.6 */
ALTER TABLE `NutsFormField` ADD COLUMN `PhpCode` TEXT NULL AFTER `Value` ;
CREATE TABLE `NutsIM` (  `ID` int(10) unsigned NOT NULL auto_increment,  `Date` datetime default NULL,  `DateViewed` datetime NOT NULL,  `NutsUserIDFrom` int(10) unsigned NOT NULL,  `NutsGroupID` int(10) unsigned NOT NULL,  `NutsUserID` int(10) unsigned NOT NULL,  `Subject` varchar(255) default NULL,  `Message` text,  `Viewed` enum('YES','NO') NOT NULL default 'NO',  `Deleted` enum('YES','NO') NOT NULL default 'NO',  `LogActionDeleteNutsUserID` int(11) NOT NULL,  `LogActionDeleteNutsGroupID` int(11) NOT NULL,  `LogActionDeleteDateGMT` datetime NOT NULL,  PRIMARY KEY  (`ID`),  KEY `Date` (`Date`),  KEY `NutsUserIDFrom` (`NutsUserIDFrom`),  KEY `NutsGroupID` (`NutsGroupID`),  KEY `NutsUserID` (`NutsUserID`),  KEY `Subject` (`Subject`),  KEY `Deleted` (`Deleted`),  KEY `LogActionDeleteNutsUserID` (`LogActionDeleteNutsUserID`),  KEY `LogActionDeleteNutsGroupID` (`LogActionDeleteNutsGroupID`),  KEY `LogActionDeleteDateGMT` (`LogActionDeleteDateGMT`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `NutsMenu`(Category,Name,BreakBefore,Position,Visible) VALUES(5,'_internal-messaging', 0, 6, 'NO');

/* update v.1.7 */
ALTER TABLE `NutsIM` ADD INDEX `Viewed` (`Viewed`);
INSERT INTO `NutsMenu`(Category,Name,BreakBefore,Position,Visible) VALUES(1,'_control-center', 0, 0, 'YES');
ALTER TABLE `NutsGroup` ADD COLUMN `BackofficeAccess` ENUM('YES','NO') NOT NULL DEFAULT 'YES' AFTER `Priority` ;
ALTER TABLE `NutsGroup` ADD COLUMN `FrontofficeAccess` ENUM('YES','NO') NOT NULL DEFAULT 'YES' AFTER `BackofficeAccess`;
INSERT INTO `NutsMenu`(Category,Name,Position,Visible, ExternalUrl) VALUES(5,'_analytics', 6,'YES', 'https://www.google.com/analytics/reporting/');
CREATE TABLE `NutsDropbox` (  `ID` int(10) unsigned NOT NULL auto_increment,  `Name` varchar(255) default NULL,  `Description` varchar(255) default NULL,  `GroupAllowed` varchar(255) NOT NULL,  `File` varchar(255) default NULL,  `Deleted` enum('YES','NO') NOT NULL default 'NO',  PRIMARY KEY  (`ID`),  KEY `GroupAllowed` (`GroupAllowed`),  KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `NutsMenu`(Category,Name,Position,Visible) VALUES(5,'_dropbox', 7,'YES');
DROP TABLE `NutsPageAccess`;
CREATE TABLE `NutsPageAccess` (  `NutsGroupID` int(10) unsigned NOT NULL default '0',  `NutsPageID` int(10) unsigned NOT NULL default '0',  KEY `NutsGroupID` (`NutsGroupID`),  KEY `NutsPageID` (`NutsPageID`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* update v.1.8 */
ALTER TABLE `NutsFormField` ADD COLUMN `FilePath` varchar(255) NOT NULL after `PhpCode`,  add column `FileAllowedExtensions` varchar(255) NOT NULL after `FilePath`,  add column `FileAllowedMimes` varchar(255) NOT NULL after `FileAllowedExtensions`,  add column `FileMaxSize` varchar(255) NOT NULL after `FileAllowedMimes`;
ALTER TABLE `NutsFormField`  ADD COLUMN `TextAfter` varchar(255) NOT NULL AFTER `FileMaxSize`;
ALTER TABLE NutsBlock ADD COLUMN `SubGroupName` VARCHAR(255) NOT NULL AFTER `GroupName`;
ALTER TABLE NutsBlock ADD INDEX `SubGroupName` (`SubGroupName`);
ALTER TABLE `NutsNews` ADD COLUMN `NewsImage` VARCHAR(255) NULL  AFTER `ID`;
CREATE TABLE `NutsSpider`(    `ID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,    `Title` VARCHAR(255) ,    `Text` LONGTEXT ,    `Url` VARCHAR(255) ,    PRIMARY KEY (`ID`) );
ALTER TABLE `NutsSpider` ADD FULLTEXT `Text` (`Title`, `Text`);
INSERT INTO `NutsMenu`(Category,Name,Position, BreakBefore) VALUES(3, '_search-engine', 9, 1);

/* update v.1.85 */
ALTER TABLE `NutsSurveyData` CHANGE COLUMN `IP` `IP` BIGINT(11) NOT NULL ;
ALTER TABLE `NutsLog` CHANGE COLUMN `IP` `IP` BIGINT(11) NOT NULL ;
ALTER TABLE `NutsUser` ADD COLUMN `Company` VARCHAR(255) NULL AFTER `Active`,  ADD COLUMN `Address` VARCHAR(255) NULL AFTER `Company`,  ADD COLUMN `Address2` VARCHAR(255) NULL AFTER `Address`,  ADD COLUMN `ZipCode` VARCHAR(255) NULL AFTER `Address2`,  ADD COLUMN `City` VARCHAR(255) NULL AFTER `ZipCode`,  ADD COLUMN `Country` VARCHAR(255) NULL AFTER `City`,  ADD COLUMN `Phone` VARCHAR(50) NULL AFTER `Country`,  ADD COLUMN `Gsm` VARCHAR(50) NULL AFTER `Phone`,  ADD COLUMN `Fax` VARCHAR(50) NULL AFTER `Gsm`;
ALTER TABLE `NutsSpider` ADD COLUMN `Language` VARCHAR(3) NULL AFTER `Url` ;
ALTER TABLE `NutsSpider` ADD INDEX `Language` (`Language`);
ALTER TABLE `NutsDropbox` ADD COLUMN `Category` VARCHAR(255) NULL AFTER `ID` ;
ALTER TABLE `NutsDropbox` ADD INDEX `Category` (`Category`);
ALTER TABLE `NutsDropbox` CHANGE COLUMN `File` `XFile` VARCHAR(255) NULL ;

/* update v.1.9 */
ALTER TABLE `NutsDropbox` ADD COLUMN `Locked` ENUM('YES','NO') NOT NULL DEFAULT 'NO' AFTER `XFile` ;
ALTER TABLE `NutsDropbox` ADD COLUMN `LockedUserID` INT UNSIGNED NOT NULL AFTER `Locked` ;
ALTER TABLE `NutsGallery` ADD COLUMN `GenerateJS` ENUM('YES','NO') NOT NULL DEFAULT 'NO' AFTER `LogoImage` ;

/* update v.1.92 */
ALTER TABLE `NutsForm` ADD COLUMN `Information` TEXT NULL AFTER `Captcha`;

/* update v.1.94 */
ALTER TABLE `NutsUser` ADD COLUMN `Gender` CHAR(5) NULL AFTER `NutsGroupID` ;
ALTER TABLE `NutsUser` ADD COLUMN `Note` TEXT NULL AFTER `Fax`;
ALTER TABLE `NutsFormField` ADD COLUMN `HtmlCode` TEXT NULL AFTER `FileMaxSize` ;

/* update v.1.96 */
ALTER TABLE `NutsUser` ADD COLUMN `Job` VARCHAR(255) NULL AFTER `Fax`;
ALTER TABLE `NutsUser` ADD COLUMN `NTVA` VARCHAR(100) NULL AFTER `Company` ;

/* update v.1.98 */
ALTER TABLE `NutsForm` ADD COLUMN `FormBeforePhp` LONGTEXT NULL AFTER `Information` ;
ALTER TABLE `NutsForm` ADD COLUMN `FormStockData` ENUM('YES','NO') NOT NULL DEFAULT 'YES' AFTER `FormValidHtmlCode`;
INSERT INTO NutsMenu (Category, Name, Position) VALUE(2, '_template_styles', 1);
UPDATE NutsMenu SET BreakAfter = 1 WHERE Name = '_template_settings';

/* update v.2.0 */
CREATE TABLE `NutsRichEditor`( `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT, `Content` TEXT NULL, `Deleted` ENUM('YES','NO') NOT NULL DEFAULT 'NO', PRIMARY KEY (`ID`), INDEX `Deleted` (`Deleted`))TYPE= MYISAM;
INSERT INTO NutsMenu (Category, Name, Position) VALUE(1, '_rte_custom', 1);
INSERT INTO NutsRichEditor (`ID`,`Content`,`Deleted`) VALUES ( '1','forced_root_block : \'\',\r\nforce_br_newlines : false,\r\nforce_p_newlines : false,    \r\nremove_linebreaks: true,\r\napply_source_formatting: false,\r\nconvert_newlines_to_brs : false,','NO');
CREATE TABLE `NutsUrlRewriting`( `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT, `Pattern` VARCHAR(255) NULL, `Replacement` VARCHAR(255) NULL, `Deleted` ENUM('YES','NO') NOT NULL DEFAULT 'NO', PRIMARY KEY (`ID`), INDEX `Deleted` (`Deleted`))TYPE= MYISAM;
INSERT INTO NutsMenu (Category, Name, Position) VALUE(2, '_url_rewriting', 2);
ALTER TABLE `NutsUrlRewriting` ADD COLUMN `Position` INT UNSIGNED NOT NULL AFTER `Replacement`;
ALTER TABLE `NutsUrlRewriting` ADD INDEX `Position` (`Position`);
ALTER TABLE `NutsUrlRewriting` ADD COLUMN `Type` ENUM('SIMPLE','REGEX') NOT NULL DEFAULT 'SIMPLE'  AFTER `ID`;

/* update v.2.1 */
CREATE TABLE `NutsPressKit`( `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT, `Date` DATE NOT NULL, `Title` VARCHAR(255) NULL, `Source` VARCHAR(255) NULL, `Deleted` ENUM('YES','NO') NOT NULL DEFAULT 'NO', PRIMARY KEY (`ID`), INDEX `Date` (`Date`), INDEX `Deleted` (`Deleted`))TYPE= MYISAM;
ALTER TABLE `NutsPressKit` ADD COLUMN `File` VARCHAR(255) NULL AFTER `Source` ;
INSERT INTO NutsMenu (Category, Name, Position) VALUE(3, '_press-kit', 10);
ALTER TABLE `NutsUser` ADD COLUMN `FrontOfficeToolbar` ENUM('YES','NO') NOT NULL DEFAULT 'YES';
CREATE TABLE `NutsIMemo`( `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT, `NutsUserID` INT UNSIGNED NOT NULL, `Text` LONGTEXT NULL, `Deleted` ENUM('YES','NO') NOT NULL DEFAULT 'NO', PRIMARY KEY (`ID`), INDEX `NutsUserID` (`NutsUserID`), INDEX `Deleted` (`Deleted`))TYPE= MYISAM;
ALTER TABLE `NutsNews` ADD COLUMN `NewsImageModel` VARCHAR(255) NULL AFTER `NewsImage` ;
UPDATE NutsMenu SET Visible = 'NO' WHERE Name = '_internal-memo' ; 



/* update v.2.11 */
UPDATE NutsMenu SET Visible = 'NO' WHERE Name = '_internal-memo';
ALTER TABLE `NutsPage` ADD COLUMN `SitemapPageType` ENUM('NORMAL','TUNNEL') NOT NULL DEFAULT 'NORMAL' AFTER `SitemapChangefreq`;
ALTER TABLE `NutsPage` ADD COLUMN `SitemapUrlRegex1` VARCHAR(255) NULL AFTER `SitemapPageType`;
ALTER TABLE `NutsPage` ADD COLUMN `SitemapUrlRegex2` VARCHAR(255) NULL AFTER `SitemapUrlRegex1`;
ALTER TABLE `NutsPage` ADD INDEX `SitemapPageType` (`SitemapPageType`);

/* update v.2.5 */
ALTER TABLE `NutsPage` ADD COLUMN `MetaRobots` VARCHAR(20) NULL AFTER `MetaKeywords` ;
ALTER TABLE `NutsPage` ADD COLUMN `DateStartOption` ENUM('YES','NO') NOT NULL DEFAULT 'NO' AFTER `SitemapUrlRegex2` ;
ALTER TABLE `NutsPage` ADD COLUMN `DateStart` DATETIME NULL AFTER `DateStartOption` ;
ALTER TABLE `NutsPage` ADD COLUMN `DateEndOption` ENUM('YES','NO') NOT NULL DEFAULT 'NO' AFTER `DateStart` ;
ALTER TABLE `NutsPage` ADD COLUMN `DateEnd` DATETIME NULL AFTER `DateEndOption` ;
ALTER TABLE `NutsPage` ADD INDEX `DateStartOption` (`DateStartOption`);
ALTER TABLE `NutsPage` ADD INDEX `DateStart` (`DateStart`);
ALTER TABLE `NutsPage` ADD INDEX `DateEnd` (`DateEnd`);
ALTER TABLE `NutsPage` ADD INDEX `DateEndOption` (`DateEndOption`);
CREATE TABLE `NutsTreatmentPercent` (  `NutsUserID` int(10) unsigned NOT NULL,  `Plugin` varchar(255) default NULL,  `Start` int(10) unsigned NOT NULL,  `End` int(10) unsigned NOT NULL,  `RecordID` int(10) unsigned NOT NULL,  `POST` text,  PRIMARY KEY  (`NutsUserID`),  KEY `Plugin` (`Plugin`),  KEY `RecordID` (`RecordID`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO NutsMenu (Category, Name, Position) VALUE(2, '_register', 55);
ALTER TABLE `NutsPage` CHANGE COLUMN `DateEndOption` `DateEndOption` ENUM('YES','NO') NOT NULL DEFAULT 'NO' ;

/* update v.2.65 */


/* update v.2.7 */
UPDATE NutsMenu SET Category = 3, Position = 4 WHERE Name = '_survey-option';
INSERT INTO NutsMenu (Category, Name, ExternalUrl, Position, Visible) VALUES (5, '_nuts-forge', 'http://www.nuts-cms.com/tools/forge/', 6, 'YES');

/* update v.2.8 */
ALTER TABLE `NutsUser` ADD COLUMN `Address3` VARCHAR(255) NULL AFTER `Address2` ;
UPDATE NutsMenu SET Category = 1, Position = 0 WHERE  Name = '_internal-memo' OR Name = '_internal-messaging';

/* update v.2.9 */
UPDATE NutsMenu SET Category = 3, Position = 4 WHERE Name = '_survey-option';
UPDATE NutsMenu SET Category = 1, Position = 0 WHERE Name IN('_internal-memo', '_internal-messaging');
INSERT INTO NutsMenu (Category, Name, Position) VALUES (1, '_file_explorer_mimes_type', 4);
CREATE TABLE `NutsFileExplorerMimesType` (`ID` int(10) unsigned NOT NULL AUTO_INCREMENT, `Extension` char(5) DEFAULT NULL, `Mimes` text, `Deleted` enum('YES','NO') NOT NULL DEFAULT 'NO', PRIMARY KEY (`ID`), KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `NutsFileExplorerMimesType`(`ID`,`Extension`,`Mimes`,`Deleted`) values (1,'GIF','image/gif','NO'),(2,'JPG','image/jpeg\nimage/pjpeg','NO'),(3,'PNG','image/png','NO'),(4,'MP3','audio/mpeg\naudio/mpeg3\naudio/x-mpeg-3\nvideo/mpeg\nvideo/x-mpeg','NO'),(5,'MP4','audio/mp4','NO'),(6,'SWF','application/x-shockwave-flash','NO'),(7,'FLV','video/x-flv\nvideo/flv','NO'),(8,'PDF','application/pdf\napplication/x-pdf\napplication/octet-stream','NO'),(9,'ZIP','application/x-compressed\napplication/zip\napplication/octet-stream\napplication/x-zip-compressed','NO'),(10,'DOC','application/msword','NO'),(11,'DOCX','application/msword\napplication/vnd.openxmlformats-officedocument.wordprocessingml.document','NO'),(12,'XLS','application/x-excel\napplication/vnd.ms-excel','NO'),(13,'XLSX','application/x-excel\napplication/vnd.openxmlformats-officedocument.spreadsheetml.sheet','NO'),(14,'PPT','application/powerpoint\napplication/mspowerpoint\napplication/powerpoint\napplication/vnd.ms-powerpoint\napplication/x-mspowerpoint\napplication/mspowerpoint','NO'),(15,'PPTX','application/powerpoint\napplication/mspowerpoint\napplication/powerpoint\napplication/vnd.ms-powerpoint\napplication/x-mspowerpoint\napplication/mspowerpoint\napplication/vnd.openxmlformats-officedocument.presentationml.presentation','NO');



/* update v.3.0 */


/* update v.3.1 */
CREATE TABLE `NutsUserListSearches`(
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `NutsUserID` int(10) unsigned NOT NULL,
  `Plugin` varchar(255) NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Serialized` text,
  `Deleted` enum('YES','NO') NOT NULL DEFAULT 'NO', 
  PRIMARY KEY (`ID`),
  KEY `NutsUserID` (`NutsUserID`),
  KEY `Plugin` (`Plugin`),
  KEY `Deleted` (`Deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


/* update v.3.2 */
INSERT INTO NutsMenu (Category, Name, Position) VALUES (1, '_settings', 0);
ALTER TABLE `NutsUser` ADD COLUMN `Avatar` VARCHAR(255) NULL ;
ALTER TABLE `NutsUser` ADD COLUMN `AvatarTmpImage` VARCHAR(255) NULL AFTER `Avatar` ;

/* update v.3.3 */
ALTER TABLE `NutsFileExplorerMimesType` ADD COLUMN `FileExplorer` ENUM('YES','NO') NOT NULL AFTER `Mimes` ;
ALTER TABLE `NutsFileExplorerMimesType` ADD COLUMN `EDM` ENUM('YES','NO') NOT NULL DEFAULT 'YES' AFTER `FileExplorer` ;
CREATE TABLE `NutsEDMGroup`(
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `Description` text,
  `Deleted` enum('YES','NO') NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`ID`),
  KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `NutsEDMGroupUser` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `NutsEDMGroupID` int(10) unsigned NOT NULL,
  `NutsUserID` int(10) unsigned NOT NULL,
  `Deleted` enum('YES','NO') NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`ID`),
  KEY `NutsEDMGroupID` (`NutsEDMGroupID`),
  KEY `NutsUserID` (`NutsUserID`),
  KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO NutsMenu (Category, Name, Position, BreakBefore) VALUES (3, '_edm', 4, 1);
INSERT INTO NutsMenu (Category, Name, Position, Visible) VALUES (3, '_edm-group', 4, 'NO');
INSERT INTO NutsMenu (Category, Name, Position, Visible) VALUES (3, '_edm-logs', 4, 'NO');

CREATE TABLE `NutsEDMFolderRights`(
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Type` enum('GROUP','USER') NOT NULL DEFAULT 'GROUP',
  `NutsEDMGroupID` int(10) unsigned NOT NULL,
  `NutsUserID` int(10) unsigned NOT NULL,
  `Folder` varchar(255) NOT NULL,
  `LIST` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `READ` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `MODIFY` enum('YES','NO') DEFAULT 'NO',
  `DELETE` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `WRITE` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `UPLOAD` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `Deleted` enum('YES','NO') NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`ID`),
  KEY `Type` (`Type`),
  KEY `NutsEDMGroupID` (`NutsEDMGroupID`),
  KEY `NutsUserID` (`NutsUserID`),
  KEY `Folder` (`Folder`),
  KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `NutsEDMLogs`(
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `NutsUserID` int(10) unsigned NOT NULL,
  `Date` datetime DEFAULT NULL,
  `Action` varchar(20) NOT NULL,
  `Object` enum('SYSTEM','FOLDER','FILE','ERROR') DEFAULT NULL,
  `ObjectName` varchar(255) DEFAULT NULL,
  `Resume` text,
  `Deleted` enum('YES','NO') NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`ID`),
  KEY `Action` (`Action`),
  KEY `Deleted` (`Deleted`),
  KEY `NutsUserID` (`NutsUserID`),
  KEY `Object` (`Object`),
  KEY `ObjectName` (`ObjectName`),
  KEY `Date` (`Date`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `NutsEDMComments` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `NutsUserID` int(10) unsigned NOT NULL,
  `Folder` varchar(255) DEFAULT NULL,
  `File` varchar(255) DEFAULT NULL,
  `Date` datetime DEFAULT NULL,
  `Message` text,
  `Deleted` enum('YES','NO') NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`ID`),
  KEY `NutsUserID` (`NutsUserID`),
  KEY `Date` (`Date`),
  KEY `Deleted` (`Deleted`),
  KEY `Folder` (`Folder`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `NutsEDMLock` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `NutsUserID` int(10) unsigned NOT NULL,
  `Date` datetime DEFAULT NULL,
  `Folder` varchar(255) DEFAULT NULL,
  `File` varchar(255) DEFAULT NULL,
  `Deleted` enum('YES','NO') NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`ID`),
  KEY `NutsUserID` (`NutsUserID`),
  KEY `Date` (`Date`),
  KEY `Deleted` (`Deleted`),
  KEY `Folder` (`Folder`),
  KEY `File` (`File`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* update v.3.5 */
DROP TABLE IF EXISTS NutsEDMLock;
CREATE TABLE `NutsEDMLock` (  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,  `NutsUserID` int(10) unsigned NOT NULL,  `Date` datetime DEFAULT NULL,  `Folder` varchar(255) DEFAULT NULL,  `File` varchar(255) DEFAULT NULL,  `Deleted` enum('YES','NO') NOT NULL DEFAULT 'NO',  PRIMARY KEY (`ID`),  KEY `NutsUserID` (`NutsUserID`),  KEY `Date` (`Date`),  KEY `Deleted` (`Deleted`),  KEY `Folder` (`Folder`),  KEY `File` (`File`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO NutsMenu (Category, Name, Position, Visible) VALUES (3, '_edm-locks', 4, 'NO');
ALTER TABLE `NutsUrlRewriting` ADD COLUMN `Tag` VARCHAR(255) NULL AFTER `Position` ;
ALTER TABLE `NutsUser` ADD COLUMN `Birthdate` DATE NULL AFTER `NutsGroupID` ;
ALTER TABLE `NutsUser` ADD INDEX `Birthdate` (`Birthdate`);
ALTER TABLE `NutsRegion` ADD COLUMN `PreviousStartEndVisible` ENUM('YES','NO') NOT NULL DEFAULT 'YES' AFTER `PagerNextText` ;
ALTER TABLE `NutsRegion` ADD COLUMN `PagerStartText` VARCHAR(255) NULL AFTER `PreviousStartEndVisible` ;
ALTER TABLE `NutsRegion` ADD COLUMN `PagerEndText` VARCHAR(255) NULL AFTER `PagerStartText` ;
ALTER TABLE `NutsRegion` ADD COLUMN  `SetUrl` VARCHAR(255) NULL;
CREATE TABLE `NutsUrlRedirect`( `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT, `Type` VARCHAR(255) NULL, `UrlOld` VARCHAR(255) NULL, `UrlNew` VARCHAR(255) NULL, `Position` INT UNSIGNED NOT NULL, `Deleted` ENUM('YES','NO') NOT NULL DEFAULT 'NO', PRIMARY KEY (`ID`), INDEX `Position` (`Position`), INDEX `Deleted` (`Deleted`))ENGINE= MYISAM DEFAULT CHARSET=utf8;
INSERT INTO NutsMenu (Category, Name, Position, Visible) VALUES (2, '_url_redirect', 13, 'YES');
ALTER TABLE `NutsNews` ADD INDEX `VirtualPageName` (`VirtualPageName`);
CREATE TABLE `NutsTrigger`( `ID` INT UNSIGNED NOT NULL AUTO_INCREMENT, `Name` VARCHAR(255) NULL, `Description` VARCHAR(255) NULL, `PhpCode` LONGTEXT NULL, `Deleted` ENUM('YES','NO') NOT NULL DEFAULT 'NO', PRIMARY KEY (`ID`), INDEX `Deleted` (`Deleted`))ENGINE= MYISAM DEFAULT CHARSET=utf8;
ALTER TABLE `NutsTrigger` ADD INDEX `Name` (`Name`);
INSERT INTO NutsMenu (Category, Name, Position, Visible) VALUES (1, '_trigger', 13, 'YES');
CREATE TABLE `NutsService` (  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,  `Name` varchar(255) DEFAULT NULL,  `Description` varchar(255) DEFAULT NULL,  `Token` char(64) DEFAULT NULL,  `Query` text,  `HookData` longtext,  `Output` varchar(20) DEFAULT NULL,  `Deleted` enum('YES','NO') NOT NULL DEFAULT 'NO',  PRIMARY KEY (`ID`),  KEY `Name` (`Name`),  KEY `Output` (`Output`),  KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `NutsService` CHANGE COLUMN `Output` `Output` VARCHAR(13) NULL;
INSERT INTO NutsMenu (Category, Name, Position, Visible) VALUES (3, '_services', 14, 'YES');
CREATE TABLE `NutsMenuCategory` (  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,  `Name` varchar(255) DEFAULT NULL,  `NameFr` varchar(255) DEFAULT NULL,  `Color` varchar(10) DEFAULT NULL,  `Position` int(10) unsigned NOT NULL,  `Deleted` enum('YES','NO') NOT NULL DEFAULT 'NO',  PRIMARY KEY (`ID`),  KEY `Position` (`Position`),  KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO NutsMenu (Category, Name, Position, Visible) VALUES (1, '_menu-category', 3, 'YES');
UPDATE NutsMenu SET BreakAfter = 1 WHERE Name = '_right-manager';
UPDATE NutsMenu SET BreakAfter = 0 WHERE Name = '_form-builder';
UPDATE NutsMenu SET Position = 2 WHERE Name = '_menu-category';
CREATE TABLE `NutsPageContentView` (  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,  `Name` varchar(255) DEFAULT NULL,  `Description` varchar(255) DEFAULT NULL,  `Html` text,  `HookData` text,  `Deleted` enum('YES','NO') NOT NULL DEFAULT 'NO',  PRIMARY KEY (`ID`),  KEY `Name` (`Name`),  KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `NutsPageContentViewField` (  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,  `NutsPageContentViewID` int(10) unsigned NOT NULL,  `Name` varchar(255) DEFAULT NULL,  `Label` varchar(255) DEFAULT NULL,  `Type` varchar(20) DEFAULT NULL,  `CssStyle` varchar(255) DEFAULT NULL,  `Value` varchar(255) DEFAULT NULL,  `Help` varchar(255) DEFAULT NULL,  `Position` int(10) unsigned NOT NULL,  `Deleted` enum('YES','NO') NOT NULL DEFAULT 'NO',  PRIMARY KEY (`ID`),  KEY `NutsPageContentViewID` (`NutsPageContentViewID`),  KEY `Position` (`Position`),  KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `NutsPageContentViewField` ADD COLUMN `SpecialOption` TEXT NULL AFTER `Type` ;
ALTER TABLE `NutsPageContentViewField` CHANGE COLUMN `SpecialOption` `SpecialOption` TEXT NULL ;
ALTER TABLE `NutsPageContentViewField` ADD COLUMN `TextAfter` VARCHAR(255) NULL AFTER `Help` ;
CREATE TABLE `NutsPageContentViewFieldData` (  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,  `NutsPageContentViewID` int(10) unsigned NOT NULL,  `NutsPageContentViewFieldID` int(10) unsigned NOT NULL,  `NutsPageID` int(10) unsigned NOT NULL,  `Value` longtext,  `Deleted` enum('YES','NO') NOT NULL DEFAULT 'NO',  PRIMARY KEY (`ID`),  KEY `NutsPageContentViewID` (`NutsPageContentViewID`),  KEY `NutsPageID` (`NutsPageID`),  KEY `NutsPageContentViewFieldID` (`NutsPageContentViewFieldID`),  KEY `Deleted` (`Deleted`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO NutsMenu (Category, Name, Position, Visible) VALUES (2, '_page-content-view', 6, 'YES');
INSERT INTO NutsMenu (Category, Name, Position, Visible) VALUES (2, '_page-content-view-fields', 6, 'NO');
ALTER TABLE `NutsPage` ADD COLUMN `NutsPageContentViewID` INT UNSIGNED NOT NULL AFTER `ZoneID`;
ALTER TABLE `NutsPage` ADD INDEX `NutsPageContentViewID` (`NutsPageContentViewID`);