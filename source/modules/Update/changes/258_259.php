<?php
global $adb;

$flds = "
	pdffieldid I(2) NOTNULL PRIMARY,
	pdffieldname C(30) DEFAULT NULL,
	pdftablename C(30) DEFAULT NULL,
	quotes_g_enabled I(1) DEFAULT 0,
	quotes_i_enabled I(1) DEFAULT 0,
	po_g_enabled I(1) DEFAULT 0,
	po_i_enabled I(1) DEFAULT 0,
	so_g_enabled I(1) DEFAULT 0,
	so_i_enabled I(1) DEFAULT 0,
	invoice_g_enabled I(1) DEFAULT 0,
	invoice_i_enabled I(1) DEFAULT 0
";
$sqlarray = $adb->datadict->CreateTableSQL('crmnow_pdf_fields', $flds);
$adb->datadict->ExecuteSQLArray($sqlarray);
$adb->query("insert into crmnow_pdf_fields values (1,'Position','crmnow_pdfcolums',1,1,1,1,1,1,1,1)");
$adb->query("insert into crmnow_pdf_fields values (2,'OrderCode','crmnow_pdfcolums',1,1,1,1,1,1,1,1)");
$adb->query("insert into crmnow_pdf_fields values (3,'Description','crmnow_pdfcolums',1,1,1,1,1,1,1,1)");
$adb->query("insert into crmnow_pdf_fields values (4,'Qty','crmnow_pdfcolums',1,1,1,1,1,1,1,1)");
$adb->query("insert into crmnow_pdf_fields values (5,'Unit','crmnow_pdfcolums',1,1,1,1,1,1,1,1)");
$adb->query("insert into crmnow_pdf_fields values (6,'UnitPrice','crmnow_pdfcolums',1,1,1,1,1,1,1,1)");
$adb->query("insert into crmnow_pdf_fields values (7,'Discount','crmnow_pdfcolums',1,1,1,1,1,1,1,1)");
$adb->query("insert into crmnow_pdf_fields values (8,'Tax','crmnow_pdfcolums',0,1,1,1,1,1,1,1)");
$adb->query("insert into crmnow_pdf_fields values (9,'LineTotal','crmnow_pdfcolums',1,1,1,1,1,1,1,1)");

$flds = "
	pdfcolumnactiveid I(2) NOTNULL PRIMARY,
	pdfmodulname C(13) DEFAULT 0,
	pdftaxmode C(10) DEFAULT 0,
	position C(8) DEFAULT 0,
	ordercode C(8) DEFAULT 0,
	description C(8) DEFAULT 0,
	qty C(8) DEFAULT 0,
	unit C(8) DEFAULT 0,
	unitprice C(8) DEFAULT 0,
	discount C(8) DEFAULT 0,
	tax C(8) DEFAULT 0,
	linetotal C(8) DEFAULT 0
";
$sqlarray = $adb->datadict->CreateTableSQL('crmnow_pdfcolums_active', $flds);
$adb->datadict->ExecuteSQLArray($sqlarray);
$adb->query("insert into crmnow_pdfcolums_active values (1,'Quotes','group','','','disabled','disabled','','disabled','','disabled','disabled')");
$adb->query("insert into crmnow_pdfcolums_active values (2,'Quotes','individual','','','disabled','disabled','','disabled','','disabled','disabled')");
$adb->query("insert into crmnow_pdfcolums_active values (3,'PurchaseOrder','group','','','disabled','disabled','','disabled','','disabled','disabled')");
$adb->query("insert into crmnow_pdfcolums_active values (4,'PurchaseOrder','individual','','','disabled','disabled','','disabled','','disabled','disabled')");
$adb->query("insert into crmnow_pdfcolums_active values (5,'SalesOrder','group','','','disabled','disabled','','disabled','','disabled','disabled')");
$adb->query("insert into crmnow_pdfcolums_active values (6,'SalesOrder','individual','','','disabled','disabled','','disabled','','disabled','disabled')");
$adb->query("insert into crmnow_pdfcolums_active values (7,'Invoice','group','','','disabled','disabled','','disabled','','disabled','disabled')");
$adb->query("insert into crmnow_pdfcolums_active values (8,'Invoice','individual','','','disabled','disabled','','disabled','','disabled','disabled')");

$flds = "
	pdfcolumnselid I(2) NOTNULL PRIMARY,
	pdfmodul C(13) DEFAULT 0,
	pdftaxmode C(10) DEFAULT 0,
	position C(7) DEFAULT 0,
	ordercode C(7) DEFAULT 0,
	description C(7) DEFAULT 0,
	qty C(7) DEFAULT 0,
	unit C(7) DEFAULT 0,
	unitprice C(7) DEFAULT 0,
	discount C(7) DEFAULT 0,
	tax C(7) DEFAULT 0,
	linetotal C(7) DEFAULT 0
";
$sqlarray = $adb->datadict->CreateTableSQL('crmnow_pdfcolums_sel', $flds);
$adb->datadict->ExecuteSQLArray($sqlarray);
$adb->query("insert into crmnow_pdfcolums_sel values (1,'Quotes','group','checked','','checked','checked','','checked','','checked','checked')");
$adb->query("insert into crmnow_pdfcolums_sel values (2,'Quotes','individual','checked','','checked','checked','','checked','','checked','checked')");
$adb->query("insert into crmnow_pdfcolums_sel values (3,'PurchaseOrder','group','checked','','checked','checked','','checked','','checked','checked')");
$adb->query("insert into crmnow_pdfcolums_sel values (4,'PurchaseOrder','individual','checked','','checked','checked','','checked','','checked','checked')");
$adb->query("insert into crmnow_pdfcolums_sel values (5,'SalesOrder','group','checked','','checked','checked','','checked','','checked','checked')");
$adb->query("insert into crmnow_pdfcolums_sel values (6,'SalesOrder','individual','checked','','checked','checked','','checked','','checked','checked')");
$adb->query("insert into crmnow_pdfcolums_sel values (7,'Invoice','group','checked','','checked','checked','','checked','','checked','checked')");
$adb->query("insert into crmnow_pdfcolums_sel values (8,'Invoice','individual','checked','','checked','checked','','checked','','checked','checked')");

$flds = "
	pdfid I(2) NOTNULL PRIMARY,
	pdfmodul C(13),
	fontid I(5) DEFAULT 0,
	fontsizebody I(2) DEFAULT 9,
	fontsizeheader I(2) DEFAULT 9,
	fontsizefooter I(2) DEFAULT 9,
	fontsizeaddress I(2) DEFAULT 9,
	dateused I(1) DEFAULT 0,
	spaceheadline I(1) DEFAULT 1,
	summaryradio C(5) DEFAULT 'true',
	gprodname C(5) DEFAULT 'true',
	gproddes C(5) DEFAULT 'true',
	gprodcom C(5) DEFAULT 'true',
	iprodname C(5) DEFAULT 'true',
	iproddes C(5) DEFAULT 'true',
	iprodcom C(5) DEFAULT 'true',
	pdflang C(5) DEFAULT 'ge_de',
	footerradio C(5) DEFAULT 'true',
	logoradio C(5) DEFAULT 'true',
	pageradio C(5) DEFAULT 'true',
	owner C(5) DEFAULT 'true',
	ownerphone C(5) DEFAULT 'true',
	poname C(5) DEFAULT 'true',
	clientid C(5) DEFAULT 'true',
	carrier C(5) DEFAULT 'true',
	paperf C(9) DEFAULT 'A4'
";
$sqlarray = $adb->datadict->CreateTableSQL('crmnow_pdfconfiguration', $flds);
$adb->datadict->ExecuteSQLArray($sqlarray);
$adb->query("insert into crmnow_pdfconfiguration values (0,'Quotes',0,8,8,7,9,1,0,'true','true','true','true','true','true','true','ge_de','false','false','false','false','false','false','false','false','A4')");
$adb->query("insert into crmnow_pdfconfiguration values (1,'PurchaseOrder',0,8,8,7,9,1,0,'true','true','true','true','true','true','true','ge_de','true','true','true','true','true','false','false','false','A4')");
$adb->query("insert into crmnow_pdfconfiguration values (2,'SalesOrder',0,8,8,7,9,1,0,'true','true','true','true','true','true','true','ge_de','true','true','true','false','false','false','false','false','A4')");
$adb->query("insert into crmnow_pdfconfiguration values (3,'Invoice',0,8,8,7,9,0,0,'true','true','true','true','true','true','true','ge_de','true','true','true','true','true','true','true','false','A4')");

$flds = "
	fontid I(2) NOTNULL PRIMARY,
	tcpdfname C(30),
	namedisplay C(50)
";
$sqlarray = $adb->datadict->CreateTableSQL('crmnow_pdffonts', $flds);
$adb->datadict->ExecuteSQLArray($sqlarray);
$adb->query("insert  into crmnow_pdffonts values (0,'dejavusans','Dejavu Sans')");
$adb->query("insert  into crmnow_pdffonts values (1,'dejavusansb','Dejavu Sans Bold')");
$adb->query("insert  into crmnow_pdffonts values (2,'dejavusansi','Dejavu Sans Italic')");
$adb->query("insert  into crmnow_pdffonts values (3,'dejavusansbi','Dejavu Sans Bold Italic')");
$adb->query("insert  into crmnow_pdffonts values (4,'dejavusanscondensed','Dejavu Sans Condensed')");
$adb->query("insert  into crmnow_pdffonts values (5,'dejavusanscondensedb','Dejavu Sans Condensed Bold')");
$adb->query("insert  into crmnow_pdffonts values (6,'dejavusanscondensedi','Dejavu Sans Condensed Italic')");
$adb->query("insert  into crmnow_pdffonts values (7,'dejavusanscondensedbi','Dejavu Sans Condensed Bold Italic')");
$adb->query("insert  into crmnow_pdffonts values (8,'dejavusans-extralight','Dejavu Sans Extra Light')");
$adb->query("insert  into crmnow_pdffonts values (9,'dejavusansi','Dejavu Sans Italic')");
$adb->query("insert  into crmnow_pdffonts values (10,'dejavusansmono','Dejavu Sans Mono')");
$adb->query("insert  into crmnow_pdffonts values (11,'dejavusansmonob','Dejavu Sans Mono Bold')");
$adb->query("insert  into crmnow_pdffonts values (12,'dejavusansmonoi','Dejavu Sans Mono Italic')");
$adb->query("insert  into crmnow_pdffonts values (13,'dejavusansmonobi','Dejavu Sans Bold Italic')");
$adb->query("insert  into crmnow_pdffonts values (14,'dejavuserif','Dejavu Serif')");
$adb->query("insert  into crmnow_pdffonts values (15,'dejavuserifb','Dejavu Serif Bold')");
$adb->query("insert  into crmnow_pdffonts values (16,'dejavuserifi','Dejavu Serif Italic')");
$adb->query("insert  into crmnow_pdffonts values (17,'dejavuserifbi','Dejavu Serif Bold Italic')");
$adb->query("insert  into crmnow_pdffonts values (18,'dejavuserifcondensed','Dejavu Serif Condensed')");
$adb->query("insert  into crmnow_pdffonts values (19,'dejavuserifcondensedb','Dejavu Serif Condensed Bold')");
$adb->query("insert  into crmnow_pdffonts values (20,'dejavuserifcondensedi','Dejavu Serif Condensed Italic')");
$adb->query("insert  into crmnow_pdffonts values (21,'dejavuserifcondensedbi','Dejavu Serif Condensed Bold Italic')");
$adb->query("insert  into crmnow_pdffonts values (22,'freemono','Free Mono')");
$adb->query("insert  into crmnow_pdffonts values (23,'freemonob','Free Mono Bold')");
$adb->query("insert  into crmnow_pdffonts values (24,'freemonoi','Free Mono Italic')");
$adb->query("insert  into crmnow_pdffonts values (25,'freemonobi','Free Mono Bold Italic')");
$adb->query("insert  into crmnow_pdffonts values (26,'freesans','Free Sans')");
$adb->query("insert  into crmnow_pdffonts values (27,'freesansb','Free Sans Bold')");
$adb->query("insert  into crmnow_pdffonts values (28,'freesansi','Free Sans Italic')");
$adb->query("insert  into crmnow_pdffonts values (29,'freesansbi','Free Sans Bold Italic')");
$adb->query("insert  into crmnow_pdffonts values (30,'freeserif','Free Serif')");
$adb->query("insert  into crmnow_pdffonts values (31,'freeserifb','Free Serif Bold')");
$adb->query("insert  into crmnow_pdffonts values (32,'freeserifi','Free Serif Italic')");
$adb->query("insert  into crmnow_pdffonts values (33,'freeserifbi','Free Serif Bold Italic')");
$adb->query("insert  into crmnow_pdffonts values (34,'helvetica','Helvetica')");
$adb->query("insert  into crmnow_pdffonts values (35,'helveticab','Helvetica Bold')");
$adb->query("insert  into crmnow_pdffonts values (36,'helveticai','Helvetica Italic')");
$adb->query("insert  into crmnow_pdffonts values (37,'helveticabi','Helvetica Bold Italic')");

$flds = "
	pdfieldid I(2) DEFAULT 0,
	pdffieldname C(19),
	pdfeditable I(1) DEFAULT 0,
	pdfmodul C(13)
";
$sqlarray = $adb->datadict->CreateTableSQL('crmnow_pdfsettings', $flds);
$adb->datadict->ExecuteSQLArray($sqlarray);
$adb->query("insert  into crmnow_pdfsettings values (1,'pdflang',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (2,'fontid',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (3,'fontsizeheader',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (4,'fontsizeaddress',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (5,'fontsizebody',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (6,'fontsizefooter',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (7,'logoradio',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (8,'dateused',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (9,'spaceheadline',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (10,'footerradio',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (11,'pageradio',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (12,'summaryradio',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (13,'gprodname',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (14,'gproddes',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (15,'gprodcom',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (16,'iprodname',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (17,'iproddes',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (18,'iprodcom',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (19,'gcolumns',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (20,'icolumns',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (21,'owner',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (22,'ownerphone',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (23,'pdflang',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (24,'fontid',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (25,'fontsizeheader',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (26,'fontsizeaddress',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (27,'fontsizebody',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (28,'fontsizefooter',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (29,'logoradio',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (30,'poname',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (31,'spaceheadline',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (32,'footerradio',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (33,'pageradio',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (34,'summaryradio',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (35,'gprodname',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (36,'gproddes',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (37,'gprodcom',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (38,'iprodname',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (39,'iproddes',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (40,'iprodcom',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (41,'gcolumns',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (42,'icolumns',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (43,'owner',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (44,'ownerphone',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (45,'pdflang',0,'PurchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (46,'fontid',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (47,'fontsizeheader',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (48,'fontsizeaddress',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (49,'fontsizebody',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (50,'fontsizefooter',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (51,'logoradio',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (52,'dateused',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (53,'spaceheadline',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (54,'footerradio',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (55,'pageradio',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (56,'summaryradio',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (57,'gprodname',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (58,'gproddes',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (59,'gprodcom',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (60,'iprodname',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (61,'iproddes',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (62,'iprodcom',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (63,'gcolumns',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (64,'icolumns',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (65,'owner',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (66,'ownerphone',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (67,'poname',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (68,'clientid',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (69,'carrier',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (70,'pdflang',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (71,'fontid',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (72,'fontsizeheader',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (73,'fontsizeaddress',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (74,'fontsizebody',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (75,'fontsizefooter',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (76,'logoradio',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (77,'dateused',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (78,'spaceheadline',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (79,'footerradio',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (80,'pageradio',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (81,'summaryradio',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (82,'gprodname',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (83,'gproddes',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (84,'gprodcom',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (85,'iprodname',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (86,'iproddes',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (87,'iprodcom',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (88,'gcolumns',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (89,'icolumns',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (90,'owner',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (91,'ownerphone',0,'SalesOrder')");
$adb->query("insert  into crmnow_pdfsettings values (92,'paperf',0,'Quotes')");
$adb->query("insert  into crmnow_pdfsettings values (93,'paperf',0,'Invoice')");
$adb->query("insert  into crmnow_pdfsettings values (94,'paperf',0,'PuchaseOrder')");
$adb->query("insert  into crmnow_pdfsettings values (95,'paperf',0,'SalesOrder')");
?>