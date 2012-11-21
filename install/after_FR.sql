UPDATE NutsUser SET `Language` = 'fr';
UPDATE NutsNews SET `Language` = 'fr', VirtualPageName = REPLACE(VirtualPageName, '/en/', '/fr/');
UPDATE NutsPage SET `Language` = 'fr';
UPDATE NutsTemplateConfiguration SET LanguageDefault = 'fr', Description = 'Theme par défaut';
UPDATE NutsUrlRewriting SET Pattern = REPLACE(Pattern, '/en/', '/fr/'), Replacement = REPLACE(Replacement, '/en/', '/fr/');

TRUNCATE TABLE NutsForm;
insert into `NutsForm` (`ID`, `Language`, `Name`, `Description`, `Caption`, `CssId`, `JsCode`, `Captcha`, `Information`, `FormBeforePhp`, `FormCustomError`, `FormValidPhpCode`, `FormValidHtmlCode`, `FormStockData`, `FormValidMailer`, `FormValidMailerFrom`, `FormValidMailerTo`, `FormValidMailerSubject`, `Deleted`) values('1','AUTO','contact_us','','Merci de remplir les champs suivants',NULL,'','NO','<br>','','','','Nous vous remercions ! Votre message a bien été envoyé.','NO','YES','','','Contact depuis votre site internet','NO');

TRUNCATE TABLE NutsFormField;
INSERT INTO `NutsFormField`(`ID`,`NutsFormID`,`Name`,`Label`,`Type`,`Required`,`Attributes`,`Email`,`OtherValidation`,`I18N`,`Value`,`PhpCode`,`FilePath`,`FileAllowedExtensions`,`FileAllowedMimes`,`FileMaxSize`,`HtmlCode`,`TextAfter`,`Position`,`Deleted`) VALUES
	(1,1,'Company','Societe','TEXT','NO','','NO','','NO','','','','','','','','',1,'NO'),
	(2,1,'Name','Votre nom','TEXT','YES','','NO','','NO','','','','','','','','',2,'NO'),
	(4,1,'Email','Votre email','TEXT','YES','','YES','','NO','','','','','','','','',3,'NO'),
	(5,1,'Message','Votre message','TEXTAREA','YES','','NO','','NO','','','','','','','','',4,'NO');
