UPDATE NutsUser SET `Language` = 'fr';
UPDATE NutsNews SET `Language` = 'fr', VirtualPageName = REPLACE(VirtualPageName, '/en/', '/fr/');
UPDATE NutsPage SET `Language` = 'fr', VirtualPagename = REPLACE(VirtualPagename, '/en/', '/fr/');
UPDATE NutsTemplateConfiguration SET LanguageDefault = 'fr', Description = 'Theme par defaut';
UPDATE NutsUrlRewriting SET Pattern = REPLACE(Pattern, '/en/', '/fr/'), Replacement = REPLACE(Replacement, '/en/', '/fr/');

TRUNCATE TABLE NutsForm;
insert into `NutsForm` (`ID`, `Language`, `Name`, `Description`, `Caption`, `CssId`, `JsCode`, `Captcha`, `Information`, `FormBeforePhp`, `FormCustomError`, `FormValidPhpCode`, `FormValidHtmlCode`, `FormStockData`, `FormValidMailer`, `FormValidMailerFrom`, `FormValidMailerTo`, `FormValidMailerSubject`, `Deleted`) values('1','AUTO','contact_us','','Merci de remplir les champs suivants',NULL,'','NO','<br>','','','','Nous vous remercions ! Nous vous rappelerons rapidement','NO','YES','','','Contact depuis votre site internet','NO');

TRUNCATE TABLE NutsFormField;
INSERT INTO `NutsFormField`(`ID`,`NutsFormID`,`Name`,`Label`,`Type`,`Required`,`Attributes`,`Email`,`OtherValidation`,`I18N`,`Value`,`PhpCode`,`FilePath`,`FileAllowedExtensions`,`FileAllowedMimes`,`FileMaxSize`,`HtmlCode`,`TextAfter`,`Position`,`Deleted`) VALUES
	(1,1,'Company','Societe','TEXT','NO','','NO','','NO','','','','','','','','',1,'NO'),
	(2,1,'Name','Votre nom','TEXT','YES','','NO','','NO','','','','','','','','',2,'NO'),
	(4,1,'Email','Votre email','TEXT','YES','','YES','','NO','','','','','','','','',3,'NO'),
	(5,1,'Message','Votre message','TEXTAREA','YES','','NO','','NO','','','','','','','','',4,'NO');



UPDATE NutsPage SET `H1` = 'A propos', VirtualPagename='a-propos', MenuName='A propos'  WHERE ID = 1;
UPDATE NutsPage SET `H1` = 'Galerie', VirtualPagename='galerie', MenuName='Galerie'  WHERE ID = 3;
UPDATE NutsPage SET `H1` = 'Contactez nous', VirtualPagename='contactez-nous', MenuName='Contactez nous'  WHERE ID = 4;
UPDATE NutsPage SET `H1` = 'Nos produits', VirtualPagename='nos-produits', MenuName='Nos produits'  WHERE ID = 5;
UPDATE NutsPage SET `H1` = 'Rechercher', VirtualPagename='rechercher', MenuName='Rechercher'  WHERE ID = 17;
UPDATE NutsPage SET `H1` = 'Actualites', MenuName='Actualites'  WHERE ID = 14;
UPDATE NutsPage SET `H1` = 'Produit 1', VirtualPagename='produit-1', MenuName='Produit 1'  WHERE ID = 6;
UPDATE NutsPage SET `H1` = 'Produit 2', VirtualPagename='produit-2', MenuName='Produit 2'  WHERE ID = 147;
UPDATE NutsPage SET `H1` = 'Produit 3', VirtualPagename='produit-3', MenuName='Produit 3'  WHERE ID = 156;

UPDATE NutsPage SET `H1` = 'M\'enregister', MenuName='M\'enregister' WHERE ID = 11;
UPDATE NutsPage SET `H1` = 'Bienvenue', MenuName='Bienvenue' WHERE ID = 12;
UPDATE NutsPage SET `H1` = 'Mon profil', MenuName='Mon profil' WHERE ID = 145;

UPDATE NutsSurvey SET Title = 'Utilisez-vous une CMS ?' WHERE ID = 1;
UPDATE NutsSurveyOption SET Title = 'Oui' WHERE ID = 1;
UPDATE NutsSurveyOption SET Title = 'Non' WHERE ID = 2;

UPDATE NutsPageContentView SET Html = REPLACE(Html, 'Price :', 'Prix :') WHERE ID = 1;
UPDATE NutsPageContentView SET Html = REPLACE(Html, 'Download available :', 'Telechargement possible :') WHERE ID = 1;
UPDATE NutsPageContentView SET Html = REPLACE(Html, 'Box color :', 'Couleur :') WHERE ID = 1;




