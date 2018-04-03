/*
SQLyog Community v12.5.1 (32 bit)
MySQL - 5.7.21 : Database - vojtars
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`vojtars` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `vojtars`;

/*Table structure for table `blog` */

DROP TABLE IF EXISTS `blog`;

CREATE TABLE `blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `IDX_C0155143A76ED395` (`user_id`),
  KEY `IDX_C01551433DA5256D` (`image_id`),
  CONSTRAINT `FK_C01551433DA5256D` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `blog_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `blog` */

insert  into `blog`(`id`,`user_id`,`image_id`,`name`,`description`,`url`,`date_add`,`active`) values
(1,1,NULL,'Můj blog','','muj-blog','2018-04-02 21:14:27',1);

/*Table structure for table `category` */

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blog_id` int(11) DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `IDX_64C19C1DAE07E97` (`blog_id`),
  KEY `IDX_64C19C13DA5256D` (`image_id`),
  CONSTRAINT `FK_64C19C13DA5256D` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_64C19C1DAE07E97` FOREIGN KEY (`blog_id`) REFERENCES `blog` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `category` */

insert  into `category`(`id`,`blog_id`,`image_id`,`name`,`description`,`url`,`date_add`,`active`) values
(1,1,NULL,'Novinky','Novinky na mém blogu','novinky','2018-04-02 21:14:54',1);

/*Table structure for table `file` */

DROP TABLE IF EXISTS `file`;

CREATE TABLE `file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `size` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `file` */

/*Table structure for table `gallery` */

DROP TABLE IF EXISTS `gallery`;

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_id` int(11) DEFAULT NULL,
  `blog_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `IDX_472B783A3DA5256D` (`image_id`),
  KEY `IDX_472B783ADAE07E97` (`blog_id`),
  CONSTRAINT `FK_472B783A3DA5256D` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_472B783ADAE07E97` FOREIGN KEY (`blog_id`) REFERENCES `blog` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `gallery` */

insert  into `gallery`(`id`,`image_id`,`blog_id`,`name`,`description`,`date_add`,`active`) values
(1,NULL,NULL,'Galerie','Galerie s úvodními fotkami blogů, homepage, projektů,...','2018-02-03 21:04:09',1),
(2,NULL,1,'Blog - Můj blog','Galerie obrázků použitých v blogu : Můj blog','2018-04-02 21:14:27',1);

/*Table structure for table `homepage` */

DROP TABLE IF EXISTS `homepage`;

CREATE TABLE `homepage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `content` text,
  `position` int(11) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `homepage` */

insert  into `homepage`(`id`,`name`,`content`,`position`,`active`) values
(1,'Paralax s Popisem','<div class=\"parallax-container valign-wrapper\">\n    <div class=\"section no-pad-bot\">\n        <div class=\"container\">\n            <div class=\"row center\">\n                <h5 class=\"header col s12 light\">A modern responsive front-end framework based on Material</h5>\n            </div>\n        </div>\n    </div>\n    <div class=\"parallax\"><img src=\"/img/background2.jpg\" alt=\"Unsplashed background img 2\"></div>\n</div>',3,1),
(2,'Blok s projektama','<!--Work-->\n<div class=\"section scrollspy\" id=\"work\">\n    <div class=\"container\">\n        <h4 class=\"header text_b\">Na čem pracuji? </h4>Nebo jsem pracoval...\n        <div class=\"row\">\n            <div class=\"col s12 m4 l4\">\n                <div class=\"card\">\n                    <div class=\"card-image waves-effect waves-block waves-light\">\n                        <img class=\"activator\" src=\"/img/project1.jpg\">\n                    </div>\n                    <div class=\"card-content\">\n                        <span class=\"card-title activator grey-text text-darken-4\">Projekt 1 <i class=\"mdi-navigation-more-vert right\"></i></span>\n                        <p><a target=\"_blank\" href=\"#\">Navštiv!</a></p>\n                    </div>\n                    <div class=\"card-reveal\">\n                        <span class=\"card-title grey-text text-darken-4\">Projekt 1 <i class=\"mdi-navigation-close right\"></i></span>\n                        <p>Popis projektu o co jde a všechno co tě k tomu napadne pro rychlé informování</p>\n                        <p>Technologie: <b>Nette 2.4, PHP 7.1, MySql, Doctrine, Nittro, REST API, Cron, RabbitMQ</b></p>\n                    </div>\n                </div>\n            </div>\n            <div class=\"col s12 m4 l4\">\n                <div class=\"card\">\n                    <div class=\"card-image waves-effect waves-block waves-light\">\n                        <img class=\"activator\" src=\"/img/project2.jpeg\">\n                    </div>\n                    <div class=\"card-content\">\n                        <span class=\"card-title activator grey-text text-darken-4\">Projekt 2 <i class=\"mdi-navigation-more-vert right\"></i></span>\n                        <p><a target=\"_blank\" href=\"#\">Navštiv!</a></p>\n                    </div>\n                    <div class=\"card-reveal\">\n                        <span class=\"card-title grey-text text-darken-4\">Projekt 2 <i class=\"mdi-navigation-close right\"></i></span>\n                        <p>Popis projektu o co jde a všechno co tě k tomu napadne pro rychlé informování</p>\n                        <p>Technologie: <b>Nette 2.4, PHP 7.0, MySql, Cron, ElasticSearch</b></p>\n                    </div>\n                </div>\n            </div>\n            <div class=\"col s12 m4 l4\">\n                <div class=\"card\">\n                    <div class=\"card-image waves-effect waves-block waves-light\">\n                        <img class=\"activator\" src=\"/img/project3.png\">\n                    </div>\n                    <div class=\"card-content\">\n                        <span class=\"card-title activator grey-text text-darken-4\">Projekt 3 <i class=\"mdi-navigation-more-vert right\"></i></span>\n                        <p><a href=\"#\">Project link</a></p>\n                    </div>\n                    <div class=\"card-reveal\">\n                        <span class=\"card-title grey-text text-darken-4\">Project Title <i class=\"mdi-navigation-close right\"></i></span>\n                        <p>Here is some more information about this project that is only revealed once clicked on.</p>\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>\n',1,1),
(3,'Blok s delším textem','<div class=\"container\">\r\n    <div class=\"section\">\r\n\r\n        <div class=\"row\">\r\n            <div class=\"col s12 center\">\r\n                <h3><i class=\"mdi-content-send brown-text\"></i></h3>\r\n                <h4>Contact Us</h4>\r\n                <p class=\"left-align light\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam scelerisque id nunc nec volutpat. Etiam pellentesque tristique arcu, non consequat magna fermentum ac. Cras ut ultricies eros. Maecenas eros justo, ullamcorper a sapien id, viverra ultrices eros. Morbi sem neque, posuere et pretium eget, bibendum sollicitudin lacus. Aliquam eleifend sollicitudin diam, eu mattis nisl maximus sed. Nulla imperdiet semper molestie. Morbi massa odio, condimentum sed ipsum ac, gravida ultrices erat. Nullam eget dignissim mauris, non tristique erat. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;</p>\r\n            </div>\r\n        </div>\r\n\r\n    </div>\r\n</div>\r\n',2,1);

/*Table structure for table `image` */

DROP TABLE IF EXISTS `image`;

CREATE TABLE `image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gallery_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `IDX_C53D045F4E7AF8F` (`gallery_id`),
  KEY `IDX_C53D045FA76ED395` (`user_id`),
  CONSTRAINT `FK_C53D045F4E7AF8F` FOREIGN KEY (`gallery_id`) REFERENCES `gallery` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_C53D045FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `image` */

/*Table structure for table `post` */

DROP TABLE IF EXISTS `post`;

CREATE TABLE `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blog_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `gallery_id` int(11) DEFAULT NULL,
  `add_user_id` int(11) DEFAULT NULL,
  `last_edit_user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `view_count` int(11) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `date_edit` datetime DEFAULT NULL,
  `public_date` datetime NOT NULL,
  `last_view_date` datetime DEFAULT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_5A8A6C8DDAE07E97` (`blog_id`),
  KEY `IDX_5A8A6C8D12469DE2` (`category_id`),
  KEY `IDX_5A8A6C8D3DA5256D` (`image_id`),
  KEY `IDX_5A8A6C8D955A6137` (`add_user_id`),
  KEY `IDX_5A8A6C8D1466127B` (`last_edit_user_id`),
  KEY `post_ibfk_1` (`gallery_id`),
  CONSTRAINT `FK_5A8A6C8D12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_5A8A6C8D1466127B` FOREIGN KEY (`last_edit_user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_5A8A6C8D3DA5256D` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_5A8A6C8D955A6137` FOREIGN KEY (`add_user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_5A8A6C8DDAE07E97` FOREIGN KEY (`blog_id`) REFERENCES `blog` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `post_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `gallery` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `post` */

insert  into `post`(`id`,`blog_id`,`category_id`,`image_id`,`gallery_id`,`add_user_id`,`last_edit_user_id`,`name`,`description`,`url`,`content`,`view_count`,`date_add`,`date_edit`,`public_date`,`last_view_date`,`public`,`deleted`) values
(1,1,1,NULL,NULL,1,1,'Můj první článek','Ukázka článku na mém novém blogu','muj-prvni-clanek','<p>Vítej na mém blogu,</p><p><br></p><p>toto je článek na ukázku.</p>',11,'2018-04-02 21:16:08','2018-04-02 21:16:08','2018-04-02 21:15:00','2018-04-03 17:56:36',1,0);

/*Table structure for table `post_has_file` */

DROP TABLE IF EXISTS `post_has_file`;

CREATE TABLE `post_has_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_has_file_ibfk_1` (`post_id`),
  KEY `post_has_file_ibfk_2` (`file_id`),
  CONSTRAINT `post_has_file_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `post_has_file_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `file` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `post_has_file` */

/*Table structure for table `project` */

DROP TABLE IF EXISTS `project`;

CREATE TABLE `project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `description` text,
  `name_tab1` varchar(255) DEFAULT NULL,
  `name_tab2` varchar(255) DEFAULT NULL,
  `name_tab3` varchar(255) DEFAULT NULL,
  `tab1` text,
  `tab2` text,
  `tab3` text,
  `show_tab1` tinyint(1) NOT NULL DEFAULT '0',
  `show_tab2` tinyint(1) NOT NULL DEFAULT '0',
  `show_tab3` tinyint(1) NOT NULL DEFAULT '0',
  `position` int(11) DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `image_id` (`image_id`),
  CONSTRAINT `project_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `project` */

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `title1` varchar(255) DEFAULT NULL,
  `title2` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `zip` int(11) DEFAULT NULL,
  `ico` int(11) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `github` varchar(255) DEFAULT NULL,
  `show_map` tinyint(1) NOT NULL DEFAULT '0',
  `show_address` tinyint(1) NOT NULL DEFAULT '0',
  `show_facebook` tinyint(1) NOT NULL DEFAULT '0',
  `show_twitter` tinyint(1) NOT NULL DEFAULT '0',
  `show_linkedin` tinyint(1) NOT NULL DEFAULT '0',
  `show_github` tinyint(1) NOT NULL DEFAULT '0',
  `show_instagram` tinyint(1) NOT NULL DEFAULT '0',
  `show_comments` tinyint(1) NOT NULL DEFAULT '1',
  `footer_text` varchar(255) DEFAULT NULL,
  `head_title` varchar(255) DEFAULT NULL,
  `head_description` varchar(255) DEFAULT NULL,
  `head_keywords` varchar(255) DEFAULT NULL,
  `head_image_id` int(11) DEFAULT NULL,
  `show_projects` tinyint(1) NOT NULL DEFAULT '0',
  `show_twitter_timeline` tinyint(1) NOT NULL DEFAULT '0',
  `send_new_subscribers` tinyint(1) NOT NULL DEFAULT '1',
  `me_menu_name` varchar(255) DEFAULT 'O mě',
  `me_name` varchar(255) DEFAULT NULL,
  `me_description` varchar(255) DEFAULT NULL,
  `me_image_id` int(11) DEFAULT NULL,
  `me_content` text,
  `me_show_page` tinyint(1) NOT NULL DEFAULT '1',
  `share_facebook` tinyint(1) NOT NULL DEFAULT '0',
  `share_twitter` tinyint(1) NOT NULL DEFAULT '0',
  `fb_app_id` varchar(255) DEFAULT NULL,
  `scripts_head` text,
  `scripts_footer` text,
  `content_color` varchar(255) DEFAULT NULL,
  `maps_api_key` varchar(255) DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `terms` text,
  `cta_own` tinyint(1) NOT NULL DEFAULT '0',
  `cta_href` varchar(255) DEFAULT NULL,
  `cta_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `image_id` (`image_id`),
  KEY `head_image_id` (`head_image_id`),
  KEY `settings_ibfk_3` (`me_image_id`),
  CONSTRAINT `settings_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `settings_ibfk_2` FOREIGN KEY (`head_image_id`) REFERENCES `image` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `settings_ibfk_3` FOREIGN KEY (`me_image_id`) REFERENCES `image` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `settings` */

insert  into `settings`(`id`,`name`,`title1`,`title2`,`url`,`image_id`,`company`,`email`,`phone`,`street`,`city`,`zip`,`ico`,`facebook`,`twitter`,`instagram`,`linkedin`,`github`,`show_map`,`show_address`,`show_facebook`,`show_twitter`,`show_linkedin`,`show_github`,`show_instagram`,`show_comments`,`footer_text`,`head_title`,`head_description`,`head_keywords`,`head_image_id`,`show_projects`,`show_twitter_timeline`,`send_new_subscribers`,`me_menu_name`,`me_name`,`me_description`,`me_image_id`,`me_content`,`me_show_page`,`share_facebook`,`share_twitter`,`fb_app_id`,`scripts_head`,`scripts_footer`,`content_color`,`maps_api_key`,`latitude`,`longitude`,`terms`,`cta_own`,`cta_href`,`cta_name`) values
(1,'název','Nadpis H1','Konkrétnější popisek','mujblog.cz',NULL,NULL,'email@email.cz','+420123456789','Ulice 11','Město',12345,0,'muj-ucet','vojtars','muj-ucet','muj-ucet','muj-ucet',0,0,0,0,0,0,0,0,'Zapsán v Živnostenském rejstříku','Muj web','Osobní web','programování, php, nette',NULL,1,0,1,'O mě','Jméno Příjmení','Moje profese',NULL,'<p>Můj příběh.<br></p>',1,0,1,NULL,NULL,NULL,'blue-grey',NULL,NULL,NULL,' <ol>\n        <li>\n            <p>Udělujete tímto souhlas společnosti <b>Společnost</b>, se sídlem <b>Sídlo</b>, IČ: <b>12345678</b> ,\n                zapsané ve veřejném rejstříku vedeném u <b>......</b>, soudu v&nbsp;<b>......</b>, oddíl <b>......</b>,\n                vložka <b>......</b>. (dále jen „Správce“), aby ve smyslu zákona č.101/2000 Sb., o ochraně osobních údajů\n                (dále jen „zákon o ochraně osobních údajů“) zpracovávala tyto osobní údaje: <b>E-mail</b>\n            </p>\n        </li>\n        <li>\n            E-mail je nutné zpracovat za účelem zasílání novinek týkajících se tohoto blogu. Tyto údaje budou Správcem zpracovány po dobu <b>5</b> let.\n        </li>\n        <li>\n            S&nbsp;výše uvedeným zpracováním udělujete svůj výslovný souhlas. Souhlas lze vzít kdykoliv zpět, a to například zasláním emailu nebo dopisu na kontaktní údaje společnosti <b>......</b>.\n        </li>\n        <li>\n            <p>Zpracování osobních údajů je prováděno Správcem, osobní údaje však pro Správce mohou zpracovávat i tito zpracovatelé:</p>\n            <ul>\n                <li>Poskytovatel softwaru <b>......</b>.</li>\n                <li>Případně další poskytovatelé zpracovatelských softwarů, služeb a aplikací, které však v současné době společnost nevyužívá.</li>\n            </ul>\n        </li>\n        <li>\n            <p>Vezměte, prosíme, na vědomí, že podle zákona o ochraně osobních údajů máte právo:</p>\n            <ul>\n                <li>vzít souhlas kdykoliv zpět,</li>\n                <li>požadovat po nás informaci, jaké vaše osobní údaje zpracováváme,</li>\n                <li>požadovat po nás vysvětlení ohledně zpracování osobních údajů,</li>\n                <li>vyžádat si u nás přístup k&nbsp;těmto údajům a tyto nechat aktualizovat nebo opravit,</li>\n                <li>požadovat po nás výmaz těchto osobních údajů,</li>\n                <li>v&nbsp;případě pochybností o dodržování povinností souvisejících se zpracováním osobních údajů obrátit se na nás nebo na Úřad pro ochranu osobních údajů.</li>\n            </ul>\n        </li>\n    </ol>',0,'https://www.seznam.cz','Vlastní CTA');

/*Table structure for table `subscriber` */

DROP TABLE IF EXISTS `subscriber`;

CREATE TABLE `subscriber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blog_id` int(11) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_add` datetime DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AD005B69DAE07E97` (`blog_id`),
  CONSTRAINT `FK_AD005B69DAE07E97` FOREIGN KEY (`blog_id`) REFERENCES `blog` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `subscriber` */

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `surname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_add` datetime DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `date_last_login` datetime DEFAULT NULL,
  `ip_last_login` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `street` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `zip` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `company` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ic` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dic` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `total_sign_in` int(11) NOT NULL,
  `ip_register` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `roles` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `user` */

insert  into `user`(`id`,`name`,`surname`,`email`,`phone`,`password`,`date_add`,`enabled`,`date_last_login`,`ip_last_login`,`street`,`city`,`avatar`,`zip`,`state`,`company`,`ic`,`dic`,`total_sign_in`,`ip_register`,`roles`) values
(1,'Name','Surname','admin@admin.cz',NULL,'$2y$10$rGf84cs45qzCxz49ugJa1Oim6D4Enq5mpn/qucjJJfQr/EV5.QSTG','2018-02-03 15:26:07',1,'2018-03-29 15:01:16','127.0.0.1','','','','','',NULL,NULL,NULL,0,NULL,'admin');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
