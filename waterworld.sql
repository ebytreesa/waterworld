-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Vært: 127.0.0.1
-- Genereringstid: 03. 06 2016 kl. 09:21:51
-- Serverversion: 5.7.9
-- PHP-version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `waterworld`
--

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `eby_destinations`
--

DROP TABLE IF EXISTS `eby_destinations`;
CREATE TABLE IF NOT EXISTS `eby_destinations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(128) NOT NULL,
  `price` int(11) NOT NULL DEFAULT '0',
  `date` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Data dump for tabellen `eby_destinations`
--

INSERT INTO `eby_destinations` (`id`, `title`, `content`, `image`, `price`, `date`) VALUES
(1, 'Indien', 'Velkommen til et af verdens mest fascinerende rejselande, som er fuld af kontraster og venlige smil. Her er altid aktivitet. Fremmede dufte blandes med farver og lyde og skaber en helt speciel harmoni, som er svÃ¦r at beskrive. Har man vÃ¦ret i Indien Ã©n gang, vil man gerne tilbage. Goa er et godt sted at starte din Indien-rejse. Her breder den ene paradisstrand sig efter den anden langs den lange kyst, og alt er lidt mere roligt end i de store byer.\r\nLommepengene rÃ¦kker langt i Goa, hvilket gÃ¸r shopping til en sand fornÃ¸jelse! VÃ¦vede tÃ¦pper, lÃ¦dervarer og krydderier er typiske souvenirs at tage med sig hjem. ForkÃ¦l dig selv med at fÃ¥ syet en hel ny garderobe, og husk at forhandle om priserne!\r\nGoas strande er et kapitel for sig. De kilometerlange paradisstrande med kokospalmerne svajende i vinden, er brede og finkornede og i vandkanten ligger glitrende muslingeskaller, som bÃ¸lgerne har skyllet op.', '1464940849-images2.jpg', 10000, '29/06'),
(2, 'Thailand', 'Det centrale Thailand har bÃ¥de storbyer og dejlige strande. Her ligger Bangkok med sit hektiske storbyliv og kulturelle sevÃ¦rdigheder. Kun et par timer vÃ¦k ligger badebyen Hua Hin. Den tropiske Ã¸ Koh Chang ligger i Siambugten, omgivet af det turkisblÃ¥ hav. Vores interessante rundrejse Det Klassiske Thailand har Bangkok som udgangspunkt.\r\nDet sydlige Thailand har en rÃ¦kke rejsemÃ¥l af forskellig karakter. FÃ¦lles for dem alle er paradisiske Ã¸er, smukke naturoplevelser og fantastiske strande. Turistvenlige Phuket har mange muligheder, bÃ¥de nÃ¥r det gÃ¦lder strande, aktiviteter og shopping. Krabiprovinsen byder pÃ¥ sÃ¥vel storby som uberÃ¸rte steder.', '1464940956-images (1).jpg', 8889, '18/7'),
(3, 'Dubai', 'PÃ¥ en Dubai rejse keder man sig aldrig. Her findes sol og strand, kunstige skibakker og meget meget mere.\r\nByen byder pÃ¥ fantastiske skylines, og med arkitektoniske bygningsvÃ¦rker pÃ¥ over 800 meter hÃ¸rer Dubai til i superligaen, nÃ¥r det gÃ¦lder imponerende bygningsvÃ¦rker.\r\nPÃ¥ en rejse til Dubai kan I bo pÃ¥ nogle af verdens dyreste og mest ekstravagante luksushoteller og besÃ¸ge de trendy shoppingcentre med verdens bedste og dyreste brands.\r\n\r\nDet solsikre vejr og kort flyvetid gÃ¸r Dubai og De Forenede Arabiske Emirater oplagt til en solferie Ã¥ret rundt. Faktisk regner det kun meget fÃ¥ dage om Ã¥ret i Dubai - om sommeren regner det typisk slet ikke.', '1464941144-shadow-pp_50861-copy.jpg', 5000, '15/7');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `eby_omos`
--

DROP TABLE IF EXISTS `eby_omos`;
CREATE TABLE IF NOT EXISTS `eby_omos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Data dump for tabellen `eby_omos`
--

INSERT INTO `eby_omos` (`id`, `title`, `content`, `image`) VALUES
(1, 'Velkommen hos Akademisk Rejsebureau', 'Velkommen hos Akademisk Rejsebureau\r\nHos Akademisk Rejsebureau udbyder vi ekspertrejser - vi har altid en erfaren akademisk ekspertrejseleder i spidsen for alle vore rejser, som deler ud af sin viden og sørger for, at rejsen bliver en indholdsrig og lærerig oplevelse. Vi sætter fokus på kulturoplevelsen, går i dybden med landets historie og kommer bag facaden, hvor vi møder den lokal kunstner eller bliver budt indenfor i de private hjem. Vores danske ekspertrejseleder formidler, oversætter og guider undervejs, og sørger for, at vores rejsende kommer hjem en smule klogere - derfor er vores slogan Tænk på din ferie ...', '1464942166-the-oberoi---mauritius25893669-h1-beach-dinner---1.jpg');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `eby_users`
--

DROP TABLE IF EXISTS `eby_users`;
CREATE TABLE IF NOT EXISTS `eby_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `level` varchar(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

--
-- Data dump for tabellen `eby_users`
--

INSERT INTO `eby_users` (`id`, `username`, `password`, `level`) VALUES
(1, 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', '2');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `eby_welcometext`
--

DROP TABLE IF EXISTS `eby_welcometext`;
CREATE TABLE IF NOT EXISTS `eby_welcometext` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `content` text NOT NULL,
  `image1` varchar(64) DEFAULT NULL,
  `image2` varchar(64) DEFAULT NULL,
  `image3` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Data dump for tabellen `eby_welcometext`
--

INSERT INTO `eby_welcometext` (`id`, `title`, `content`, `image1`, `image2`, `image3`) VALUES
(1, 'Waterworld er store oplevelser uanset Ã¥rstid. PrÃ¸v vores Trip Finder og bliv klogere pÃ¥ Waterworld.', 'GrÃ¸nland er store oplevelser uanset Ã¥rstid.\r\nPrÃ¸v vores Trip Finder og bliv klogere pÃ¥ GrÃ¸nland.', '1464871801-images4.jpg', '1464871801-images2.jpg', '1464871801-images3.jpg');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
