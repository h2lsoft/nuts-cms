/* Simulate Nuts user */
ALTER TABLE `NutsUser` ADD COLUMN `LogActionCreateNutsUserID` INT(11) NOT NULL;
ALTER TABLE `NutsUser` ADD COLUMN `LogActionCreateNutsGroupID` INT(11) NOT NULL;
ALTER TABLE `NutsUser` ADD COLUMN `LogActionCreateDateGMT` DATETIME NOT NULL;


/* Nuts Group */
INSERT INTO `NutsGroup`(`ID`,`Name`,`Description`,`TinyMceConfig`,`Priority`,`BackofficeAccess`,`FrontofficeAccess`,`AllowUpload`,`AllowEdit`,`AllowDelete`,`AllowFolders`,`Deleted`) VALUES
	(2,'Admin','Admin group','',2,'YES','YES','YES','YES','YES','YES','NO'),
	(3,'Visitor','Visitor group','',3,'NO','YES','YES','YES','YES','YES','NO');

/* Nuts Zone */
TRUNCATE TABLE NutsZone;
insert  into `NutsZone`(`ID`,`Type`,`Name`,`CssName`,`Description`,`Url`,`Navbar`,`Visible`,`Deleted`) VALUES
	(1,'MTYPEENU','Account','account','User account zone','','NO','YES','NO'),
	(3,'MENU','Footer','footer','Footer zone','','NO','YES','NO');

/* Nuts Page */
TRUNCATE TABLE NutsPage;
insert  into `NutsPage`(`ID`,`NutsPageID`,`NutsUserID`,`ContentType`,`ZoneID`,`HeaderImage`,`Language`,`DateCreation`,`DateUpdate`,`Template`,`MetaTitle`,`MetaDescription`,`MetaKeywords`,`MetaRobots`,`H1`,`VirtualPagename`,`MenuName`,`ContentResume`,`Content`,`State`,`MenuVisible`,`TopBar`,`BottomBar`,`Comments`,`AccessRestricted`,`Position`,`_HasChildren`,`CacheTime`,`Deleted`,`Event`,`CustomVars`,`CustomBlock`,`Tags`,`Note`,`Sitemap`,`SitemapChangefreq`,`SitemapPageType`,`SitemapUrlRegex1`,`SitemapUrlRegex2`,`DateStartOption`,`DateStart`,`DateEndOption`,`DateEnd`,`SitemapPriority`,`X_Test2`,`X_Test3`,`X_Test`) values (1,0,1,'TEXT',0,'','en','2011-06-29 22:28:37','2012-05-10 15:00:31','','About us','','','','About us','about-us','About us','','<p><img class=\"left_border\" src=\"/library/media/images/user/business.jpg\" height=\"150\" border=\"0\" width=\"209\"></p><p>\"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\"</p><p>\"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur ?\"</p><p><cite lang=\"en\">But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure ?</cite></p><p>\"But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it ?</p>','PUBLISHED','YES','YES','YES','NO','NO',2,'NO',0,'NO','YES','','a:1:{s:5:\"Right\";a:3:{i:0;s:1:\"3\";i:1;s:1:\"1\";i:2;s:1:\"2\";}}','','','YES','weekly','NORMAL','','','NO','0000-00-00 00:00:00','NO','0000-00-00 00:00:00','0.5',NULL,NULL,NULL);
insert  into `NutsPage`(`ID`,`NutsPageID`,`NutsUserID`,`ContentType`,`ZoneID`,`HeaderImage`,`Language`,`DateCreation`,`DateUpdate`,`Template`,`MetaTitle`,`MetaDescription`,`MetaKeywords`,`MetaRobots`,`H1`,`VirtualPagename`,`MenuName`,`ContentResume`,`Content`,`State`,`MenuVisible`,`TopBar`,`BottomBar`,`Comments`,`AccessRestricted`,`Position`,`_HasChildren`,`CacheTime`,`Deleted`,`Event`,`CustomVars`,`CustomBlock`,`Tags`,`Note`,`Sitemap`,`SitemapChangefreq`,`SitemapPageType`,`SitemapUrlRegex1`,`SitemapUrlRegex2`,`DateStartOption`,`DateStart`,`DateEndOption`,`DateEnd`,`SitemapPriority`,`X_Test2`,`X_Test3`,`X_Test`) values (3,0,1,'TEXT',0,'','en','2011-06-30 00:32:15','2011-07-04 01:22:01','full-page.html','Gallery','','','','Gallery','gallery','Gallery','','\n{@NUTS	TYPE=\'GALLERY\'	NAME=\'Gallery test\'}\n','PUBLISHED','YES','YES','YES','YES','NO',5,'NO',0,'NO','YES','','a:0:{}','','','YES','weekly','NORMAL','','','NO','0000-00-00 00:00:00','NO','0000-00-00 00:00:00','0.5',NULL,NULL,NULL);
insert  into `NutsPage`(`ID`,`NutsPageID`,`NutsUserID`,`ContentType`,`ZoneID`,`HeaderImage`,`Language`,`DateCreation`,`DateUpdate`,`Template`,`MetaTitle`,`MetaDescription`,`MetaKeywords`,`MetaRobots`,`H1`,`VirtualPagename`,`MenuName`,`ContentResume`,`Content`,`State`,`MenuVisible`,`TopBar`,`BottomBar`,`Comments`,`AccessRestricted`,`Position`,`_HasChildren`,`CacheTime`,`Deleted`,`Event`,`CustomVars`,`CustomBlock`,`Tags`,`Note`,`Sitemap`,`SitemapChangefreq`,`SitemapPageType`,`SitemapUrlRegex1`,`SitemapUrlRegex2`,`DateStartOption`,`DateStart`,`DateEndOption`,`DateEnd`,`SitemapPriority`,`X_Test2`,`X_Test3`,`X_Test`) values (4,0,1,'TEXT',0,'','en','2011-06-30 00:32:52','2012-01-31 17:24:17','full-page.html','Contact us','','','','Contact us','contact-us','Contact us','','\n{@NUTS	TYPE=\'FORM\'	NAME=\'contact_us\'}\n','PUBLISHED','YES','YES','YES','NO','NO',6,'NO',0,'NO','YES','','a:0:{}','','','YES','weekly','NORMAL','','','NO','0000-00-00 00:00:00','NO','0000-00-00 00:00:00','0.5',NULL,NULL,NULL);
insert  into `NutsPage`(`ID`,`NutsPageID`,`NutsUserID`,`ContentType`,`ZoneID`,`HeaderImage`,`Language`,`DateCreation`,`DateUpdate`,`Template`,`MetaTitle`,`MetaDescription`,`MetaKeywords`,`MetaRobots`,`H1`,`VirtualPagename`,`MenuName`,`ContentResume`,`Content`,`State`,`MenuVisible`,`TopBar`,`BottomBar`,`Comments`,`AccessRestricted`,`Position`,`_HasChildren`,`CacheTime`,`Deleted`,`Event`,`CustomVars`,`CustomBlock`,`Tags`,`Note`,`Sitemap`,`SitemapChangefreq`,`SitemapPageType`,`SitemapUrlRegex1`,`SitemapUrlRegex2`,`DateStartOption`,`DateStart`,`DateEndOption`,`DateEnd`,`SitemapPriority`,`X_Test2`,`X_Test3`,`X_Test`) values (5,0,1,'TEXT',0,'','en','2011-06-29 22:34:13','2011-07-04 01:20:16','','Products','','','','Products','products','Products','','<b>Please choose your product :</b><br><br>\n\n{@NUTS	TYPE=\'MENU\'	CONTENT=\'ALL CHILDRENS\'	FROM=\'Products\'	ID=\'5\'	OUTPUT=\'LI&gt;UL\'	CSS=\'menu_5\'	ATTRIBUTES=\'\'	INCLUDE_PARENT=\'0\'}\n','PUBLISHED','YES','YES','YES','NO','NO',4,'YES',0,'NO','YES','','a:1:{s:5:\"Right\";a:2:{i:0;s:1:\"3\";i:1;s:1:\"2\";}}','','','YES','weekly','NORMAL','','','NO','0000-00-00 00:00:00','NO','0000-00-00 00:00:00','0.5',NULL,NULL,NULL);
insert  into `NutsPage`(`ID`,`NutsPageID`,`NutsUserID`,`ContentType`,`ZoneID`,`HeaderImage`,`Language`,`DateCreation`,`DateUpdate`,`Template`,`MetaTitle`,`MetaDescription`,`MetaKeywords`,`MetaRobots`,`H1`,`VirtualPagename`,`MenuName`,`ContentResume`,`Content`,`State`,`MenuVisible`,`TopBar`,`BottomBar`,`Comments`,`AccessRestricted`,`Position`,`_HasChildren`,`CacheTime`,`Deleted`,`Event`,`CustomVars`,`CustomBlock`,`Tags`,`Note`,`Sitemap`,`SitemapChangefreq`,`SitemapPageType`,`SitemapUrlRegex1`,`SitemapUrlRegex2`,`DateStartOption`,`DateStart`,`DateEndOption`,`DateEnd`,`SitemapPriority`,`X_Test2`,`X_Test3`,`X_Test`) values (6,5,1,'TEXT',0,'','en','2011-06-29 22:44:08','2011-07-04 01:20:35','','Product 1','','','','Product 1','product-1','Product 1','','<img class=\"left_border\" src=\"/library/media/images/user/box.jpg\" alt=\"Box product 1\" border=\"0\" height=\"200\" width=\"250\"><p>Quaestione igitur per multiplices dilatata fortunas cum ambigerentur quaedam, non nulla levius actitata constaret, post multorum clades Apollinares ambo pater et filius in exilium acti cum ad locum Crateras nomine pervenissent, villam scilicet suam quae ab Antiochia vicensimo et quarto disiungitur lapide, ut mandatum est, fractis cruribus occiduntur.</p><p>Post hoc impie perpetratum quod in aliis quoque iam timebatur, tamquam licentia crudelitati indulta per suspicionum nebulas aestimati quidam noxii damnabantur. quorum pars necati, alii puniti bonorum multatione actique laribus suis extorres nullo sibi relicto praeter querelas et lacrimas, stipe conlaticia victitabant, et civili iustoque imperio ad voluntatem converso cruentam, claudebantur opulentae domus et clarae.</p>Ob haec et huius modi multa, quae cernebantur in paucis, omnibus timeri sunt coepta. et ne tot malis dissimulatis paulatimque serpentibus acervi crescerent aerumnarum, nobilitatis decreto legati mittuntur: Praetextatus ex urbi praefecto et ex vicario Venustus et ex consulari Minervius oraturi, ne delictis supplicia sint grandiora, neve senator quisquam inusitato et inlicito more tormentis exponeretur.<br><br><br>','PUBLISHED','YES','YES','YES','NO','NO',9,'NO',0,'NO','YES','','a:1:{s:5:\"Right\";a:2:{i:0;s:1:\"3\";i:1;s:1:\"2\";}}','','','YES','weekly','NORMAL','','','NO','0000-00-00 00:00:00','NO','0000-00-00 00:00:00','0.5',NULL,NULL,NULL);
insert  into `NutsPage`(`ID`,`NutsPageID`,`NutsUserID`,`ContentType`,`ZoneID`,`HeaderImage`,`Language`,`DateCreation`,`DateUpdate`,`Template`,`MetaTitle`,`MetaDescription`,`MetaKeywords`,`MetaRobots`,`H1`,`VirtualPagename`,`MenuName`,`ContentResume`,`Content`,`State`,`MenuVisible`,`TopBar`,`BottomBar`,`Comments`,`AccessRestricted`,`Position`,`_HasChildren`,`CacheTime`,`Deleted`,`Event`,`CustomVars`,`CustomBlock`,`Tags`,`Note`,`Sitemap`,`SitemapChangefreq`,`SitemapPageType`,`SitemapUrlRegex1`,`SitemapUrlRegex2`,`DateStartOption`,`DateStart`,`DateEndOption`,`DateEnd`,`SitemapPriority`,`X_Test2`,`X_Test3`,`X_Test`) values (7,5,1,'TEXT',0,'','en','2011-06-30 00:44:33','2011-07-04 01:20:54','','Product 2','','','','Product 2','product-2','Product 2','','<img class=\"left_border\" src=\"/library/media/images/user/box.jpg\" alt=\"Box product 1\" border=\"0\" height=\"200\" width=\"250\"><p>Quaestione igitur per multiplices dilatata fortunas cum ambigerentur quaedam, non nulla levius actitata constaret, post multorum clades Apollinares ambo pater et filius in exilium acti cum ad locum Crateras nomine pervenissent, villam scilicet suam quae ab Antiochia vicensimo et quarto disiungitur lapide, ut mandatum est, fractis cruribus occiduntur.</p><p>Post hoc impie perpetratum quod in aliis quoque iam timebatur, tamquam licentia crudelitati indulta per suspicionum nebulas aestimati quidam noxii damnabantur. quorum pars necati, alii puniti bonorum multatione actique laribus suis extorres nullo sibi relicto praeter querelas et lacrimas, stipe conlaticia victitabant, et civili iustoque imperio ad voluntatem converso cruentam, claudebantur opulentae domus et clarae.</p>Ob haec et huius modi multa, quae cernebantur in paucis, omnibus timeri sunt coepta. et ne tot malis dissimulatis paulatimque serpentibus acervi crescerent aerumnarum, nobilitatis decreto legati mittuntur: Praetextatus ex urbi praefecto et ex vicario Venustus et ex consulari Minervius oraturi, ne delictis supplicia sint grandiora, neve senator quisquam inusitato et inlicito more tormentis exponeretur.<br><br><br>','PUBLISHED','YES','YES','YES','NO','NO',10,'NO',0,'NO','YES','','a:1:{s:5:\"Right\";a:2:{i:0;s:1:\"3\";i:1;s:1:\"2\";}}','','','YES','weekly','NORMAL','','','NO','0000-00-00 00:00:00','NO','0000-00-00 00:00:00','0.5',NULL,NULL,NULL);
insert  into `NutsPage`(`ID`,`NutsPageID`,`NutsUserID`,`ContentType`,`ZoneID`,`HeaderImage`,`Language`,`DateCreation`,`DateUpdate`,`Template`,`MetaTitle`,`MetaDescription`,`MetaKeywords`,`MetaRobots`,`H1`,`VirtualPagename`,`MenuName`,`ContentResume`,`Content`,`State`,`MenuVisible`,`TopBar`,`BottomBar`,`Comments`,`AccessRestricted`,`Position`,`_HasChildren`,`CacheTime`,`Deleted`,`Event`,`CustomVars`,`CustomBlock`,`Tags`,`Note`,`Sitemap`,`SitemapChangefreq`,`SitemapPageType`,`SitemapUrlRegex1`,`SitemapUrlRegex2`,`DateStartOption`,`DateStart`,`DateEndOption`,`DateEnd`,`SitemapPriority`,`X_Test2`,`X_Test3`,`X_Test`) values (8,5,1,'TEXT',0,'','en','2011-06-30 00:44:55','2011-07-04 01:21:22','','Product 3','','','','Product 3','product-3','Product 3','','<img class=\"left_border\" src=\"/library/media/images/user/box.jpg\" alt=\"Box product 1\" border=\"0\" height=\"200\" width=\"250\"><p>Quaestione igitur per multiplices dilatata fortunas cum ambigerentur quaedam, non nulla levius actitata constaret, post multorum clades Apollinares ambo pater et filius in exilium acti cum ad locum Crateras nomine pervenissent, villam scilicet suam quae ab Antiochia vicensimo et quarto disiungitur lapide, ut mandatum est, fractis cruribus occiduntur.</p><p>Post hoc impie perpetratum quod in aliis quoque iam timebatur, tamquam licentia crudelitati indulta per suspicionum nebulas aestimati quidam noxii damnabantur. quorum pars necati, alii puniti bonorum multatione actique laribus suis extorres nullo sibi relicto praeter querelas et lacrimas, stipe conlaticia victitabant, et civili iustoque imperio ad voluntatem converso cruentam, claudebantur opulentae domus et clarae.</p>Ob haec et huius modi multa, quae cernebantur in paucis, omnibus timeri sunt coepta. et ne tot malis dissimulatis paulatimque serpentibus acervi crescerent aerumnarum, nobilitatis decreto legati mittuntur: Praetextatus ex urbi praefecto et ex vicario Venustus et ex consulari Minervius oraturi, ne delictis supplicia sint grandiora, neve senator quisquam inusitato et inlicito more tormentis exponeretur.<br><br><br>','PUBLISHED','YES','YES','YES','NO','NO',11,'YES',0,'NO','YES','','a:1:{s:5:\"Right\";a:2:{i:0;s:1:\"3\";i:1;s:1:\"2\";}}','','','YES','weekly','NORMAL','','','NO','0000-00-00 00:00:00','NO','0000-00-00 00:00:00','0.5',NULL,NULL,NULL);
insert  into `NutsPage`(`ID`,`NutsPageID`,`NutsUserID`,`ContentType`,`ZoneID`,`HeaderImage`,`Language`,`DateCreation`,`DateUpdate`,`Template`,`MetaTitle`,`MetaDescription`,`MetaKeywords`,`MetaRobots`,`H1`,`VirtualPagename`,`MenuName`,`ContentResume`,`Content`,`State`,`MenuVisible`,`TopBar`,`BottomBar`,`Comments`,`AccessRestricted`,`Position`,`_HasChildren`,`CacheTime`,`Deleted`,`Event`,`CustomVars`,`CustomBlock`,`Tags`,`Note`,`Sitemap`,`SitemapChangefreq`,`SitemapPageType`,`SitemapUrlRegex1`,`SitemapUrlRegex2`,`DateStartOption`,`DateStart`,`DateEndOption`,`DateEnd`,`SitemapPriority`,`X_Test2`,`X_Test3`,`X_Test`) values (9,0,1,'TEXT',0,'','en','2011-06-30 03:44:28','2012-06-01 13:37:07','full-page.html','Welcome','','','','Welcome','/','Home','','<img src=\"/library/media/images/user/home.jpg\" alt=\"Home\" border=\"0\"><div class=\"clear\">&nbsp;</div><table style=\"width: 100%;\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\"><tbody><tr><td style=\"width: 460px;\" valign=\"top\"><h2>Welcome</h2>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. <br><br><br> <a class=\"more_info\" href=\"{@NUTS	TYPE=\'PAGE\'	CONTENT=\'URL\'	ID=\'1\'}\"> more info</a><h2>&nbsp;</h2><h2>Our Products</h2><ul><li><a href=\"{@NUTS	TYPE=\'PAGE\'	CONTENT=\'URL\'	ID=\'6\'}\"> View our product 1</a></li><li><a href=\"{@NUTS	TYPE=\'PAGE\'	CONTENT=\'URL\'	ID=\'7\'}\"> View our product 2</a></li><li><a href=\"{@NUTS	TYPE=\'PAGE\'	CONTENT=\'URL\'	ID=\'8\'}\"> View our produc</a>t 3</li></ul></td><td valign=\"top\"><br></td><td valign=\"top\"><h2>News and Events</h2>\n\n{@NUTS	TYPE=\'REGION\'	NAME=\'News-home-List\'}\n\n</td></tr></tbody></table>','PUBLISHED','NO','YES','YES','NO','NO',1,'NO',0,'NO','YES','','a:0:{}','','','YES','weekly','NORMAL','','','NO','0000-00-00 00:00:00','NO','0000-00-00 00:00:00','0.5','2011-12-06','2011-12-06 12:50:00',NULL);
insert  into `NutsPage`(`ID`,`NutsPageID`,`NutsUserID`,`ContentType`,`ZoneID`,`HeaderImage`,`Language`,`DateCreation`,`DateUpdate`,`Template`,`MetaTitle`,`MetaDescription`,`MetaKeywords`,`MetaRobots`,`H1`,`VirtualPagename`,`MenuName`,`ContentResume`,`Content`,`State`,`MenuVisible`,`TopBar`,`BottomBar`,`Comments`,`AccessRestricted`,`Position`,`_HasChildren`,`CacheTime`,`Deleted`,`Event`,`CustomVars`,`CustomBlock`,`Tags`,`Note`,`Sitemap`,`SitemapChangefreq`,`SitemapPageType`,`SitemapUrlRegex1`,`SitemapUrlRegex2`,`DateStartOption`,`DateStart`,`DateEndOption`,`DateEnd`,`SitemapPriority`,`X_Test2`,`X_Test3`,`X_Test`) values (10,0,1,'TEXT',1,'','en','2011-06-30 16:13:47','2011-07-04 14:27:29','full-page.html','Login','','','','Login','/en/login/','Login','','\n{@NUTS	TYPE=\'PLUGIN\'	NAME=\'_login\'	PARAMETERS=\'\'}\n','PUBLISHED','NO','YES','YES','NO','NO',1,'YES',0,'NO','YES','','a:0:{}','','','NO','weekly','NORMAL','','','NO','0000-00-00 00:00:00','NO','0000-00-00 00:00:00','0.5',NULL,NULL,NULL);
insert  into `NutsPage`(`ID`,`NutsPageID`,`NutsUserID`,`ContentType`,`ZoneID`,`HeaderImage`,`Language`,`DateCreation`,`DateUpdate`,`Template`,`MetaTitle`,`MetaDescription`,`MetaKeywords`,`MetaRobots`,`H1`,`VirtualPagename`,`MenuName`,`ContentResume`,`Content`,`State`,`MenuVisible`,`TopBar`,`BottomBar`,`Comments`,`AccessRestricted`,`Position`,`_HasChildren`,`CacheTime`,`Deleted`,`Event`,`CustomVars`,`CustomBlock`,`Tags`,`Note`,`Sitemap`,`SitemapChangefreq`,`SitemapPageType`,`SitemapUrlRegex1`,`SitemapUrlRegex2`,`DateStartOption`,`DateStart`,`DateEndOption`,`DateEnd`,`SitemapPriority`,`X_Test2`,`X_Test3`,`X_Test`) values (11,0,1,'TEXT',1,'','en','2011-06-30 19:07:18','2011-07-04 14:26:49','full-page.html','Login','','','','Register','/en/register/','Register','','\n{@NUTS	TYPE=\'PLUGIN\'	NAME=\'_register\'	PARAMETERS=\'\'}\n','PUBLISHED','NO','YES','YES','NO','NO',2,'NO',0,'NO','YES','','a:0:{}','','','NO','weekly','NORMAL','','','NO','0000-00-00 00:00:00','NO','0000-00-00 00:00:00','0.5',NULL,NULL,NULL);
insert  into `NutsPage`(`ID`,`NutsPageID`,`NutsUserID`,`ContentType`,`ZoneID`,`HeaderImage`,`Language`,`DateCreation`,`DateUpdate`,`Template`,`MetaTitle`,`MetaDescription`,`MetaKeywords`,`MetaRobots`,`H1`,`VirtualPagename`,`MenuName`,`ContentResume`,`Content`,`State`,`MenuVisible`,`TopBar`,`BottomBar`,`Comments`,`AccessRestricted`,`Position`,`_HasChildren`,`CacheTime`,`Deleted`,`Event`,`CustomVars`,`CustomBlock`,`Tags`,`Note`,`Sitemap`,`SitemapChangefreq`,`SitemapPageType`,`SitemapUrlRegex1`,`SitemapUrlRegex2`,`DateStartOption`,`DateStart`,`DateEndOption`,`DateEnd`,`SitemapPriority`,`X_Test2`,`X_Test3`,`X_Test`) values (12,10,1,'TEXT',1,'','en','2011-06-30 17:14:31','2011-07-04 14:27:42','','Welcome','','','','Welcome','/en/my_account/','Welcome','','This is the private space<br>','PUBLISHED','YES','YES','YES','NO','YES',1,'NO',0,'NO','YES','','a:1:{s:5:\"Right\";a:3:{i:0;s:1:\"3\";i:1;s:1:\"1\";i:2;s:1:\"2\";}}','','','NO','weekly','NORMAL','','','NO','0000-00-00 00:00:00','NO','0000-00-00 00:00:00','0.5',NULL,NULL,NULL);
insert  into `NutsPage`(`ID`,`NutsPageID`,`NutsUserID`,`ContentType`,`ZoneID`,`HeaderImage`,`Language`,`DateCreation`,`DateUpdate`,`Template`,`MetaTitle`,`MetaDescription`,`MetaKeywords`,`MetaRobots`,`H1`,`VirtualPagename`,`MenuName`,`ContentResume`,`Content`,`State`,`MenuVisible`,`TopBar`,`BottomBar`,`Comments`,`AccessRestricted`,`Position`,`_HasChildren`,`CacheTime`,`Deleted`,`Event`,`CustomVars`,`CustomBlock`,`Tags`,`Note`,`Sitemap`,`SitemapChangefreq`,`SitemapPageType`,`SitemapUrlRegex1`,`SitemapUrlRegex2`,`DateStartOption`,`DateStart`,`DateEndOption`,`DateEnd`,`SitemapPriority`,`X_Test2`,`X_Test3`,`X_Test`) values (13,0,1,'TEXT',1,'','en','2011-06-30 17:24:27','2011-07-04 14:27:55','full-page.html','','','','','Access Restricted','','Access restricted','','\n{@NUTS	TYPE=\'PLUGIN\'	NAME=\'_logon_restricted_access\'	PARAMETERS=\'\'}\n','PUBLISHED','NO','YES','YES','NO','NO',3,'NO',0,'NO','YES','','a:0:{}','','','NO','weekly','NORMAL','','','NO','0000-00-00 00:00:00','NO','0000-00-00 00:00:00','0.5',NULL,NULL,NULL);
insert  into `NutsPage`(`ID`,`NutsPageID`,`NutsUserID`,`ContentType`,`ZoneID`,`HeaderImage`,`Language`,`DateCreation`,`DateUpdate`,`Template`,`MetaTitle`,`MetaDescription`,`MetaKeywords`,`MetaRobots`,`H1`,`VirtualPagename`,`MenuName`,`ContentResume`,`Content`,`State`,`MenuVisible`,`TopBar`,`BottomBar`,`Comments`,`AccessRestricted`,`Position`,`_HasChildren`,`CacheTime`,`Deleted`,`Event`,`CustomVars`,`CustomBlock`,`Tags`,`Note`,`Sitemap`,`SitemapChangefreq`,`SitemapPageType`,`SitemapUrlRegex1`,`SitemapUrlRegex2`,`DateStartOption`,`DateStart`,`DateEndOption`,`DateEnd`,`SitemapPriority`,`X_Test2`,`X_Test3`,`X_Test`) values (14,0,1,'TEXT',0,'','en','2011-06-30 21:26:04','2011-07-04 01:18:21','','News and events','','','','News and events','news-and-events','News and events','','\n{@NUTS	TYPE=\'REGION\'	NAME=\'News-List\'}\n','PUBLISHED','YES','YES','YES','NO','NO',3,'YES',0,'NO','YES','','a:1:{s:5:\"Right\";a:3:{i:0;s:1:\"3\";i:1;s:1:\"1\";i:2;s:1:\"2\";}}','','','YES','weekly','TUNNEL','?tpg=','news','NO','0000-00-00 00:00:00','NO','0000-00-00 00:00:00','0.5',NULL,NULL,NULL);
insert  into `NutsPage`(`ID`,`NutsPageID`,`NutsUserID`,`ContentType`,`ZoneID`,`HeaderImage`,`Language`,`DateCreation`,`DateUpdate`,`Template`,`MetaTitle`,`MetaDescription`,`MetaKeywords`,`MetaRobots`,`H1`,`VirtualPagename`,`MenuName`,`ContentResume`,`Content`,`State`,`MenuVisible`,`TopBar`,`BottomBar`,`Comments`,`AccessRestricted`,`Position`,`_HasChildren`,`CacheTime`,`Deleted`,`Event`,`CustomVars`,`CustomBlock`,`Tags`,`Note`,`Sitemap`,`SitemapChangefreq`,`SitemapPageType`,`SitemapUrlRegex1`,`SitemapUrlRegex2`,`DateStartOption`,`DateStart`,`DateEndOption`,`DateEnd`,`SitemapPriority`,`X_Test2`,`X_Test3`,`X_Test`) values (15,14,1,'TEXT',0,'','en','2011-06-30 21:27:56','2012-03-30 14:11:34','','','','','','','','@News','','\n{@NUTS	TYPE=\'PLUGIN\'	NAME=\'_news-reader\'	PARAMETERS=\'\'}\n','PUBLISHED','NO','YES','YES','NO','NO',8,'NO',0,'NO','YES','','a:1:{s:5:\"Right\";a:3:{i:0;s:1:\"3\";i:1;s:1:\"1\";i:2;s:1:\"2\";}}','','','YES','weekly','NORMAL','','','NO','0000-00-00 00:00:00','NO','0000-00-00 00:00:00','0.5',NULL,NULL,NULL);
insert  into `NutsPage`(`ID`,`NutsPageID`,`NutsUserID`,`ContentType`,`ZoneID`,`HeaderImage`,`Language`,`DateCreation`,`DateUpdate`,`Template`,`MetaTitle`,`MetaDescription`,`MetaKeywords`,`MetaRobots`,`H1`,`VirtualPagename`,`MenuName`,`ContentResume`,`Content`,`State`,`MenuVisible`,`TopBar`,`BottomBar`,`Comments`,`AccessRestricted`,`Position`,`_HasChildren`,`CacheTime`,`Deleted`,`Event`,`CustomVars`,`CustomBlock`,`Tags`,`Note`,`Sitemap`,`SitemapChangefreq`,`SitemapPageType`,`SitemapUrlRegex1`,`SitemapUrlRegex2`,`DateStartOption`,`DateStart`,`DateEndOption`,`DateEnd`,`SitemapPriority`,`X_Test2`,`X_Test3`,`X_Test`) values (16,0,1,'TEXT',3,'','en','2011-06-30 23:42:19','2011-08-23 14:50:17','full-page.html','Sitemap','','','','Sitemap','sitemap','Sitemap','','\n{@NUTS	TYPE=\'MENU\'	CONTENT=\'ALL CHILDRENS\'	FROM=\'Main menu\'	ZONE_ID=\'0\'	OUTPUT=\'LI&gt;UL\'	CSS=\'sitemap\'	ATTRIBUTES=\'\'	INCLUDE_PARENT=\'0\'}\n','PUBLISHED','YES','YES','YES','NO','NO',1,'NO',0,'NO','YES','','a:0:{}','','','YES','weekly','NORMAL','','','NO','0000-00-00 00:00:00','NO','0000-00-00 00:00:00','0.5',NULL,NULL,NULL);
insert  into `NutsPage`(`ID`,`NutsPageID`,`NutsUserID`,`ContentType`,`ZoneID`,`HeaderImage`,`Language`,`DateCreation`,`DateUpdate`,`Template`,`MetaTitle`,`MetaDescription`,`MetaKeywords`,`MetaRobots`,`H1`,`VirtualPagename`,`MenuName`,`ContentResume`,`Content`,`State`,`MenuVisible`,`TopBar`,`BottomBar`,`Comments`,`AccessRestricted`,`Position`,`_HasChildren`,`CacheTime`,`Deleted`,`Event`,`CustomVars`,`CustomBlock`,`Tags`,`Note`,`Sitemap`,`SitemapChangefreq`,`SitemapPageType`,`SitemapUrlRegex1`,`SitemapUrlRegex2`,`DateStartOption`,`DateStart`,`DateEndOption`,`DateEnd`,`SitemapPriority`,`X_Test2`,`X_Test3`,`X_Test`) values (17,0,1,'TEXT',0,'','en','2011-07-04 14:44:04','2011-08-19 01:22:14','full-page.html','Search','','','','Search','search','Search','','\n{@NUTS	TYPE=\'PLUGIN\'	NAME=\'_search-engine\'	PARAMETERS=\'\'}\n','PUBLISHED','NO','YES','YES','NO','NO',7,'NO',0,'NO','YES','a:1:{s:4:\"Test\";s:0:\"\";}','a:0:{}','','','NO','weekly','NORMAL','','','NO','0000-00-00 00:00:00','NO','0000-00-00 00:00:00','0.5',NULL,NULL,NULL);


/* Nuts PageAccess */
TRUNCATE TABLE NutsPageAccess;
insert  into `NutsPageAccess`(`NutsGroupID`,`NutsPageID`) values (3,12);
insert  into `NutsPageAccess`(`NutsGroupID`,`NutsPageID`) values (2,12);
insert  into `NutsPageAccess`(`NutsGroupID`,`NutsPageID`) values (1,12);

/* Nuts PageVersion */
TRUNCATE TABLE NutsPageVersion;



/* Nuts Form Builder */
TRUNCATE TABLE NutsForm;
INSERT INTO `NutsForm`(`ID`,`Language`,`Name`,`Description`,`Caption`,`CssId`,`JsCode`,`Captcha`,`Information`,`FormBeforePhp`,`FormCustomError`,`FormValidPhpCode`,`FormValidHtmlCode`,`FormStockData`,`FormValidMailer`,`FormValidMailerFrom`,`FormValidMailerTo`,`FormValidMailerSubject`,`Deleted`) VALUES
	(1,'AUTO','contact_us','','Please fille this form below',NULL,'','NO','<br>','','','','Thank you ! Your feedback has been received.','NO','YES','','','Contact from your website','NO');

TRUNCATE TABLE NutsFormField;
INSERT INTO `NutsFormField`(`ID`,`NutsFormID`,`Name`,`Label`,`Type`,`Required`,`Attributes`,`Email`,`OtherValidation`,`I18N`,`Value`,`PhpCode`,`FilePath`,`FileAllowedExtensions`,`FileAllowedMimes`,`FileMaxSize`,`HtmlCode`,`TextAfter`,`Position`,`Deleted`) VALUES
	(1,1,'Company','Your company','TEXT','NO','','NO','','NO','','','','','','','','',1,'NO'),
	(2,1,'Name','Your name','TEXT','YES','','NO','','NO','','','','','','','','',2,'NO'),
	(4,1,'Email','Your email','TEXT','YES','','YES','','NO','','','','','','','','',3,'NO'),
	(5,1,'Message','Your message','TEXTAREA','YES','','NO','','NO','','','','','','','','',4,'NO');

/* Nuts Gallery */
INSERT INTO `NutsGallery`(`ID`,`Name`,`Description`,`LogoImage`,`GenerateJS`,`Position`,`Active`,`Deleted`) VALUES
	(1,'Gallery test','<br>',NULL,'YES',1,'YES','NO');

INSERT INTO `NutsGalleryImage`(`ID`,`NutsGalleryID`,`MainImage`,`BigImage`,`HDImage`,`Legend`,`Description`,`Active`,`Position`,`Deleted`) VALUES
	(1,1,'1.jpg',NULL,NULL,'Business zen',NULL,'YES',1,'NO'),
	(2,1,'2.jpg',NULL,NULL,'Girls charming smile 014704',NULL,'YES',2,'NO'),
	(3,1,'3.jpg',NULL,NULL,'Grow your business',NULL,'YES',3,'NO'),
	(4,1,'4.jpg',NULL,NULL,'Business consulting winning team',NULL,'YES',4,'NO'),
	(5,1,'5.jpg',NULL,NULL,'Business',NULL,'YES',5,'NO'),
	(6,1,'6.jpg',NULL,NULL,'Man demo',NULL,'YES',6,'NO'),
	(7,1,'7.jpg',NULL,NULL,'Meeting',NULL,'YES',7,'NO'),
	(8,1,'8.jpg',NULL,NULL,'Smiley man',NULL,'YES',8,'NO');

/* Nuts Block */
TRUNCATE TABLE NutsBlock;
INSERT INTO `NutsBlock`(`ID`,`GroupName`,`SubGroupName`,`Name`,`Type`,`Text`,`Preview`,`Visible`,`Deleted`) VALUES
	(1,'RIGHT','','our_products','HTML','<a href=\"{@NUTS	TYPE=\'PAGE\'	CONTENT=\'URL\'	ID=\'5\'}\"><img src=\"/library/media/images/user/blocks/our_products.png\" alt=\"Our products\" border=\"0\" height=\"100\" width=\"230\"></a><br><br>','/library/media/images/user/nuts_block_preview/our_products.png','YES','NO'),
	(2,'RIGHT','','contact_us','HTML','<a href=\"{@NUTS	TYPE=\'PAGE\'	CONTENT=\'URL\'	ID=\'4\'}\"><img src=\"/library/media/images/user/blocks/contact_us.png\" alt=\"Contact us\" border=\"0\" height=\"100\" width=\"230\"></a>','/library/media/images/user/nuts_block_preview/contact_us.png','YES','NO'),
	(3,'RIGHT','','survey','HTML','\n{@NUTS	TYPE=\'SURVEY\'	ID=\'1\'	TITLE=\'Do you already use a CMS / CMF ?\'}\n<br>\n','/library/media/images/user/nuts_block_preview/survey.png','YES','NO');

/* Nuts News */
INSERT INTO `NutsNews`(`ID`,`NewsImage`,`NewsImageModel`,`NutsUserID`,`Language`,`DateGMT`,`DateGMTExpiration`,`Title`,`Resume`,`Text`,`Tags`,`Event`,`Comment`,`Active`,`VirtualPageName`,`Filter1`,`Filter2`,`Filter3`,`Archived`,`Deleted`) VALUES
	(1,'1.jpg','',1,'en','2011-04-05',NULL,'There are many variations of passages','Donec non turpis eget turpis eleifend consectetur ac lobortis massa. Suspendisse sed dui erat, vel pellentesque turpis. Sed enim tortor, molestie eu mattis id, vestibulum non sem. Donec tempor faucibus dui, eget mollis tellus blandit eu. Maecenas ultricies egestas urna eu tempus.<br>','<p>\r\nSed tellus nisl, ultricies posuere aliquet et, egestas ac orci. \r\nCurabitur nec tempus urna. Aenean justo mauris, accumsan eget posuere \r\nin, auctor in velit. Donec aliquet, ante quis bibendum tristique, lorem \r\nipsum faucibus elit, nec placerat est nisi vel leo. Donec ut bibendum \r\nneque. In eleifend vestibulum justo. Donec suscipit elementum justo eu \r\nsagittis. Sed vitae dolor enim, eget accumsan nibh. Pellentesque rhoncus\r\n urna eu risus hendrerit pulvinar. Nulla facilisi. Aliquam blandit \r\nornare risus eget imperdiet. Duis at diam ac risus condimentum tempor. \r\nDuis hendrerit, dolor sit amet facilisis malesuada, tellus nisi \r\ndignissim turpis, in pretium nibh enim at augue. Vestibulum pellentesque\r\n mi tellus, quis volutpat nisl. Nulla venenatis venenatis mattis. Nulla \r\nvitae mauris eu sapien pharetra gravida.\r\n</p>\r\n<p>\r\nSed vestibulum ipsum a nisi auctor viverra placerat risus auctor. Nulla \r\ntristique dignissim tellus eget dignissim. Mauris a nulla vitae mi \r\nultrices pharetra pretium id libero. Pellentesque mollis fermentum \r\npurus, rutrum pretium tortor commodo non. In velit sem, luctus mollis \r\niaculis et, vehicula molestie nisl. Praesent eu sagittis tortor. In eros\r\n erat, rhoncus sed cursus ut, pharetra eget nunc. Morbi viverra arcu id \r\norci consectetur suscipit. Nullam faucibus adipiscing neque, et pharetra\r\n enim volutpat vel. Aliquam in neque ligula. Fusce mollis ante eu lectus\r\n suscipit consequat. Fusce quis dolor massa, in consectetur diam. Duis \r\nturpis erat, dapibus eu ornare vel, hendrerit ac justo. Vivamus rhoncus \r\nurna eu risus auctor sed varius ligula gravida. Suspendisse sed nisi sed\r\n nibh pretium euismod vel in tortor. Integer euismod quam et lectus \r\nultrices euismod. Nullam vulputate accumsan turpis eget fermentum. \r\nAliquam id lobortis leo.\r\n</p>\r\n<p>\r\nPellentesque dictum lobortis erat in aliquet. Cras at quam diam, in \r\nlaoreet sem. Duis eget diam quam, ultrices blandit lacus. Curabitur \r\nhendrerit commodo turpis, ac ullamcorper purus consequat vel. Nullam ut \r\nnibh enim, vitae sollicitudin purus. Cras neque turpis, imperdiet eget \r\nblandit a, adipiscing ac tellus. Cras laoreet quam quis felis \r\ncondimentum dictum. Maecenas vitae nunc quam. Suspendisse est lacus, \r\npharetra sed vehicula eu, rutrum quis est. Sed condimentum viverra \r\nvehicula. Vivamus laoreet, purus nec dictum congue, mauris nisl congue \r\nturpis, mollis volutpat lorem purus vel magna. Aliquam dictum nisl ac \r\norci euismod porttitor.\r\n</p>','','YES','YES','YES','',NULL,NULL,NULL,'NO','NO'),
	(2,'2.jpg','',1,'en','2011-06-02',NULL,'Donec non turpis eget turpis eleifend consectetur ac lobortis massa','Curabitur dolor urna, lacinia nec semper at, dignissim id velit. Vivamus\r\n ligula libero, vulputate ut feugiat lobortis, tincidunt nec neque. \r\nVivamus enim mauris, tincidunt quis vehicula et, porttitor sit amet dui.\r\n Aenean adipiscing, orci fringilla venenatis semper, eros dui viverra \r\nmetus, sed accumsan purus urna eget turpis. Nullam quis faucibus dui. \r\nCras accumsan, mi vel elementum mollis, lorem est tincidunt nulla, id \r\ntempor justo magna vel purus.','<p>\r\nNullam vel ante quis mauris vehicula viverra at nec felis. Nulla \r\nfacilisi. Integer semper laoreet nisl, nec adipiscing justo tempus \r\nultricies. Pellentesque neque leo, accumsan ac rutrum ac, sagittis vel \r\njusto. Cum sociis natoque penatibus et magnis dis parturient montes, \r\nnascetur ridiculus mus. Duis massa ante, tincidunt vel ullamcorper ac, \r\nadipiscing non libero. Vivamus eget tortor et ante posuere porttitor vel\r\n id ante. Morbi risus mauris, tempus quis convallis nec, accumsan quis \r\nnisi. Curabitur elementum auctor erat, at fringilla odio rutrum ut. Duis\r\n nec lorem mollis eros volutpat convallis. Quisque sodales imperdiet \r\nfeugiat. Nullam tincidunt molestie fermentum. Fusce et mauris justo, id \r\niaculis tellus. Cum sociis natoque penatibus et magnis dis parturient \r\nmontes, nascetur ridiculus mus. Nulla vehicula enim eu risus lobortis \r\neget venenatis nisl mollis. Phasellus eu leo at nibh dapibus faucibus.\r\n</p>\r\n<p>\r\nDonec aliquet urna nibh, sed sollicitudin orci. Cum sociis natoque \r\npenatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce\r\n iaculis pulvinar dignissim. Nulla a enim risus, eget ultrices est. \r\nPraesent sagittis porttitor nibh, ac auctor neque fermentum quis. Duis \r\nullamcorper, nulla non vulputate sodales, odio massa faucibus tellus, in\r\n pharetra elit risus egestas lacus. Fusce faucibus interdum augue, quis \r\nmalesuada ipsum pulvinar et. Nam venenatis leo sed ligula viverra \r\ncommodo. Proin in arcu sed nibh iaculis aliquet ac vitae ipsum. \r\nCurabitur vitae lectus neque, sit amet cursus est. Nullam eleifend \r\nsuscipit arcu pulvinar placerat. Duis rhoncus facilisis neque.\r\n</p>','','YES','YES','YES','',NULL,NULL,NULL,'NO','NO'),
	(3,'3.jpg','',1,'en','2011-03-22',NULL,'Quisque eu tellus at enim viverra gravida ut in leo','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer \r\nvestibulum mauris eget urna sagittis at tempus sapien consectetur. \r\nPellentesque pellentesque, mi sit amet tempor congue, lacus ante congue \r\nerat, nec pharetra eros ligula venenatis erat. Ut lectus ante, aliquet \r\nut hendrerit in, laoreet ut augue. Ut vitae pretium purus. Proin rhoncus\r\n porttitor tortor ac placerat. Ut et sem in magna bibendum faucibus \r\nvitae sed neque. <br>','<p>\r\nPraesent commodo, erat nec mattis eleifend, sapien turpis blandit dui, \r\nsit amet pharetra purus quam non nunc. Nulla vestibulum diam nec risus \r\niaculis mattis. Cras viverra mi dictum nibh lacinia sit amet venenatis \r\nenim rhoncus. Sed molestie quam et ipsum laoreet pulvinar. Nulla vel \r\ndolor ligula. Integer non rutrum augue. Proin aliquam risus at quam \r\nvulputate non fermentum purus tincidunt. Fusce vehicula tortor a purus \r\ntristique pharetra sollicitudin et erat. Etiam molestie scelerisque \r\nrisus, sit amet hendrerit augue pulvinar at. Pellentesque eu dolor in \r\nvelit vestibulum blandit. Donec mollis nibh id lectus convallis \r\nfacilisis eu et orci. Nulla adipiscing tempor augue ac consequat.\r\n</p>\r\n<p>\r\nNullam nibh enim, viverra at pellentesque et, malesuada ut felis. Ut \r\nhendrerit aliquam leo, sed vestibulum sem dapibus eget. Vestibulum ante \r\nipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; \r\nDuis luctus, tellus vitae convallis accumsan, ligula est consectetur \r\norci, at vehicula erat dolor interdum magna. Integer pellentesque ligula\r\n quis libero mollis at lobortis lorem blandit. Morbi at mauris tellus, \r\nin sagittis nisl. Mauris pharetra nunc quis nibh mollis vehicula. Aenean\r\n odio diam, tempor ac vehicula eu, molestie vel arcu. Sed in nisl eget \r\nligula laoreet ullamcorper. Class aptent taciti sociosqu ad litora \r\ntorquent per conubia nostra, per inceptos himenaeos. Vestibulum et \r\nporttitor dolor. Ut ipsum justo, scelerisque et pretium at, hendrerit \r\neget orci. In non sem nunc. Nullam non tempor eros.\r\n</p>\r\n<p>\r\nPellentesque faucibus neque et mauris cursus non rutrum dolor ultricies.\r\n Morbi faucibus sem eget tortor scelerisque luctus. Morbi convallis \r\nconsequat bibendum. Etiam ac nisi lacus. Curabitur vel eros sed dui \r\naliquam convallis. Suspendisse dapibus porttitor aliquet. Nunc tempor, \r\nipsum eu tristique aliquam, risus ligula blandit est, ac iaculis lectus \r\norci in nunc. Vestibulum ante ipsum primis in faucibus orci luctus et \r\nultrices posuere cubilia Curae; Integer mi odio, luctus non faucibus \r\nnon, iaculis ut orci. Vivamus condimentum, augue ac semper tincidunt, \r\npurus nisi cursus urna, at ultricies quam neque ut magna. Maecenas \r\nornare arcu nec velit placerat quis luctus ipsum pellentesque. Praesent \r\nut nisl sed eros tincidunt ornare. Nunc non libero vitae arcu semper \r\nviverra. Vivamus cursus, dolor ac convallis porta, diam erat sagittis \r\nlibero, a mattis ipsum nibh sit amet massa. In eget pellentesque nunc. \r\nMauris vitae eros a eros condimentum hendrerit a et magna.\r\n</p>','','YES','YES','YES','',NULL,NULL,NULL,'NO','NO');

/* Nuts Region */
TRUNCATE TABLE NutsRegion;
INSERT INTO `NutsRegion`(`ID`,`Name`,`Description`,`PhpCode`,`Query`,`HtmlBefore`,`Html`,`HtmlAfter`,`HtmlNoRecord`,`Caption`,`Result`,`HookData`,`PreviousNextVisible`,`Pager`,`PagerPreviousText`,`PagerNextText`,`Deleted`) VALUES
	(1,'News-home-List','List of news of homepage','','SELECT\n		ID,\n		Title,\n		Resume\n		DateGMT,\n		DATE_FORMAT(DateGMT, \'%d/%m/%Y\') AS DateX\nFROM\n		NutsNews\nWHERE\n		Deleted = \'NO\' AND\n		DateGMT <= NOW()\nORDER BY\n		DateGMT DESC','<div id=\"home_news_list\">','<div class=\"news\">\n	<span class=\"date\">{DateX}</span><br />\n	<a class=\"title\" href=\"{Uri}\">{Title}</a>\n</div>','</div>','',NULL,3,'$uri = strtolower(trim($row[\'Title\']));\n$uri = str_replace(\' \', \'-\', $uri);\n$uri = str_replace(\'--\', \'-\', $uri);\n$uri = str_replace(\'\"\', \'\', $uri);\n\n$uri = \"/en/15-news,\".$row[\'ID\'].\",\".$uri.\".html\";\n\n$row[\'Uri\']  = $uri;','YES','NO','','','NO'),
	(2,'News-List','List of news','','SELECT\n		ID,\n		Title,\n		Resume\n		DateGMT,\n		DATE_FORMAT(DateGMT, \'%d/%m/%Y\') AS DateX\nFROM\n		NutsNews\nWHERE\n		Deleted = \'NO\' AND\n		DateGMT <= NOW()\nORDER BY\n		DateGMT DESC','<div id=\"news_list\">','<div class=\"news\">\n	<span class=\"date\">{DateX}</span><br />\n	<a class=\"title\" href=\"{Uri}\">{Title}</a>\n</div>','</div>','',NULL,5,'$uri = strtolower(trim($row[\'Title\']));\n$uri = str_replace(\' \', \'-\', $uri);\n$uri = str_replace(\'--\', \'-\', $uri);\n$uri = \"/en/15-news,\".$row[\'ID\'].\",\".$uri.\".html\";\n\n$row[\'Uri\']  = $uri;','YES','YES','« Previous','Next »','NO');

/* Rte */
TRUNCATE TABLE NutsRichEditor;
INSERT INTO `NutsRichEditor`(`ID`,`Content`,`Deleted`) VALUES
	(1,'forced_root_block : \'\',\r\nforce_br_newlines : false,\r\nforce_p_newlines : false,    \r\nremove_linebreaks: true,\r\napply_source_formatting: false,\r\nconvert_newlines_to_brs : false,','NO');

/* RSS */
TRUNCATE TABLE NutsRss;
INSERT INTO `NutsRss`(`ID`,`RssTitle`,`RssLink`,`RssDescription`,`RssCopyright`,`RssImage`,`PhpCode`,`Query`,`HookFunction`,`RssLimit`,`Deleted`) VALUES
	(1,'News and Events','{WEBSITE_NAME}/','RSS news and events','','','','SELECT\n		Title AS title,\n		Resume AS description,\n		DATE_FORMAT(DateGMT, \'%m-%d-%Y %h:%i\') AS pubDate,\n		CONCAT(\'/en/15-news,\',ID,\',\',LOWER(REPLACE(Title,\' \', \'-\')),\'.html\') AS link\nFROM\n		NutsNews\nWHERE\n		Deleted = \'NO\' AND\n		Active = \'YES\' AND\n		DateGMT <= NOW()','',20,'NO');

/* Spider */
TRUNCATE TABLE NutsSpider;


/* Template configuration */
TRUNCATE TABLE NutsTemplateConfiguration;
INSERT INTO `NutsTemplateConfiguration`(`ID`,`LanguageDefault`,`Languages`,`Theme`,`Description`,`Deleted`) VALUES
	(1,'en','','default','This is the default theme','NO');

/* Url rewriting */
TRUNCATE TABLE NutsUrlRewriting;
INSERT INTO `NutsUrlRewriting`(`ID`,`Type`,`Pattern`,`Replacement`,`Position`,`Deleted`) VALUES
	(1,'SIMPLE','/en/login/','/en/10.html',1,'NO'),
	(2,'SIMPLE','/en/register/','/en/11.html',2,'NO'),
	(3,'SIMPLE','/en/my_account/','/en/12.html',3,'NO'),
	(4,'SIMPLE','/en/access_restricted/','/en/13.html',4,'NO');

/* Nuts Survey */
TRUNCATE TABLE NutsSurvey;
INSERT INTO `NutsSurvey`(`ID`,`Title`,`I18N`,`ViewResult`,`Deleted`) VALUES
	(1,'Do you already use a CMS / CMF ?','NO','YES','NO');
TRUNCATE TABLE NutsSurveyOption;
INSERT INTO `NutsSurveyOption`(`ID`,`NutsSurveyID`,`Title`,`I18N`,`Position`,`Deleted`) VALUES
	(1,1,'Yes','NO',1,'NO'),
	(2,1,'No','NO',2,'NO');


/* Nuts Menu */
/* Nuts Super Admin Rights */
TRUNCATE TABLE NutsMenu;
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('1','1','_configuration','','0','1','3','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('2','2','_zone-manager','','0','0','13','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('3','2','_template_settings','','0','1','2','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('4','1','_user-manager','','0','0','6','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('5','1','_group-manager','','1','0','5','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('6','2','_page-manager','','0','0','15','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('7','5','_logs','','0','0','3','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('8','1','_right-manager','','0','0','7','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('9','2','_news','','0','1','16','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('10','5','_updater','','0','0','4','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('11','5','_phpinfo','','0','0','2','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('12','2','_region-manager','','0','0','6','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('13','2','_gallery','','0','0','17','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('14','2','_gallery_image','','0','1','18','NO','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('15','2','_pattern','','0','1','12','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('17','5','_documentation','http://www.nuts-cms.com/en/2-documentation.html','0','0','5','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('18','2','_block_builder','','0','0','4','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('19','2','_email','','0','1','3','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('20','2','_i18n',NULL,'0','0','5','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('22','1','_plugins',NULL,'0','0','4','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('23','1','_rte_template',NULL,'1','0','9','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('24','1','_page-versionning',NULL,'1','0','11','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('25','2','_media','','0','0','19','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('27','2','_form-builder','','1','1','10','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('28','2','_form-builder-fields','','0','0','11','NO','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('29','3','_newsletter-mailing-list',NULL,'0','0','1','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('30','3','_newsletter-mailing-list-suscriber',NULL,'0','0','2','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('31','3','_newsletter',NULL,'0','0','3','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('32','3','_survey','','1','1','6','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('33','3','_survey-option',NULL,'0','0','7','NO','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('34','2','_sitemap','','1','0','8','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('35','2','_rss','','1','0','7','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('36','1','_internal-messaging',NULL,'0','0','0','NO','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('37','1','_control-center',NULL,'0','0','2','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('38','3','_analytics','https://www.google.com/analytics/reporting/','0','0','10','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('39','3','_dropbox','','0','1','9','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('40','2','_search-engine','','1','0','9','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('41','2','_template_styles',NULL,'0','0','1','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('42','1','_rte_custom',NULL,'0','0','10','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('43','2','_url_rewriting',NULL,'0','0','14','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('44','3','_press-kit',NULL,'0','0','8','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('45','2','_register','','0','0','20','NO','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('46','1','_internal-memo','','0','0','0','NO','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('47','5','_nuts-forge','http://www.nuts-cms.com/tools/forge','0','0','6','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('48','1','_file_explorer_mimes_type',NULL,'0','0','4','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('49','1','_settings',NULL,'0','0','0','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('50','3','_edm-group','','0','0','5','NO','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('51','3','_edm','','1','0','4','YES','NO');
insert into `NutsMenu` (`ID`, `Category`, `Name`, `ExternalUrl`, `BreakBefore`, `BreakAfter`, `Position`, `Visible`, `Deleted`) values('52','3','_edm-logs',NULL,'0','0','4','NO','NO');



/*Data for the table `NutsMenuRight` */
TRUNCATE TABLE NutsMenuRight;
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3382','11','1','view');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3381','47','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3380','7','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3379','17','1','exec');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3378','33','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3377','33','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3376','33','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3375','33','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3374','32','1','reporting');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3373','32','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3372','32','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3371','32','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3370','32','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3369','44','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3368','44','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3367','44','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3366','44','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3365','30','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3364','30','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3363','30','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3362','30','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3361','29','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3360','29','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3359','29','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3358','29','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3357','31','1','view');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3356','31','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3355','31','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3354','52','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3353','50','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3352','50','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3351','50','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3350','50','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3349','51','1','administration');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3348','51','1','exec');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3347','39','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3346','39','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3345','39','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3344','39','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3343','38','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3342','2','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3341','2','1','view');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3340','2','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3339','2','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3338','2','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3337','43','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3336','43','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3335','43','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3334','43','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3333','41','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3332','3','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3331','3','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3330','34','1','info');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3329','40','1','info');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3328','35','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3327','35','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3326','35','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3325','35','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3324','45','1','exec');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3323','12','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3322','12','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3321','12','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3320','12','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3319','15','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3318','15','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3317','15','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3316','15','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3315','6','1','exec');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3314','9','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3313','9','1','view');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3312','9','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3311','9','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3310','9','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3309','25','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3308','25','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3307','25','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3227','44','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3226','44','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3225','30','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3224','30','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3223','30','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3222','30','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3221','29','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3220','29','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3219','29','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3218','29','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3217','31','2','view');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3216','31','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3215','31','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3214','51','2','exec');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3213','39','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3212','39','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3211','39','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3210','39','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3209','38','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3208','2','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3207','2','2','view');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3206','2','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3205','2','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3204','2','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3203','43','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3202','43','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3201','43','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3200','43','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3199','41','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3198','45','2','exec');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3197','15','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3196','15','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3195','15','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3194','15','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3193','6','2','exec');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3192','9','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3191','9','2','view');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3190','9','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3189','9','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3188','9','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3187','25','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3186','25','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3185','25','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3184','25','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3183','20','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3182','20','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3181','20','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3180','20','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3179','14','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3178','14','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3177','14','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3176','14','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3175','13','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3174','13','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3173','13','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3172','13','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3171','28','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3170','28','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3169','28','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3168','28','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3167','27','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3166','27','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3165','27','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3164','27','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3163','19','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3162','19','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3161','19','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3160','19','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3159','18','2','view');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3158','18','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3157','18','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3156','18','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3306','25','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3305','20','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3304','20','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3303','20','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3302','20','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3301','14','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3300','14','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3299','14','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3298','14','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3297','13','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3296','13','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3295','13','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3155','18','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3154','4','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3153','4','2','view');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3152','4','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3151','4','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3150','4','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3149','23','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3148','23','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3147','23','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3146','23','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3145','8','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3144','24','2','viewer');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3143','24','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3142','36','2','view');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3141','36','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3140','36','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3139','36','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3138','46','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3137','5','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3136','5','2','view');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3135','5','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3134','5','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3133','5','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3294','13','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3293','28','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3292','28','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3291','28','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3290','28','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3289','27','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3288','27','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3287','27','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3286','27','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3285','19','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3284','19','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3283','19','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3282','19','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3281','18','1','view');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3280','18','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3279','18','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3278','18','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3277','18','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3276','4','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3275','4','1','view');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3274','4','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3273','4','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3272','4','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3271','49','1','exec');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3270','23','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3269','23','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3268','23','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3267','23','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3266','42','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3265','42','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3264','8','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3263','22','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3262','22','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3261','22','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3260','22','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3259','24','1','viewer');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3258','24','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3257','36','1','view');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3256','36','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3255','36','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3254','36','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3253','46','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3252','5','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3251','5','1','view');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3250','5','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3249','5','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3248','5','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3247','48','1','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3246','48','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3245','48','1','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3244','48','1','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3243','37','1','exec');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3242','1','1','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3228','44','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3229','44','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3230','32','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3231','32','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3232','32','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3233','32','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3234','32','2','reporting');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3235','33','2','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3236','33','2','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3237','33','2','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3238','33','2','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3239','17','2','exec');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3240','11','2','view');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3241','10','2','exec');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3384','10','1','exec');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3385','46','4','edit');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3386','36','4','list');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3387','36','4','add');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3388','36','4','delete');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3389','36','4','view');
insert into `NutsMenuRight` (`ID`, `NutsMenuID`, `NutsGroupID`, `Name`) values('3390','51','4','exec');

