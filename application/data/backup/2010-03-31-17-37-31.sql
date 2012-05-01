-- MySQL dump 10.13  Distrib 5.1.44, for redhat-linux-gnu (i386)
--
-- Host: localhost    Database: biz_1
-- ------------------------------------------------------
-- Server version	5.1.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `access`
--

DROP TABLE IF EXISTS `access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `access` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT 'roleid',
  `privilege_id` int(10) unsigned NOT NULL COMMENT 'privilege id',
  PRIMARY KEY (`access_id`),
  KEY `role_id` (`role_id`),
  KEY `privilege_id` (`privilege_id`),
  CONSTRAINT `access_ibfk_3` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `access_ibfk_4` FOREIGN KEY (`privilege_id`) REFERENCES `privilege` (`privilege_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `access`
--

LOCK TABLES `access` WRITE;
/*!40000 ALTER TABLE `access` DISABLE KEYS */;
/*!40000 ALTER TABLE `access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account` (
  `account_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(320) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billing_address_line_1` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `billing_address_line_2` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `billing_address_line_3` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billing_address_line_4` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billing_city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billing_state` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billing_postal_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billing_country` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_address_line_1` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_address_line_2` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_address_line_3` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_address_line_4` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_state` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_postal_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_country` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `assigned_to` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created` int(11) NOT NULL,
  `branch_id` int(10) unsigned NOT NULL,
  `updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`account_id`),
  KEY `branch_id` (`branch_id`),
  KEY `created_by` (`created_by`),
  KEY `assigned_to` (`assigned_to`),
  CONSTRAINT `account_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `account_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `account_ibfk_3` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account`
--

LOCK TABLES `account` WRITE;
/*!40000 ALTER TABLE `account` DISABLE KEYS */;
INSERT INTO `account` VALUES (1,'Microsoft','1-884-790-2133','(794) 914-4651',NULL,'1-418-645-9326','sem@nequevenenatis.ca','P.O. Box 741, 731 Enim. Ave','P.O. Box 925, 4556 Tempor Rd.','7989 Ante Road','P.O. Box 981, 9412 Ultrices. St.','Scarborough','Northwest Territories','54924','Grenada','286-6948 Sagittis. Rd.','532-2812 Ante St.','P.O. Box 540, 8824 Vel Rd.','Ap #470-7209 Vestibulum Street','Quincy','Flevoland','8654FS','Yemen','imperdiet non, vestibulum nec, euismod in, dolor. Fusce',1,1,1227780195,1,1232847309),(2,'Borland','1-768-101-8673','(842) 549-7617',NULL,'1-555-769-3580','hendrerit.consectetuer.cursus@arcuSedeu.org','352-4322 Vestibulum Ave','177 Morbi St.','3819 Ornare Street','P.O. Box 466, 6006 Risus. St.','North Charleston','NB','R8S 1D1','Serbia and Montenegro','9369 Imperdiet St.','294-8426 Arcu. Ave','174-8016 Orci, Rd.','491-2154 Cum Ave','St. Petersburg','NB','37620','Holy See (Vatican City State)','nec, diam. Duis mi enim, condimentum eget,',1,1,1245442358,1,1232235384),(3,'Adobe','1-392-490-1388','(691) 324-2204',NULL,'1-942-122-9969','Aliquam.tincidunt@elitafeugiat.com','231-7831 Nonummy Avenue','4676 Per Ave','2864 Tortor. St.','Ap #282-4160 Pellentesque Avenue','Valdosta','Sark','06848','Montserrat','277-6058 Vitae Rd.','6271 Urna. St.','4767 Pellentesque Rd.','P.O. Box 711, 1952 Lobortis St.','New London','Friesland','ZL23 6US','Reunion','penatibus et magnis dis parturient montes, nascetur',1,1,1220911181,1,1242352846),(4,'Macromedia','1-456-116-5391','(268) 806-0197',NULL,'1-421-720-3920','in@Pellentesqueut.org','P.O. Box 146, 4387 Cubilia Rd.','P.O. Box 338, 9802 Eleifend St.','P.O. Box 347, 8927 Vulputate Avenue','P.O. Box 993, 3766 At Rd.','Keene','Gelderland','V7F 3C3','Guam','388-4162 Mauris Road','P.O. Box 439, 4021 Congue, St.','2959 Facilisi. Ave','Ap #379-7411 Mauris. St.','Kenosha','Delaware','FY9 5IS','Rwanda','ut erat. Sed nunc est, mollis',1,1,1233444789,1,1221950638),(5,'Borland','1-840-605-8953','(321) 832-6226',NULL,'1-643-233-5349','vehicula@necdiam.ca','3548 Semper Av.','Ap #745-2499 Ut St.','173-3349 Tempus St.','1165 Orci. Ave','Baton Rouge','Northwest Territories','1328TQ','Albania','P.O. Box 858, 4623 Nulla. Road','4479 Erat Avenue','Ap #765-7686 Magnis Ave','763-1275 Morbi St.','Basin','NL','5253SH','Georgia','vel pede blandit congue. In scelerisque',1,1,1274313065,1,1232122596),(6,'Adobe','1-901-294-6290','(924) 504-6315',NULL,'1-619-614-3591','ipsum.Curabitur@Cras.edu','Ap #451-8620 Duis Av.','P.O. Box 307, 7979 Pede. Road','523-8149 Nulla Rd.','9895 Nec St.','Newark','Noord Holland','4208DW','Cameroon','4392 Ornare. St.','5456 Faucibus Road','442-5777 Fringilla Avenue','Ap #217-1935 Euismod Av.','Muncie','Northwest Territories','GD7F 5LP','Kyrgyzstan','mi. Duis risus odio, auctor vitae, aliquet nec, imperdiet nec,',1,1,1226458168,1,1233975908),(7,'Borland','1-433-305-3789','(234) 238-6786',NULL,'1-837-349-0456','feugiat.non.lobortis@pede.edu','Ap #811-4547 Semper Rd.','9689 Enim. Avenue','8576 Risus. St.','824-556 Ut Ave','Decatur','PEM','WT34 5VZ','Marshall Islands','8613 Ante Avenue','4486 Facilisis Rd.','388-6287 Vivamus Rd.','Ap #995-1806 Gravida Rd.','Johnson City','Gr.','A3B 7D8','Haiti','ut, pellentesque eget, dictum placerat, augue. Sed molestie. Sed id',1,1,1255759118,1,1257011869),(8,'Microsoft','1-360-821-8425','(609) 524-9020',NULL,'1-749-591-9157','Quisque@tempuseu.org','Ap #163-5858 Nullam Road','Ap #167-1740 Dui. Ave','P.O. Box 883, 7572 Convallis Rd.','246-1627 Mollis Rd.','College Park','LEX','10961','Mozambique','1512 Ac Street','P.O. Box 575, 1664 Luctus Avenue','823-9652 Mus. Av.','623-7845 Aenean Avenue','St. Petersburg','Zld.','6427GD','Slovakia','vehicula. Pellentesque tincidunt tempus risus. Donec egestas. Duis',1,1,1228643298,1,1250205726),(9,'Google','1-927-441-0937','(245) 970-5330',NULL,'1-649-546-7499','nec@eu.com','684-5916 A St.','P.O. Box 767, 8614 Nec, Av.','295-1097 Ac Av.','Ap #496-2988 Rutrum Av.','Woburn','OFF','97458','Gibraltar','P.O. Box 509, 4372 In Rd.','Ap #197-5125 Ullamcorper. Ave','951 Erat. Rd.','781-7435 Viverra. Av.','Cheyenne','Fr.','YN7K 9IB','Liberia','non magna. Nam ligula elit,',1,1,1271780926,1,1232201520),(10,'Adobe','1-563-412-8968','(373) 639-6762',NULL,'1-876-809-6231','egestas@penatibusetmagnis.edu','P.O. Box 884, 6376 Gravida Avenue','338-7687 Ante. St.','496-8078 Et St.','Ap #469-1443 Quis Street','Ocean City','New Mexico','VX44 8OV','Benin','772-174 Proin Rd.','700-8701 Vivamus Rd.','P.O. Box 290, 2546 Lorem Street','590-6823 Feugiat St.','Gilette','Gld.','Z43 9OV','Ghana','orci. Ut semper',1,1,1218648563,1,1231340669),(11,'Borland','1-300-726-7707','(390) 568-0598',NULL,'1-491-877-5745','erat.vitae@Namligulaelit.org','722-3519 Eu, Street','192-5208 Pede Avenue','Ap #478-8854 A Avenue','5906 Purus. Av.','Normal','Groningen','L7K 2B4','Bouvet Island','Ap #271-5336 Non, Rd.','713-4398 Vestibulum Rd.','P.O. Box 875, 5857 Faucibus. Street','172-715 Mauris. St.','Warwick','ID','M87 9CJ','Iceland','erat vitae risus. Duis a mi fringilla mi',1,1,1272400204,1,1217604591),(12,'Yahoo','1-597-413-8008','(242) 929-0146',NULL,'1-326-506-2360','vitae.nibh.Donec@quis.org','333-9866 Dictum. Avenue','587-4272 Pellentesque Road','781-9717 Etiam Rd.','P.O. Box 856, 4949 Luctus, St.','Diamond Bar','PE','X3H 9N7','Germany','5347 Sed, St.','Ap #789-7529 Aliquet, Ave','P.O. Box 940, 957 Lobortis, Rd.','P.O. Box 860, 9117 Integer Rd.','Laughlin','SK','B9B 6N7','Bermuda','justo. Praesent luctus.',1,1,1269792572,1,1259479087),(13,'Apple Systems','1-506-587-5771','(252) 510-3883',NULL,'1-705-322-6226','In@pharetrafeliseget.com','2599 Turpis Rd.','871-3514 Eu St.','428-8526 Auctor Avenue','Ap #417-3004 Arcu. Ave','Spokane Valley','MS','MK5T 7QF','Mauritania','Ap #469-5554 Et St.','913-520 Nisi. Ave','Ap #199-2615 Pretium Ave','566-3435 Molestie Av.','Hermitage','IL','A6Q 6Y3','Svalbard and Jan Mayen','iaculis aliquet diam. Sed diam lorem, auctor quis, tristique',1,1,1276244020,1,1235949273),(14,'Macromedia','1-266-969-0255','(284) 128-6235',NULL,'1-678-645-9201','non.dui.nec@orcisemeget.edu','P.O. Box 781, 8582 Felis. Street','Ap #273-352 Magna. Avenue','Ap #231-4020 Ipsum. St.','P.O. Box 120, 4452 Mi Av.','Omaha','Northwest Territories','42620','Mongolia','Ap #926-2300 Imperdiet St.','Ap #553-8713 Morbi St.','493-587 Eget, Rd.','Ap #393-724 Egestas Road','Jacksonville','NFK','83706','Samoa','dictum. Phasellus in felis. Nulla tempor augue',1,1,1235594211,1,1274220331),(15,'Macromedia','1-184-852-6363','(531) 247-6265',NULL,'1-938-431-1164','et.ultrices@turpis.org','P.O. Box 326, 7091 Sed St.','P.O. Box 719, 8696 Rutrum Ave','741-1315 Et, Road','Ap #847-573 Ullamcorper. Ave','Topeka','Ov.','32008','Saudi Arabia','Ap #602-9367 Aliquam Rd.','P.O. Box 232, 9554 Massa. Street','P.O. Box 512, 9823 Et Avenue','Ap #881-8648 Primis Rd.','Bend','British Columbia','Q1C 5GJ','New Zealand','montes, nascetur ridiculus mus. Proin',1,1,1244933604,1,1267089372),(16,'Borland','1-550-605-2168','(404) 836-2735',NULL,'1-346-747-9598','sem@eratSed.edu','679-3880 Ipsum Ave','694-9063 Ante St.','P.O. Box 618, 6682 Habitant Road','P.O. Box 378, 5879 Lobortis, Av.','Marshall','Nunavut','9792XA','Saint Kitts and Nevis','Ap #899-445 Turpis Avenue','Ap #131-6665 Libero Rd.','P.O. Box 162, 1772 Pulvinar Rd.','Ap #438-3412 Sed Avenue','Hickory','MOG','51753','Gabon','quam a felis ullamcorper',1,1,1226239293,1,1225477077),(17,'Adobe','1-937-303-1548','(271) 411-7132',NULL,'1-376-953-0088','Nulla.semper.tellus@Aliquam.ca','P.O. Box 829, 4670 Taciti Avenue','P.O. Box 256, 3708 Nec, Avenue','P.O. Box 566, 7041 Nulla Road','978-7750 Ac Av.','Biloxi','Co. Armagh','T1A 2B9','Japan','5787 Primis Street','845-1533 Nibh Road','8549 Sed Av.','3701 Gravida Ave','Las Cruces','Northamptonshire','6169YF','Korea','rutrum urna, nec',1,1,1274354357,1,1263062060),(18,'Lavasoft','1-726-490-0060','(985) 338-6690',NULL,'1-711-877-8786','sed.turpis.nec@hendreritidante.ca','P.O. Box 908, 2906 Tincidunt Rd.','P.O. Box 639, 1171 Ultrices Av.','2195 Magna. Road','4752 Diam. Av.','Alhambra','NB','D2D 1DK','Egypt','930 Imperdiet Rd.','3743 Turpis Street','138-7390 Tellus Ave','Ap #853-2680 Mollis Street','Chico','LAN','9887ZA','Antigua and Barbuda','porttitor scelerisque neque. Nullam nisl. Maecenas malesuada fringilla est.',1,1,1247209122,1,1215261223),(19,'Finale','1-829-522-4012','(112) 958-4257',NULL,'1-246-866-7002','Pellentesque@purusMaecenas.com','8760 Dolor Road','6999 Nec Avenue','P.O. Box 169, 3469 In Av.','P.O. Box 665, 345 Lectus Rd.','Salt Lake City','Friesland','Q8N 4T0','Hungary','Ap #595-9851 In Ave','356-407 Est Avenue','5831 Egestas Street','7105 Euismod Street','Palos Verdes Estates','ON','4800JZ','Russian Federation','ipsum sodales purus,',1,1,1219276075,1,1263603576),(20,'Sibelius','1-615-627-9079','(477) 727-9800',NULL,'1-933-943-3031','velit.justo@laoreet.com','636-4737 Mauris Ave','254-6911 Felis St.','Ap #414-9969 Gravida Avenue','159-3421 Mus. Road','San Luis Obispo','Ontario','U72 2DF','Lithuania','P.O. Box 318, 5489 Sed Avenue','P.O. Box 746, 6297 Vitae, Street','782-7199 Nisl St.','Ap #524-3696 Taciti Rd.','Montgomery','HAM','51005','Faroe Islands','diam eu dolor',1,1,1275034690,1,1251216467),(21,'Macromedia','1-366-727-5795','(848) 267-6900',NULL,'1-969-572-5418','varius.Nam@euaccumsan.edu','Ap #642-7505 Egestas. Av.','P.O. Box 144, 5519 Amet Road','P.O. Box 857, 9131 Sit Av.','Ap #276-3272 Pellentesque Av.','Gulfport','SK','B7U 8X2','Zimbabwe','7638 Ac, Ave','Ap #319-5370 Laoreet Ave','6402 Risus. Road','506-5778 Metus. Ave','Monterey','Tayside','R1W 5U8','Equatorial Guinea','orci. Donec nibh. Quisque nonummy',1,1,1224200677,1,1242781873),(22,'Altavista','1-199-700-4089','(872) 470-0364',NULL,'1-712-616-8076','lacus@etlibero.org','6789 Faucibus Avenue','Ap #570-2260 Mi Avenue','972-6418 Tempor Street','159-6930 Dui. Road','Waterbury','WA','O25 2AM','Seychelles','Ap #876-9568 Egestas Ave','905 Cum Rd.','8083 In, Avenue','Ap #424-1719 Sem, St.','Aberdeen','Colorado','O6 2GN','Denmark','Ut nec urna et arcu imperdiet ullamcorper. Duis at',1,1,1243730731,1,1252350552),(23,'Lycos','1-672-752-3196','(448) 349-2319',NULL,'1-137-307-5278','est.tempor@etmagna.edu','Ap #343-8317 Ipsum Av.','122-8733 Est, Rd.','P.O. Box 168, 1412 Metus St.','Ap #783-3848 At St.','Los Angeles','Cheshire','QL7M 6KT','Nigeria','Ap #415-5666 Vehicula. Rd.','Ap #378-1322 Lacinia Road','Ap #979-5953 Ac Av.','Ap #596-996 Cursus Ave','Lexington','U.','V8K 4Q7','Antigua and Barbuda','erat volutpat. Nulla facilisis. Suspendisse commodo tincidunt nibh. Phasellus nulla.',1,1,1227469828,1,1223186780),(24,'Sibelius','1-375-696-4708','(479) 469-1105',NULL,'1-576-462-6199','libero.at.auctor@molestiearcuSed.ca','483-1879 Pede. St.','2178 Pede, Av.','P.O. Box 440, 7345 Posuere Rd.','P.O. Box 975, 5137 Nulla Av.','Marietta','WY','43385','Paraguay','9817 Nulla Rd.','861-2729 Aliquam St.','P.O. Box 781, 5738 Lorem, St.','6157 Proin Rd.','Coral Springs','Co. Cavan','2891WB','Colombia','dolor dapibus gravida.',1,1,1259904128,1,1221682147),(25,'Yahoo','1-318-733-9120','(132) 327-6249',NULL,'1-706-115-3662','Nullam.scelerisque@dapibusrutrum.org','Ap #978-1799 Vitae Av.','Ap #952-2554 Ornare St.','838 Porta Ave','1576 Vitae Av.','Latrobe','Friesland','C8C 7U2','Guadeloupe','133-6354 Pellentesque, Av.','Ap #215-8469 Auctor, Avenue','538-6219 Eget Street','Ap #987-2644 Sodales Ave','Flint','Texas','RY6 3XR','Algeria','dapibus',1,1,1267104998,1,1265498653),(26,'Lavasoft','1-952-818-5058','(128) 174-3543',NULL,'1-729-927-6518','Suspendisse.aliquet.sem@loremsemper.edu','Ap #659-6416 Sagittis St.','2104 Viverra. Rd.','Ap #172-1985 Pellentesque Rd.','3189 Aliquet Road','Gallup','N.-Br.','4249XB','Finland','295-5320 Quisque Avenue','Ap #261-7154 A, Street','P.O. Box 629, 923 Parturient Rd.','455-4659 Aenean St.','Hudson','Manitoba','19160','Iceland','Vestibulum ante ipsum primis',1,1,1275654434,1,1235290726),(27,'Lycos','1-838-352-1722','(426) 747-7194',NULL,'1-911-183-5377','sem@mi.ca','P.O. Box 428, 3718 Pede St.','Ap #922-4775 Vehicula. Ave','369-5863 Ut Ave','9054 Dignissim Street','Greensboro','MGM','IC7S 4ZL','Benin','P.O. Box 618, 1911 Semper Avenue','7954 Felis St.','P.O. Box 635, 1104 Lacus. Av.','P.O. Box 658, 6865 Etiam Rd.','Westfield','N.-H.','2758XA','Western Sahara','lacinia mattis. Integer eu lacus. Quisque imperdiet, erat',1,1,1247511493,1,1261049576),(28,'Lycos','1-894-277-4422','(322) 101-6073',NULL,'1-982-661-4160','molestie@congueturpisIn.org','P.O. Box 139, 7056 Mauris Rd.','6809 Cras Road','1391 Bibendum. St.','Ap #118-4129 Metus Rd.','San Francisco','Alberta','S6Q 8S3','Eritrea','5034 Facilisis Street','Ap #429-3873 Ridiculus Av.','9341 A, Road','722 Lorem Av.','Aliso Viejo','L.','R2 6ZG','Brazil','sed orci lobortis augue',1,1,1218447310,1,1275241825),(29,'Lycos','1-527-457-9205','(226) 524-2732',NULL,'1-913-184-0966','aliquet.vel@nonenimMauris.org','718-8888 Sed Av.','P.O. Box 633, 4295 Interdum. Avenue','Ap #239-3109 Sed St.','280-1125 Et St.','Eau Claire','OR','R4W 5J7','Palau','P.O. Box 951, 8282 Dolor, St.','Ap #422-6906 Litora Street','5444 Tortor Rd.','5025 Neque St.','Rawlins','Clackmannanshire','K1H 5A1','Saint Vincent and The Grenadines','felis eget varius',1,1,1273118405,1,1243426532),(30,'Chami','1-466-277-5800','(818) 299-1750',NULL,'1-170-247-6405','mi.lorem.vehicula@parturient.org','P.O. Box 835, 3661 A, Street','Ap #148-8411 Ut, Ave','2742 Gravida Avenue','293-9880 Ut Avenue','Kennewick','Kentucky','87261','Kenya','Ap #238-1902 Condimentum Rd.','967-678 Egestas St.','P.O. Box 265, 8639 Vitae St.','6602 Mi Street','West Memphis','YT','O91 6CQ','Sierra Leone','non massa non ante bibendum ullamcorper.',1,1,1221482835,1,1263368801),(31,'Lycos','1-205-432-9236','(403) 962-2417',NULL,'1-184-722-2672','volutpat@Donec.edu','P.O. Box 968, 1960 Mus. Av.','P.O. Box 221, 3013 Blandit Road','Ap #389-5448 Phasellus Road','728-1584 Vestibulum Road','Oxford','Wyoming','RW68 6ZC','Saint Helena','P.O. Box 829, 2958 Molestie Avenue','Ap #179-7364 Nec St.','Ap #365-728 Neque Ave','799-9569 In Avenue','Duncan','IL','VW31 5KV','Nicaragua','neque pellentesque',1,1,1269836297,1,1241868215),(32,'Adobe','1-706-564-3968','(445) 519-2133',NULL,'1-121-924-5213','sit@arcuac.com','Ap #113-8057 Tincidunt St.','206-9053 Etiam Road','P.O. Box 564, 4349 Nisl. St.','Ap #663-5850 Rutrum Rd.','Waltham','TX','12898','Finland','P.O. Box 661, 9880 Fusce Avenue','884-7357 Erat St.','999-5903 Morbi Rd.','8653 A, St.','Lock Haven','Nottinghamshire','85955','Guatemala','mi. Duis risus odio, auctor vitae, aliquet',1,1,1221522219,1,1240247495),(33,'Apple Systems','1-395-851-5475','(939) 621-0397',NULL,'1-428-638-3656','fringilla.mi@acmattis.org','P.O. Box 631, 8588 Ante. St.','P.O. Box 708, 1967 Velit Rd.','P.O. Box 790, 7514 Faucibus Av.','7262 Sit St.','Reading','Zld.','4350FI','Micronesia','8433 Turpis. St.','Ap #651-8622 Convallis Avenue','Ap #524-9576 Donec Rd.','1529 Eget Rd.','Geneva','YT','6533JF','Guam','eget varius ultrices, mauris ipsum porta elit, a feugiat tellus',1,1,1233838080,1,1228621055),(34,'Macromedia','1-704-817-4618','(132) 114-7529',NULL,'1-917-328-8259','pharetra.Quisque.ac@Aliquamvulputateullamcorper.edu','Ap #846-9989 Vitae Av.','754-9413 Rutrum Avenue','Ap #175-4178 Mi St.','Ap #437-3031 Magna. St.','San Francisco','Arkansas','S9A 7K9','Azerbaijan','Ap #383-5683 Ut Av.','P.O. Box 716, 3049 Parturient Ave','P.O. Box 816, 1224 Etiam Avenue','P.O. Box 877, 6657 Tempor Rd.','Eden Prairie','L.','2778LL','Armenia','erat eget',1,1,1255791067,1,1238603278),(35,'Macromedia','1-617-131-2181','(631) 707-8920',NULL,'1-824-781-2422','Nullam.scelerisque.neque@magnaUt.ca','P.O. Box 952, 5749 Amet, Ave','3987 Velit. Rd.','P.O. Box 179, 8558 Hymenaeos. Avenue','Ap #630-5811 Varius. St.','La Crosse','Utrecht','E0V 1RB','Palau','4915 Ante, Rd.','113-1676 Vel, St.','Ap #584-585 Mollis. Rd.','3885 Consequat Street','Edina','North Dakota','QJ1P 8VQ','Congo','a, magna. Lorem ipsum dolor sit amet, consectetuer adipiscing',1,1,1243565413,1,1216935123),(36,'Altavista','1-408-374-6904','(866) 649-0975',NULL,'1-980-321-7231','vitae@ipsum.edu','Ap #121-4201 Erat Road','P.O. Box 869, 4821 Eu Av.','Ap #582-6296 Cras Av.','Ap #219-7495 Tempor Rd.','Rehoboth Beach','Wisconsin','I2A 7I8','Spain','120-8954 Ipsum. Road','P.O. Box 454, 1680 Dapibus Rd.','4453 Duis St.','P.O. Box 470, 9492 Tortor. Av.','Michigan City','DFS','75174','Bhutan','id, blandit at, nisi. Cum',1,1,1238946748,1,1273569269),(37,'Lavasoft','1-508-561-2474','(325) 770-2467',NULL,'1-501-775-5873','taciti.sociosqu@loremluctus.org','5772 Diam Road','3981 Enim. Road','P.O. Box 104, 7756 Pharetra Rd.','Ap #200-7028 Hendrerit. Ave','Richland','Gr.','03309','Kuwait','3857 Dolor Ave','126-5829 Risus St.','Ap #797-3511 A Rd.','Ap #969-5342 Magna St.','Overland Park','MB','41783','Saint Vincent and The Grenadines','Nulla tincidunt, neque',1,1,1224507842,1,1259518307),(38,'Lavasoft','1-167-560-4011','(959) 192-3924',NULL,'1-386-344-8744','ante@Etiam.edu','Ap #842-9984 Congue, Av.','Ap #628-8136 Fringilla Ave','P.O. Box 478, 1536 Aliquet Av.','P.O. Box 362, 9376 Facilisis, Ave','Lakeland','Quebec','X5Y 9D6','Japan','5589 Consectetuer Rd.','Ap #343-552 Ultricies Road','2158 Cubilia Rd.','655-804 Velit. Av.','Redondo Beach','Powys','B7S 9G3','Greenland','Aenean sed pede nec ante blandit viverra. Donec tempus, lorem',1,1,1224807884,1,1252369364),(39,'Adobe','1-698-390-3673','(816) 880-4043',NULL,'1-656-887-4565','risus.quis.diam@velvenenatis.com','719 Odio Ave','6833 Urna, St.','Ap #824-2431 Tellus Ave','Ap #317-8006 Ac St.','Frankfort','VT','MB2 2DS','Holy See (Vatican City State)','P.O. Box 830, 9286 Fames Road','Ap #258-6614 Euismod St.','8445 Aliquet. Avenue','722-7686 Sapien, St.','Manitowoc','Fl.','44023','Zambia','neque et nunc. Quisque',1,1,1237048985,1,1273992797),(40,'Sibelius','1-784-225-0691','(931) 323-1941',NULL,'1-510-468-8635','urna.et.arcu@Maurisvel.edu','362-7006 Ante Ave','P.O. Box 633, 4622 Duis Street','Ap #374-3602 Dui, Avenue','Ap #867-2221 Metus Av.','Cedar Falls','LDY','K58 1ZC','Chile','Ap #531-2217 Id, Rd.','6249 Mauris Av.','5209 Mauris Av.','538-5433 Risus. Avenue','Saint Paul','Nova Scotia','F8R 7G8','Mali','cursus',1,1,1232580670,1,1226486763),(41,'Altavista','1-400-422-6083','(478) 460-9308',NULL,'1-591-341-3472','Donec.feugiat@pedemalesuada.org','P.O. Box 716, 7088 Pretium Av.','439-4569 Mi St.','473-4589 Donec Road','458-1770 Vitae, Avenue','Stamford','Gld.','21496','Kuwait','5422 Luctus St.','8971 Bibendum. Street','930-758 Nisl. Avenue','Ap #519-8205 Dictum Rd.','Kent','LA','H4B 3Z4','Lithuania','velit. Quisque varius. Nam porttitor scelerisque neque. Nullam nisl. Maecenas',1,1,1252571292,1,1260412467),(42,'Microsoft','1-931-731-0722','(768) 608-4773',NULL,'1-628-158-6030','magnis.dis.parturient@miDuis.edu','P.O. Box 521, 3312 Lobortis, St.','P.O. Box 332, 8541 A St.','885-8789 Nibh. St.','P.O. Box 577, 5539 Lorem, Rd.','Parker','MGY','6370UY','Antigua and Barbuda','P.O. Box 222, 1077 Arcu Av.','5406 Dui St.','P.O. Box 845, 6565 Ipsum Street','526-1942 Duis Rd.','Laguna Woods','Mississippi','US6Y 8QY','Monaco','est, mollis',1,1,1248233287,1,1235106656),(43,'Apple Systems','1-437-385-9558','(243) 352-5581',NULL,'1-171-711-2141','a@consectetueradipiscing.edu','P.O. Box 927, 1124 Augue Rd.','Ap #326-3960 Convallis St.','7019 Ante Rd.','Ap #157-1759 Penatibus St.','Laurel','Groningen','SE6 6HO','Puerto Rico','P.O. Box 135, 3431 Malesuada Av.','Ap #216-4838 Ac Ave','9334 Primis Street','441-4875 Sit Rd.','Walla Walla','FL','86080','Barbados','tempus, lorem fringilla ornare placerat, orci lacus',1,1,1214228369,1,1221461920),(44,'Chami','1-583-771-6686','(587) 259-2842',NULL,'1-250-802-4425','Lorem.ipsum@Inat.org','P.O. Box 854, 8212 In Avenue','Ap #573-4862 Lacus. Road','Ap #153-8881 At Rd.','996-1641 Adipiscing Street','Kona','Ontario','28606','Albania','873-6804 Est. Street','Ap #262-2075 Tempor Avenue','505-3925 Eget St.','1886 Imperdiet Road','San Rafael','Zuid Holland','9578HY','San Marino','Donec fringilla. Donec feugiat metus sit amet ante. Vivamus',1,1,1221793409,1,1235852588),(45,'Microsoft','1-701-134-0918','(306) 126-9592',NULL,'1-228-773-2986','nec.eleifend.non@dolorsit.org','Ap #994-5441 Nec Road','Ap #192-3535 Nisi Street','428-6395 Et Ave','3472 Orci. Rd.','Everett','Alabama','U7E 7IQ','Anguilla','813-4548 Leo. Ave','P.O. Box 214, 7288 Ridiculus St.','P.O. Box 563, 1333 Feugiat Rd.','Ap #738-4583 Nisi St.','Morgan City','Virginia','74611','Solomon Islands','orci luctus et',1,1,1214085767,1,1228381373),(46,'Google','1-746-646-4110','(429) 644-8195',NULL,'1-902-485-3155','Cras.lorem.lorem@diam.ca','725-1937 Ultrices St.','351-6622 Nulla. St.','148-7168 Lacus, Ave','2908 Consectetuer St.','Jersey City','U.','8075GC','Niger','603-1267 Lorem, Road','7568 Sed Rd.','P.O. Box 174, 3413 Tristique Rd.','P.O. Box 304, 2581 Tempor Avenue','Sunnyvale','Ontario','85046','Spain','ridiculus',1,1,1227181151,1,1248663701),(47,'Microsoft','1-149-261-9460','(838) 994-5003',NULL,'1-550-329-8743','nec.mauris@ligulaelitpretium.ca','Ap #839-3945 Maecenas Ave','6292 Feugiat. Ave','P.O. Box 263, 7380 Ac Ave','690-5401 Semper. Rd.','Miami','ROX','W5Z 7Y7','Honduras','3831 Nibh Road','P.O. Box 600, 7049 Nunc St.','P.O. Box 961, 914 Lacus. St.','Ap #651-1440 Eu Road','Woodruff','Gld.','A3 6KU','Saudi Arabia','a felis ullamcorper viverra. Maecenas',1,1,1235422446,1,1215949114),(48,'Macromedia','1-557-787-3819','(397) 722-6895',NULL,'1-537-831-2414','natoque.penatibus.et@Donecconsectetuermauris.com','7218 Erat Road','4204 Semper, Av.','Ap #790-4086 Nullam Ave','656 Libero. Ave','Beaumont','NH','MB3 8WC','Ghana','Ap #257-8402 At, St.','P.O. Box 993, 6607 Orci, Avenue','Ap #845-9816 Gravida. St.','P.O. Box 180, 5440 Enim Rd.','Pottsville','AK','WI8 7TU','Uzbekistan','amet, faucibus ut, nulla. Cras eu tellus eu',1,1,1267467254,1,1266955686),(49,'Finale','1-694-451-2899','(679) 420-1399',NULL,'1-758-265-5842','sollicitudin@ipsum.com','Ap #684-8732 Mollis Avenue','Ap #545-6336 Sapien, Street','2782 Metus. Rd.','P.O. Box 124, 6189 Nec Ave','Coatesville','British Columbia','04563','Chile','Ap #603-8612 Velit Avenue','P.O. Box 725, 1074 Mauris Road','Ap #391-3867 Nec, Ave','4187 Habitant St.','Aguadilla','Nevada','V1 4KZ','Netherlands','erat. Sed nunc est, mollis non, cursus non,',1,1,1221745477,1,1263077838),(50,'Macromedia','1-801-492-1835','(331) 144-8754',NULL,'1-398-926-4343','amet.ornare.lectus@ultricesDuis.org','2601 Sed St.','933-4816 Mattis. Street','3586 Urna, Road','4182 Donec Rd.','Huntington Park','YKS','O7 9SN','Cook Islands','Ap #288-3309 Molestie Av.','595-7361 Mauris Rd.','P.O. Box 236, 9512 Congue, Street','P.O. Box 319, 8238 Amet, Av.','Arvada','Utrecht','G8I 6Z9','Sweden','sem magna nec quam. Curabitur vel',1,1,1224272546,1,1263618840),(51,'Macromedia','1-168-579-7533','(521) 197-8757',NULL,'1-117-558-4401','massa.Integer@gravidanuncsed.org','215-6239 Justo Street','8644 Velit. Rd.','Ap #600-5479 Tempus, Av.','Ap #344-264 Purus. Street','Bend','NYK','DH0H 9TX','Senegal','Ap #311-1358 Lectus. Rd.','710-6779 Donec Road','337-2433 Egestas, Ave','3121 Parturient St.','Delta Junction','WIL','2385MZ','Turks and Caicos Islands','natoque penatibus et magnis dis parturient montes, nascetur ridiculus',1,1,1265367219,1,1246596540),(52,'Altavista','1-843-526-7642','(241) 107-2488',NULL,'1-155-654-6732','Aenean.massa@miDuisrisus.org','739-352 Malesuada Rd.','864-8447 Bibendum Av.','P.O. Box 982, 4634 Gravida Rd.','4861 Non St.','Richland','Gld.','O4L 3L0','Bolivia','4207 Semper Avenue','132-262 Et, St.','309-4805 Tellus. St.','Ap #788-3690 Est, Rd.','New Britain','Gelderland','27763','Solomon Islands','lacus.',1,1,1264905360,1,1245120142),(53,'Adobe','1-157-795-5122','(850) 999-5223',NULL,'1-107-655-8324','ornare.facilisis@aliquet.edu','Ap #192-8263 Dolor Rd.','984-9178 Et, Avenue','599-1512 Ullamcorper, Avenue','P.O. Box 543, 3025 Donec Rd.','Rock Springs','Kent','1369FO','French Southern Territories','Ap #175-9175 Malesuada Rd.','Ap #826-4062 Gravida. Street','P.O. Box 287, 2241 Enim. St.','7104 Molestie. Rd.','Cape Girardeau','Utrecht','N5 1PL','Grenada','enim. Nunc ut erat. Sed nunc est, mollis non,',1,1,1270629418,1,1249274853),(54,'Cakewalk','1-909-230-7959','(503) 414-5379',NULL,'1-816-749-8057','vitae.sodales@Duisdignissimtempor.org','254-1460 Sed Rd.','P.O. Box 723, 521 Varius. Ave','Ap #634-8736 Vitae, St.','P.O. Box 286, 1740 Tincidunt Road','Macomb','OH','32146','Slovenia','8735 Venenatis Road','427-769 Ac Rd.','125-9489 Malesuada Road','Ap #239-8948 Bibendum. St.','Rock Island','Fl.','9428XT','Georgia','In lorem. Donec',1,1,1241253359,1,1215081567),(55,'Lycos','1-469-773-6165','(148) 320-9326',NULL,'1-958-677-6428','eu.enim.Etiam@tortorNunc.com','3229 Mi St.','220-4264 Neque. Rd.','620-7628 Nulla. Road','Ap #393-134 Nec St.','Fort Dodge','Noord Brabant','50887','Jamaica','7973 Phasellus Avenue','144-1188 Aliquam Road','611-419 Dignissim Ave','P.O. Box 136, 3595 Rutrum Road','Sioux Falls','Powys','T1B 6J3','United Kingdom','rutrum eu, ultrices sit amet,',1,1,1252889247,1,1216407710),(56,'Finale','1-953-575-2858','(297) 642-7438',NULL,'1-870-440-1223','non.lorem.vitae@rhoncusidmollis.org','5522 Tincidunt Ave','Ap #315-4263 Luctus Ave','Ap #129-1645 Ornare, Ave','P.O. Box 757, 9599 Purus, Avenue','Hammond','Louisiana','50813','Liechtenstein','1776 Aliquam Rd.','P.O. Box 501, 5974 Ornare, Rd.','168-8346 Sed Rd.','100-3261 Mauris St.','Ephraim','Missouri','65988','Macedonia','Proin',1,1,1272284208,1,1268659876),(57,'Borland','1-915-596-4623','(519) 540-6249',NULL,'1-842-849-1625','velit.Cras@semutcursus.org','P.O. Box 315, 9912 Pretium Ave','P.O. Box 573, 2473 Proin Ave','7750 Cras Rd.','Ap #518-1607 Purus Av.','Lynwood','NH','5057BD','Latvia','Ap #707-2165 Mauris, Rd.','P.O. Box 584, 1000 Pede Rd.','Ap #632-7675 Nisl. Ave','P.O. Box 570, 3073 Adipiscing. St.','Oxnard','Co. Londonderry','29446','United Arab Emirates','rhoncus id, mollis nec, cursus a, enim. Suspendisse',1,1,1261013084,1,1216972376),(58,'Apple Systems','1-785-448-1776','(335) 946-3323',NULL,'1-915-669-9503','In.tincidunt@feugiat.edu','P.O. Box 456, 5103 Praesent Ave','4919 At, Rd.','P.O. Box 977, 6788 Sit Road','Ap #588-8741 Diam. Road','Cudahy','LET','IT9S 2VJ','Bolivia','487-9404 Cum Avenue','757-3432 Dictum Av.','Ap #121-1367 Enim. Avenue','Ap #305-5702 Curabitur Avenue','Fullerton','Grampian','6786XP','Tonga','nec ante. Maecenas mi felis, adipiscing fringilla,',1,1,1230066260,1,1242091971),(59,'Finale','1-621-454-0326','(385) 885-4261',NULL,'1-865-721-8048','nunc@Duis.com','978-9975 Et Ave','6674 Vitae St.','P.O. Box 588, 9497 In Ave','5711 Sem Ave','Costa Mesa','Alabama','K9Z 8Y9','Algeria','Ap #323-5379 Sagittis. Av.','841-4966 Dapibus Av.','Ap #902-2019 Ornare, Road','P.O. Box 898, 5196 Pellentesque Street','Billings','Friesland','17166','United States Minor Outlying Islands','nisi. Cum',1,1,1267482559,1,1235941367),(60,'Cakewalk','1-792-513-4205','(975) 129-4117',NULL,'1-334-535-7243','augue.Sed@massa.ca','P.O. Box 704, 9842 Malesuada Avenue','Ap #386-6191 Consectetuer Rd.','P.O. Box 289, 4103 Convallis Street','Ap #357-9583 Ultricies Rd.','Bradford','Gelderland','I53 3IX','Bahamas','P.O. Box 930, 5940 Faucibus Rd.','818-493 Massa. Street','P.O. Box 614, 4443 Vitae Ave','419-4355 Praesent Av.','Pasco','Morayshire','A6D 6X4','Belarus','sit amet, dapibus id, blandit at, nisi.',1,1,1215031643,1,1263174036),(61,'Chami','1-983-535-4971','(811) 570-9348',NULL,'1-671-241-4959','aliquam.enim.nec@lobortisauguescelerisque.ca','201-3590 Sagittis. Rd.','Ap #776-4805 Nisi Avenue','668-2509 Ad Av.','4592 Molestie St.','Glens Falls','POW','R7 0UC','Mauritius','289-9247 Erat Rd.','Ap #413-9345 Mollis Rd.','190-7825 Tellus Road','P.O. Box 252, 6330 Vestibulum Avenue','Dubuque','BC','9258IF','Holy See (Vatican City State)','ante. Vivamus non lorem vitae odio sagittis',1,1,1264829515,1,1227332173),(62,'Microsoft','1-723-916-8326','(740) 452-4042',NULL,'1-661-344-7396','lacinia@liberoIntegerin.org','Ap #377-3028 Quis, St.','Ap #380-4068 Luctus St.','2832 Ultrices Rd.','6545 A, St.','DeKalb','Zeeland','93956','Fiji','613 Dui. Rd.','Ap #881-5002 Phasellus St.','336-1397 Non Avenue','4368 Dolor. Street','Redding','YT','4994ZB','New Caledonia','et nunc. Quisque ornare tortor at',1,1,1271611521,1,1240219484),(63,'Finale','1-844-168-4588','(584) 175-2506',NULL,'1-504-349-3860','ante.iaculis@risusDonec.org','6400 Consequat Avenue','P.O. Box 619, 3002 Condimentum. Av.','3922 Consequat Road','P.O. Box 536, 255 Commodo Avenue','La Habra','Ayrshire','D8Z 2L7','United States Minor Outlying Islands','P.O. Box 751, 6352 Augue Street','Ap #181-1593 Lorem St.','Ap #816-5903 Phasellus St.','608 Nec Av.','Apple Valley','Virginia','49025','Cyprus','cursus a, enim. Suspendisse',1,1,1256112108,1,1220739303),(64,'Lavasoft','1-617-504-3534','(796) 109-5246',NULL,'1-347-239-0176','ornare.libero.at@nullavulputatedui.ca','P.O. Box 372, 5169 Proin St.','316-1778 Commodo St.','7602 Rutrum Street','P.O. Box 151, 2167 Lacinia Road','Catskill','N.-H.','25050','Nauru','7871 Diam Rd.','Ap #740-7206 Ullamcorper St.','1886 Risus Ave','127-7954 Volutpat. St.','Chattanooga','NT','8677MS','Sierra Leone','in',1,1,1214375230,1,1218065489),(65,'Macromedia','1-266-479-2479','(847) 882-8950',NULL,'1-104-121-9900','sem.molestie@sedorcilobortis.org','8206 Cras St.','Ap #249-4857 Scelerisque Ave','Ap #490-9999 Non St.','P.O. Box 387, 9446 Nisl Rd.','Bellflower','ANS','10597','Gabon','2251 Natoque Avenue','Ap #935-8331 Mattis Rd.','P.O. Box 143, 238 Nunc Avenue','3454 Sed St.','Culver City','IA','E5Z 9R8','Luxembourg','eget varius ultrices, mauris',1,1,1251577464,1,1231121372),(66,'Cakewalk','1-644-425-6745','(430) 724-1996',NULL,'1-712-570-3638','dui.Fusce.diam@arcu.com','2789 Mauris Av.','Ap #697-6802 Pharetra Rd.','P.O. Box 650, 8391 Sed, Av.','Ap #322-5775 Mauris, Street','Buena Park','GAL','1634YZ','Burkina Faso','Ap #536-7620 Aliquet, Rd.','4742 Ante Street','P.O. Box 127, 1094 Accumsan St.','7652 Quis Rd.','Scranton','PE','GM1V 9XZ','Estonia','magna sed dui. Fusce aliquam, enim nec tempus',1,1,1264555306,1,1214653530),(67,'Lavasoft','1-247-199-5184','(187) 729-5713',NULL,'1-180-131-7346','bibendum.fermentum.metus@Nunc.org','P.O. Box 261, 5085 Ut Ave','449-9295 Pede St.','467-4363 Proin Street','944-4372 Erat Road','Nampa','South Yorkshire','G2F 7U4','Bahamas','Ap #531-7915 Id Road','Ap #558-1753 Ultrices Avenue','6779 Commodo Avenue','P.O. Box 982, 5591 Lorem Ave','Laguna Hills','Zeeland','M9V 9T9','Thailand','aliquet libero. Integer in',1,1,1226243952,1,1251103656),(68,'Lavasoft','1-365-627-0250','(949) 489-3279',NULL,'1-728-232-6590','nisl@nullaDonec.ca','1275 Ipsum. Road','P.O. Box 470, 8683 Nec Avenue','8236 Sagittis Street','3095 Cursus Rd.','Tok','RAD','39778','Spain','590 At, Ave','528-2386 Dui. Road','P.O. Box 300, 9841 Dolor St.','P.O. Box 632, 8653 Orci, Ave','Waycross','Co. Armagh','3034SS','Sierra Leone','non leo.',1,1,1232107319,1,1264577398),(69,'Macromedia','1-279-903-2167','(835) 122-3215',NULL,'1-980-235-9962','lacus.Quisque@Duisacarcu.org','P.O. Box 204, 2233 Aliquam Rd.','Ap #351-150 Nunc Avenue','170-7077 Pharetra, Rd.','Ap #117-2321 Fringilla, St.','Warner Robins','Fr.','0743LJ','Switzerland','Ap #335-7111 Congue. Road','Ap #795-5263 Diam. Avenue','279-1621 Donec Avenue','Ap #601-3646 Consectetuer Road','Saratoga Springs','Hawaii','29297','Christmas Island','fringilla euismod enim. Etiam gravida molestie arcu.',1,1,1271513098,1,1217726932),(70,'Yahoo','1-167-340-1704','(776) 333-5840',NULL,'1-508-700-7138','parturient.montes@loremsemper.org','6554 At Street','172-8061 Orci St.','P.O. Box 279, 2607 Eu Road','6301 Dui St.','Bethlehem','LA','J3U 1AC','Gabon','P.O. Box 122, 5759 Placerat St.','8727 Ut, Street','576-1161 Luctus Rd.','182-1405 Lorem Ave','Hampton','Dr.','QB8R 6CX','Korea, Republic of','Quisque ac libero nec ligula consectetuer rhoncus. Nullam velit dui,',1,1,1269221674,1,1227942091),(71,'Lycos','1-121-998-0856','(931) 971-3530',NULL,'1-287-879-4412','elit.Nulla@Uttincidunt.edu','P.O. Box 443, 9284 Praesent Road','938-2110 Purus Street','Ap #686-5114 Libero St.','398-6108 Enim Av.','Chelsea','Argyllshire','0079VC','French Guiana','883-8436 Metus Street','820-7737 Lacus. Road','497 Vitae Road','Ap #743-5962 Non, Rd.','Cruz Bay','Illinois','PC94 9YW','Greece','tincidunt dui augue eu tellus. Phasellus elit pede,',1,1,1254847326,1,1262647259),(72,'Lavasoft','1-548-304-3983','(876) 637-7982',NULL,'1-155-733-6218','lectus@sollicitudin.ca','354-3397 Rhoncus. Road','P.O. Box 398, 7527 Natoque St.','437-9305 Tellus St.','405-1555 Ligula Road','San Antonio','Utrecht','7960PT','Botswana','Ap #102-9954 Adipiscing Rd.','3583 Suspendisse Avenue','234-8483 Diam St.','9742 Lectus St.','Johnson City','U.','O3E 1JE','Austria','mi felis, adipiscing fringilla,',1,1,1272012089,1,1234791135),(73,'Adobe','1-573-919-5516','(936) 320-9207',NULL,'1-227-841-8537','lorem.ut.aliquam@Aeneangravida.ca','296-4715 Vestibulum Street','6366 Nonummy St.','Ap #346-3468 Integer St.','Ap #956-7277 Donec Avenue','Hawthorne','Utrecht','15130','Bhutan','Ap #869-8341 Molestie Av.','P.O. Box 278, 1331 Nisi St.','909 Est St.','482-9435 Nibh Ave','El Paso','Yukon','R9V 5T2','Nigeria','amet orci. Ut sagittis lobortis mauris. Suspendisse aliquet molestie tellus.',1,1,1243989720,1,1265615352),(74,'Adobe','1-850-961-6591','(328) 276-8325',NULL,'1-555-187-9174','id@Nulla.org','P.O. Box 321, 5312 Curabitur Rd.','2369 Nulla Rd.','5939 Nec Rd.','P.O. Box 484, 6462 Accumsan St.','New London','Co. Carlow','2694WM','Taiwan, Province of China','P.O. Box 249, 7993 Tincidunt, Street','Ap #983-8653 Mi, Ave','678-1725 Aenean Rd.','9984 Volutpat St.','Bandon','Gld.','8939UH','Saint Pierre and Miquelon','tincidunt tempus risus. Donec egestas.',1,1,1258444107,1,1220407244),(75,'Google','1-149-512-2162','(990) 426-0173',NULL,'1-277-261-9361','non.lacinia.at@leoVivamus.edu','910-8454 Ipsum. St.','7040 Blandit Av.','377-898 Id Avenue','P.O. Box 865, 3621 Dolor Rd.','Hartland','Nova Scotia','80278','Turks and Caicos Islands','Ap #878-6061 Proin Rd.','Ap #189-8068 Imperdiet St.','P.O. Box 372, 5055 Ante. Avenue','862-1961 Rhoncus. Rd.','Egg Harbor','Wisconsin','OI41 2PV','Honduras','feugiat. Sed nec metus facilisis',1,1,1221143363,1,1227644478),(76,'Microsoft','1-135-791-0423','(117) 480-0992',NULL,'1-714-671-8242','et.rutrum.eu@nequeIn.com','993-8760 Ligula Rd.','4172 Fringilla St.','Ap #520-7934 Ut, Av.','663-1867 Ut Ave','Bartlesville','Kansas','I6 8IM','Mali','7965 Sodales Rd.','197-8955 Facilisis Rd.','Ap #281-6343 Massa Rd.','Ap #504-3722 Et Rd.','North Adams','ON','K5 6FW','Bangladesh','arcu. Sed et libero. Proin',1,1,1251445235,1,1221565538),(77,'Microsoft','1-543-753-3999','(270) 679-1347',NULL,'1-580-266-4391','amet@lectusquis.edu','Ap #168-7655 Cursus Av.','Ap #552-8268 Sodales St.','6301 Pede. Rd.','P.O. Box 833, 7797 Consectetuer Ave','Easthampton','WES','S2Q 7X0','Yemen','8067 Mauris. Rd.','181-4763 Nunc Ave','P.O. Box 806, 3423 Sed Avenue','P.O. Box 196, 4397 Ultrices. Rd.','Atwater','Flevoland','9427OA','Central African Republic','amet orci. Ut sagittis lobortis mauris. Suspendisse',1,1,1229552987,1,1234712033),(78,'Finale','1-945-430-3721','(422) 179-5444',NULL,'1-945-595-0307','eu.erat@a.com','3053 Vehicula St.','Ap #640-2825 Enim, Avenue','332-8627 Penatibus Road','2514 Suspendisse Ave','East St. Louis','WI','46029','Greece','5321 Placerat, Rd.','5153 Placerat, Street','P.O. Box 953, 8011 Congue Rd.','3545 Fusce St.','Huntington Beach','NB','98157','New Zealand','aliquet magna a neque. Nullam ut nisi a odio',1,1,1267164417,1,1261869470),(79,'Cakewalk','1-851-466-2643','(220) 124-3549',NULL,'1-729-194-8209','at.risus.Nunc@utnisi.com','Ap #797-1639 Luctus, Ave','117-9735 Consectetuer Avenue','256-9101 Aenean Rd.','P.O. Box 282, 9602 Lacus. Av.','Brownsville','Overijssel','0511OD','Mayotte','P.O. Box 154, 4280 Eu St.','Ap #350-1136 Est. Avenue','P.O. Box 509, 457 Tempor Street','8278 Cursus St.','Roanoke','MD','48142','Mongolia','lectus ante dictum mi, ac',1,1,1218761658,1,1226608226),(80,'Borland','1-509-997-9361','(897) 385-8566',NULL,'1-880-271-3651','cursus@Pellentesque.edu','5525 Mauris Av.','392-8054 Bibendum St.','646-7553 Vestibulum St.','Ap #881-2985 Eu, Street','Rutland','Gelderland','Q3C 2Q1','Namibia','211-3722 Ut Avenue','292-4586 Cras Av.','P.O. Box 899, 9873 Nibh. Rd.','5446 Auctor St.','Macon','Utah','7488VT','Sao Tome and Principe','ac, fermentum vel,',1,1,1227793978,1,1252200231),(81,'Sibelius','1-308-153-8017','(884) 287-6211',NULL,'1-797-228-7594','cursus.Nunc@dignissimlacus.edu','P.O. Box 206, 7937 Metus. Ave','Ap #274-1398 Nam Avenue','7738 Aenean Avenue','Ap #971-7089 Aliquam Avenue','Peru','Overijssel','52960','Norway','7672 Sapien. Ave','Ap #946-8128 Libero Ave','2602 Curabitur Rd.','5668 A, Avenue','Paducah','KEN','S6S 4N4','Mexico','pede. Nunc sed',1,1,1249733367,1,1228943069),(82,'Lavasoft','1-824-524-2816','(462) 667-7265',NULL,'1-273-848-8520','Nunc.ut@non.org','3414 Tellus. Rd.','Ap #414-7336 Lacinia Rd.','125-3358 Sed St.','916-9740 Lorem Rd.','Fairfax','DFS','00737','Dominica','3697 At, St.','509-5601 Sit Road','960-2075 Nec, Avenue','755-9225 Nunc. Av.','New Kensington','Ov.','55997','Guatemala','vulputate eu, odio.',1,1,1235101080,1,1238961355),(83,'Sibelius','1-156-801-7676','(540) 869-0733',NULL,'1-153-743-9644','ultrices.sit@malesuadautsem.com','P.O. Box 720, 1433 Neque. Road','Ap #841-336 Sed St.','P.O. Box 137, 1669 Pharetra Av.','Ap #814-7935 In St.','College Station','FLN','8490EE','New Zealand','8082 Integer St.','7344 Lectus Avenue','Ap #854-8626 Pede. Ave','Ap #256-8143 Pede. Rd.','Youngstown','Noord Brabant','0159ZW','Tunisia','hendrerit id, ante. Nunc mauris sapien, cursus in, hendrerit consectetuer,',1,1,1257647458,1,1247297768),(84,'Cakewalk','1-166-480-4063','(942) 902-5100',NULL,'1-941-862-9896','ut.mi@maurisaliquam.com','1901 Augue St.','P.O. Box 281, 4954 Tincidunt. St.','Ap #650-4158 Integer Street','274-7753 Sagittis. Rd.','Cedarburg','AB','2214AK','New Zealand','Ap #507-6200 Adipiscing Av.','947-1881 Gravida Av.','476-7850 Arcu. Ave','2191 Arcu. St.','Duncan','North Riding of Yorkshire','WQ43 2TE','Nauru','urna. Nullam lobortis quam a felis ullamcorper',1,1,1229757320,1,1240459224),(85,'Lycos','1-337-905-5488','(304) 946-3125',NULL,'1-394-692-2314','ornare.egestas@odioAliquamvulputate.com','3919 Curabitur Ave','Ap #672-2530 Porttitor Av.','8642 Ante, St.','451-913 Sem St.','Springfield','DFD','93697','Cocos (Keeling) Islands','9715 Ipsum Road','7974 Mi Av.','P.O. Box 495, 444 Orci Avenue','P.O. Box 206, 5634 Etiam Road','Lincoln','California','06285','Christmas Island','dictum eleifend, nunc risus varius orci, in consequat',1,1,1238937199,1,1250462594),(86,'Adobe','1-255-244-0403','(465) 214-9462',NULL,'1-988-410-4757','dolor.tempus@eu.ca','2544 Dui, Road','Ap #260-7695 Donec Street','780-7722 Ipsum Street','Ap #750-8729 Curabitur Avenue','Bossier City','DON','33858','Bulgaria','9886 Molestie St.','Ap #155-883 Auctor St.','6085 Donec Ave','Ap #420-2052 Ut Rd.','Auburn','British Columbia','D3F 1H4','Croatia','dictum',1,1,1225515770,1,1266562530),(87,'Sibelius','1-384-396-4284','(636) 699-3207',NULL,'1-487-877-9882','sagittis.Duis.gravida@feugiat.com','3027 Eget St.','2663 Nisi Road','P.O. Box 137, 8681 Arcu Rd.','Ap #749-9652 Maecenas St.','Hawthorne','Prince Edward Island','58841','Saudi Arabia','P.O. Box 870, 4987 Purus St.','628-482 In, Avenue','P.O. Box 444, 8640 Integer Street','322-4830 Nulla. Rd.','Boulder City','North Dakota','9234KG','South Georgia and The South Sandwich Islands','Aliquam',1,1,1232964203,1,1213838018),(88,'Yahoo','1-487-302-2257','(785) 704-0362',NULL,'1-437-643-6390','ornare@Nuncpulvinararcu.org','3098 Urna. Street','6275 In Rd.','Ap #639-7264 Semper Ave','5236 Ullamcorper St.','Manitowoc','GAL','78897','Christmas Island','P.O. Box 460, 6002 Tortor, Road','P.O. Box 650, 2470 Et St.','9404 Sem Rd.','2559 Nonummy Avenue','Racine','Zeeland','O4O 7V1','Netherlands','quam a felis ullamcorper viverra. Maecenas iaculis aliquet diam. Sed',1,1,1248729561,1,1217571827),(89,'Yahoo','1-763-125-5613','(529) 461-5495',NULL,'1-293-819-7627','ipsum.Curabitur@feugiat.ca','Ap #301-6032 Mauris Rd.','8024 Scelerisque Ave','6956 Cursus Rd.','P.O. Box 125, 8491 Tortor Avenue','Nashua','SRK','Q2P 2K2','Armenia','2019 Nisi. St.','5176 Suscipit, Av.','Ap #632-7063 Velit St.','949-1967 Ullamcorper Rd.','Broken Arrow','KIK','7445RA','Guatemala','id, blandit at, nisi. Cum sociis',1,1,1261570980,1,1264313629),(90,'Apple Systems','1-144-787-4219','(913) 564-1715',NULL,'1-241-599-1755','mauris@magnisdisparturient.com','750 Donec Street','P.O. Box 636, 1024 In Ave','Ap #755-169 Turpis Street','P.O. Box 202, 4683 Ridiculus Street','Oswego','Zuid Holland','06700','Maldives','Ap #708-9528 Sit Ave','8490 Mauris Road','1407 Sit St.','Ap #601-7550 Elit. Avenue','Milwaukee','New York','N33 1BI','France','sit amet massa. Quisque porttitor',1,1,1226950311,1,1240760327),(91,'Apple Systems','1-780-717-2413','(546) 978-0293',NULL,'1-401-285-7215','libero.Morbi.accumsan@dictumeu.org','410 Nec, Rd.','Ap #269-9408 Lobortis Street','6426 Dapibus Avenue','6204 Mauris Ave','Phoenix','SUT','JI08 2DP','Liberia','306-7611 Tortor. Ave','8789 Ac Av.','Ap #992-3760 Fames Rd.','9765 Ante. Ave','Lakeland','Co. Laois','2516UX','Syrian Arab Republic','Duis volutpat nunc sit amet metus. Aliquam',1,1,1267891762,1,1254562109),(92,'Microsoft','1-326-110-3629','(418) 195-5490',NULL,'1-332-178-9764','dolor.Fusce@utquamvel.edu','517-5253 Imperdiet, Street','Ap #275-9732 Rutrum Avenue','Ap #155-6829 Vel, Av.','P.O. Box 822, 3472 Magna. Street','San Antonio','Gelderland','S7Q 3B5','Syrian Arab Republic','135-516 Aptent Rd.','Ap #876-7445 Mauris Ave','Ap #176-4564 Curabitur St.','P.O. Box 243, 6777 A, Road','Lawrenceville','South Carolina','I8C 4K8','Zambia','id magna et',1,1,1243023509,1,1233542659),(93,'Finale','1-551-391-5729','(219) 463-8787',NULL,'1-887-232-0940','Quisque@luctussit.edu','3121 Arcu. Rd.','136-6878 Justo Ave','P.O. Box 661, 4728 Mattis. Ave','784-1163 Sem Road','Manassas Park','Fl.','85714','Korea','P.O. Box 450, 2133 Interdum Rd.','Ap #254-3710 Nulla St.','596-844 Convallis Avenue','P.O. Box 720, 7160 Purus. St.','Norman','AVN','ZS9 2YA','Tanzania, United Republic of','Integer urna.',1,1,1259936392,1,1271523409),(94,'Sibelius','1-531-187-0672','(570) 619-6947',NULL,'1-691-120-8450','metus.eu@pretiumneque.edu','P.O. Box 890, 1709 Vehicula. Road','3809 Molestie Road','759-9492 Accumsan Avenue','Ap #807-6282 Elit Rd.','North Charleston','Hawaii','2231KQ','Malta','Ap #356-3811 Mauris St.','P.O. Box 869, 8343 Mus. Street','985-1615 Mauris Rd.','608-5618 Imperdiet Rd.','Moline','Wisconsin','8052KU','India','sit amet nulla.',1,1,1245268872,1,1260148903),(95,'Macromedia','1-882-735-5307','(881) 145-7596',NULL,'1-678-316-3933','mauris@enim.ca','966-1680 Mauris St.','P.O. Box 497, 7960 Cras Ave','Ap #129-9497 In St.','Ap #171-8670 In, Rd.','Toledo','Avon','6638XD','Spain','412-4753 Arcu Avenue','Ap #987-302 Lacus. Street','5139 Suspendisse Av.','Ap #748-7769 Dictum. Ave','Sun Valley','OXF','Y3Y 2VH','Mexico','nisl arcu iaculis enim, sit',1,1,1249391202,1,1262408536),(96,'Cakewalk','1-971-628-4577','(403) 839-1548',NULL,'1-762-764-2067','neque.venenatis@Etiamgravida.edu','Ap #427-1143 Risus. Road','P.O. Box 792, 6084 Ut, Ave','Ap #154-1007 Egestas Ave','Ap #791-5854 Vestibulum Av.','Hermitage','Utrecht','64378','Bermuda','Ap #500-3386 Ipsum. Rd.','984-3443 Vehicula Rd.','5448 Gravida Ave','8093 Vulputate, Av.','Inglewood','MO','0107QJ','China','mattis. Integer',1,1,1238622799,1,1267733561),(97,'Cakewalk','1-865-789-8005','(737) 819-2186',NULL,'1-678-133-1352','vitae.erat@vestibulummassarutrum.edu','7489 Lobortis Avenue','459-3433 Adipiscing Rd.','529-7476 Eget Rd.','887-9273 Non St.','Laguna Woods','West Sussex','3031CX','Maldives','1076 Eu, Street','6784 Nascetur Street','Ap #347-7165 Ultricies Street','499-4766 Elit Rd.','Davenport','Noord Holland','9356LC','Trinidad and Tobago','Etiam laoreet, libero',1,1,1236904150,1,1229837793),(98,'Apple Systems','1-948-432-2689','(198) 471-8061',NULL,'1-277-835-9626','nec@Vestibulum.org','960 Diam St.','P.O. Box 906, 7026 Sed, Av.','4524 Proin Rd.','Ap #892-1611 Sociis Road','Frederiksted','Noord Brabant','Z8E 8X1','Puerto Rico','651-6460 Morbi St.','675-6443 Mauris St.','8751 Vel Av.','401-9408 Id Av.','Pembroke Pines','Sutherland','87244','Luxembourg','ipsum. Suspendisse sagittis. Nullam vitae',1,1,1252866437,1,1218405813),(99,'Macromedia','1-481-668-4467','(812) 384-4834',NULL,'1-653-901-9039','elit.sed@dolortempusnon.com','8750 Tellus Rd.','Ap #353-8051 Augue Rd.','P.O. Box 495, 9662 Non, St.','Ap #227-3073 Ante. Avenue','Mesa','Nova Scotia','69651','Cuba','P.O. Box 370, 6027 Non Rd.','Ap #843-6715 Vitae, Ave','7394 Eu, Avenue','P.O. Box 563, 2928 Lobortis Rd.','Newburyport','Saskatchewan','L4 6SB','Uganda','Donec consectetuer mauris id sapien.',1,1,1253645758,1,1252345004),(100,'Adobe','1-584-995-5492','(201) 669-7468',NULL,'1-154-892-1794','sit.amet@nibh.com','6715 Sociosqu Rd.','4969 Est St.','P.O. Box 309, 1603 Enim, Road','P.O. Box 752, 4368 Vel Av.','Irwindale','NT','M7B 3KN','Madagascar','6124 Mauris St.','8607 Sem. St.','Ap #434-1866 Sollicitudin Ave','8486 Eros. Av.','Ann Arbor','Noord Holland','XA8 9WK','Bhutan','dictum eu, eleifend nec, malesuada ut, sem. Nulla interdum. Curabitur',1,1,1239371517,1,1229142126);
/*!40000 ALTER TABLE `account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `account_notes`
--

DROP TABLE IF EXISTS `account_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account_notes` (
  `account_notes_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `notes` text CHARACTER SET latin1 NOT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`account_notes_id`),
  KEY `account_id` (`account_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `account_notes_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `account_notes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_notes`
--

LOCK TABLES `account_notes` WRITE;
/*!40000 ALTER TABLE `account_notes` DISABLE KEYS */;
INSERT INTO `account_notes` VALUES (1,13,'test',1270024972,1),(2,13,'test',1270026133,1),(3,13,'test',1270026137,1),(4,13,'test',1270026163,1),(5,13,'test',1270026223,1),(6,26,'test',1270026307,1),(7,26,'tetded',1270026315,1);
/*!40000 ALTER TABLE `account_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch`
--

DROP TABLE IF EXISTS `branch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch` (
  `branch_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `branch_manager` int(10) unsigned DEFAULT NULL,
  `branch_name` varchar(200) CHARACTER SET latin1 NOT NULL,
  `address_line_1` varchar(100) CHARACTER SET latin1 NOT NULL,
  `address_line_2` varchar(100) CHARACTER SET latin1 NOT NULL,
  `address_line_3` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `address_line_4` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `city` varchar(100) CHARACTER SET latin1 NOT NULL,
  `state` varchar(100) CHARACTER SET latin1 NOT NULL,
  `postal_code` varchar(10) CHARACTER SET latin1 NOT NULL,
  `country` varchar(100) CHARACTER SET latin1 NOT NULL,
  `phone` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `fax` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(320) CHARACTER SET latin1 DEFAULT NULL,
  `service_tax_number` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `tin` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `parent_branch_id` int(10) unsigned DEFAULT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  `is_hq` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`branch_id`),
  UNIQUE KEY `branch_name` (`branch_name`),
  KEY `branch_manager` (`branch_manager`),
  KEY `parent_branch_id` (`parent_branch_id`),
  CONSTRAINT `branch_ibfk_1` FOREIGN KEY (`branch_manager`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `branch_ibfk_2` FOREIGN KEY (`parent_branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch`
--

LOCK TABLES `branch` WRITE;
/*!40000 ALTER TABLE `branch` DISABLE KEYS */;
INSERT INTO `branch` VALUES (1,NULL,'Head Quarters','#506, 10 B Main Road','I Block','Jayanagar','','Bangalore','Karnataka','560011','India',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0);
/*!40000 ALTER TABLE `branch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaign`
--

DROP TABLE IF EXISTS `campaign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campaign` (
  `campaign_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `assigned_to` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `created` int(11) NOT NULL COMMENT 'when campaign was created',
  `branch_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`campaign_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `branch_id` (`branch_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `campaign_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `campaign_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `campaign_ibfk_3` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaign`
--

LOCK TABLES `campaign` WRITE;
/*!40000 ALTER TABLE `campaign` DISABLE KEYS */;
INSERT INTO `campaign` VALUES (1,'test','test',1268159400,1268332200,1,1,1270021854,1);
/*!40000 ALTER TABLE `campaign` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact` (
  `contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `middle_name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `work_phone` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `home_phone` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `mobile` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `fax` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `title` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `department` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `work_email` varchar(320) CHARACTER SET latin1 DEFAULT NULL,
  `other_email` varchar(320) CHARACTER SET latin1 DEFAULT NULL,
  `do_not_call` tinyint(1) NOT NULL DEFAULT '0',
  `email_opt_out` tinyint(1) NOT NULL DEFAULT '0',
  `billing_city` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `billing_state` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `billing_postal_code` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `billing_country` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `shipping_city` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `shipping_state` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `shipping_postal_code` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `shipping_country` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  `reports_to` int(10) unsigned DEFAULT NULL,
  `salutation_id` int(10) unsigned DEFAULT NULL,
  `assistant_id` int(10) unsigned DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `assigned_to` int(10) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  `branch_id` int(10) unsigned NOT NULL,
  `account_id` int(10) unsigned DEFAULT NULL,
  `billing_address_line_1` varchar(100) CHARACTER SET latin1 NOT NULL,
  `billing_address_line_2` varchar(100) CHARACTER SET latin1 NOT NULL,
  `billing_address_line_3` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `billing_address_line_4` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `shipping_address_line_1` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `shipping_address_line_2` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `shipping_address_line_3` varchar(100) CHARACTER SET latin1 NOT NULL,
  `shipping_address_line_4` varchar(100) CHARACTER SET latin1 NOT NULL,
  `ss_enabled` tinyint(1) DEFAULT '0',
  `ss_active` tinyint(1) DEFAULT NULL,
  `ss_password` varchar(32) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`contact_id`),
  KEY `reports_to` (`reports_to`),
  KEY `salutation_id` (`salutation_id`),
  KEY `assistant_id` (`assistant_id`),
  KEY `branch_id` (`branch_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact`
--

LOCK TABLES `contact` WRITE;
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
INSERT INTO `contact` VALUES (1,'Kumara','Bhoja','Raja','123456','123456','','','Rajakarani','','bhimanna@example.com','bhimanna@example.com',0,0,'Bangalore','Karnataka','560011','India','Bangalore','Karnataka','560011','India','',0,0,0,'0000-00-00',1,1,1270021490,0,1,0,'#1234, 1st Cross, 2nd Main,','3rd Stage, Fouranagara','','','#1234, 1st Cross, 2nd Main,','3rd Stage, Fouranagara','','',0,NULL,NULL),(2,'Dana','Quinn','Drew','1-247-687-8694','1-305-852-2827','1 64 660 7989-7111','(870) 657-5006','dictum magna.','ante','nonummy@convallis.edu','nec.ante.blandit@enimEtiam.com',0,0,'427-2064 Mus. Street','276-3665 Sit Rd.','9605SD','Cayman Islands','Forrest City','CHS','1865CP','Zambia','eget massa. Suspendisse eleifend. Cras',1,1,NULL,'2007-03-06',1,1,1239987849,1248563420,1,1,'112-2062 Pellentesque Avenue','571-6992 Pharetra. Road','Ap #177-2953 Convallis, Rd.','P.O. Box 691, 7474 Pede. Street','Ap #711-848 Quam Street','P.O. Box 468, 3744 In St.','650-2763 Et Street','Ap #791-4777 Convallis Avenue',0,NULL,NULL),(3,'Eugenia','Dahlia','Branden','1-134-155-7289','1-150-272-5057','1 41 181 6559-8791','(206) 779-5790','pede sagittis','Nam','Nullam.ut.nisi@erosProin.org','Aenean.euismod@sagittisfelisDonec.edu',0,0,'Ap #575-1361 Felis, St.','P.O. Box 568, 3561 Ut Street','B9E 6S1','Oman','Minnetonka','Northwest Territories','AA1O 1OU','Dominican Republic','netus et malesuada fames ac',1,1,NULL,'2009-06-23',1,1,1242708876,1248319953,1,1,'712-8443 Dictum. Rd.','2266 Accumsan Street','6980 Lobortis St.','Ap #976-4554 Risus Street','690 Sapien St.','P.O. Box 800, 3243 Morbi St.','Ap #239-2799 Ligula. St.','1521 Sit Rd.',0,NULL,NULL),(4,'Jordan','Driscoll','Timon','1-780-550-7292','1-602-642-8252','1 43 466 9674-8617','(763) 736-6627','egestas. Sed','porta','arcu@seddictum.org','ultricies.ornare.elit@urna.com',0,0,'P.O. Box 460, 6318 Id St.','7046 Interdum. St.','0586FK','Thailand','Williamsport','Groningen','8669BG','Timor-leste','a, enim. Suspendisse aliquet, sem',1,1,NULL,'2007-08-07',1,1,1243381065,1248993720,1,1,'Ap #263-6082 Massa. Rd.','2578 Quisque Av.','523-8455 Ut, Road','Ap #976-5359 Ultrices Avenue','5577 Sem St.','P.O. Box 204, 2741 Sem. Av.','Ap #157-1863 Tellus Rd.','1211 Auctor. Street',0,NULL,NULL),(5,'Bruno','Latifah','Grant','1-496-889-7592','1-390-818-9216','1 86 798 9791-3353','(931) 917-8498','sit amet,','Phasellus','Sed.malesuada.augue@fermentumfermentumarcu.org','eget.metus@massa.edu',0,0,'1234 Dolor. St.','801-4906 Conubia St.','R4H 4G7','Holy See (Vatican City State)','Belleville','Noord Holland','3551QU','Egypt','fringilla. Donec feugiat metus sit',1,1,NULL,'2006-08-16',1,1,1252296816,1248649731,1,1,'Ap #280-3233 In Street','581-2174 Tortor Road','912-1368 Proin Ave','Ap #393-3401 Ipsum St.','Ap #497-6527 Donec St.','765-256 Vel St.','122-7554 Montes, Rd.','P.O. Box 934, 4805 Elit, Avenue',0,NULL,NULL),(6,'Ferris','Kendall','Charles','1-743-669-9920','1-676-970-6732','1 13 963 1573-0915','(371) 361-5673','penatibus et','dignissim','eu@Nullamscelerisque.org','amet.luctus@sit.org',0,0,'P.O. Box 206, 9760 Consectetuer Rd.','P.O. Box 715, 3715 Purus St.','65011','South Africa','Lawrence','U.','89490','Korea, Republic of','Nullam ut nisi a odio',1,1,NULL,'2006-05-23',1,1,1228104464,1248819829,1,1,'288-5045 Urna Ave','P.O. Box 409, 6009 Sit Ave','P.O. Box 317, 914 Senectus Ave','6166 Nibh St.','Ap #194-9149 Iaculis Av.','Ap #312-1896 Sagittis. Ave','P.O. Box 981, 6387 Eu Road','222-4868 Tincidunt, Rd.',0,NULL,NULL),(7,'Priscilla','Orlando','Francesca','1-585-657-5133','1-842-619-8321','1 52 897 2019-8546','(112) 864-1026','sit amet','pharetra,','augue.Sed.molestie@vitaealiquam.org','eu.neque.pellentesque@inhendreritconsectetuer.org',0,0,'Ap #693-7683 Lectus Av.','278-2887 Nascetur St.','0982AC','Turkey','Fort Collins','Nevada','H7X 9N4','Bouvet Island','convallis ligula. Donec luctus aliquet',1,1,NULL,'2010-01-12',1,1,1241676065,1248200134,1,1,'P.O. Box 710, 4500 Convallis St.','P.O. Box 584, 2592 Proin Road','8835 Ac Avenue','P.O. Box 639, 8956 Fringilla Avenue','294-8849 Arcu. St.','P.O. Box 995, 1268 Fringilla, Rd.','3060 Vitae Avenue','Ap #927-9563 Diam Rd.',0,NULL,NULL),(8,'Patricia','Jaime','Cleo','1-830-720-8899','1-921-817-8199','1 17 303 1937-3557','(529) 517-4076','Cras lorem','parturient','ac.urna.Ut@imperdietnec.ca','pulvinar.arcu@urnaconvalliserat.edu',0,0,'P.O. Box 453, 4269 Tincidunt Rd.','P.O. Box 252, 4550 Eget Avenue','Z4Y 5M6','Botswana','Revere','Noord Brabant','N2B 9O4','South Africa','non, dapibus rutrum, justo. Praesent',1,1,NULL,'2007-06-27',1,1,1229811224,1248892305,1,1,'843-4983 Semper Ave','115-6916 Et, Ave','Ap #469-3417 Imperdiet Av.','759-8275 Velit. Avenue','Ap #581-8120 Auctor, St.','Ap #256-6157 Accumsan Road','652-7111 Faucibus. Av.','209-1437 Turpis St.',0,NULL,NULL),(9,'Carson','Bertha','Abbot','1-853-547-2274','1-932-868-9208','1 67 658 1692-7902','(356) 179-8281','convallis, ante','magna.','Pellentesque.tincidunt.tempus@luctusipsum.com','mi.lacinia@Donec.com',0,0,'8421 Mollis St.','4197 Mauris Rd.','Z6E 7M0','Wallis and Futuna','Gilette','Northwest Territories','G3A 4G8','Saint Lucia','Morbi accumsan laoreet ipsum. Curabitur',1,1,NULL,'2009-12-07',1,1,1235429609,1248853612,1,1,'8690 Et Av.','258-2400 Felis. St.','196-9421 Convallis Av.','P.O. Box 799, 2963 Accumsan Street','P.O. Box 373, 9196 Mauris St.','Ap #635-6032 Donec Rd.','928-4894 Netus Rd.','Ap #697-2926 Vivamus St.',0,NULL,NULL),(10,'Sylvia','Naida','Barclay','1-370-802-5375','1-505-452-9124','1 62 993 6654-3681','(751) 915-6707','litora torquent','lacus.','Ut@velit.ca','interdum.libero.dui@montesnasceturridiculus.ca',0,0,'Ap #886-7566 Tempus Street','Ap #134-2818 Tincidunt, Rd.','F3S 8C7','Morocco','Grand Rapids','Merionethshire','V1U 7V7','El Salvador','nulla. Integer urna. Vivamus molestie',1,1,NULL,'2006-05-13',1,1,1246154403,1248598605,1,1,'270-3060 Ut Avenue','P.O. Box 859, 2896 Neque. St.','Ap #342-7241 Pharetra. Avenue','5398 Faucibus St.','157-9608 Eu Rd.','107-3362 Nulla Rd.','3190 Maecenas Ave','Ap #635-1644 A, St.',0,NULL,NULL),(11,'Shellie','Yen','Britanni','1-656-717-8166','1-780-480-1288','1 79 432 5723-6392','(964) 755-3121','nec, malesuada','neque','risus@ridiculusmusProin.edu','erat.semper.rutrum@odio.com',0,0,'P.O. Box 610, 144 Metus. Rd.','848 Et Avenue','MO1A 8HH','Barbados','Las Cruces','MO','22082','United Kingdom','nunc nulla vulputate dui, nec',1,1,NULL,'2009-08-04',1,1,1218876028,1248672625,1,1,'P.O. Box 832, 1259 Mus. St.','498-6369 Orci, Av.','P.O. Box 301, 2214 Tincidunt Street','939-2595 Posuere St.','P.O. Box 175, 3136 Proin St.','P.O. Box 745, 8437 In, Ave','2407 Duis Street','8513 Mauris, Avenue',0,NULL,NULL),(12,'Deacon','Hanae','Jillian','1-966-283-3326','1-747-870-4873','1 89 200 2084-3340','(969) 393-6500','molestie dapibus','velit','dignissim.Maecenas.ornare@cursus.edu','odio.a@lorem.org',0,0,'Ap #949-4939 Tincidunt St.','9927 Ante. Street','Y64 2FB','Faroe Islands','Eufaula','BC','60820','Heard Island and Mcdonald Islands','est mauris, rhoncus id, mollis',1,1,NULL,'2005-07-12',1,1,1217149211,1248194788,1,1,'454-6204 Adipiscing Street','Ap #769-6742 Parturient Av.','Ap #236-5638 Donec Road','P.O. Box 708, 9087 Facilisis St.','615-189 Orci, Ave','Ap #469-7780 Praesent Ave','P.O. Box 255, 1334 Aliquet. Ave','4481 Donec Av.',0,NULL,NULL),(13,'Libby','Marcia','Lacota','1-127-687-8562','1-298-255-8925','1 64 771 6752-3126','(884) 669-2811','sed pede','dolor','augue.id@magna.com','placerat.augue.Sed@malesuadamalesuadaInteger.edu',0,0,'5639 Posuere, St.','Ap #625-8109 Diam. St.','2192IV','Korea','Laramie','VA','0068OA','Afghanistan','aptent taciti sociosqu ad litora',1,1,NULL,'2009-10-05',1,1,1222225254,1248862980,1,1,'8180 Morbi Street','Ap #893-9674 Erat Street','P.O. Box 755, 6185 At Rd.','Ap #224-8468 Non St.','4729 Dui. Ave','791-4125 Aliquam Road','746-9407 Lorem, Ave','Ap #514-2406 Enim Rd.',0,NULL,NULL),(14,'Knox','Ima','Amaya','1-257-869-4889','1-563-516-2509','1 87 802 5551-9524','(301) 359-1447','mattis. Integer','vulputate,','tellus.eu.augue@aliquet.com','Pellentesque@interdum.com',0,0,'9770 Purus, Rd.','Ap #950-2395 Aenean Ave','J2O 3G9','San Marino','Amesbury','East Riding of Yorkshire','X4O 7J2','Lebanon','amet metus. Aliquam erat volutpat.',1,1,NULL,'2008-06-04',1,1,1216837129,1248772751,1,1,'Ap #254-1047 Nulla St.','P.O. Box 739, 7765 Tellus. Av.','8779 Sit Road','6834 Nunc Av.','P.O. Box 632, 9813 Erat. Rd.','Ap #652-9508 Semper Ave','Ap #265-5321 Dolor Rd.','Ap #893-2423 Malesuada Road',0,NULL,NULL),(15,'Madeson','Lesley','Addison','1-998-836-5990','1-150-937-5259','1 97 530 2237-8368','(241) 701-6112','eleifend nec,','eu,','Maecenas.mi@blanditNam.com','egestas.Sed@ultrices.edu',0,0,'602-9768 Placerat, St.','Ap #183-9879 Taciti Av.','S2J 4I4','Norway','Loudon','New Brunswick','I9O 9Z8','Sao Tome and Principe','Donec consectetuer mauris id sapien.',1,1,NULL,'2009-07-21',1,1,1260953998,1248837599,1,1,'5442 Facilisis. Street','2662 At Rd.','P.O. Box 380, 8372 At Avenue','6080 Aptent St.','Ap #156-3087 Metus. Rd.','P.O. Box 139, 4757 Mauris. Ave','817-3954 Pede. Rd.','5655 Elit Ave',0,NULL,NULL),(16,'Ifeoma','Tate','Linda','1-468-452-9588','1-715-665-4110','1 65 568 2905-8784','(520) 725-6037','lectus. Nullam','justo','cursus.non@imperdietdictum.com','id.risus@hendreritaarcu.ca',0,0,'7936 Neque St.','570-474 Tellus. Rd.','08964','Barbados','Yigo','FLN','73571','Germany','massa. Suspendisse eleifend. Cras sed',1,1,NULL,'2008-02-29',1,1,1226499210,1248996652,1,1,'Ap #199-7902 Placerat, Ave','522-370 Luctus. Road','368-5407 Luctus Av.','P.O. Box 314, 6867 Eu St.','P.O. Box 493, 2110 Dolor. Road','P.O. Box 724, 989 Ut Road','4752 At Road','P.O. Box 830, 608 Nec Rd.',0,NULL,NULL),(17,'Joan','Sean','Emmanuel','1-315-897-0240','1-645-828-8997','1 37 383 3647-4705','(385) 198-4049','Proin vel','arcu.','libero@nec.com','nisi.Cum.sociis@eget.com',0,0,'9680 Sed Rd.','Ap #363-9805 Eget, Road','O1B 4O0','South Georgia and The South Sandwich Islands','Liberal','Drenthe','7196CF','Armenia','risus. Duis a mi fringilla',1,1,NULL,'2010-06-13',1,1,1218041178,1248867608,1,1,'7839 Sem Road','6483 Porttitor Road','Ap #841-4563 Eleifend. St.','6812 Quis, Road','703-1049 Et Av.','Ap #466-2806 Feugiat Rd.','P.O. Box 989, 9546 Class Avenue','Ap #570-1950 Magna. Av.',0,NULL,NULL),(18,'Mariam','Carter','Liberty','1-874-368-2340','1-701-157-4804','1 90 458 2007-2669','(196) 819-2263','sollicitudin orci','nec','lorem.semper.auctor@nisi.edu','lorem.fringilla@velit.edu',0,0,'212-9283 Sed St.','Ap #524-7720 Fringilla Ave','Q6A 9V6','Equatorial Guinea','Darlington','MT','BC0E 0OX','Mongolia','malesuada id, erat. Etiam vestibulum',1,1,NULL,'2009-08-28',1,1,1244465080,1248154938,1,1,'Ap #712-9813 Ante Ave','575-7646 Nec St.','Ap #385-7858 Aliquam St.','9564 Ligula. Road','544 Purus. Road','P.O. Box 519, 9018 Vivamus Rd.','P.O. Box 290, 2800 Nulla Rd.','410-9906 Quam. Avenue',0,NULL,NULL),(19,'Maile','Stephen','Philip','1-994-372-7511','1-802-363-7502','1 72 531 8714-5945','(472) 647-8558','ut ipsum','eget','nec.cursus@scelerisquemollis.edu','scelerisque.dui.Suspendisse@aclibero.edu',0,0,'8514 Montes, St.','9620 Malesuada Road','R1U 1V5','Palestinian Territory, Occupied','Indianapolis','SAL','9800SX','Morocco','In nec orci. Donec nibh.',1,1,NULL,'2009-06-02',1,1,1275783332,1249048207,1,1,'P.O. Box 983, 3347 Sit Avenue','Ap #691-1363 Vestibulum Rd.','8962 Ridiculus Ave','3709 Ut Road','P.O. Box 363, 4331 Malesuada Av.','1443 Sem, Avenue','P.O. Box 448, 5061 Amet, St.','523-2574 Nec, Rd.',0,NULL,NULL),(20,'Xandra','Allen','Thomas','1-136-120-4048','1-951-360-3785','1 12 118 5604-6950','(662) 703-1926','dolor. Quisque','hendrerit.','dictum@Vivamussitamet.ca','eu.eros@ligula.com',0,0,'1722 Semper Av.','P.O. Box 885, 6269 Cum Rd.','XL52 5MN','Costa Rica','Lima','Oregon','X6Y 1Y5','Czech Republic','lacinia mattis. Integer eu lacus.',1,1,NULL,'2006-06-14',1,1,1231707022,1248368222,1,1,'2208 Nec Road','Ap #507-9411 Erat Avenue','160-8764 Libero St.','6358 Quisque St.','Ap #572-902 Ut Av.','P.O. Box 701, 7283 Sed Rd.','P.O. Box 545, 8906 Nec Rd.','Ap #908-4410 Ante. Ave',0,NULL,NULL),(21,'Charissa','Sean','Quynn','1-318-119-8797','1-448-718-3839','1 98 885 5707-5797','(608) 696-4609','nisi. Cum','arcu.','semper@consectetuer.ca','Cras@fringillacursus.org',0,0,'347-1485 Augue. Road','P.O. Box 712, 7108 Odio, St.','G12 7VF','Paraguay','Grafton','Prince Edward Island','L4N 7M4','Singapore','sapien, cursus in, hendrerit consectetuer,',1,1,NULL,'2009-08-05',1,1,1220353899,1248822713,1,1,'P.O. Box 293, 723 Mus. Ave','1285 Semper Avenue','P.O. Box 192, 5478 Vel, Road','4795 Aenean Street','2695 Id Ave','125-3052 Nascetur Rd.','P.O. Box 189, 3301 Libero Av.','Ap #975-3233 Curabitur Street',0,NULL,NULL),(22,'Kadeem','Rana','Iola','1-216-367-4622','1-414-979-9017','1 32 114 6171-8220','(846) 693-8170','nascetur ridiculus','parturient','convallis.erat.eget@pharetrafelis.org','lobortis@variusNamporttitor.edu',0,0,'233-1039 Velit. Avenue','278-1936 Eleifend Road','77269','Virgin Islands, British','Philadelphia','Gelderland','53984','Zimbabwe','erat. Etiam vestibulum massa rutrum',1,1,NULL,'2008-02-20',1,1,1234798528,1248282239,1,1,'882-3133 Risus. Road','6117 Fringilla. Rd.','7444 Vitae St.','Ap #614-8252 Bibendum Street','Ap #478-4760 Mi. Rd.','9029 Mus. Street','P.O. Box 993, 8729 Eu Avenue','Ap #681-5455 Consectetuer Road',0,NULL,NULL),(23,'Darius','Lance','Callie','1-160-733-4913','1-487-850-6426','1 42 576 3414-9346','(420) 769-9037','elit elit','orci','varius@fringillaornareplacerat.edu','odio.Nam@erat.ca',0,0,'P.O. Box 189, 7693 Ac Street','911-8278 Ipsum Road','51841','Dominican Republic','Daly City','Nova Scotia','00231','Zimbabwe','placerat. Cras dictum ultricies ligula.',1,1,NULL,'2007-08-14',1,1,1275736865,1248500618,1,1,'112-5125 Erat Street','1140 At, Avenue','P.O. Box 610, 2863 Blandit Road','P.O. Box 564, 7020 Sed Ave','4679 Est, Av.','Ap #819-7571 Iaculis Road','Ap #401-4352 Lectus St.','7645 Feugiat Road',0,NULL,NULL),(24,'Ezra','Unity','Sophia','1-121-655-2709','1-398-721-4099','1 36 367 6558-5398','(553) 181-1840','pede nec','a,','et@Seddiam.ca','quis.lectus@nullaIn.com',0,0,'7392 Ut, Street','P.O. Box 232, 1892 Aliquam Road','OS5 7VK','Brunei Darussalam','Tallahassee','New Brunswick','K4J 3C5','New Zealand','faucibus orci luctus et ultrices',1,1,NULL,'2008-04-07',1,1,1221630279,1248929089,1,1,'290-2915 Cursus. Road','460-3560 Sagittis Rd.','Ap #237-2734 Et St.','Ap #207-8126 Erat Rd.','949-4065 Pharetra. Ave','6285 Aptent Av.','Ap #805-2336 Erat Av.','Ap #463-5374 Quisque St.',0,NULL,NULL),(25,'Blaine','Phillip','Kessie','1-246-512-1308','1-157-475-2390','1 17 308 9430-1836','(678) 891-8713','ultrices sit','ante,','nisi@sedturpisnec.edu','Cum.sociis.natoque@uteratSed.org',0,0,'P.O. Box 947, 1842 Ac St.','849-4158 Arcu. Av.','G17 1YX','Malawi','Joplin','Friesland','Y2 5XK','South Africa','Vestibulum ante ipsum primis in',1,1,NULL,'2009-07-03',1,1,1229722915,1248199288,1,1,'974-493 Malesuada Rd.','Ap #336-6388 Scelerisque Road','Ap #873-1983 Auctor Av.','P.O. Box 152, 9922 Eget Road','7742 Arcu Rd.','5487 Diam. Rd.','8309 Id Street','8378 Duis Street',0,NULL,NULL),(26,'Wynter','Colton','Lacy','1-385-153-2373','1-474-852-2017','1 84 762 9408-6615','(210) 544-3881','vel sapien','quam','molestie.tellus.Aenean@insodaleselit.org','eget.tincidunt.dui@a.com',0,0,'Ap #333-2934 Nec Road','949-247 Vehicula Av.','O1L 9W2','Antarctica','Burlingame','Prince Edward Island','4517VP','Micronesia','Fusce fermentum fermentum arcu. Vestibulum',1,1,NULL,'2006-09-06',1,1,1275053867,1248877515,1,1,'P.O. Box 175, 8143 Mauris, Ave','227-4014 Euismod Av.','Ap #872-9574 Donec Av.','297-211 Convallis Street','Ap #566-3872 Sit Av.','3309 Eu, Street','875-1693 A, Avenue','373-1015 Convallis, Ave',0,NULL,NULL),(27,'Kane','Nerea','Oleg','1-325-328-7580','1-445-769-6905','1 49 786 2176-8490','(123) 909-8022','ridiculus mus.','libero.','facilisis.magna.tellus@mifelis.org','at@pharetraut.edu',0,0,'P.O. Box 988, 7110 Quam St.','Ap #233-4583 Vitae, Road','JY4E 0RM','Reunion','North Tonawanda','Groningen','59920','Grenada','semper erat, in consectetuer ipsum',1,1,NULL,'2006-10-13',1,1,1250118907,1248893908,1,1,'8115 Velit Av.','P.O. Box 894, 7677 Mattis. Rd.','304-5738 Mauris Avenue','Ap #308-9759 Consectetuer Avenue','P.O. Box 797, 3806 Nunc Rd.','P.O. Box 959, 7473 Fermentum Rd.','P.O. Box 831, 9696 Ipsum St.','212-2577 Morbi Road',0,NULL,NULL),(28,'Cullen','Nina','Cameron','1-597-292-4868','1-942-204-0742','1 82 276 6888-5034','(754) 993-6820','ipsum non','Vivamus','dictum@mieleifend.edu','fermentum.metus.Aenean@Aliquam.com',0,0,'P.O. Box 798, 4011 Laoreet Rd.','Ap #380-5732 Nunc. Av.','O20 1CG','El Salvador','City of Industry','NL','X2L 7O2','Lebanon','sociis natoque penatibus et magnis',1,1,NULL,'2007-09-26',1,1,1234894640,1248677054,1,1,'Ap #159-1146 Erat Avenue','3590 Lorem, Road','P.O. Box 428, 5754 Mauris Rd.','984-284 Eu Rd.','668-8873 Sem Rd.','Ap #243-3046 Scelerisque Ave','P.O. Box 360, 1205 Urna. Rd.','609-2372 Purus. Road',0,NULL,NULL),(29,'Noelle','Brendan','Rhea','1-843-608-6091','1-460-235-3698','1 72 986 4480-5690','(150) 821-8454','neque. In','et','aliquet.magna.a@molestie.edu','tincidunt.aliquam.arcu@Suspendisse.ca',0,0,'751-3540 Volutpat Street','996-4063 Diam Road','G1G 2L7','Moldova','Union City','Newfoundland and Labrador','18181','Bolivia','ligula. Aenean euismod mauris eu',1,1,NULL,'2007-08-16',1,1,1241303687,1248673006,1,1,'Ap #310-5678 Sed Avenue','5892 Faucibus Ave','Ap #933-4523 Rutrum Rd.','P.O. Box 935, 2796 Ipsum Rd.','521-9703 Iaculis Ave','Ap #194-7692 Magna, Rd.','P.O. Box 494, 708 Porttitor Road','Ap #278-9166 Odio St.',0,NULL,NULL),(30,'Dawn','Kim','Kitra','1-840-692-5253','1-603-568-6339','1 67 107 7269-8272','(342) 504-0300','lacus. Etiam','mattis.','non.dapibus.rutrum@ac.ca','sit@augueid.org',0,0,'1526 Sit Av.','651-738 Lorem Av.','H2B 7E3','Russian Federation','Dothan','YKS','35637','Kyrgyzstan','enim. Mauris quis turpis vitae',1,1,NULL,'2007-12-18',1,1,1260599982,1248282280,1,1,'P.O. Box 624, 9140 Malesuada St.','P.O. Box 307, 2421 Nec Rd.','Ap #316-8913 Suspendisse Street','517-6562 Ultrices. St.','P.O. Box 824, 3476 Aenean St.','P.O. Box 417, 5226 Suscipit St.','Ap #199-1285 Sed, Rd.','8128 Non Road',0,NULL,NULL),(31,'Marshall','Zena','Janna','1-877-757-0036','1-344-675-7007','1 67 873 3419-4681','(357) 167-3208','a mi','tellus.','sollicitudin.adipiscing@necquam.com','Donec@mauris.edu',0,0,'638-1947 Bibendum. Street','1430 Eu, St.','XO1 6ZL','Reunion','Statesboro','N.-Br.','44090','Comoros','sed sem egestas blandit. Nam',1,1,NULL,'2006-03-31',1,1,1246335160,1249057578,1,1,'428-3054 Erat, Rd.','Ap #742-2088 Vitae, Rd.','P.O. Box 215, 2832 Nec Av.','Ap #692-9023 Dictum Av.','P.O. Box 518, 411 Tellus Ave','469 Nisl Rd.','9177 Lacinia Street','P.O. Box 913, 9309 Sed Street',0,NULL,NULL),(32,'Sloane','MacKenzie','Brody','1-242-374-2411','1-182-585-9753','1 14 368 5573-1856','(284) 608-6195','a sollicitudin','odio','dapibus.gravida@turpisnon.com','vulputate.eu.odio@vitaealiquetnec.ca',0,0,'548-7482 Facilisis Road','378-5003 Tempus Road','C1 6UJ','Madagascar','Fort Worth','YT','X9N 4K2','Virgin Islands, British','Proin dolor. Nulla semper tellus',1,1,NULL,'2008-10-13',1,1,1272565652,1248319350,1,1,'P.O. Box 225, 4205 Diam St.','P.O. Box 702, 4884 Odio, St.','Ap #149-9728 Adipiscing Av.','2219 Massa Avenue','340-447 Arcu Street','694-5686 Duis Rd.','P.O. Box 761, 6897 Vel, Rd.','Ap #480-2901 Adipiscing St.',0,NULL,NULL),(33,'Francesca','Thomas','Elizabeth','1-253-213-0452','1-983-946-5920','1 29 741 4847-8683','(105) 409-9388','viverra. Donec','ultrices','tempus@in.org','sodales.at@egestasAliquam.ca',0,0,'Ap #125-2438 Erat, Rd.','P.O. Box 856, 7727 Etiam Ave','1220OC','Switzerland','Denver','ANS','1011IR','Tonga','convallis, ante lectus convallis est,',1,1,NULL,'2006-07-17',1,1,1252594371,1248699494,1,1,'722-6607 Elementum, Road','P.O. Box 299, 333 Donec Ave','Ap #735-3032 Curabitur Ave','294-226 Habitant Av.','1850 Vel Avenue','3026 Elit, Rd.','268-1135 Ac Ave','8039 Quis Av.',0,NULL,NULL),(34,'Mariko','Ursa','Alec','1-617-380-3645','1-714-638-1115','1 86 793 6612-5187','(915) 951-3184','mauris sapien,','Donec','ipsum.sodales.purus@nuncQuisque.org','quis@Integer.org',0,0,'821-5956 Mus. Road','609-8870 Cursus Avenue','V2G 2H1','Norway','Tampa','Fr.','Z4 9TB','Sudan','scelerisque dui. Suspendisse ac metus',1,1,NULL,'2005-12-25',1,1,1248357588,1248690797,1,1,'Ap #893-3027 Ipsum. Rd.','8825 Etiam Rd.','8958 Erat, St.','9067 Aenean St.','9389 Eu, Road','Ap #984-1278 Sed Ave','Ap #784-5051 A, St.','P.O. Box 319, 6899 Aliquet Road',0,NULL,NULL),(35,'Yeo','Naida','Todd','1-600-801-1356','1-973-800-8398','1 81 613 1540-2362','(443) 666-1269','in, hendrerit','ornare','ridiculus@Suspendisseeleifend.org','nostra@NullafacilisisSuspendisse.com',0,0,'5876 Vivamus Avenue','5611 Maecenas St.','0796KE','Taiwan, Province of China','Alhambra','NT','ZF68 1GV','El Salvador','ut, sem. Nulla interdum. Curabitur',1,1,NULL,'2007-09-11',1,1,1246389035,1248625254,1,1,'7670 Nec Ave','100-4926 Tristique Road','322-4029 Enim. Rd.','Ap #488-1284 Ipsum Street','9972 Vitae Rd.','Ap #471-7341 Malesuada Street','7485 Posuere, Avenue','Ap #570-405 Cum Ave',0,NULL,NULL),(36,'Herrod','Chava','Armand','1-990-113-4815','1-788-263-8239','1 49 832 1264-4744','(856) 294-3511','scelerisque neque.','Aliquam','velit.in.aliquet@nunc.edu','sagittis@mattisornarelectus.ca',0,0,'P.O. Box 915, 4832 Aenean St.','347 Nam Ave','6730AY','Czech Republic','Danbury','CUL','41851','France','vitae aliquam eros turpis non',1,1,NULL,'2006-12-31',1,1,1244016845,1248239697,1,1,'P.O. Box 910, 1117 Mauris. Av.','Ap #414-2690 Posuere Street','405-2576 Non, Rd.','Ap #529-9792 Nunc St.','518-3558 Enim, Rd.','Ap #124-4564 A Rd.','715-8862 Bibendum Avenue','P.O. Box 198, 2863 Nunc Avenue',0,NULL,NULL),(37,'Hamish','Kirk','Sebastian','1-780-908-4010','1-363-720-7667','1 13 642 8551-1380','(570) 320-4230','elit, pretium','gravida.','id@egetlaoreetposuere.edu','imperdiet.ullamcorper.Duis@turpisNulla.com',0,0,'P.O. Box 469, 3368 Sem St.','P.O. Box 208, 2903 Nam Av.','P2G 1J6','Belgium','Watervliet','Zuid Holland','20863','Virgin Islands, U.S.','facilisis non, bibendum sed, est.',1,1,NULL,'2009-02-04',1,1,1241688466,1248768912,1,1,'P.O. Box 269, 4215 Mauris Street','4889 Integer St.','P.O. Box 346, 5666 Aliquet St.','828-2466 Facilisis, St.','Ap #318-8538 Inceptos Road','Ap #723-6165 Amet Road','P.O. Box 265, 3592 Aliquam Road','404-4034 Eu Rd.',0,NULL,NULL),(38,'Regina','Boris','Lana','1-131-655-4788','1-407-632-3016','1 23 149 3831-9913','(188) 626-3114','lectus pede','vitae','dictum.eu.eleifend@metus.com','euismod.in.dolor@Donectincidunt.com',0,0,'935-1882 Suscipit, St.','592-8643 Ipsum Av.','4408OF','Egypt','La Habra Heights','WOR','2561RX','Zimbabwe','tincidunt vehicula risus. Nulla eget',1,1,NULL,'2008-07-01',1,1,1268285206,1248251635,1,1,'P.O. Box 440, 6506 Dolor St.','175-3901 Nec Rd.','917-2508 Mauris Av.','Ap #386-3206 Fermentum St.','519-6429 In St.','520-2742 Libero Rd.','643-9660 Cras Rd.','452-7793 Aliquam Ave',0,NULL,NULL),(39,'Rosalyn','Mechelle','Lara','1-158-775-0002','1-449-514-9385','1 84 591 9397-9585','(362) 973-8963','enim. Curabitur','Suspendisse','natoque.penatibus@vulputate.edu','In.condimentum.Donec@mipede.org',0,0,'7736 A St.','825-5529 Felis, Avenue','8511KD','Saint Vincent and The Grenadines','San Bernardino','OH','50058','Zimbabwe','gravida molestie arcu. Sed eu',1,1,NULL,'2007-03-29',1,1,1267462920,1248141721,1,1,'Ap #564-7817 Quisque St.','Ap #757-9256 Auctor Rd.','463-7670 Consectetuer Av.','794-3802 Vel Street','440-7179 Et Rd.','478-3633 Cursus. Rd.','434-3290 Nibh Av.','8414 Vitae Av.',0,NULL,NULL),(40,'Kuame','Rigel','Kevin','1-529-202-5763','1-407-112-5652','1 49 670 4690-4447','(467) 714-3583','Nunc mauris.','vel,','lobortis.mauris@nibhQuisque.edu','Suspendisse.sed.dolor@Nullamlobortisquam.edu',0,0,'9996 Fames Av.','322-9610 Eu Street','65018','Nauru','Annapolis','SK','5543JL','China','justo sit amet nulla. Donec',1,1,NULL,'2005-07-10',1,1,1261815702,1248630729,1,1,'117-4307 Orci Road','254-2925 Hendrerit Avenue','Ap #200-2390 Et Avenue','Ap #587-844 Nisi. Ave','731-9273 Arcu. Avenue','339 Nulla Street','918-4591 Nibh St.','3697 Eu Road',0,NULL,NULL),(41,'Pamela','Bo','Martina','1-603-724-4095','1-297-884-1646','1 89 523 8275-3323','(796) 247-1981','nascetur ridiculus','penatibus','rhoncus@metusAeneansed.org','purus.in@etnunc.org',0,0,'6428 Id Ave','P.O. Box 632, 6621 Auctor Street','IX90 0WX','Cambodia','Mesquite','Indiana','U4Z 4U2','Israel','ligula. Nullam feugiat placerat velit.',1,1,NULL,'2007-10-30',1,1,1220878285,1249005888,1,1,'P.O. Box 346, 3434 Feugiat Av.','P.O. Box 207, 2483 Aliquet. Road','Ap #297-5237 Nullam Av.','5552 Cursus Avenue','8318 Tincidunt, Ave','P.O. Box 155, 9953 Nam Ave','290-7497 Aenean Avenue','P.O. Box 343, 9136 Dolor. Rd.',0,NULL,NULL),(42,'Brenda','Hayes','Cameran','1-526-772-6770','1-569-639-6305','1 62 417 4465-8700','(578) 745-4491','urna justo','tempor','sociis.natoque@erat.com','Curabitur.ut.odio@rhoncusid.org',0,0,'P.O. Box 891, 1275 Sociosqu Rd.','P.O. Box 877, 1149 Ornare, Rd.','UG2H 7GK','Cameroon','Sandy','Maine','NR56 4ET','Grenada','ornare. In faucibus. Morbi vehicula.',1,1,NULL,'2007-02-03',1,1,1263143200,1248140403,1,1,'3198 Blandit Av.','364-1025 Pede. Street','132-737 Ut Road','Ap #654-1714 Nunc Rd.','5827 Luctus Av.','Ap #218-4443 Eu, Rd.','P.O. Box 473, 2991 Amet Rd.','1541 Nunc Ave',0,NULL,NULL),(43,'Alma','Cullen','Dante','1-267-268-7448','1-628-323-8377','1 98 217 9189-3109','(826) 690-0872','dui. Cum','posuere','Lorem.ipsum@nuncacmattis.ca','varius.ultrices@Nuncullamcorpervelit.edu',0,0,'P.O. Box 371, 5248 Leo. Rd.','Ap #517-2701 Suspendisse Road','X8E 9Q8','Djibouti','Elsmere','BDF','DK64 9EW','Malawi','Suspendisse tristique neque venenatis lacus.',1,1,NULL,'2007-04-14',1,1,1241081440,1248821368,1,1,'144-6111 Eget, Av.','P.O. Box 752, 3942 Ac Street','P.O. Box 244, 7931 Proin Street','758-5229 Duis Rd.','P.O. Box 393, 7624 Magna Rd.','5616 Lectus, St.','823-944 Adipiscing Av.','P.O. Box 734, 6177 Varius. Road',0,NULL,NULL),(44,'Selma','Beatrice','Wallace','1-121-846-3220','1-823-664-4242','1 52 396 4622-7239','(324) 166-2654','sit amet,','tempor','bibendum@odiotristique.ca','enim.mi.tempor@Aliquam.ca',0,0,'182-8413 A Rd.','P.O. Box 229, 6504 Nulla Street','R0H 8SU','Grenada','Ponce','NS','A9Z 0UE','Indonesia','velit eget laoreet posuere, enim',1,1,NULL,'2007-11-06',1,1,1219907843,1248516610,1,1,'9564 Justo Street','190-4098 Dolor, Avenue','Ap #941-3486 Rhoncus. St.','107-4245 Tristique Rd.','Ap #873-7906 Non, Av.','P.O. Box 808, 6467 Eget Avenue','P.O. Box 998, 9494 Feugiat. Avenue','6383 Pede, Avenue',0,NULL,NULL),(45,'Amber','Dorian','Stewart','1-298-502-8063','1-821-611-8976','1 99 682 5785-9779','(539) 829-4577','vel quam','scelerisque','ipsum.ac.mi@dolor.org','sociis.natoque.penatibus@eutellus.org',0,0,'P.O. Box 649, 6013 Luctus St.','239-8471 Urna Street','Q9B 1L8','Seychelles','Cedar Falls','ON','WU84 3CV','Saint Pierre and Miquelon','amet diam eu dolor egestas',1,1,NULL,'2005-09-26',1,1,1273515152,1248403386,1,1,'P.O. Box 939, 5175 Et Street','2364 Mattis. Avenue','585-930 Orci, Street','8258 Sed, Road','7234 Nunc Street','500-4889 Amet, St.','357 Donec Av.','P.O. Box 572, 6809 Dictum St.',0,NULL,NULL),(46,'Yoshio','Igor','Meredith','1-289-530-5893','1-712-413-8584','1 47 599 7566-9287','(341) 399-2273','et, rutrum','sodales.','lorem.eget@consequatdolorvitae.ca','amet.dapibus@vestibulumnec.com',0,0,'P.O. Box 137, 3529 Arcu. Ave','P.O. Box 754, 5681 Interdum. Street','85918','New Zealand','Lockport','CHS','E9X 2Z2','Bosnia and Herzegovina','tellus. Nunc lectus pede, ultrices',1,1,NULL,'2005-11-02',1,1,1240895194,1248932775,1,1,'Ap #369-3707 Placerat, Av.','293-8826 A, St.','P.O. Box 307, 7326 Id St.','Ap #356-5785 Vivamus Road','Ap #982-382 Vel Rd.','P.O. Box 354, 6010 Molestie Av.','557-2554 Vestibulum Avenue','301-6898 Eleifend Rd.',0,NULL,NULL),(47,'Curran','Raphael','Teegan','1-406-743-0995','1-795-287-4464','1 81 766 1932-7267','(831) 231-1971','Nullam enim.','libero','Quisque.porttitor.eros@Quisquevarius.ca','diam.luctus.lobortis@Donec.ca',0,0,'1745 Egestas. Rd.','563-2971 Libero. Rd.','44256','Colombia','Gatlinburg','SRK','J4A 9G1','Thailand','Donec elementum, lorem ut aliquam',1,1,NULL,'2008-11-12',1,1,1237070063,1248189935,1,1,'P.O. Box 748, 4192 Ridiculus St.','Ap #686-5011 Blandit. Rd.','Ap #365-2736 Dictum Av.','Ap #737-6317 Lobortis, Rd.','Ap #780-4741 Dolor Av.','257-4355 In Av.','Ap #779-4234 Nam Ave','6716 Dictum Rd.',0,NULL,NULL),(48,'Dieter','Hadassah','Cruz','1-185-451-0279','1-226-625-9307','1 15 544 1313-7346','(882) 185-8761','dui quis','ridiculus','semper@Donec.edu','Donec@milorem.com',0,0,'Ap #219-9322 Sapien, Rd.','997-3428 Nulla. Rd.','J1S 2U3','Faroe Islands','Derby','AK','8190CI','Lithuania','In ornare sagittis felis. Donec',1,1,NULL,'2007-07-15',1,1,1223375167,1248731915,1,1,'Ap #695-6775 Molestie Rd.','4585 Velit. Street','Ap #675-1437 Tempor Street','P.O. Box 392, 9528 A, Avenue','610-9266 Habitant Road','Ap #272-6128 Praesent Road','7177 Vel Rd.','569-9114 Metus Rd.',0,NULL,NULL),(49,'Fredericka','Yardley','Jeremy','1-651-760-7908','1-433-497-9132','1 63 970 3296-2427','(594) 674-6580','sem semper','Mauris','massa@eleifend.edu','ac.mi@facilisiseget.org',0,0,'4439 Dolor. Rd.','5347 Nisl St.','4301UR','Puerto Rico','Ocean City','Overijssel','72331','South Africa','vestibulum lorem, sit amet ultricies',1,1,NULL,'2010-04-13',1,1,1250437020,1248262585,1,1,'772-6038 Netus Av.','P.O. Box 867, 1615 Fusce Rd.','P.O. Box 950, 3126 Nullam Av.','P.O. Box 141, 2170 Tempus Av.','P.O. Box 179, 4997 Dolor St.','Ap #783-9746 Orci, Street','5713 Fusce Av.','Ap #118-6810 Porttitor Rd.',0,NULL,NULL),(50,'Hilel','Catherine','Colleen','1-321-699-6010','1-321-794-4646','1 52 207 7131-2644','(704) 764-9771','Quisque libero','pellentesque','nunc@lacusQuisqueimperdiet.org','eget.ipsum@magnanecquam.com',0,0,'Ap #202-5966 Dignissim Road','P.O. Box 361, 5060 Elit Ave','27225','United Kingdom','Huntsville','Fife','0175HJ','Guadeloupe','vestibulum massa rutrum magna. Cras',1,1,NULL,'2009-03-07',1,1,1238443774,1248586183,1,1,'5787 Tempus St.','9322 Cras St.','471-7704 Non St.','P.O. Box 949, 696 Accumsan Av.','273-8624 Habitant Rd.','Ap #910-781 Erat Rd.','589-6962 Id Ave','P.O. Box 396, 613 Feugiat St.',0,NULL,NULL),(51,'Alma','Dora','Cassady','1-369-250-1686','1-904-460-2799','1 14 615 7567-7704','(720) 736-6067','gravida molestie','Aliquam','sagittis.placerat@odio.com','vitae@elitafeugiat.com',0,0,'1225 Egestas Rd.','4287 Ac Rd.','R0 4BT','Australia','Bradbury','CLK','5660FL','Bouvet Island','cursus vestibulum. Mauris magna. Duis',1,1,NULL,'2009-03-26',1,1,1259471386,1249049331,1,1,'1569 Vivamus Ave','4829 Pellentesque Street','Ap #367-2249 Ridiculus St.','Ap #807-7470 Ad St.','6258 Eu, Av.','Ap #955-5103 At Rd.','P.O. Box 969, 7313 Ligula. Rd.','7634 At Avenue',0,NULL,NULL),(52,'Conan','Rashad','Danielle','1-988-201-3590','1-770-465-0507','1 32 791 7888-6174','(194) 220-0378','lacus. Aliquam','Nam','Nulla.facilisis@Fusce.ca','litora.torquent.per@quis.edu',0,0,'P.O. Box 196, 2808 In Road','P.O. Box 178, 2373 Tempus, Street','J3 5LS','French Southern Territories','El Monte','NT','F2Q 3G8','United Kingdom','tempor, est ac mattis semper,',1,1,NULL,'2008-03-24',1,1,1247304330,1248784091,1,1,'P.O. Box 484, 8228 Eu Rd.','Ap #117-4383 Enim. Av.','2992 Arcu. Av.','Ap #937-8165 Luctus St.','Ap #765-6896 Mi St.','713-3387 Lorem, Ave','158-1104 Sit St.','P.O. Box 760, 4227 Nibh. Av.',0,NULL,NULL),(53,'Cooper','Carter','Neil','1-119-835-7139','1-806-580-1152','1 66 445 5297-3685','(668) 249-4374','semper rutrum.','arcu.','Donec@porttitorinterdum.org','libero.mauris.aliquam@tristique.org',0,0,'Ap #752-3724 Erat Rd.','P.O. Box 659, 9676 Vitae Road','0235RE','Andorra','Plainfield','Kincardineshire','5312SF','Myanmar','sem ut dolor dapibus gravida.',1,1,NULL,'2009-12-17',1,1,1224967731,1248836446,1,1,'2220 Amet Ave','Ap #302-3036 Massa. Street','1523 Mi Ave','Ap #945-1224 Suspendisse Ave','8373 Non St.','Ap #431-5422 Risus. Ave','Ap #678-2379 Gravida St.','767-8253 Enim, Rd.',0,NULL,NULL),(54,'Yoshi','Xandra','Brenden','1-361-408-9995','1-283-950-3103','1 76 601 1700-7133','(374) 174-0268','metus. Aenean','ligula','et.ultrices@euenimEtiam.ca','massa@fringillaporttitor.com',0,0,'P.O. Box 671, 419 Id Avenue','281-9902 Varius Ave','C8 0ED','Norway','Atwater','N.-Br.','7348AM','Norfolk Island','Suspendisse sagittis. Nullam vitae diam.',1,1,NULL,'2007-07-24',1,1,1225114660,1248811490,1,1,'2096 Nulla Avenue','590-2315 Ut Ave','Ap #239-661 Cum Rd.','6063 Duis Rd.','Ap #564-2804 Sed, Rd.','P.O. Box 199, 3348 Aliquet Road','P.O. Box 238, 9328 Duis Avenue','8111 Quisque Road',0,NULL,NULL),(55,'Roanna','Kai','Aline','1-515-627-2032','1-697-931-0446','1 46 836 2259-7549','(101) 821-2471','dolor egestas','Donec','a.felis@vestibulumneceuismod.ca','ligula.Aliquam@Maurisquis.org',0,0,'6077 Ut Street','P.O. Box 705, 7480 Conubia Av.','X09 0GJ','Togo','Kokomo','Zld.','03109','Honduras','metus. In lorem. Donec elementum,',1,1,NULL,'2006-03-25',1,1,1243427424,1248643680,1,1,'Ap #139-4731 Donec Rd.','P.O. Box 111, 6484 Magna. Rd.','116-3375 Morbi Avenue','939-9685 Mauris Av.','335-8801 Lectus Av.','P.O. Box 260, 3430 Laoreet Av.','9999 Risus Av.','P.O. Box 595, 4415 Eget Road',0,NULL,NULL),(56,'Alma','Chiquita','Reagan','1-168-426-7161','1-194-942-0554','1 45 539 5835-0156','(999) 661-2711','lacus. Mauris','semper','amet.orci@gravida.edu','sapien.cursus@dapibusquam.ca',0,0,'8379 Felis Rd.','6472 Nec Av.','54415','Panama','Wisconsin Rapids','New Jersey','J6C 8D8','Palestinian Territory, Occupied','arcu. Vestibulum ante ipsum primis',1,1,NULL,'2008-06-01',1,1,1216675901,1248931414,1,1,'P.O. Box 745, 5895 Vehicula Rd.','6738 Neque Rd.','Ap #665-2252 Rutrum Rd.','Ap #725-8961 Sed Av.','Ap #184-637 Cras Av.','4829 Lobortis Ave','P.O. Box 387, 9428 Pharetra Road','Ap #106-6203 Elementum St.',0,NULL,NULL),(57,'Faith','Moana','Mariam','1-722-930-5099','1-970-908-8502','1 64 274 7700-4776','(970) 131-0307','Nulla dignissim.','eu,','eu.eleifend.nec@acliberonec.com','consectetuer@Maecenasiaculisaliquet.org',0,0,'280-946 Massa. St.','4343 Nunc Avenue','NY0R 9IT','Poland','Pierre','PA','9371LM','Belgium','dis parturient montes, nascetur ridiculus',1,1,NULL,'2005-12-26',1,1,1275142718,1249055365,1,1,'5808 Duis Street','P.O. Box 912, 3711 Malesuada Rd.','717-9091 Mi St.','9628 Nullam St.','336-1683 Libero St.','P.O. Box 761, 5754 Metus St.','Ap #901-636 Massa Ave','P.O. Box 164, 5569 A Rd.',0,NULL,NULL),(58,'Stone','Malcolm','Ulysses','1-278-802-7770','1-283-620-4450','1 84 355 8837-5283','(273) 450-1529','tortor at','fermentum','non@et.org','erat@est.ca',0,0,'9439 Sapien, Road','P.O. Box 997, 6073 Amet Street','Z7F 0VU','Eritrea','Salem','Nebraska','V8J 6T4','Trinidad and Tobago','libero. Proin mi. Aliquam gravida',1,1,NULL,'2007-01-25',1,1,1229391232,1248401303,1,1,'P.O. Box 821, 6968 Vulputate Avenue','Ap #619-1234 Dolor Av.','Ap #203-9895 Phasellus Ave','649 Curabitur Avenue','Ap #491-5100 Cras Rd.','2126 Urna Avenue','Ap #947-8309 Eget Avenue','Ap #776-5895 Lorem Ave',0,NULL,NULL),(59,'Cathleen','Francesca','Nero','1-403-500-4809','1-463-963-7876','1 56 524 1002-3754','(105) 144-1487','at fringilla','Sed','massa.Integer@nequeSed.ca','Donec@tinciduntcongue.ca',0,0,'9791 Risus Ave','Ap #578-7926 Sem Street','IV07 9MP','Rwanda','Farrell','AL','85985','Egypt','ornare sagittis felis. Donec tempor,',1,1,NULL,'2005-09-15',1,1,1238262817,1248292832,1,1,'P.O. Box 783, 7658 Sem, St.','9300 Sed St.','P.O. Box 237, 2569 Dolor Street','P.O. Box 916, 5806 Iaculis Avenue','P.O. Box 444, 2065 Amet Ave','882-8163 Risus. Road','5043 Sit Rd.','372-8593 Lacus. Ave',0,NULL,NULL),(60,'Camille','Dale','Tasha','1-440-650-8469','1-273-497-7852','1 45 722 1511-7052','(127) 202-7034','justo faucibus','ultrices','felis.Nulla@posuerecubilia.ca','tempus.non.lacinia@maurisblandit.ca',0,0,'812-7702 Penatibus Rd.','373-9274 Donec Av.','B6G 8S6','Falkland Islands (Malvinas)','Brigham City','U.','0878OW','Puerto Rico','sem ut dolor dapibus gravida.',1,1,NULL,'2009-06-12',1,1,1222652539,1248562364,1,1,'P.O. Box 804, 9669 Sed Av.','429-4297 Hendrerit Rd.','8051 Lobortis St.','654 Sed Road','Ap #159-4974 Ipsum. Ave','P.O. Box 141, 4835 Egestas Street','3425 Elementum, St.','P.O. Box 122, 5895 Orci. St.',0,NULL,NULL),(61,'Logan','Elizabeth','Wendy','1-169-142-4326','1-297-649-7545','1 74 975 1706-0067','(171) 315-5764','semper, dui','inceptos','tempor.bibendum.Donec@odio.ca','est.congue@dis.ca',0,0,'Ap #876-2111 Ligula Rd.','5688 Porta Avenue','TE7H 7NY','Costa Rica','Akron','Fife','J8M 2CH','Brunei Darussalam','imperdiet nec, leo. Morbi neque',1,1,NULL,'2007-04-05',1,1,1271138763,1248620861,1,1,'2086 Lorem Av.','332-2649 Elit St.','P.O. Box 780, 1822 Risus Rd.','Ap #382-9539 Libero. Rd.','920-3995 Eu, Road','7284 Proin Avenue','Ap #282-465 Lacus. Street','5006 Purus. Road',0,NULL,NULL),(62,'Preston','Bethany','Philip','1-768-582-9359','1-291-132-0302','1 66 550 8456-5332','(280) 484-9769','dolor, nonummy','velit','Duis@euduiCum.com','semper@idmagnaet.ca',0,0,'P.O. Box 623, 953 Adipiscing Road','992-1111 Phasellus Avenue','N2O 5Y2','Kiribati','Taunton','Zuid Holland','2868XK','Saint Vincent and The Grenadines','placerat velit. Quisque varius. Nam',1,1,NULL,'2006-03-17',1,1,1240957039,1248775649,1,1,'P.O. Box 413, 7005 Libero Av.','9238 Vel, St.','1863 Eget St.','Ap #734-9844 Tellus St.','6558 Eu Rd.','P.O. Box 248, 137 Sit Street','5624 Placerat. Rd.','7926 Tempor Ave',0,NULL,NULL),(63,'Helen','William','Unity','1-284-466-8725','1-740-713-9159','1 71 825 5039-0219','(755) 443-6901','a neque.','metus','Fusce@estvitaesodales.edu','mauris.sit.amet@mauris.com',0,0,'Ap #139-667 Velit St.','P.O. Box 547, 622 Interdum Street','L2J 1E4','Cuba','Easton','Delaware','P31 6JU','Uruguay','tincidunt orci quis lectus. Nullam',1,1,NULL,'2005-09-18',1,1,1255779495,1248447521,1,1,'P.O. Box 795, 399 Nunc Ave','3396 Dapibus Street','3830 Fermentum Rd.','P.O. Box 105, 5214 Orci St.','111 Sed Rd.','641-8718 Est Rd.','369-6077 Non St.','340-8261 Diam Rd.',0,NULL,NULL),(64,'Maris','Alden','Candace','1-851-393-2984','1-674-227-7138','1 66 581 8094-1036','(355) 685-4312','a, magna.','porttitor','Nullam.enim@Mauris.ca','Integer.vitae.nibh@Proinvelit.ca',0,0,'P.O. Box 663, 7000 Nunc Street','P.O. Box 785, 9671 Vitae Ave','5480TZ','Cyprus','Marietta','Worcestershire','2938QM','Monaco','viverra. Donec tempus, lorem fringilla',1,1,NULL,'2006-03-01',1,1,1269339764,1248336618,1,1,'418-121 Amet Rd.','P.O. Box 137, 2858 Sociis St.','Ap #638-698 Augue Road','3564 Auctor, Av.','2854 Ultrices. Rd.','5678 Ornare, St.','200-739 Magna Avenue','778-2014 Dui, Rd.',0,NULL,NULL),(65,'Jacob','Salvador','Damon','1-785-446-3367','1-125-356-1130','1 44 219 5919-7376','(910) 643-2235','sodales elit','dis','tellus@nequeSedeget.ca','urna.convallis.erat@pede.ca',0,0,'Ap #399-4710 Dictum Rd.','P.O. Box 132, 6976 Bibendum Ave','I8N 6A9','Botswana','San Mateo','Gr.','46571','United Arab Emirates','neque. In ornare sagittis felis.',1,1,NULL,'2005-12-23',1,1,1256130102,1248244785,1,1,'583-2096 Pretium St.','478-5282 A, St.','Ap #903-7073 Neque Rd.','P.O. Box 622, 2374 Mattis Ave','P.O. Box 454, 176 Consectetuer Ave','Ap #308-838 Massa. Rd.','P.O. Box 411, 3919 Duis Av.','Ap #796-9377 Felis Rd.',0,NULL,NULL),(66,'Amelia','MacKensie','Haviva','1-850-722-2410','1-650-716-9158','1 49 364 7437-0755','(838) 152-2697','non ante','Aenean','nonummy.Fusce.fermentum@sedorci.ca','porttitor.scelerisque@semperpretiumneque.edu',0,0,'Ap #601-2524 At Rd.','2498 Et Street','B3S 4N3','Dominican Republic','Escondido','CON','RW22 4LW','Saint Vincent and The Grenadines','tempus risus. Donec egestas. Duis',1,1,NULL,'2009-07-15',1,1,1251516189,1248469451,1,1,'304 Sed Rd.','P.O. Box 620, 2389 Cras Ave','943-2580 Nullam Rd.','P.O. Box 299, 8296 A, Rd.','Ap #392-9473 Mauris Ave','988-5136 Risus, Street','961-2897 Donec Rd.','4447 Tincidunt Road',0,NULL,NULL),(67,'Wanda','Cherokee','Remedios','1-547-743-8714','1-487-360-6651','1 28 240 5454-6390','(165) 452-5964','vitae diam.','non','mi@scelerisquescelerisque.com','nunc.id.enim@Curabitur.com',0,0,'7494 Luctus Road','P.O. Box 782, 4472 Felis Road','9492BZ','Syrian Arab Republic','Kenosha','QC','O3M 3A3','Papua New Guinea','aliquam arcu. Aliquam ultrices iaculis',1,1,NULL,'2008-10-03',1,1,1251782974,1248250165,1,1,'295-1743 Lobortis Ave','Ap #801-4453 Ornare. St.','Ap #234-7181 Gravida Ave','2250 Tincidunt. Ave','Ap #723-973 Adipiscing St.','187-9430 Dictum Av.','702-918 Nunc, Rd.','583-5377 Natoque Av.',0,NULL,NULL),(68,'Beck','Yeo','Julie','1-766-533-4534','1-105-788-7873','1 14 563 7188-1367','(910) 356-6077','non, lacinia','diam','Ut.sagittis@eleifend.com','nec.tempus@scelerisquemollisPhasellus.edu',0,0,'P.O. Box 887, 7009 A, Av.','214-2057 Nulla St.','N1X 2D6','Tunisia','Rock Island','Gelderland','2761MA','Haiti','Duis a mi fringilla mi',1,1,NULL,'2006-06-03',1,1,1250963383,1248318348,1,1,'2128 Sit Road','Ap #473-2753 Cursus. Avenue','P.O. Box 847, 1883 Penatibus Avenue','869-8796 Orci. Street','337 Sociis Street','Ap #434-4383 Magna. St.','Ap #105-1070 Arcu Ave','613-8887 Tincidunt St.',0,NULL,NULL),(69,'Natalie','Sean','Hiram','1-692-804-2939','1-329-435-7724','1 42 504 8534-4714','(995) 514-8070','a, scelerisque','semper','hendrerit.consectetuer@Sednuncest.ca','Nullam@Suspendisse.org',0,0,'P.O. Box 920, 470 Malesuada Rd.','P.O. Box 126, 6711 Magna Ave','D2 9YI','Paraguay','Zanesville','Quebec','OL1T 3EI','Anguilla','eget, ipsum. Donec sollicitudin adipiscing',1,1,NULL,'2008-08-16',1,1,1224356080,1248432423,1,1,'Ap #789-3151 Metus Avenue','955-6047 Maecenas St.','Ap #952-3719 Pellentesque Av.','Ap #928-7476 Eu, Road','9935 Cum Street','582-2999 Vel, Rd.','P.O. Box 909, 342 Sed Road','960-4806 Varius St.',0,NULL,NULL),(70,'Gail','Kristen','Kendall','1-214-538-9266','1-536-280-6191','1 28 200 5958-5197','(640) 543-0184','interdum feugiat.','et','a@lobortismaurisSuspendisse.ca','eu.neque.pellentesque@miDuis.org',0,0,'Ap #365-3799 Ultrices Rd.','987-9820 Imperdiet Road','L3J 5K2','Zambia','Charlottesville','Zeeland','B3Y 4Q2','Saint Vincent and The Grenadines','neque tellus, imperdiet non, vestibulum',1,1,NULL,'2007-06-21',1,1,1265709307,1248871382,1,1,'7021 Arcu Rd.','P.O. Box 612, 768 Cras St.','3318 Ipsum Street','Ap #705-8318 Lobortis Rd.','745-7984 Ac St.','Ap #316-1842 Nunc Avenue','4334 Condimentum. Street','P.O. Box 497, 8028 Elit Ave',0,NULL,NULL),(71,'Callum','Adria','Sopoline','1-927-382-8513','1-650-409-9216','1 69 136 2843-8331','(169) 345-4810','in consectetuer','vel','Morbi@Maurisut.edu','tempor@congue.edu',0,0,'P.O. Box 241, 7278 Nunc Road','P.O. Box 990, 5251 Nulla. Rd.','81520','Norway','Frederiksted','Zuid Holland','66277','Afghanistan','dictum magna. Ut tincidunt orci',1,1,NULL,'2006-11-16',1,1,1262449900,1248646240,1,1,'9227 Quisque St.','9445 Suspendisse Street','Ap #250-5230 Ipsum Rd.','298-2906 Eget, Rd.','8081 Elementum Rd.','P.O. Box 604, 1837 Mi Rd.','Ap #503-2256 A Street','P.O. Box 629, 5890 Eu Road',0,NULL,NULL),(72,'Yasir','Keith','Caldwell','1-613-249-5626','1-224-684-2935','1 77 751 8168-6847','(262) 118-2705','vel turpis.','tristique','nec@tempusrisusDonec.ca','laoreet.posuere@noncursusnon.org',0,0,'Ap #576-8257 Donec Avenue','215-5299 Velit. Road','6018WF','Hungary','Bartlesville','Overijssel','G11 7ZO','Cayman Islands','Aliquam ornare, libero at auctor',1,1,NULL,'2007-01-20',1,1,1251637689,1248250163,1,1,'634-1848 Mauris St.','9613 Lorem Street','P.O. Box 317, 522 Sapien Rd.','478-9484 Velit. Ave','340-7115 Ut Road','1676 Mauris Avenue','505-2823 Id St.','101-3434 Eget St.',0,NULL,NULL),(73,'Gloria','Wynne','Louis','1-908-126-3654','1-106-816-7398','1 73 243 6479-4394','(297) 823-3643','vitae, erat.','enim,','gravida.non@adipiscingfringillaporttitor.ca','magnis.dis.parturient@purus.edu',0,0,'Ap #471-5354 Natoque Rd.','510-9474 Imperdiet Ave','T6O 1I0','Kyrgyzstan','Parma','NY','57392','Grenada','egestas lacinia. Sed congue, elit',1,1,NULL,'2007-04-07',1,1,1214458966,1248756017,1,1,'P.O. Box 589, 151 Amet, Rd.','P.O. Box 510, 2039 Ipsum. Av.','734-9807 Pellentesque St.','Ap #396-7995 Vestibulum. Rd.','P.O. Box 366, 3772 Proin Rd.','926-4304 Fusce Avenue','3394 Lobortis. Road','P.O. Box 860, 320 Aliquet Avenue',0,NULL,NULL),(74,'Iliana','Hedy','Prescott','1-981-775-2129','1-270-636-3251','1 41 312 6880-2591','(779) 300-5923','lacus. Ut','eget,','lacinia.at.iaculis@Fusce.edu','ligula@malesuada.org',0,0,'449-2725 Pretium St.','P.O. Box 895, 6700 At Avenue','99859','Bahamas','Shelton','Iowa','47081','Georgia','malesuada malesuada. Integer id magna',1,1,NULL,'2008-12-13',1,1,1214746919,1248505987,1,1,'P.O. Box 588, 1660 Malesuada St.','P.O. Box 639, 1745 Pede. Rd.','P.O. Box 389, 9857 Vestibulum St.','936-2455 Elit, St.','Ap #810-1726 Ultrices St.','6931 Lorem Street','P.O. Box 606, 8595 Lorem Rd.','3314 Pellentesque, Ave',0,NULL,NULL),(75,'Odysseus','Justina','Jonah','1-765-156-2964','1-260-181-2787','1 41 329 9127-6742','(258) 978-5201','mi enim,','egestas.','ornare@lacusQuisque.com','Sed@disparturient.com',0,0,'Ap #635-563 Eleifend Street','Ap #665-5369 Consectetuer Street','L8 3YC','Kuwait','Canton','NU','6063RR','Oman','dolor sit amet, consectetuer adipiscing',1,1,NULL,'2007-11-01',1,1,1222762883,1248584427,1,1,'P.O. Box 694, 383 Phasellus Avenue','P.O. Box 495, 1497 Curabitur Street','P.O. Box 409, 4032 Eu Ave','116-3467 Malesuada St.','P.O. Box 227, 8646 Eros Ave','P.O. Box 199, 6203 Nec Rd.','Ap #351-9601 Vitae Street','P.O. Box 761, 2732 Nec, Av.',0,NULL,NULL),(76,'Freya','Tarik','Wesley','1-423-828-8595','1-613-974-0363','1 99 347 5197-0923','(305) 291-1472','Nullam scelerisque','nec','semper@nequevitaesemper.ca','eget@risusDonecegestas.ca',0,0,'6013 Tellus Ave','Ap #795-3729 Ante, St.','99710','Croatia','Tallahassee','Alberta','5782TL','Slovakia','luctus aliquet odio. Etiam ligula',1,1,NULL,'2007-01-20',1,1,1268653124,1248204272,1,1,'4017 Ante St.','660-4409 A St.','P.O. Box 349, 1102 Libero Av.','Ap #912-4047 Lorem Rd.','6863 Ultricies Rd.','595-7069 Sem Av.','Ap #866-8297 Vel Rd.','5311 Aliquam Street',0,NULL,NULL),(77,'Nyssa','Kathleen','Wylie','1-630-842-4118','1-976-715-9726','1 47 511 7064-5609','(794) 935-0157','elementum, lorem','dui','Vivamus.molestie.dapibus@neque.org','accumsan.interdum.libero@auctorMauris.edu',0,0,'P.O. Box 241, 3240 Fusce Avenue','832-4838 Ut Ave','3584TK','Costa Rica','Montpelier','South Dakota','46269','Azerbaijan','ligula. Nullam feugiat placerat velit.',1,1,NULL,'2006-03-13',1,1,1219106116,1248924372,1,1,'3861 Adipiscing Ave','693 Luctus Road','9691 Mauris Ave','9489 Cras Ave','Ap #898-9746 Lorem Road','754-8220 Magna Avenue','4811 Dui Street','P.O. Box 281, 3446 Dignissim Avenue',0,NULL,NULL),(78,'Ariel','Marah','Frances','1-200-993-6923','1-382-321-6098','1 39 369 5825-4564','(664) 933-2660','neque et','dapibus','metus.In@Seddiam.edu','pretium.aliquet@Curabitur.edu',0,0,'P.O. Box 317, 6847 Tellus, St.','939-5013 Est, Av.','4377AL','Slovenia','Visalia','Manitoba','1033TW','Croatia','pellentesque. Sed dictum. Proin eget',1,1,NULL,'2009-12-03',1,1,1264008067,1248173687,1,1,'760-4060 Ac Rd.','710-4243 Eu, Avenue','Ap #726-8878 Nibh Avenue','3176 Mattis Av.','P.O. Box 496, 8543 Fermentum Ave','1351 Leo. St.','P.O. Box 348, 8812 Pellentesque Road','351-9038 Mi St.',0,NULL,NULL),(79,'Neville','Wilma','Fiona','1-398-978-7719','1-524-425-3397','1 42 692 9215-1480','(378) 256-2515','dui. Fusce','sed','dictum.ultricies.ligula@Aliquamnecenim.com','nulla.ante@estconguea.com',0,0,'Ap #113-6655 Commodo St.','970-7829 Tempor Rd.','97413','Nepal','Pendleton','British Columbia','OV7H 8KE','French Guiana','risus. Quisque libero lacus, varius',1,1,NULL,'2008-11-13',1,1,1251665429,1248207571,1,1,'P.O. Box 866, 4913 Duis Street','208-8397 Cursus. Street','4834 Nulla Rd.','123-7646 Libero Rd.','4670 Gravida Road','Ap #513-180 Eget, St.','732-6214 Non, Road','P.O. Box 719, 8495 Nibh Rd.',0,NULL,NULL),(80,'Harlan','Roary','Myles','1-946-470-7788','1-273-588-9843','1 31 976 1454-2237','(874) 941-7335','amet ultricies','aliquam','Curabitur.egestas@NullafacilisiSed.ca','rutrum@diam.com',0,0,'2971 Nec St.','Ap #916-7711 Proin St.','F21 3AM','Rwanda','Moline','CLK','YN67 8FT','French Polynesia','non, cursus non, egestas a,',1,1,NULL,'2010-01-31',1,1,1249318971,1248834105,1,1,'4558 Tincidunt Road','291-8123 Montes, Rd.','318-6235 Et, Av.','P.O. Box 522, 9099 Ut St.','Ap #320-7775 Nunc Ave','Ap #957-7324 Mauris Av.','Ap #846-9539 Tellus St.','516-5671 Facilisis Road',0,NULL,NULL),(81,'Colette','Amal','Inez','1-520-341-7314','1-132-327-2923','1 60 364 6098-1434','(568) 642-7931','enim consequat','tincidunt','non.leo@Nullamlobortisquam.edu','porttitor@Integer.org',0,0,'Ap #649-9619 Iaculis St.','300-4788 Eu St.','D5K 9L7','Argentina','New Haven','Oklahoma','6892VT','Cyprus','cursus. Nunc mauris elit, dictum',1,1,NULL,'2007-02-06',1,1,1224846025,1248274592,1,1,'780-7925 Quam St.','875-9712 Iaculis St.','Ap #141-471 Iaculis Street','9381 In, Rd.','Ap #176-9364 Egestas Ave','P.O. Box 797, 6099 Vel Av.','142-5500 Aliquam Street','P.O. Box 334, 1958 Cursus. Av.',0,NULL,NULL),(82,'Amery','Dakota','Donna','1-393-399-2889','1-599-260-9292','1 60 868 1900-1231','(660) 698-3843','In lorem.','erat,','at@sitamet.ca','gravida.Aliquam.tincidunt@VivamusrhoncusDonec.org',0,0,'Ap #760-7476 Semper Ave','P.O. Box 896, 6466 Aliquet. St.','26765','Chile','Vicksburg','QC','77634','Israel','consequat dolor vitae dolor. Donec',1,1,NULL,'2010-05-02',1,1,1223540944,1248067321,1,1,'4367 Quisque Ave','6721 Ultrices. Avenue','P.O. Box 164, 2663 Tempor St.','417-9950 Ac, Street','554-4696 Ut St.','4746 Sodales St.','572-1193 Purus St.','Ap #941-4306 Fames Rd.',0,NULL,NULL),(83,'Geoffrey','Lionel','Dieter','1-563-705-9450','1-495-935-8860','1 22 227 7210-8455','(725) 329-8237','mi fringilla','sit','ligula@Fusce.edu','vel@lorem.com',0,0,'Ap #242-3226 Interdum St.','386 Nisl. Rd.','P7I 5O6','Ukraine','Glendora','KID','E1J 8F6','Heard Island and Mcdonald Islands','tristique pellentesque, tellus sem mollis',1,1,NULL,'2006-10-09',1,1,1226103094,1248203010,1,1,'Ap #516-7104 Nibh Road','P.O. Box 773, 946 Libero Ave','Ap #402-5927 Fringilla Rd.','Ap #316-3568 Tempor Rd.','Ap #671-4464 Quam. Avenue','P.O. Box 226, 5935 Sem Ave','6406 Lobortis Ave','Ap #841-8620 Aenean Rd.',0,NULL,NULL),(84,'Harper','Timon','Kaye','1-968-553-1779','1-746-254-2792','1 49 956 5349-2089','(834) 242-9493','dui. Cras','odio','enim.gravida.sit@lacus.org','tristique.senectus@orciin.org',0,0,'5519 Fusce Avenue','Ap #740-1472 Nibh. St.','P6O 8O0','Seychelles','Utica','Co. Limerick','E7C 2F9','Guadeloupe','Donec luctus aliquet odio. Etiam',1,1,NULL,'2008-03-09',1,1,1266654250,1248252416,1,1,'761-2986 Nibh Road','P.O. Box 192, 7505 Eu Rd.','337-8648 Curabitur Street','Ap #684-5923 Et Rd.','5426 Interdum Rd.','Ap #123-5373 Ullamcorper, Road','934-6231 Proin Street','P.O. Box 830, 3740 Elit. St.',0,NULL,NULL),(85,'Cecilia','Kylan','Amanda','1-493-357-8235','1-821-278-1244','1 73 195 2793-3973','(308) 180-3496','non justo.','purus','lorem.ipsum@lacinia.ca','Vivamus.nisi@amet.com',0,0,'P.O. Box 745, 1305 Tristique Av.','7083 Fringilla Ave','59763','Ecuador','Miami','Groningen','0728VY','United Arab Emirates','Mauris vel turpis. Aliquam adipiscing',1,1,NULL,'2006-04-11',1,1,1265370440,1248967483,1,1,'7402 Hendrerit St.','Ap #345-3996 Ullamcorper, Avenue','239-3430 Felis, St.','630-8462 Purus. Rd.','981 Enim. Road','5053 Quis Road','6492 Lobortis Rd.','Ap #837-8213 Ultrices St.',0,NULL,NULL),(86,'Lester','Idola','Basil','1-930-454-2178','1-992-708-2932','1 53 954 5420-8301','(556) 977-6676','nec ante.','rutrum','elit.erat@disparturientmontes.edu','erat.Etiam.vestibulum@Aliquamfringilla.org',0,0,'P.O. Box 333, 4611 Odio, Road','Ap #101-1211 Elit, Rd.','A8 4SN','Croatia','Fernley','Friesland','B8P 1D0','Tonga','lorem tristique aliquet. Phasellus fermentum',1,1,NULL,'2009-01-30',1,1,1251789403,1248944985,1,1,'P.O. Box 341, 8859 Lobortis Rd.','6806 Bibendum. Avenue','P.O. Box 827, 6826 A, Street','Ap #856-6513 Ut St.','Ap #729-7437 Imperdiet, Avenue','Ap #950-1821 Justo Rd.','P.O. Box 201, 4450 Nascetur Road','4876 Ac Avenue',0,NULL,NULL),(87,'Reed','Kyla','Teegan','1-167-767-4442','1-419-245-7372','1 89 424 4192-6823','(599) 333-6578','vulputate mauris','enim.','fames@vestibulumMaurismagna.ca','Vivamus.euismod.urna@Mauriseuturpis.org',0,0,'Ap #136-130 Sapien. Av.','P.O. Box 706, 3784 Mauris Av.','R4 9IC','Palau','Centennial','Ov.','W34 5JL','United Arab Emirates','risus. In mi pede, nonummy',1,1,NULL,'2008-02-04',1,1,1240140636,1248638262,1,1,'800-7245 Nunc Rd.','P.O. Box 731, 6323 At Road','2854 Sit St.','P.O. Box 691, 9739 Sodales Rd.','678-2207 Orci. St.','9127 Donec Street','P.O. Box 588, 5967 Curabitur St.','663-6843 Ante Street',0,NULL,NULL),(88,'Hedda','Frances','Evelyn','1-441-787-8465','1-511-724-5185','1 43 297 4548-9724','(982) 677-3224','augue ut','imperdiet','velit.eu@egetipsum.org','amet@nibh.edu',0,0,'Ap #273-4086 Dui, Rd.','Ap #955-8049 Rutrum, Avenue','K3Y 7M2','Burkina Faso','Statesboro','Overijssel','FK34 8WI','Israel','fermentum risus, at fringilla purus',1,1,NULL,'2009-02-27',1,1,1246262647,1248072228,1,1,'561-3490 Ridiculus St.','P.O. Box 733, 5516 Phasellus St.','5411 Ac Street','3341 Arcu St.','P.O. Box 927, 1874 Tellus Ave','P.O. Box 106, 5772 Libero St.','9660 Facilisis Avenue','518-4143 Fringilla. Street',0,NULL,NULL),(89,'Micah','Jessamine','Boris','1-214-274-3679','1-499-699-7950','1 93 105 5591-0131','(559) 195-7997','netus et','sapien,','erat.vitae.risus@rhoncus.com','sapien.Cras@dignissimlacusAliquam.org',0,0,'P.O. Box 322, 9875 Sed Av.','480-9026 Orci Road','9896VX','Jamaica','Oneonta','Z.-H.','S3K 9I6','Finland','sociis natoque penatibus et magnis',1,1,NULL,'2007-11-20',1,1,1215495865,1248663523,1,1,'P.O. Box 305, 9449 Libero Street','579-2529 Ultrices Road','Ap #652-1858 Eu, Ave','Ap #398-2554 Nunc Street','Ap #554-372 Ipsum Avenue','853-5824 Congue. St.','9802 Congue, Rd.','Ap #317-2154 Egestas. Av.',0,NULL,NULL),(90,'Daria','Joel','Hannah','1-929-113-6454','1-509-580-8666','1 34 612 4355-8850','(862) 227-7618','Praesent interdum','Praesent','dolor.dolor@ornarefacilisis.com','non@anuncIn.ca',0,0,'P.O. Box 590, 7316 Sapien. St.','P.O. Box 201, 3119 Purus Road','4002RG','Antarctica','Eureka','New Mexico','9555HN','United States','dictum ultricies ligula. Nullam enim.',1,1,NULL,'2007-04-01',1,1,1261305056,1248432145,1,1,'978-3855 Non St.','667-6607 Risus. Rd.','455-6757 Enim Street','220-8795 Faucibus Road','1025 Dictum Rd.','351-8000 Nibh. St.','Ap #978-6681 Facilisis. Road','3814 Nam Road',0,NULL,NULL),(91,'Desirae','Quinn','Ignacia','1-824-529-2181','1-689-220-4711','1 90 255 5997-7350','(702) 724-5403','parturient montes,','lobortis.','vitae.sodales@urnaVivamusmolestie.com','arcu.iaculis.enim@Nullamut.ca',0,0,'288 Vulputate Rd.','875-6537 Mauris. Rd.','97133','Mongolia','Kahului','Gelderland','3839RK','Cook Islands','Mauris molestie pharetra nibh. Aliquam',1,1,NULL,'2005-07-28',1,1,1214456287,1249000210,1,1,'7590 Libero Street','Ap #440-9696 Nascetur Rd.','Ap #696-1302 Posuere Av.','Ap #537-2861 Dui. Avenue','Ap #678-4674 Nunc St.','272-7039 Mauris Ave','5284 Arcu. Av.','P.O. Box 429, 3070 Quis Rd.',0,NULL,NULL),(92,'Lee','Gage','Dane','1-498-568-0104','1-370-718-1354','1 23 980 2137-5515','(461) 362-3151','egestas rhoncus.','nec','lobortis@magnaPraesent.org','ac.orci.Ut@Morbiaccumsanlaoreet.org',0,0,'Ap #335-8162 Diam St.','7955 Mauris Av.','Y53 2WL','Somalia','West Lafayette','SHI','0257MJ','Armenia','Donec egestas. Duis ac arcu.',1,1,NULL,'2007-02-26',1,1,1246581712,1248592284,1,1,'Ap #864-8507 Tincidunt Avenue','P.O. Box 264, 6623 Varius St.','P.O. Box 419, 7548 Ac Rd.','2900 Tincidunt. Road','Ap #297-7143 Phasellus Road','P.O. Box 935, 8538 Auctor St.','1776 Donec St.','P.O. Box 431, 7406 Fringilla, Ave',0,NULL,NULL),(93,'Aubrey','Kiona','Heidi','1-906-373-6768','1-230-921-1294','1 48 636 6736-2763','(481) 758-4407','purus ac','vitae,','vel@eget.org','vel.venenatis@nibhDonec.ca',0,0,'Ap #477-4438 Arcu. Ave','P.O. Box 339, 5386 Ullamcorper St.','2527YI','Jamaica','Kankakee','NT','2896HX','Somalia','ligula consectetuer rhoncus. Nullam velit',1,1,NULL,'2008-11-28',1,1,1220552506,1248348989,1,1,'P.O. Box 811, 1971 Morbi Ave','849-8974 In Street','P.O. Box 196, 8926 Non Av.','Ap #463-251 Eu Road','3767 Ut Street','Ap #131-8297 Molestie St.','P.O. Box 287, 9619 Convallis, Av.','286-6606 Risus Road',0,NULL,NULL),(94,'Cody','Beverly','Lillian','1-127-970-6106','1-590-906-9918','1 13 707 2243-3036','(724) 885-8507','lobortis tellus','Suspendisse','lectus.justo.eu@euplacerateget.com','nulla.Integer.vulputate@etrutrumeu.ca',0,0,'P.O. Box 999, 284 Id, Rd.','364-7476 Convallis St.','NO2 3CH','Canada','Port Jervis','Fl.','W0W 8BM','Timor-leste','porttitor interdum. Sed auctor odio',1,1,NULL,'2006-03-30',1,1,1256438224,1248162990,1,1,'P.O. Box 736, 4749 Sed Ave','480-5219 Mauris Ave','P.O. Box 314, 7096 Dui. Street','6857 Ut St.','P.O. Box 870, 2887 Duis Av.','483-6499 Commodo Rd.','P.O. Box 802, 2228 Convallis St.','664-7365 Semper Street',0,NULL,NULL);
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_notes`
--

DROP TABLE IF EXISTS `contact_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_notes` (
  `contact_notes_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` int(10) unsigned NOT NULL,
  `notes` text CHARACTER SET utf8 NOT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`contact_notes_id`),
  KEY `opportunity_id` (`contact_id`,`created_by`),
  KEY `created_by` (`created_by`),
  KEY `contact_id` (`contact_id`),
  CONSTRAINT `contact_notes_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `contact_notes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_notes`
--

LOCK TABLES `contact_notes` WRITE;
/*!40000 ALTER TABLE `contact_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currency`
--

DROP TABLE IF EXISTS `currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currency` (
  `currency_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `symbol` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `name` varchar(10) CHARACTER SET latin1 NOT NULL,
  `short_description` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `description` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currency`
--

LOCK TABLES `currency` WRITE;
/*!40000 ALTER TABLE `currency` DISABLE KEYS */;
/*!40000 ALTER TABLE `currency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fa_account`
--

DROP TABLE IF EXISTS `fa_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fa_account` (
  `fa_account_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `debit` decimal(10,0) DEFAULT NULL,
  `credit` decimal(10,0) DEFAULT NULL,
  `transaction_timestamp` int(11) DEFAULT NULL,
  `notes` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`fa_account_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fa_account`
--

LOCK TABLES `fa_account` WRITE;
/*!40000 ALTER TABLE `fa_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `fa_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fa_contact`
--

DROP TABLE IF EXISTS `fa_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fa_contact` (
  `fa_contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` int(10) unsigned NOT NULL,
  `debit` decimal(10,0) DEFAULT NULL,
  `credit` decimal(10,0) DEFAULT NULL,
  `transaction_timestamp` int(11) DEFAULT NULL,
  `notes` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`fa_contact_id`),
  KEY `contact_id` (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fa_contact`
--

LOCK TABLES `fa_contact` WRITE;
/*!40000 ALTER TABLE `fa_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `fa_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fa_group`
--

DROP TABLE IF EXISTS `fa_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fa_group` (
  `fa_group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `fa_group_category_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`fa_group_id`),
  UNIQUE KEY `name` (`name`),
  KEY `fa_group_category_id` (`fa_group_category_id`),
  CONSTRAINT `fa_group_ibfk_1` FOREIGN KEY (`fa_group_category_id`) REFERENCES `fa_group` (`fa_group_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fa_group`
--

LOCK TABLES `fa_group` WRITE;
/*!40000 ALTER TABLE `fa_group` DISABLE KEYS */;
INSERT INTO `fa_group` VALUES (1,'Capital Account',1),(2,'Loans',1),(3,'Current Liabilities',1),(4,'Fixed Assets',2),(5,'Investments',2),(6,'Current Assets',2),(7,'Miscellaneous Expenses',2),(8,'Suspense Account',NULL),(9,'Sales Accounts',3),(10,'Purchase Accounts',3),(11,'Direct Incomes',3),(12,'Indirect Incomes',3),(13,'Direct Expenses',3),(14,'Indirect Expenses',3),(15,'Reserves & Surplus',4),(16,'Bank OD Account',5),(17,'Secured Loans',5),(18,'Unsecured Loans',5),(19,'Duties And Taxes',6),(20,'Provisions',6),(21,'Sundry Creditors',6),(22,'Stock In Hand',7),(23,'Deposits',7),(24,'Loans And Advances',7),(25,'Sundry Debtors',7),(26,'Cash In Hand',7),(27,'Bank Accounts',7);
/*!40000 ALTER TABLE `fa_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fa_group_category`
--

DROP TABLE IF EXISTS `fa_group_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fa_group_category` (
  `fa_group_category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`fa_group_category_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fa_group_category`
--

LOCK TABLES `fa_group_category` WRITE;
/*!40000 ALTER TABLE `fa_group_category` DISABLE KEYS */;
INSERT INTO `fa_group_category` VALUES (2,'Assets'),(4,'Capital Account'),(7,'Current Assets'),(6,'Current Liabilities'),(1,'Liability'),(5,'Loans'),(3,'Profit & Loss');
/*!40000 ALTER TABLE `fa_group_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fa_ledger`
--

DROP TABLE IF EXISTS `fa_ledger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fa_ledger` (
  `fa_ledger_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `fa_group_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`fa_ledger_id`),
  UNIQUE KEY `name` (`name`),
  KEY `fa_group_id` (`fa_group_id`),
  CONSTRAINT `fa_ledger_ibfk_1` FOREIGN KEY (`fa_group_id`) REFERENCES `fa_group` (`fa_group_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fa_ledger`
--

LOCK TABLES `fa_ledger` WRITE;
/*!40000 ALTER TABLE `fa_ledger` DISABLE KEYS */;
INSERT INTO `fa_ledger` VALUES (1,'Sales Account',9);
/*!40000 ALTER TABLE `fa_ledger` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fa_ledger_entry`
--

DROP TABLE IF EXISTS `fa_ledger_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fa_ledger_entry` (
  `fa_ledger_entry_id` int(11) NOT NULL AUTO_INCREMENT,
  `fa_ledger_id` int(10) unsigned NOT NULL,
  `debit` decimal(10,0) NOT NULL,
  `credit` decimal(10,0) NOT NULL,
  `notes` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `transaction_timestamp` int(11) NOT NULL,
  PRIMARY KEY (`fa_ledger_entry_id`),
  KEY `fa_ledger_id` (`fa_ledger_id`),
  CONSTRAINT `fa_ledger_entry_ibfk_1` FOREIGN KEY (`fa_ledger_id`) REFERENCES `fa_ledger` (`fa_ledger_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fa_ledger_entry`
--

LOCK TABLES `fa_ledger_entry` WRITE;
/*!40000 ALTER TABLE `fa_ledger_entry` DISABLE KEYS */;
INSERT INTO `fa_ledger_entry` VALUES (1,1,'0','0','Opening balance',1269957367);
/*!40000 ALTER TABLE `fa_ledger_entry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory` (
  `inventory_id` int(10) unsigned NOT NULL,
  `warehouse_id` int(10) unsigned NOT NULL,
  `purchase_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `serial_no` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `box_no` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `item_notes` varchar(200) CHARACTER SET latin1 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice`
--

DROP TABLE IF EXISTS `invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice` (
  `invoice_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `invoice_type` int(10) unsigned NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `branch_id` int(10) unsigned NOT NULL,
  `to_type` int(10) unsigned NOT NULL,
  `to_type_id` int(10) unsigned NOT NULL,
  `debit_entry_id` int(10) unsigned NOT NULL,
  `notes` text CHARACTER SET utf8,
  `delivery_terms` text CHARACTER SET utf8,
  `payment_terms` text CHARACTER SET utf8,
  `purchase_order` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `contact_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`invoice_id`),
  KEY `contact_id` (`contact_id`),
  CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice`
--

LOCK TABLES `invoice` WRITE;
/*!40000 ALTER TABLE `invoice` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_item`
--

DROP TABLE IF EXISTS `invoice_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_item` (
  `invoice_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `invoice_item_type` int(11) NOT NULL,
  `invoice_item_inventory_id` int(10) unsigned NOT NULL,
  `unit_price` decimal(10,0) NOT NULL,
  `quantity` decimal(10,0) NOT NULL,
  `tax_type_id` int(10) unsigned DEFAULT NULL,
  `item_description` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`invoice_item_id`),
  KEY `invoice_id` (`invoice_id`),
  CONSTRAINT `invoice_item_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_item`
--

LOCK TABLES `invoice_item` WRITE;
/*!40000 ALTER TABLE `invoice_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead`
--

DROP TABLE IF EXISTS `lead`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead` (
  `lead_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `middle_name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `company_name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `home_phone` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `work_phone` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `mobile` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `fax` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(320) CHARACTER SET latin1 DEFAULT NULL,
  `address_line_1` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `address_line_2` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `address_line_3` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `address_line_4` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `city` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `state` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `postal_code` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `country` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  `do_not_call` tinyint(1) NOT NULL DEFAULT '0',
  `email_opt_out` tinyint(1) NOT NULL DEFAULT '0',
  `converted` tinyint(1) NOT NULL,
  `created` int(11) NOT NULL,
  `updated` int(11) DEFAULT NULL,
  `lead_source_id` int(10) unsigned DEFAULT NULL,
  `lead_status_id` int(10) unsigned DEFAULT NULL,
  `assigned_to` int(10) unsigned DEFAULT NULL,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`lead_id`),
  KEY `created_by` (`created_by`),
  KEY `branch_id` (`branch_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `lead_status_id` (`lead_status_id`),
  KEY `lead_source_id` (`lead_source_id`),
  CONSTRAINT `lead_ibfk_2` FOREIGN KEY (`lead_status_id`) REFERENCES `lead_status` (`lead_status_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `lead_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `lead_ibfk_5` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `lead_ibfk_6` FOREIGN KEY (`lead_source_id`) REFERENCES `lead_source` (`lead_source_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead`
--

LOCK TABLES `lead` WRITE;
/*!40000 ALTER TABLE `lead` DISABLE KEYS */;
INSERT INTO `lead` VALUES (1,'Alika','Jana','Honorato','vitae, orci.',NULL,NULL,NULL,NULL,NULL,'P.O. Box 284, 7483 Luctus St.','2053 Nullam St.',NULL,NULL,'Wisconsin Rapids','Newfoundland and Labrador',NULL,'Armenia',NULL,0,0,0,1248063000,NULL,1,1,1,1,1),(2,'Halla','Xantha','Kylynn','nec, cursus',NULL,NULL,NULL,NULL,NULL,'Ap #569-6555 Sed Rd.','475 Metus. Ave',NULL,NULL,'Lakeland','Manitoba',NULL,'Mayotte',NULL,0,0,0,1247101023,NULL,1,1,1,1,1),(3,'Barrett','Chantale','Erich','Quisque nonummy',NULL,NULL,NULL,NULL,NULL,'P.O. Box 571, 8239 Ut, Ave','723-7737 A, St.',NULL,NULL,'Edina','VT',NULL,'Liechtenstein',NULL,0,0,0,1245652018,NULL,1,1,1,1,1),(4,'Lunea','Basil','Skyler','rhoncus id,',NULL,NULL,NULL,NULL,NULL,'346-158 Elit, St.','Ap #951-5090 Feugiat Street',NULL,NULL,'Oak Ridge','NY',NULL,'Australia',NULL,0,0,0,1249434878,NULL,1,1,1,1,1),(5,'Brett','Ina','Jelani','Phasellus nulla.',NULL,NULL,NULL,NULL,NULL,'Ap #264-7464 Sed, Rd.','P.O. Box 812, 7535 Elit. Rd.',NULL,NULL,'Green Bay','Prince Edward Island',NULL,'Australia',NULL,0,0,0,1246750532,NULL,1,1,1,1,1),(6,'Arden','Arden','TaShya','molestie dapibus',NULL,NULL,NULL,NULL,NULL,'734 Sem. Street','5462 Felis Road',NULL,NULL,'Charlotte','ME',NULL,'Madagascar',NULL,0,0,0,1248313905,NULL,1,1,1,1,1),(7,'Armando','Hayden','Benjamin','enim, gravida',NULL,NULL,NULL,NULL,NULL,'Ap #172-1843 A, Street','814-3365 Ac, Street',NULL,NULL,'Spartanburg','LA',NULL,'Mauritius',NULL,0,0,0,1247428779,NULL,1,1,1,1,1),(8,'Iliana','Joelle','Rashad','malesuada fames',NULL,NULL,NULL,NULL,NULL,'737 Mattis. St.','Ap #345-4218 Ac Street',NULL,NULL,'Moorhead','Utah',NULL,'Moldova',NULL,0,0,0,1244808593,NULL,1,1,1,1,1),(9,'Scarlett','Cheyenne','Kane','nascetur ridiculus',NULL,NULL,NULL,NULL,NULL,'P.O. Box 169, 7890 Ullamcorper, Avenue','Ap #778-4486 Nisl. Street',NULL,NULL,'Williston','Prince Edward Island',NULL,'France',NULL,0,0,0,1246548765,NULL,1,1,1,1,1),(10,'Sophia','Shellie','Ursa','ridiculus mus.',NULL,NULL,NULL,NULL,NULL,'3464 Eu St.','539-5705 Eleifend. Av.',NULL,NULL,'Chino','NB',NULL,'French Southern Territories',NULL,0,0,0,1249285181,NULL,1,1,1,1,1),(11,'Drake','Ali','Lacy','tincidunt, neque',NULL,NULL,NULL,NULL,NULL,'250-7681 Sem, Road','Ap #358-7014 Ac, Street',NULL,NULL,'Hilo','WA',NULL,'Egypt',NULL,0,0,0,1249034355,NULL,1,1,1,1,1),(12,'Isabelle','Carl','Celeste','Integer tincidunt',NULL,NULL,NULL,NULL,NULL,'Ap #853-4530 Enim St.','Ap #861-504 Arcu. St.',NULL,NULL,'Troy','NL',NULL,'Serbia and Montenegro',NULL,0,0,0,1248785628,NULL,1,1,1,1,1),(13,'Leroy','Alexander','Sandra','vitae risus.',NULL,NULL,NULL,NULL,NULL,'5529 Enim Avenue','P.O. Box 338, 6288 Quis St.',NULL,NULL,'Escondido','NB',NULL,'Moldova',NULL,0,0,0,1248428848,NULL,1,1,1,1,1),(14,'Scarlett','Daquan','Dorothy','orci, consectetuer',NULL,NULL,NULL,NULL,NULL,'Ap #904-6651 Senectus Av.','Ap #772-3193 Vel Av.',NULL,NULL,'Saratoga Springs','Hawaii',NULL,'Palau',NULL,0,0,0,1246487430,NULL,1,1,1,1,1),(15,'Galena','Sybil','Azalia','dictum augue',NULL,NULL,NULL,NULL,NULL,'4266 Nam Street','P.O. Box 356, 3658 Maecenas Ave',NULL,NULL,'Great Falls','Mississippi',NULL,'Argentina',NULL,0,0,0,1245093005,NULL,1,1,1,1,1),(16,'Freya','Orli','Kane','eu turpis.',NULL,NULL,NULL,NULL,NULL,'439-4124 Auctor Av.','P.O. Box 470, 1030 Aliquam Ave',NULL,NULL,'Walnut','Virginia',NULL,'Fiji',NULL,0,0,0,1244844155,NULL,1,1,1,1,1),(17,'Evan','Barbara','Deanna','sem semper',NULL,NULL,NULL,NULL,NULL,'830-2837 Phasellus Avenue','Ap #705-853 Per St.',NULL,NULL,'Guayama','Nova Scotia',NULL,'Spain',NULL,0,0,0,1245507248,NULL,1,1,1,1,1),(18,'Kaye','Alden','Julie','sem egestas',NULL,NULL,NULL,NULL,NULL,'P.O. Box 310, 180 Nascetur Rd.','3773 Non Rd.',NULL,NULL,'Valdez','AB',NULL,'Liechtenstein',NULL,0,0,0,1247139230,NULL,1,1,1,1,1),(19,'Jessamine','Amanda','Upton','augue porttitor',NULL,NULL,NULL,NULL,NULL,'958-3128 Eu Rd.','135-5558 Ultricies Rd.',NULL,NULL,'Los Alamitos','NL',NULL,'Tunisia',NULL,0,0,0,1245778229,NULL,1,1,1,1,1),(20,'Amelia','David','Randall','Cras dictum',NULL,NULL,NULL,NULL,NULL,'262-2831 Faucibus Av.','P.O. Box 289, 4670 Erat, Rd.',NULL,NULL,'Saint Albans','NS',NULL,'Peru',NULL,0,0,0,1244880549,NULL,1,1,1,1,1),(21,'Cody','Nayda','Winter','ridiculus mus.',NULL,NULL,NULL,NULL,NULL,'P.O. Box 347, 9365 Scelerisque St.','P.O. Box 292, 6417 Lacus. Ave',NULL,NULL,'Saint Albans','Saskatchewan',NULL,'Russian Federation',NULL,0,0,0,1247496283,NULL,1,1,1,1,1),(22,'Clio','Kirby','Oliver','tempor bibendum.',NULL,NULL,NULL,NULL,NULL,'184-1682 Nunc Road','591-1546 Non St.',NULL,NULL,'Cicero','MI',NULL,'Norfolk Island',NULL,0,0,0,1248146743,NULL,1,1,1,1,1),(23,'Gage','Reuben','Hadley','senectus et',NULL,NULL,NULL,NULL,NULL,'4541 Aliquam Avenue','7836 Commodo St.',NULL,NULL,'Hartford','Yukon',NULL,'Virgin Islands, British',NULL,0,0,0,1248153207,NULL,1,1,1,1,1),(24,'Ralph','Ferdinand','Lacy','Nulla interdum.',NULL,NULL,NULL,NULL,NULL,'Ap #708-6840 Integer Street','Ap #437-1401 Consectetuer, Ave',NULL,NULL,'Farrell','YT',NULL,'Azerbaijan',NULL,0,0,0,1248589018,NULL,1,1,1,1,1),(25,'Kareem','Damian','Zenia','non, lobortis',NULL,NULL,NULL,NULL,NULL,'Ap #278-1990 Ullamcorper. Street','5103 Felis Ave',NULL,NULL,'Fort Smith','BC',NULL,'Equatorial Guinea',NULL,0,0,0,1248114430,NULL,1,1,1,1,1),(26,'Kylie','Owen','Noelani','auctor velit.',NULL,NULL,NULL,NULL,NULL,'4302 Eleifend, Street','Ap #719-1425 Vulputate Avenue',NULL,NULL,'Victoria','Saskatchewan',NULL,'Greenland',NULL,0,0,0,1245436249,NULL,1,1,1,1,1),(27,'Adam','Flynn','Rhiannon','Praesent luctus.',NULL,NULL,NULL,NULL,NULL,'115-598 Tellus. Road','1480 Quam Street',NULL,NULL,'Iowa City','Arizona',NULL,'Nepal',NULL,0,0,0,1249270936,NULL,1,1,1,1,1),(28,'Tasha','Xantha','Elvis','adipiscing elit.',NULL,NULL,NULL,NULL,NULL,'511-8490 Metus. Street','P.O. Box 686, 8707 Cras Av.',NULL,NULL,'South Burlington','IA',NULL,'Syrian Arab Republic',NULL,0,0,0,1246851845,NULL,1,1,1,1,1),(29,'Melissa','Pandora','Clare','quis massa.',NULL,NULL,NULL,NULL,NULL,'Ap #676-8297 At, St.','883-6079 Rutrum Avenue',NULL,NULL,'Bradford','New Brunswick',NULL,'Somalia',NULL,0,0,0,1248956929,NULL,1,1,1,1,1),(30,'Glenna','Tashya','Amaya','metus urna',NULL,NULL,NULL,NULL,NULL,'9875 Magna Av.','Ap #156-8215 Non, Avenue',NULL,NULL,'Raleigh','BC',NULL,'Myanmar',NULL,0,0,0,1249255043,NULL,1,1,1,1,1),(31,'Josiah','Kylie','Yoko','quis, tristique',NULL,NULL,NULL,NULL,NULL,'964-9558 Sem Road','8136 Felis Ave',NULL,NULL,'Christiansted','NS',NULL,'Equatorial Guinea',NULL,0,0,0,1248258534,NULL,1,1,1,1,1),(32,'Ayanna','Tucker','Savannah','iaculis odio.',NULL,NULL,NULL,NULL,NULL,'Ap #261-7019 Aliquam Street','5299 Magna. Rd.',NULL,NULL,'Laguna Niguel','NB',NULL,'Andorra',NULL,0,0,0,1245391294,NULL,1,1,1,1,1),(33,'Dahlia','Sarah','Ciaran','neque pellentesque',NULL,NULL,NULL,NULL,NULL,'Ap #513-3748 Elit Street','P.O. Box 988, 4959 Elit Av.',NULL,NULL,'Montebello','Northwest Territories',NULL,'Guyana',NULL,0,0,0,1245920578,NULL,1,1,1,1,1),(34,'Shannon','Fritz','Hope','luctus, ipsum',NULL,NULL,NULL,NULL,NULL,'329-8712 Cursus Street','102-6727 Cras Av.',NULL,NULL,'Burlingame','Ontario',NULL,'Bahamas',NULL,0,0,0,1248291579,NULL,1,1,1,1,1),(35,'TaShya','Lois','Bianca','lorem, auctor',NULL,NULL,NULL,NULL,NULL,'6072 Auctor Road','2614 Nec St.',NULL,NULL,'Thomasville','New Jersey',NULL,'Greece',NULL,0,0,0,1247829483,NULL,1,1,1,1,1),(36,'Teegan','Pandora','Frances','imperdiet nec,',NULL,NULL,NULL,NULL,NULL,'7146 Mollis Rd.','P.O. Box 114, 143 Non, Street',NULL,NULL,'Rye','IL',NULL,'French Southern Territories',NULL,0,0,0,1246720302,NULL,1,1,1,1,1),(37,'Lydia','Ingrid','Noble','eget lacus.',NULL,NULL,NULL,NULL,NULL,'7872 Purus. Rd.','506-7771 Eu St.',NULL,NULL,'Sioux Falls','ND',NULL,'Tajikistan',NULL,0,0,0,1248338125,NULL,1,1,1,1,1),(38,'Quamar','Marny','Nehru','posuere at,',NULL,NULL,NULL,NULL,NULL,'Ap #874-5300 Sem, Street','931-722 Donec Rd.',NULL,NULL,'Melrose','NS',NULL,'Equatorial Guinea',NULL,0,0,0,1249168470,NULL,1,1,1,1,1),(39,'TaShya','Colby','Camilla','Suspendisse eleifend.',NULL,NULL,NULL,NULL,NULL,'P.O. Box 299, 9508 Nec, Av.','9415 Libero. Avenue',NULL,NULL,'Atlantic City','Kentucky',NULL,'Burundi',NULL,0,0,0,1249101111,NULL,1,1,1,1,1),(40,'Velma','Keith','Reagan','natoque penatibus',NULL,NULL,NULL,NULL,NULL,'Ap #189-936 Non Ave','P.O. Box 934, 8506 Arcu Avenue',NULL,NULL,'Natchitoches','Utah',NULL,'Virgin Islands, British',NULL,0,0,0,1248972580,NULL,1,1,1,1,1),(41,'Calvin','Cleo','Reece','vitae mauris',NULL,NULL,NULL,NULL,NULL,'6607 Mauris, Street','428-7436 Inceptos Rd.',NULL,NULL,'Bloomington','CO',NULL,'Swaziland',NULL,0,0,0,1248659396,NULL,1,1,1,1,1),(42,'Adara','Abel','Kenyon','eu nulla',NULL,NULL,NULL,NULL,NULL,'126-8874 Dolor Ave','1746 Donec Road',NULL,NULL,'New Madrid','Illinois',NULL,'Dominican Republic',NULL,0,0,0,1245788167,NULL,1,1,1,1,1),(43,'Sage','Danielle','Shelley','eget mollis',NULL,NULL,NULL,NULL,NULL,'3429 Vitae Av.','P.O. Box 453, 1662 Massa St.',NULL,NULL,'Utica','Nova Scotia',NULL,'French Polynesia',NULL,0,0,0,1247896969,NULL,1,1,1,1,1),(44,'Venus','Olympia','Kareem','nulla. Cras',NULL,NULL,NULL,NULL,NULL,'Ap #745-2334 Convallis Road','P.O. Box 837, 6062 Tellus. Rd.',NULL,NULL,'Dubuque','Pennsylvania',NULL,'Turkmenistan',NULL,0,0,0,1247523121,NULL,1,1,1,1,1),(45,'Ina','Shea','Kato','nonummy ut,',NULL,NULL,NULL,NULL,NULL,'Ap #310-1235 Consectetuer Street','2912 Phasellus Avenue',NULL,NULL,'Huntsville','AL',NULL,'Bangladesh',NULL,0,0,0,1244831390,NULL,1,1,1,1,1),(46,'Sophia','Zia','Dane','eu tellus',NULL,NULL,NULL,NULL,NULL,'710-8742 Quis Ave','Ap #388-538 Nisi. Avenue',NULL,NULL,'Oakland','Saskatchewan',NULL,'Ghana',NULL,0,0,0,1249156711,NULL,1,1,1,1,1),(47,'Ramona','Cody','Xyla','at sem',NULL,NULL,NULL,NULL,NULL,'576-1351 Mollis. Avenue','9409 Fames Av.',NULL,NULL,'Valdosta','Nova Scotia',NULL,'Latvia',NULL,0,0,0,1248068306,NULL,1,1,1,1,1),(48,'Dara','Illana','Wanda','Proin sed',NULL,NULL,NULL,NULL,NULL,'4230 Felis Ave','362-3754 Et, Av.',NULL,NULL,'Lower Burrell','Manitoba',NULL,'Australia',NULL,0,0,0,1248950470,NULL,1,1,1,1,1),(49,'Brock','Xantha','Ina','hendrerit id,',NULL,NULL,NULL,NULL,NULL,'P.O. Box 924, 2302 Gravida Ave','8673 Ut, Rd.',NULL,NULL,'Martinsburg','Florida',NULL,'Christmas Island',NULL,0,0,0,1246759989,NULL,1,1,1,1,1),(50,'Stacy','Yasir','Fritz','semper pretium',NULL,NULL,NULL,NULL,NULL,'Ap #904-519 Est, Rd.','P.O. Box 605, 7911 Magnis Rd.',NULL,NULL,'Liberal','OR',NULL,'Nepal',NULL,0,0,0,1247657429,NULL,1,1,1,1,1),(51,'Davis','Nayda','Lysandra','tempor arcu.',NULL,NULL,NULL,NULL,NULL,'2814 Dignissim Rd.','9489 Eu, Road',NULL,NULL,'Huntington Beach','Nova Scotia',NULL,'United States Minor Outlying Islands',NULL,0,0,0,1246622268,NULL,1,1,1,1,1),(52,'Morgan','Jana','Ezekiel','Donec felis',NULL,NULL,NULL,NULL,NULL,'Ap #405-4053 Penatibus Av.','1646 Diam Avenue',NULL,NULL,'Clemson','Wyoming',NULL,'Reunion',NULL,0,0,0,1247332152,NULL,1,1,1,1,1),(53,'Tamara','Maile','Grady','ipsum cursus',NULL,NULL,NULL,NULL,NULL,'P.O. Box 879, 4924 Nisi St.','P.O. Box 107, 595 Lorem, Rd.',NULL,NULL,'Jordan Valley','AB',NULL,'Bolivia',NULL,0,0,0,1245898014,NULL,1,1,1,1,1),(54,'Sarah','Heidi','Odette','iaculis odio.',NULL,NULL,NULL,NULL,NULL,'863-9074 Adipiscing. Road','9746 In Av.',NULL,NULL,'McAlester','Mississippi',NULL,'Korea',NULL,0,0,0,1247363191,NULL,1,1,1,1,1),(55,'Miranda','Fiona','Odessa','sociosqu ad',NULL,NULL,NULL,NULL,NULL,'Ap #501-6396 Arcu. Rd.','P.O. Box 813, 7062 Nibh Av.',NULL,NULL,'Milwaukee','GA',NULL,'Paraguay',NULL,0,0,0,1248568009,NULL,1,1,1,1,1),(56,'Quinn','Joel','Phoebe','rutrum, justo.',NULL,NULL,NULL,NULL,NULL,'482-8023 Sem St.','P.O. Box 510, 381 Ut St.',NULL,NULL,'Atwater','QC',NULL,'Cuba',NULL,0,0,0,1248003962,NULL,1,1,1,1,1),(57,'Macey','Melodie','Harrison','iaculis odio.',NULL,NULL,NULL,NULL,NULL,'9789 Orci, Av.','P.O. Box 513, 7175 Metus Avenue',NULL,NULL,'Bay St. Louis','Yukon',NULL,'Australia',NULL,0,0,0,1246755807,NULL,1,1,1,1,1),(58,'Lana','Beck','Bryar','non, sollicitudin',NULL,NULL,NULL,NULL,NULL,'Ap #979-8374 Dictum Ave','Ap #185-8846 Ullamcorper Av.',NULL,NULL,'Morgantown','Maryland',NULL,'Equatorial Guinea',NULL,0,0,0,1249409953,NULL,1,1,1,1,1),(59,'Bryar','Omar','Zelenia','Maecenas mi',NULL,NULL,NULL,NULL,NULL,'P.O. Box 385, 6812 Arcu. St.','Ap #690-1230 Ac Avenue',NULL,NULL,'Lockport','VA',NULL,'Romania',NULL,0,0,0,1245893805,NULL,1,1,1,1,1),(60,'Petra','Shelby','Uriah','lorem tristique',NULL,NULL,NULL,NULL,NULL,'9212 Sit Rd.','2742 Pede. Av.',NULL,NULL,'Madison','NB',NULL,'Palestinian Territory, Occupied',NULL,0,0,0,1248760945,NULL,1,1,1,1,1),(61,'Veda','Barbara','Hunter','vehicula aliquet',NULL,NULL,NULL,NULL,NULL,'P.O. Box 834, 4167 Hendrerit Rd.','P.O. Box 723, 7266 Adipiscing Av.',NULL,NULL,'Alexandria','Arkansas',NULL,'Malta',NULL,0,0,0,1245975467,NULL,1,1,1,1,1),(62,'Zachery','John','Martin','elit. Etiam',NULL,NULL,NULL,NULL,NULL,'P.O. Box 851, 7020 Elit, Ave','Ap #190-9353 Aliquam St.',NULL,NULL,'Gadsden','Ontario',NULL,'Saint Lucia',NULL,0,0,0,1247057098,NULL,1,1,1,1,1),(63,'Unity','Hamish','Alisa','risus varius',NULL,NULL,NULL,NULL,NULL,'P.O. Box 235, 9424 Diam Rd.','5216 Sem. Road',NULL,NULL,'Atlantic City','Alberta',NULL,'French Guiana',NULL,0,0,0,1246487711,NULL,1,1,1,1,1),(64,'Olivia','Idona','Uta','interdum. Curabitur',NULL,NULL,NULL,NULL,NULL,'Ap #739-2881 Sociis Av.','171-1828 Mattis Street',NULL,NULL,'Clovis','Mississippi',NULL,'Romania',NULL,0,0,0,1247859096,NULL,1,1,1,1,1),(65,'Hector','Eaton','MacKenzie','nunc sed',NULL,NULL,NULL,NULL,NULL,'P.O. Box 275, 7162 Lacus. St.','440-6910 Risus. Ave',NULL,NULL,'Olean','Prince Edward Island',NULL,'Senegal',NULL,0,0,0,1247884682,NULL,1,1,1,1,1),(66,'Illiana','Ciara','Omar','accumsan laoreet',NULL,NULL,NULL,NULL,NULL,'Ap #924-277 Ut St.','P.O. Box 338, 2706 Nec St.',NULL,NULL,'Wichita Falls','Ontario',NULL,'Ireland',NULL,0,0,0,1245676640,NULL,1,1,1,1,1),(67,'Jasper','Dominic','Lawrence','Phasellus dolor',NULL,NULL,NULL,NULL,NULL,'P.O. Box 633, 3811 Rhoncus. Av.','256-6625 Turpis St.',NULL,NULL,'Kokomo','Louisiana',NULL,'Guatemala',NULL,0,0,0,1244884044,NULL,1,1,1,1,1),(68,'Ashton','Beau','Xaviera','ac, eleifend',NULL,NULL,NULL,NULL,NULL,'9824 Mauris Street','P.O. Box 574, 1143 Fringilla, Ave',NULL,NULL,'Fajardo','Indiana',NULL,'Svalbard and Jan Mayen',NULL,0,0,0,1245882312,NULL,1,1,1,1,1),(69,'Rebecca','Molly','Merrill','mattis ornare,',NULL,NULL,NULL,NULL,NULL,'P.O. Box 728, 1169 Orci Ave','P.O. Box 608, 8726 Sapien, Avenue',NULL,NULL,'San Diego','Northwest Territories',NULL,'Fiji',NULL,0,0,0,1246989151,NULL,1,1,1,1,1),(70,'Jonah','Halla','Matthew','vel quam',NULL,NULL,NULL,NULL,NULL,'181-708 Mauris Street','2395 Feugiat St.',NULL,NULL,'Pendleton','New Brunswick',NULL,'Uruguay',NULL,0,0,0,1249624316,NULL,1,1,1,1,1),(71,'Florence','Hilary','May','nunc sit',NULL,NULL,NULL,NULL,NULL,'P.O. Box 794, 8635 Nam St.','P.O. Box 550, 1753 Suspendisse St.',NULL,NULL,'Hartford','Iowa',NULL,'Tokelau',NULL,0,0,0,1247590305,NULL,1,1,1,1,1),(72,'Isaac','Camille','Chloe','purus mauris',NULL,NULL,NULL,NULL,NULL,'417-7197 Aliquet Rd.','529-185 Cursus, Rd.',NULL,NULL,'Murfreesboro','Nova Scotia',NULL,'Papua New Guinea',NULL,0,0,0,1248264510,NULL,1,1,1,1,1),(73,'Kiona','Jonas','Medge','sagittis felis.',NULL,NULL,NULL,NULL,NULL,'Ap #770-3049 Felis, Av.','Ap #998-8294 Morbi St.',NULL,NULL,'Durant','New Brunswick',NULL,'Samoa',NULL,0,0,0,1249012111,NULL,1,1,1,1,1),(74,'Serina','Herman','Eliana','vitae, sodales',NULL,NULL,NULL,NULL,NULL,'P.O. Box 496, 7166 Fusce Av.','206-4121 Ipsum St.',NULL,NULL,'Oxford','Alabama',NULL,'Slovakia',NULL,0,0,0,1246160509,NULL,1,1,1,1,1),(75,'Ursa','Geraldine','Quincy','Phasellus ornare.',NULL,NULL,NULL,NULL,NULL,'Ap #584-1279 Phasellus St.','Ap #113-3696 Amet Rd.',NULL,NULL,'Pasco','Nova Scotia',NULL,'Liberia',NULL,0,0,0,1248019380,NULL,1,1,1,1,1),(76,'Eaton','Lavinia','Chaney','Fusce mollis.',NULL,NULL,NULL,NULL,NULL,'P.O. Box 764, 7283 Rutrum Rd.','359-5650 Est, Street',NULL,NULL,'Santa Fe','NU',NULL,'Niue',NULL,0,0,0,1247903107,NULL,1,1,1,1,1),(77,'Justina','Elizabeth','Nissim','eros. Proin',NULL,NULL,NULL,NULL,NULL,'P.O. Box 826, 7943 Porttitor Street','Ap #375-9785 Id, Av.',NULL,NULL,'San Fernando','NB',NULL,'Uganda',NULL,0,0,0,1247535343,NULL,1,1,1,1,1),(78,'Bertha','Hasad','Adrienne','sapien imperdiet',NULL,NULL,NULL,NULL,NULL,'7601 Lacus, Street','6869 Tincidunt Street',NULL,NULL,'Barrow','NS',NULL,'Guyana',NULL,0,0,0,1246106065,NULL,1,1,1,1,1),(79,'Chastity','Judith','Maia','Nullam nisl.',NULL,NULL,NULL,NULL,NULL,'240-8159 Phasellus Av.','P.O. Box 765, 8061 Tellus St.',NULL,NULL,'Youngstown','AK',NULL,'Central African Republic',NULL,0,0,0,1248044165,NULL,1,1,1,1,1),(80,'Phillip','Lesley','Damian','lectus, a',NULL,NULL,NULL,NULL,NULL,'P.O. Box 253, 5090 Lorem Ave','Ap #684-4297 Elementum, Rd.',NULL,NULL,'Plano','MB',NULL,'Bouvet Island',NULL,0,0,0,1247007133,NULL,1,1,1,1,1),(81,'Demetrius','Timon','Porter','at, iaculis',NULL,NULL,NULL,NULL,NULL,'785 Donec Road','P.O. Box 371, 1203 Nam Rd.',NULL,NULL,'Warwick','QC',NULL,'Guam',NULL,0,0,0,1247552230,NULL,1,1,1,1,1),(82,'Whilemina','Curran','Chancellor','lorem. Donec',NULL,NULL,NULL,NULL,NULL,'Ap #812-843 Nulla Road','4806 Lectus, St.',NULL,NULL,'Ephraim','North Carolina',NULL,'Mali',NULL,0,0,0,1246981955,NULL,1,1,1,1,1),(83,'Teegan','Lenore','Ciara','imperdiet ullamcorper.',NULL,NULL,NULL,NULL,NULL,'P.O. Box 398, 9871 Eros St.','Ap #156-8446 Imperdiet Ave',NULL,NULL,'Carolina','Quebec',NULL,'Cocos (Keeling) Islands',NULL,0,0,0,1245062399,NULL,1,1,1,1,1),(84,'Katell','Gretchen','Edward','dolor sit',NULL,NULL,NULL,NULL,NULL,'P.O. Box 352, 368 Quam Av.','406-7925 Risus. Rd.',NULL,NULL,'Cairo','Saskatchewan',NULL,'Saint Vincent and The Grenadines',NULL,0,0,0,1248888630,NULL,1,1,1,1,1),(85,'Odessa','Charde','Laith','iaculis nec,',NULL,NULL,NULL,NULL,NULL,'7074 Euismod Avenue','4893 In Ave',NULL,NULL,'Quincy','New Jersey',NULL,'Costa Rica',NULL,0,0,0,1248135315,NULL,1,1,1,1,1),(86,'Nayda','Daquan','Amy','est arcu',NULL,NULL,NULL,NULL,NULL,'113-7412 Tellus St.','Ap #816-4290 Gravida Ave',NULL,NULL,'Syracuse','HI',NULL,'Guatemala',NULL,0,0,0,1246378436,NULL,1,1,1,1,1),(87,'Nora','Omar','Kaitlin','ante blandit',NULL,NULL,NULL,NULL,NULL,'Ap #215-5852 Bibendum Ave','Ap #658-5285 Ultricies Rd.',NULL,NULL,'Fremont','DC',NULL,'Bahamas',NULL,0,0,0,1247446613,NULL,1,1,1,1,1),(88,'Andrew','Tashya','Nadine','Nullam scelerisque',NULL,NULL,NULL,NULL,NULL,'347-8402 Ullamcorper. Road','6999 Ullamcorper Avenue',NULL,NULL,'Sturgis','AZ',NULL,'Senegal',NULL,0,0,0,1246623835,NULL,1,1,1,1,1),(89,'Dalton','Arden','Hanae','ac facilisis',NULL,NULL,NULL,NULL,NULL,'626-2950 Laoreet Road','Ap #446-8412 Cras St.',NULL,NULL,'Rutland','Quebec',NULL,'Saint Vincent and The Grenadines',NULL,0,0,0,1248444160,NULL,1,1,1,1,1),(90,'Jasmine','Ursula','Brenden','dui. Fusce',NULL,NULL,NULL,NULL,NULL,'783-879 Gravida. St.','Ap #145-3713 Id Street',NULL,NULL,'Orem','Prince Edward Island',NULL,'French Southern Territories',NULL,0,0,0,1249493238,NULL,1,1,1,1,1),(91,'Ariel','Logan','Phoebe','odio. Etiam',NULL,NULL,NULL,NULL,NULL,'347 Nullam Ave','639-1632 Eu St.',NULL,NULL,'Buena Park','QC',NULL,'Tajikistan',NULL,0,0,0,1249640574,NULL,1,1,1,1,1),(92,'Jackson','Vincent','Yvonne','dapibus rutrum,',NULL,NULL,NULL,NULL,NULL,'P.O. Box 736, 8260 Diam St.','Ap #322-2371 Fermentum Road',NULL,NULL,'San Luis Obispo','OK',NULL,'San Marino',NULL,0,0,0,1249245617,NULL,1,1,1,1,1),(93,'Blossom','Wyoming','Kennedy','aliquet. Proin',NULL,NULL,NULL,NULL,NULL,'964-4361 Integer Rd.','P.O. Box 329, 3387 Sed St.',NULL,NULL,'Manchester','Nunavut',NULL,'Mongolia',NULL,0,0,0,1248381313,NULL,1,1,1,1,1),(94,'Owen','Kuame','Amethyst','nec, leo.',NULL,NULL,NULL,NULL,NULL,'Ap #156-4340 Vestibulum St.','481-8755 Posuere, Road',NULL,NULL,'Lincoln','BC',NULL,'India',NULL,0,0,0,1249017618,NULL,1,1,1,1,1),(95,'Nerea','Athena','Elmo','erat volutpat.',NULL,NULL,NULL,NULL,NULL,'7908 Mauris Av.','624-3675 Dis Avenue',NULL,NULL,'Fond du Lac','PE',NULL,'Faroe Islands',NULL,0,0,0,1244943793,NULL,1,1,1,1,1),(96,'Abel','Autumn','Marvin','enim. Etiam',NULL,NULL,NULL,NULL,NULL,'Ap #884-2303 Rutrum Road','P.O. Box 338, 2508 Nibh Ave',NULL,NULL,'Belpre','UT',NULL,'Christmas Island',NULL,0,0,0,1247752919,NULL,1,1,1,1,1),(97,'Deborah','Ulla','Abbot','scelerisque, lorem',NULL,NULL,NULL,NULL,NULL,'P.O. Box 559, 6630 Dapibus St.','Ap #723-6557 Nec St.',NULL,NULL,'Easthampton','Saskatchewan',NULL,'Georgia',NULL,0,0,0,1246543390,NULL,1,1,1,1,1),(98,'Xerxes','Sade','Norman','hendrerit neque.',NULL,NULL,NULL,NULL,NULL,'594-5592 Integer Rd.','P.O. Box 304, 1487 Ut Ave',NULL,NULL,'DuBois','Alabama',NULL,'Solomon Islands',NULL,0,0,0,1247350376,NULL,1,1,1,1,1),(99,'Ahmed','Jasper','Linus','dolor, nonummy',NULL,NULL,NULL,NULL,NULL,'Ap #934-5115 Est Road','P.O. Box 867, 9813 Justo Road',NULL,NULL,'Provo','District of Columbia',NULL,'Peru',NULL,0,0,0,1245067246,NULL,1,1,1,1,1),(100,'Carolyn','Frances','Sybill','ut ipsum',NULL,NULL,NULL,NULL,NULL,'359-7930 Velit Road','Ap #422-2576 Turpis Avenue',NULL,NULL,'Canandaigua','British Columbia',NULL,'Gambia',NULL,0,0,0,1244820484,NULL,1,1,1,1,1);
/*!40000 ALTER TABLE `lead` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_notes`
--

DROP TABLE IF EXISTS `lead_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_notes` (
  `lead_notes_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` int(10) unsigned NOT NULL,
  `notes` text NOT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`lead_notes_id`),
  KEY `opportunity_id` (`lead_id`,`created_by`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `lead_notes_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `lead` (`lead_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `lead_notes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_notes`
--

LOCK TABLES `lead_notes` WRITE;
/*!40000 ALTER TABLE `lead_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `lead_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_source`
--

DROP TABLE IF EXISTS `lead_source`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_source` (
  `lead_source_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`lead_source_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_source`
--

LOCK TABLES `lead_source` WRITE;
/*!40000 ALTER TABLE `lead_source` DISABLE KEYS */;
INSERT INTO `lead_source` VALUES (1,'Other',NULL),(2,'Cold Call',NULL),(3,'Existing Customer',NULL),(4,'Self Generated',NULL),(5,'Employee',NULL),(6,'Partner',NULL),(7,'Public Relations',NULL),(8,'Direct Mail',NULL),(9,'Conference',NULL),(10,'Tradeshow',NULL),(11,'Website',NULL),(12,'Word of mouth',NULL),(13,'Email',NULL),(14,'Campaign',NULL);
/*!40000 ALTER TABLE `lead_source` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_status`
--

DROP TABLE IF EXISTS `lead_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lead_status` (
  `lead_status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`lead_status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_status`
--

LOCK TABLES `lead_status` WRITE;
/*!40000 ALTER TABLE `lead_status` DISABLE KEYS */;
INSERT INTO `lead_status` VALUES (1,'Other',NULL),(2,'New',NULL),(3,'Assigned',NULL),(4,'In process',NULL),(5,'Dead',NULL);
/*!40000 ALTER TABLE `lead_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `list`
--

DROP TABLE IF EXISTS `list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `list` (
  `list_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` int(11) NOT NULL COMMENT 'when list was created',
  `created_by` int(10) unsigned NOT NULL,
  `show_in_customer_portal` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`list_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `list_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `list`
--

LOCK TABLES `list` WRITE;
/*!40000 ALTER TABLE `list` DISABLE KEYS */;
INSERT INTO `list` VALUES (1,'test','test',1270021427,1,1),(2,'test 2','test',1270021441,1,0);
/*!40000 ALTER TABLE `list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `priority` varchar(100) CHARACTER SET latin1 NOT NULL,
  `message` varchar(250) CHARACTER SET latin1 NOT NULL,
  `log_timestamp` varchar(50) CHARACTER SET latin1 NOT NULL,
  `priority_name` varchar(25) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
INSERT INTO `log` VALUES (1,'6','Session opended for sudheer.s@sudheer.net','2010-03-30T23:58:52+05:30','INFO'),(2,'6','Session opended for sudheer.s@sudheer.net','2010-03-31T13:13:28+05:30','INFO');
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manufacturer`
--

DROP TABLE IF EXISTS `manufacturer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manufacturer` (
  `manufacturer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`manufacturer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manufacturer`
--

LOCK TABLES `manufacturer` WRITE;
/*!40000 ALTER TABLE `manufacturer` DISABLE KEYS */;
/*!40000 ALTER TABLE `manufacturer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opportunity`
--

DROP TABLE IF EXISTS `opportunity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `opportunity` (
  `opportunity_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_type` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `amount` double DEFAULT NULL,
  `expected_close_date` int(11) NOT NULL,
  `description` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `lead_source_id` int(10) unsigned DEFAULT NULL,
  `sales_stage_id` int(10) unsigned DEFAULT NULL,
  `account_id` int(10) unsigned DEFAULT NULL,
  `contact_id` int(10) unsigned DEFAULT NULL,
  `assigned_to` int(10) unsigned NOT NULL,
  `branch_id` int(10) unsigned DEFAULT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `updated` int(11) DEFAULT NULL,
  PRIMARY KEY (`opportunity_id`),
  KEY `created_by` (`created_by`),
  KEY `branch_id` (`branch_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `contact_id` (`contact_id`),
  KEY `account_id` (`account_id`),
  KEY `sales_stage_id` (`sales_stage_id`),
  KEY `lead_source_id` (`lead_source_id`),
  CONSTRAINT `opportunity_ibfk_1` FOREIGN KEY (`lead_source_id`) REFERENCES `lead_source` (`lead_source_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `opportunity_ibfk_2` FOREIGN KEY (`sales_stage_id`) REFERENCES `sales_stage` (`sales_stage_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `opportunity_ibfk_3` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `opportunity_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opportunity`
--

LOCK TABLES `opportunity` WRITE;
/*!40000 ALTER TABLE `opportunity` DISABLE KEYS */;
INSERT INTO `opportunity` VALUES (2,1,'test',343,1268073000,'test',1,2,19,0,1,1,1270026892,NULL,NULL),(3,2,'test',44,1269628200,'test',6,6,21,19,1,1,1270026964,NULL,NULL);
/*!40000 ALTER TABLE `opportunity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opportunity_contact`
--

DROP TABLE IF EXISTS `opportunity_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `opportunity_contact` (
  `opportunity_id` int(11) unsigned NOT NULL,
  `contact_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`opportunity_id`),
  KEY `contact_id` (`contact_id`),
  CONSTRAINT `opportunity_contact_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `opportunity_contact_ibfk_2` FOREIGN KEY (`opportunity_id`) REFERENCES `opportunity` (`opportunity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opportunity_contact`
--

LOCK TABLES `opportunity_contact` WRITE;
/*!40000 ALTER TABLE `opportunity_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `opportunity_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opportunity_notes`
--

DROP TABLE IF EXISTS `opportunity_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `opportunity_notes` (
  `opportunity_notes_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `opportunity_id` int(10) unsigned NOT NULL,
  `notes` text CHARACTER SET utf8 NOT NULL,
  `created` int(11) NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`opportunity_notes_id`),
  KEY `opportunity_id` (`opportunity_id`,`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opportunity_notes`
--

LOCK TABLES `opportunity_notes` WRITE;
/*!40000 ALTER TABLE `opportunity_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `opportunity_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organization_details`
--

DROP TABLE IF EXISTS `organization_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organization_details` (
  `organization_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(150) CHARACTER SET latin1 NOT NULL,
  `website` varchar(200) CHARACTER SET latin1 DEFAULT NULL COMMENT 'website url',
  `description` varchar(500) CHARACTER SET latin1 DEFAULT NULL,
  `logo` varchar(500) CHARACTER SET latin1 DEFAULT NULL,
  `logo_for_documents` varchar(500) CHARACTER SET latin1 DEFAULT NULL,
  `footer` varchar(500) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`organization_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization_details`
--

LOCK TABLES `organization_details` WRITE;
/*!40000 ALTER TABLE `organization_details` DISABLE KEYS */;
INSERT INTO `organization_details` VALUES (1,'Binary Vibes',NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `organization_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `privilege`
--

DROP TABLE IF EXISTS `privilege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `privilege` (
  `privilege_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`privilege_id`)
) ENGINE=InnoDB AUTO_INCREMENT=145 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `privilege`
--

LOCK TABLES `privilege` WRITE;
/*!40000 ALTER TABLE `privilege` DISABLE KEYS */;
INSERT INTO `privilege` VALUES (1,'list users',NULL),(2,'administer users',NULL),(3,'administer permissions',NULL),(4,'change own email address',NULL),(5,'access administration pages',NULL),(6,'update bizsense',NULL),(7,'administer bizsense',NULL),(8,'access branch pages',NULL),(9,'create branch',NULL),(10,'edit branch',NULL),(11,'delete branch',NULL),(12,'administer branch',NULL),(13,'access lead pages',NULL),(14,'administer leads',NULL),(15,'create leads',NULL),(16,'view own leads',NULL),(17,'edit own leads',NULL),(18,'convert own leads',NULL),(19,'delete own leads',NULL),(20,'view own role leads',NULL),(21,'edit own role leads',NULL),(22,'convert own role leads',NULL),(23,'delete own role leads',NULL),(24,'view own branch leads',NULL),(25,'edit own branch leads',NULL),(26,'convert own branch leads',NULL),(27,'delete own branch leads',NULL),(28,'view all leads',NULL),(29,'edit all leads',NULL),(30,'convert all leads',NULL),(31,'delete all leads',NULL),(32,'assign leads to own role users',NULL),(33,'assign leads to own branch users',NULL),(34,'assign leads to any user',NULL),(35,'import leads',NULL),(36,'access purchase pages',NULL),(37,'create purchase',NULL),(38,'edit purchase',NULL),(39,'administer purchase',NULL),(40,'access account pages',NULL),(41,'create accounts',NULL),(42,'view own accounts',NULL),(43,'edit own accounts',NULL),(44,'delete own accounts',NULL),(45,'view own role accounts',NULL),(46,'edit own role accounts',NULL),(47,'delete own role accounts',NULL),(48,'view own branch accounts',NULL),(49,'edit own branch accounts',NULL),(50,'delete own branch accounts',NULL),(51,'view all accounts',NULL),(52,'edit all accounts',NULL),(53,'delete all accounts',NULL),(54,'assign accounts to own role users',NULL),(55,'assign accounts to own branch users',NULL),(56,'assign accounts to any user',NULL),(57,'administer accounts',NULL),(58,'access contact pages',NULL),(59,'create contacts',NULL),(60,'view own contacts',NULL),(61,'edit own contacts',NULL),(62,'delete own contacts',NULL),(63,'view own role contacts',NULL),(64,'edit own role contacts',NULL),(65,'delete own role contacts',NULL),(66,'view own branch contacts',NULL),(67,'edit own branch contacts',NULL),(68,'delete own branch contacts',NULL),(69,'view all contacts',NULL),(70,'edit all contacts',NULL),(71,'delete all contacts',NULL),(72,'assign contacts to own role users',NULL),(73,'assign contacts to own branch users',NULL),(74,'assign contacts to any user',NULL),(75,'administer contacts',NULL),(76,'access opportunity pages',NULL),(77,'create opportunities',NULL),(78,'view own opportunities',NULL),(79,'edit own opportunities',NULL),(80,'delete own opportunities',NULL),(81,'view own role opportunities',NULL),(82,'edit own role opportunities',NULL),(83,'delete own role opportunities',NULL),(84,'view own branch opportunities',NULL),(85,'edit own branch opportunities',NULL),(86,'delete own branch opportunities',NULL),(87,'view all opportunities',NULL),(88,'edit all opportunities',NULL),(89,'delete all opportunities',NULL),(90,'assign opportunities to own role users',NULL),(91,'assign opportunities to own branch users',NULL),(92,'assign opportunities to any user',NULL),(93,'administer opportunities',NULL),(94,'administer profiles',NULL),(95,'edit own profile',NULL),(96,'access profile pages',NULL),(97,'access organization pages',NULL),(98,'access report pages',NULL),(99,'access lead reports',NULL),(100,'access opportunity reports',NULL),(101,'access contact reports',NULL),(102,'access account reports',NULL),(103,'access time pages',NULL),(104,'administer time module',NULL),(105,'access product pages',NULL),(106,'view products',NULL),(107,'create products',NULL),(108,'edit products',NULL),(109,'delete products',NULL),(110,'administer products',NULL),(111,'set product prices of own branch',NULL),(112,'set product prices of all branches',NULL),(113,'access service pages',NULL),(114,'view service items',NULL),(115,'create service items',NULL),(116,'edit service items',NULL),(117,'delete service items',NULL),(118,'administer service items',NULL),(119,'access quote pages',NULL),(120,'create quotes',NULL),(121,'view own quotes',NULL),(122,'view own role quotes',NULL),(123,'view own branch quotes',NULL),(124,'view all quotes',NULL),(125,'edit own quotes',NULL),(126,'edit own branch quotes',NULL),(127,'edit all quotes',NULL),(128,'delete own quotes',NULL),(129,'delete own branch quotes',NULL),(130,'delete all quotes',NULL),(131,'administer quotes',NULL),(132,'create invoices',NULL),(133,'edit invoices',NULL),(134,'delete invoices',NULL),(135,'view invoices',NULL),(136,'administer invoices',NULL),(137,'access ticket pages',NULL),(138,'access finance pages',NULL),(139,'access campaign pages',NULL),(140,'create campaign',NULL),(141,'view campaign',NULL),(142,'edit campaign',NULL),(143,'delete campaign',NULL),(144,'access activity pages',NULL);
/*!40000 ALTER TABLE `privilege` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `description` varchar(500) CHARACTER SET latin1 DEFAULT NULL,
  `unit_price` decimal(10,0) NOT NULL,
  `taxable` tinyint(4) NOT NULL DEFAULT '1',
  `tax_type_id` int(10) unsigned DEFAULT NULL,
  `subscribable` tinyint(4) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`product_id`),
  KEY `tax_type_id` (`tax_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (1,'test','tet','333',1,0,1,1);
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_item_general`
--

DROP TABLE IF EXISTS `product_item_general`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_item_general` (
  `product_general_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `unit_price` double NOT NULL,
  PRIMARY KEY (`product_general_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_item_general`
--

LOCK TABLES `product_item_general` WRITE;
/*!40000 ALTER TABLE `product_item_general` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_item_general` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile` (
  `profile_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `middle_name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `personal_email` varchar(320) CHARACTER SET latin1 DEFAULT NULL,
  `work_phone` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `home_phone` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `mobile` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `branch_id` int(10) unsigned NOT NULL,
  `reports_to` int(10) unsigned DEFAULT NULL,
  `primary_role` int(11) DEFAULT NULL,
  PRIMARY KEY (`profile_id`),
  KEY `primary_role` (`primary_role`),
  KEY `reports_to` (`reports_to`),
  KEY `branch_id` (`branch_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `profile_ibfk_2` FOREIGN KEY (`reports_to`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `profile_ibfk_3` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `profile_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile`
--

LOCK TABLES `profile` WRITE;
/*!40000 ALTER TABLE `profile` DISABLE KEYS */;
INSERT INTO `profile` VALUES (1,'Sudheer','','S',NULL,NULL,NULL,NULL,1,1,NULL,NULL);
/*!40000 ALTER TABLE `profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase`
--

DROP TABLE IF EXISTS `purchase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase` (
  `purchase_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(100) CHARACTER SET latin1 NOT NULL,
  `vendor_sale_reference` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `other_delivery_point_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `other_delivery_point` text CHARACTER SET latin1,
  `delivery_terms` text CHARACTER SET latin1 NOT NULL,
  `payment_terms` text CHARACTER SET latin1 NOT NULL,
  `other_purchase_terms` text CHARACTER SET latin1,
  `shipping_instructions` text CHARACTER SET latin1,
  `purchase_notes` text CHARACTER SET latin1,
  `internal_purchase_notes` text CHARACTER SET latin1,
  `vendor_id` int(10) unsigned NOT NULL,
  `vendor_contact_id` int(10) unsigned DEFAULT NULL,
  `delivery_point` int(10) unsigned DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `consignee_branch` int(10) unsigned NOT NULL,
  PRIMARY KEY (`purchase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase`
--

LOCK TABLES `purchase` WRITE;
/*!40000 ALTER TABLE `purchase` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_products`
--

DROP TABLE IF EXISTS `purchase_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_products` (
  `purchase_products_id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `unit_price` double NOT NULL,
  `tax_type_id` int(11) unsigned DEFAULT NULL,
  `quantity` double NOT NULL,
  PRIMARY KEY (`purchase_products_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_products`
--

LOCK TABLES `purchase_products` WRITE;
/*!40000 ALTER TABLE `purchase_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quote`
--

DROP TABLE IF EXISTS `quote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quote` (
  `quote_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `branch_id` int(10) unsigned NOT NULL,
  `assigned_to` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `account_id` int(10) unsigned NOT NULL,
  `contact_id` int(10) unsigned DEFAULT NULL,
  `subject` varchar(200) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  `delivery_terms` text CHARACTER SET latin1,
  `payment_terms` text CHARACTER SET latin1,
  `internal_notes` text CHARACTER SET latin1,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`quote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quote`
--

LOCK TABLES `quote` WRITE;
/*!40000 ALTER TABLE `quote` DISABLE KEYS */;
/*!40000 ALTER TABLE `quote` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quote_products`
--

DROP TABLE IF EXISTS `quote_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quote_products` (
  `quote_product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quote_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `unit_price` double NOT NULL,
  `quantity` double NOT NULL,
  `tax_type_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`quote_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quote_products`
--

LOCK TABLES `quote_products` WRITE;
/*!40000 ALTER TABLE `quote_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `quote_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resource`
--

DROP TABLE IF EXISTS `resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resource` (
  `resource_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET latin1 NOT NULL,
  `description` varchar(250) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resource`
--

LOCK TABLES `resource` WRITE;
/*!40000 ALTER TABLE `resource` DISABLE KEYS */;
/*!40000 ALTER TABLE `resource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'role id',
  `name` varchar(50) CHARACTER SET latin1 NOT NULL COMMENT 'role name',
  `description` varchar(250) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'default','default');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_stage`
--

DROP TABLE IF EXISTS `sales_stage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales_stage` (
  `sales_stage_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  `context` int(1) NOT NULL,
  PRIMARY KEY (`sales_stage_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_stage`
--

LOCK TABLES `sales_stage` WRITE;
/*!40000 ALTER TABLE `sales_stage` DISABLE KEYS */;
INSERT INTO `sales_stage` VALUES (2,'Qualification 133','test',0),(4,'Value Proposition',NULL,0),(5,'Identifiying Decision Makers',NULL,0),(6,'Perception Analysis',NULL,0),(7,'Proposal/Price Quote',NULL,0),(8,'Negotion/Review',NULL,0),(9,'Closed Won',NULL,1),(10,'Closed Lost',NULL,2);
/*!40000 ALTER TABLE `sales_stage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salutation`
--

DROP TABLE IF EXISTS `salutation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salutation` (
  `salutation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET latin1 NOT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`salutation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salutation`
--

LOCK TABLES `salutation` WRITE;
/*!40000 ALTER TABLE `salutation` DISABLE KEYS */;
INSERT INTO `salutation` VALUES (1,'Mr',NULL),(2,'Ms',NULL),(3,'Dr',NULL),(4,'Prof',NULL);
/*!40000 ALTER TABLE `salutation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timezone` varchar(50) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shift`
--

DROP TABLE IF EXISTS `shift`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shift` (
  `shift_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `in_time` int(11) NOT NULL,
  `out_time` int(11) DEFAULT NULL,
  `uid` int(10) unsigned NOT NULL,
  `work_location_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`shift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shift`
--

LOCK TABLES `shift` WRITE;
/*!40000 ALTER TABLE `shift` DISABLE KEYS */;
/*!40000 ALTER TABLE `shift` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_email`
--

DROP TABLE IF EXISTS `site_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_email` (
  `site_email_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(320) CHARACTER SET latin1 NOT NULL,
  `from_name` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT 'BizSense Site Mailer',
  `transport` varchar(50) CHARACTER SET latin1 NOT NULL,
  `smtp_server` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  `smtp_require_auth` tinyint(1) DEFAULT NULL,
  `smtp_auth` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `smtp_username` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  `smtp_password` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `smtp_secure_connection` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `smtp_port` int(11) DEFAULT NULL,
  `footer` longtext CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`site_email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_email`
--

LOCK TABLES `site_email` WRITE;
/*!40000 ALTER TABLE `site_email` DISABLE KEYS */;
/*!40000 ALTER TABLE `site_email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task` (
  `task_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `start_date` int(11) DEFAULT NULL,
  `end_date` int(11) DEFAULT NULL,
  `assigned_to` int(10) unsigned DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `task_status_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`task_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `task_status_id` (`task_status_id`),
  CONSTRAINT `task_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `task_ibfk_2` FOREIGN KEY (`task_status_id`) REFERENCES `task_status` (`task_status_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task`
--

LOCK TABLES `task` WRITE;
/*!40000 ALTER TABLE `task` DISABLE KEYS */;
/*!40000 ALTER TABLE `task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_category`
--

DROP TABLE IF EXISTS `task_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_category` (
  `task_category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`task_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_category`
--

LOCK TABLES `task_category` WRITE;
/*!40000 ALTER TABLE `task_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_status`
--

DROP TABLE IF EXISTS `task_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_status` (
  `task_status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `closed_context` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`task_status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_status`
--

LOCK TABLES `task_status` WRITE;
/*!40000 ALTER TABLE `task_status` DISABLE KEYS */;
INSERT INTO `task_status` VALUES (1,'new','',0);
/*!40000 ALTER TABLE `task_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_time`
--

DROP TABLE IF EXISTS `task_time`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_time` (
  `task_tme_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `in_time` datetime NOT NULL,
  `out_time` int(11) DEFAULT NULL,
  `uid` int(10) unsigned NOT NULL,
  `description` varchar(100) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`task_tme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_time`
--

LOCK TABLES `task_time` WRITE;
/*!40000 ALTER TABLE `task_time` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_time` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_class`
--

DROP TABLE IF EXISTS `tax_class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tax_class` (
  `tax_class_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`tax_class_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tax_class`
--

LOCK TABLES `tax_class` WRITE;
/*!40000 ALTER TABLE `tax_class` DISABLE KEYS */;
INSERT INTO `tax_class` VALUES (1,'Taxable',NULL),(2,'Non Taxable',NULL);
/*!40000 ALTER TABLE `tax_class` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_type`
--

DROP TABLE IF EXISTS `tax_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tax_type` (
  `tax_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `percentage` double NOT NULL,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `has_add_on` tinyint(1) NOT NULL DEFAULT '0',
  `fa_ledger_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`tax_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tax_type`
--

LOCK TABLES `tax_type` WRITE;
/*!40000 ALTER TABLE `tax_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket`
--

DROP TABLE IF EXISTS `ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket` (
  `ticket_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) CHARACTER SET utf8 NOT NULL,
  `description` text CHARACTER SET utf8,
  `contact_id` int(10) unsigned NOT NULL,
  `created` int(11) DEFAULT NULL,
  `ticket_status_id` int(10) unsigned NOT NULL,
  `assigned_to` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ticket_id`),
  KEY `contact_id` (`contact_id`),
  KEY `ticket_status_id` (`ticket_status_id`),
  KEY `assigned_to` (`assigned_to`),
  CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ticket_ibfk_2` FOREIGN KEY (`ticket_status_id`) REFERENCES `ticket_status` (`ticket_status_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ticket_ibfk_3` FOREIGN KEY (`assigned_to`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket`
--

LOCK TABLES `ticket` WRITE;
/*!40000 ALTER TABLE `ticket` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_comment`
--

DROP TABLE IF EXISTS `ticket_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_comment` (
  `ticket_comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL,
  `title` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8,
  `created` int(11) NOT NULL,
  `created_by_type` int(11) NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ticket_comment_id`),
  KEY `ticket_id` (`ticket_id`),
  CONSTRAINT `ticket_comment_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_comment`
--

LOCK TABLES `ticket_comment` WRITE;
/*!40000 ALTER TABLE `ticket_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_status`
--

DROP TABLE IF EXISTS `ticket_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_status` (
  `ticket_status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `closed_context` int(11) NOT NULL,
  PRIMARY KEY (`ticket_status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_status`
--

LOCK TABLES `ticket_status` WRITE;
/*!40000 ALTER TABLE `ticket_status` DISABLE KEYS */;
INSERT INTO `ticket_status` VALUES (1,'new',0),(2,'open',0),(3,'assigned',0),(4,'closed',1);
/*!40000 ALTER TABLE `ticket_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `url_access`
--

DROP TABLE IF EXISTS `url_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `url_access` (
  `url_access_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'urlAccessId',
  `url` varchar(100) CHARACTER SET latin1 NOT NULL COMMENT 'URL module/controller/action',
  `privilege_name` varchar(500) CHARACTER SET latin1 DEFAULT NULL,
  `assertion_class` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`url_access_id`)
) ENGINE=InnoDB AUTO_INCREMENT=218 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `url_access`
--

LOCK TABLES `url_access` WRITE;
/*!40000 ALTER TABLE `url_access` DISABLE KEYS */;
INSERT INTO `url_access` VALUES (1,'default/update/index','update bizsense',''),(2,'default/update/update','update bizsense',''),(3,'admin/cache/clear','update bizsense',''),(4,'admin/email/index','administer bizsense',''),(5,'admin/email/set','administer bizsense',''),(6,'admin/siteinfo/index','administer bizsense',''),(7,'admin/siteinfo/footer','administer bizsense',''),(8,'admin/org/index','administer bizsense',''),(9,'admin/org/edit','administer bizsense',''),(10,'admin/org/viewdetails','administer bizsense',''),(11,'admin/salutation/index','administer bizsense',''),(12,'admin/salutation/add','administer bizsense',''),(13,'admin/salutation/delete','administer bizsense',''),(14,'admin/salutation/edit','administer bizsense',''),(15,'admin/timezone/set','administer bizsense',''),(16,'admin/timezone/index','administer bizsense',''),(17,'admin/status/index','administer bizsense',''),(18,'admin/status/viewlog','administer bizsense',''),(19,'admin/sitelogo/index','administer bizsense',''),(20,'admin/sitelogo/set','administer bizsense',''),(21,'admin/sitelogo/documentlogo','administer bizsense',''),(22,'admin/branch/index','access branch pages',''),(23,'admin/branch/viewdetails','access branch pages','Core_Model_Branch_Acl_CanAccess'),(24,'admin/branch/create','administer bizsense',''),(25,'admin/branch/edit','access branch pages','Core_Model_Branch_Acl_CanEdit'),(26,'default/jsonstore/branch','access branch pages',''),(27,'admin/branch/delete','access branch pages','Core_Model_Branch_Acl_CanDelete'),(28,'admin/index/index','access administration pages',''),(29,'default/index/index','auth privileges',''),(30,'default/user/logout','auth privileges',''),(31,'default/user/changepass','auth privileges',''),(32,'default/user/users','administer users',''),(33,'default/user/edit','administer users',''),(34,'default/user/roles','administer users',''),(35,'default/user/add','administer users',''),(36,'default/user/deleterole','administer users',''),(37,'default/user/addrole','administer users',''),(38,'default/user/editrole','administer users',''),(39,'default/user/permissions','administer users',''),(40,'default/user/timezone','administer users',''),(41,'default/user/primaryrole','administer users',''),(42,'default/user/viewdetails','','Core_Model_User_Acl_CanAccess'),(43,'default/user/editprofile','administer users',''),(44,'default/jsonstore/primaryrole','administer users',''),(45,'default/user/jsonstore','auth privileges',''),(46,'default/jsonstore/profile','list users',''),(47,'default/lead/index','access lead pages',''),(48,'default/lead/assignto','access lead pages',''),(49,'default/lead/viewdetails','access lead pages','Core_Model_Lead_Acl_CanAccess'),(50,'default/lead/notes','access lead pages','Core_Model_Lead_Acl_CanAccess'),(51,'default/lead/createnote','access lead pages','Core_Model_Lead_Acl_CanAccess'),(52,'default/lead/edit','access lead pages','Core_Model_Lead_Acl_CanEdit'),(53,'default/lead/assigntobranch','access lead pages',''),(54,'default/lead/create','create leads',''),(55,'default/lead/delete','access lead pages','Core_Model_Lead_Acl_CanDelete'),(56,'default/lead/setleadsource','administer leads',''),(57,'default/lead/addstatus','administer leads',''),(58,'default/lead/editstatus','administer leads',''),(59,'default/lead/deletestatus','administer leads',''),(60,'default/lead/editsource','administer leads',''),(61,'default/lead/addsource','administer leads',''),(62,'default/lead/deletesource','administer leads',''),(63,'default/lead/settings','administer leads',''),(64,'default/lead/setleadstatus','administer leads',''),(65,'default/lead/convert','convert own leads','Core_Model_Lead_Acl_CanConvert'),(66,'default/lead/import','import leads',''),(67,'default/lead/setdefaultassignee','administer leads',''),(68,'default/purchase/index','access purchase pages',''),(69,'default/purchase/viewdetails','access purchase pages',''),(70,'default/purchase/create','access purchase pages',''),(71,'default/purchase/edit','access purchase pages',''),(72,'default/account/index','access account pages',''),(73,'default/account/jsonstore','access account pages',''),(74,'default/account/viewdetails','access account pages','Core_Model_Account_Acl_CanAccess'),(75,'default/account/edit','access account pages','Core_Model_Account_Acl_CanEdit'),(76,'default/account/delete','access account pages','Core_Model_Account_Acl_CanDelete'),(77,'default/account/contacts','access account pages','Core_Model_Account_Acl_CanAccess'),(78,'default/account/assignto','access account pages',''),(79,'default/account/assigntobranch','access account pages',''),(80,'default/account/notes','access account pages',''),(81,'default/account/createnote','access account pages',''),(82,'default/account/editnote','access account pages',''),(83,'default/account/deletenote','access account pages',''),(84,'default/account/create','create accounts',''),(85,'default/contact/index','access contact pages',''),(86,'default/contact/jsonstore','access contact pages',''),(87,'default/contact/viewdetails','access contact pages','Core_Model_Contact_Acl_CanAccess'),(88,'default/contact/notes','access contact pages','Core_Model_Contact_Acl_CanAccess'),(89,'default/contact/createnote','access contact pages','Core_Model_Contact_Acl_CanAccess'),(90,'default/contact/ssaccount','access contact pages','Core_Model_Contact_Acl_CanAccess'),(91,'default/contact/edit','access contact pages','Core_Model_Contact_Acl_CanEdit'),(92,'default/contact/delete','access contact pages','Core_Model_Contact_Acl_CanDelete'),(93,'default/contact/assignto','access contact pages',''),(94,'default/contact/assigntobranch','access contact pages',''),(95,'default/contact/create','create contacts',''),(96,'default/contact/allcontactjsonstore','view all contacts',''),(97,'default/opportunity/index','access opportunity pages',''),(98,'default/opportunity/jsonstore','access opportunity pages',''),(99,'default/opportunity/viewdetails','access opportunity pages','Core_Model_Opportunity_Acl_CanAccess'),(100,'default/opportunity/edit','access opportunity pages','Core_Model_Opportunity_Acl_CanEdit'),(101,'default/opportunity/delete','access opportunity pages','Core_Model_Opportunity_Acl_CanDelete'),(102,'default/opportunity/assignto','access opportunity pages',''),(103,'default/opportunity/assigntobranch','access opportunity pages',''),(104,'default/opportunity/create','create opportunities',''),(105,'default/opportunity/notes','access opportunity pages','Core_Model_Opportunity_Acl_CanAccess'),(106,'default/opportunity/createnote','access opportunity pages','Core_Model_Opportunity_Acl_CanEdit'),(107,'reports/index/index','access report pages',''),(108,'reports/lead/index','access lead reports',''),(109,'reports/lead/daterange','access lead reports',''),(110,'reports/lead/csvexport','access lead reports',''),(111,'reports/lead/browse','access lead reports',''),(112,'reports/opportunity/index','access opportunity reports',''),(113,'reports/opportunity/daterange','access opportunity reports',''),(114,'reports/opportunity/csvexport','access opportunity reports',''),(115,'reports/opportunity/browse','access opportunity reports',''),(116,'reports/contact/index','access contact reports',''),(117,'reports/contact/daterange','access contact reports',''),(118,'reports/contact/csvexport','access contact reports',''),(119,'reports/contact/browse','access contact reports',''),(120,'reports/account/index','access account reports',''),(121,'reports/account/daterange','access account reports',''),(122,'reports/account/csvexport','access account reports',''),(123,'reports/account/browse','access account reports',''),(124,'time/index/index','access time pages',''),(125,'time/category/index','access time pages',''),(126,'time/category/create','access time pages',''),(127,'time/location/index','access time pages',''),(128,'time/location/create','access time pages',''),(129,'default/product/index','access product pages',''),(130,'default/product/viewdetails','access product pages',''),(131,'default/product/setprice','access product pages',''),(132,'default/product/jsonstore','access product pages',''),(133,'default/jsonstore/taxtype','access product pages',''),(134,'default/product/create','create products',''),(135,'default/product/edit','edit products',''),(136,'default/product/delete','delete products',''),(137,'default/product/setgeneral','access product pages',''),(138,'default/product/setsubscribable','access product pages',''),(139,'admin/manufacturer/index','administer products',''),(140,'admin/manufacturer/add','administer products',''),(141,'admin/manufacturer/delete','administer products',''),(142,'admin/manufacturer/edit','administer products',''),(143,'admin/pcategory/index','administer products',''),(144,'admin/pcategory/add','administer products',''),(145,'admin/pcategory/edit','administer products',''),(146,'admin/pstatus/index','administer products',''),(147,'admin/pstatus/add','administer products',''),(148,'admin/pstatus/edit','administer products',''),(149,'finance/tax/index','administer products',''),(150,'finance/tax/class','administer products',''),(151,'finance/tax/addclass','administer products',''),(152,'finance/tax/editclass','administer products',''),(153,'finance/tax/deletetype','administer products',''),(154,'finance/tax/store','access product pages',''),(155,'admin/currency/add','administer products',''),(156,'admin/currency/index','administer products',''),(157,'admin/currency/edit','administer products',''),(158,'admin/currency/delete','administer products',''),(159,'finance/tax/addtype','administer products',''),(160,'finance/tax/listtype','administer products',''),(161,'finance/tax/edittype','administer products',''),(162,'finance/tax/viewtypedetails','administer products',''),(163,'default/service/index','view service items',''),(164,'default/service/viewdetails','view service items',''),(165,'default/service/edit','edit service items',''),(166,'default/service/delete','delete service items',''),(167,'default/quote/index','access quote pages',''),(168,'default/quote/create','access quote pages',''),(169,'default/quote/edit','access quote pages','Core_Model_Quote_Acl_CanEdit'),(170,'default/quote/viewdetails','access quote pages','Core_Model_Quote_Acl_CanAccess'),(171,'default/quote/export','access quote pages','Core_Model_Quote_Acl_CanAccess'),(172,'default/quote/delete','','Core_Model_Quote_Acl_CanDelete'),(173,'default/invoice/create','create invoices',''),(174,'default/invoice/index','view invoices',''),(175,'default/invoice/viewdetails','view invoices',''),(176,'default/invoice/export','view invoices',''),(177,'default/invoice/delete','delete invoices',''),(178,'default/invoice/edit','edit invoices',''),(179,'default/invoice/settings','administer invoices',''),(180,'admin/webservice/index','administer bizsense',''),(181,'admin/webservice/viewdetails','administer bizsense',''),(182,'admin/webservice/create','administer bizsense',''),(183,'admin/webservice/edit','administer bizsense',''),(184,'admin/webservice/delete','administer bizsense',''),(185,'admin/webservice/selfserviceapp','administer bizsense',''),(186,'ticket/index/index','access ticket pages',''),(187,'ticket/index/viewdetails','access ticket pages',''),(188,'ticket/index/create','access ticket pages',''),(189,'ticket/index/edit','access ticket pages',''),(190,'ticket/index/delete','access ticket pages',''),(191,'ticket/status/index','access ticket pages',''),(192,'ticket/status/create','access ticket pages',''),(193,'ticket/status/edit','access ticket pages',''),(194,'ticket/status/delete','access ticket pages',''),(195,'ticket/settings/defaultassignee','access ticket pages',''),(196,'finance/index/index','access finance pages',''),(197,'finance/group/index','access finance pages',''),(198,'finance/ledger/index','access finance pages',''),(199,'finance/ledger/entries','access finance pages',''),(200,'finance/ledger/create','access finance pages',''),(201,'finance/ledger/edit','access finance pages',''),(202,'finance/ledger/delete','access finance pages',''),(203,'default/campaign/index','access campaign pages',''),(204,'default/campaign/create','create campaign',''),(205,'default/campaign/viewdetails','view campaign',''),(206,'default/campaign/edit','edit campaign',''),(207,'default/campaign/delete','delete campaign',''),(208,'activity/task/index','access activity pages',''),(209,'activity/task/create','access activity pages',''),(210,'activity/task/edit','access activity pages',''),(211,'activity/task/viewdetails','access activity pages',''),(212,'activity/task/settings','access activity pages',''),(213,'activity/task/addstatus','access activity pages',''),(214,'activity/taskstatus/index','access activity pages',''),(215,'activity/taskstatus/create','access activity pages',''),(216,'activity/taskstatus/edit','access activity pages',''),(217,'activity/taskstatus/viewdetails','access activity pages','');
/*!40000 ALTER TABLE `url_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(320) CHARACTER SET latin1 NOT NULL COMMENT 'username',
  `password` varchar(50) CHARACTER SET latin1 NOT NULL COMMENT 'password',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'block status',
  `mode` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'pw req+',
  `hash` varchar(32) CHARACTER SET latin1 NOT NULL,
  `created` int(11) NOT NULL COMMENT 'when account was created',
  `alt_email` varchar(150) CHARACTER SET latin1 NOT NULL COMMENT 'alternate email',
  `host_created` varchar(250) CHARACTER SET latin1 NOT NULL COMMENT 'hostname',
  `ip_created` varchar(20) CHARACTER SET latin1 NOT NULL COMMENT 'IP address',
  `last_access_ip` varchar(32) CHARACTER SET latin1 DEFAULT NULL,
  `last_access_host` varchar(60) CHARACTER SET latin1 DEFAULT NULL,
  `accessed` int(11) DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `user_timezone` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'sudheer.s@sudheer.net','5f4dcc3b5aa765d61d8327deb882cf99',1,0,'',1269957365,'','','',NULL,NULL,NULL,1,NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_role` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) NOT NULL,
  KEY `role_id` (`role_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `variable`
--

DROP TABLE IF EXISTS `variable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `variable` (
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `value` longtext CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `variable`
--

LOCK TABLES `variable` WRITE;
/*!40000 ALTER TABLE `variable` DISABLE KEYS */;
INSERT INTO `variable` VALUES ('version','0.1.8Alpha');
/*!40000 ALTER TABLE `variable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouse`
--

DROP TABLE IF EXISTS `warehouse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warehouse` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET latin1 NOT NULL,
  `branchId` int(10) unsigned NOT NULL,
  `addressLine1` varchar(100) CHARACTER SET latin1 NOT NULL,
  `addressLine2` varchar(100) CHARACTER SET latin1 NOT NULL,
  `addressLine3` varchar(100) CHARACTER SET latin1 NOT NULL,
  `addressLine4` varchar(100) CHARACTER SET latin1 NOT NULL,
  `city` varchar(100) CHARACTER SET latin1 NOT NULL,
  `state` varchar(100) CHARACTER SET latin1 NOT NULL,
  `country` varchar(100) CHARACTER SET latin1 NOT NULL,
  `postalCode` varchar(20) CHARACTER SET latin1 NOT NULL,
  `phone` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `fax` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `incharge` int(10) unsigned NOT NULL,
  `description` text CHARACTER SET latin1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse`
--

LOCK TABLES `warehouse` WRITE;
/*!40000 ALTER TABLE `warehouse` DISABLE KEYS */;
/*!40000 ALTER TABLE `warehouse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_location`
--

DROP TABLE IF EXISTS `work_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `work_location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `description` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_location`
--

LOCK TABLES `work_location` WRITE;
/*!40000 ALTER TABLE `work_location` DISABLE KEYS */;
/*!40000 ALTER TABLE `work_location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ws_access`
--

DROP TABLE IF EXISTS `ws_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ws_access` (
  `ws_access_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ws_application_id` int(10) unsigned NOT NULL,
  `ws_access_timestamp` int(10) unsigned NOT NULL,
  `success` int(11) NOT NULL,
  PRIMARY KEY (`ws_access_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ws_access`
--

LOCK TABLES `ws_access` WRITE;
/*!40000 ALTER TABLE `ws_access` DISABLE KEYS */;
/*!40000 ALTER TABLE `ws_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ws_application`
--

DROP TABLE IF EXISTS `ws_application`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ws_application` (
  `ws_application_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET latin1 NOT NULL,
  `api_key` varchar(40) CHARACTER SET latin1 NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ws_application_id`),
  UNIQUE KEY `api_key` (`api_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ws_application`
--

LOCK TABLES `ws_application` WRITE;
/*!40000 ALTER TABLE `ws_application` DISABLE KEYS */;
/*!40000 ALTER TABLE `ws_application` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ws_ss_client`
--

DROP TABLE IF EXISTS `ws_ss_client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ws_ss_client` (
  `ws_ss_client_id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(300) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`ws_ss_client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ws_ss_client`
--

LOCK TABLES `ws_ss_client` WRITE;
/*!40000 ALTER TABLE `ws_ss_client` DISABLE KEYS */;
/*!40000 ALTER TABLE `ws_ss_client` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-03-31 17:37:31
