SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `adminUser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `contactName` varchar(255) NOT NULL,
  `contactEmail` varchar(255) NOT NULL,
  `passwordHash` varchar(40) NOT NULL,
  `passwordSalt` varchar(64) NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

CREATE TABLE IF NOT EXISTS `buyList` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uploadDate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

CREATE TABLE IF NOT EXISTS `buyListItem` (
  `buyListId` int(10) unsigned NOT NULL,
  `isbn13` varchar(13) NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `price` decimal(6,2) NOT NULL,
  PRIMARY KEY (`buyListId`,`isbn13`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `invoice` (
  `id` varchar(13) NOT NULL,
  `sellerId` int(10) unsigned NOT NULL,
  `sellerInvoiceNumber` varchar(255) NOT NULL,
  `shipDate` date NOT NULL,
  `invoiceStatus` tinyint(4) unsigned NOT NULL,
  `buyListId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sellerId_2` (`sellerId`,`sellerInvoiceNumber`),
  KEY `sellerId` (`sellerId`),
  KEY `buyListId` (`buyListId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `invoiceAction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoiceId` varchar(13) NOT NULL,
  `actionDate` datetime NOT NULL,
  `who` varchar(255) NOT NULL,
  `what` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoiceId` (`invoiceId`),
  KEY `what` (`what`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

CREATE TABLE IF NOT EXISTS `invoiceItem` (
  `invoiceId` varchar(13) NOT NULL,
  `isbn13` varchar(13) NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `price` decimal(6,2) NOT NULL,
  PRIMARY KEY (`invoiceId`,`isbn13`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `invoiceNote` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoiceId` varchar(13) NOT NULL,
  `date` datetime NOT NULL,
  `note` text NOT NULL,
  `who` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoiceId` (`invoiceId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

CREATE TABLE IF NOT EXISTS `seller` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `contactName` varchar(255) NOT NULL,
  `contactEmail` varchar(255) NOT NULL,
  `contactPhone` varchar(255) DEFAULT NULL,
  `payeeName` varchar(255) DEFAULT NULL,
  `paymentAddress1` text,
  `paymentAddress2` text,
  `paymentCity` varchar(255) DEFAULT NULL,
  `paymentState` varchar(2) DEFAULT NULL,
  `paymentZip` varchar(10) DEFAULT NULL,
  `passwordHash` varchar(40) NOT NULL,
  `passwordSalt` varchar(64) NOT NULL,
  `passwordChange` tinyint(2) unsigned NOT NULL,
  `sellerStatus` tinyint(4) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contactEmail` (`contactEmail`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;


ALTER TABLE `buyListItem`
  ADD CONSTRAINT `buyListItem_ibfk_1` FOREIGN KEY (`buyListId`) REFERENCES `buyList` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`sellerId`) REFERENCES `seller` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_ibfk_2` FOREIGN KEY (`buyListId`) REFERENCES `buyList` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `invoiceAction`
  ADD CONSTRAINT `invoiceAction_ibfk_1` FOREIGN KEY (`invoiceId`) REFERENCES `invoice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `invoiceItem`
  ADD CONSTRAINT `invoiceItem_ibfk_1` FOREIGN KEY (`invoiceId`) REFERENCES `invoice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `invoiceNote`
  ADD CONSTRAINT `invoiceNote_ibfk_1` FOREIGN KEY (`invoiceId`) REFERENCES `invoice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
