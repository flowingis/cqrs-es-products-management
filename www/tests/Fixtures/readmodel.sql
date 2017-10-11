SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productId` varchar (60) NOT NULL,
  `barcode` varchar (60) NOT NULL,
  `name` varchar (120) NOT NULL,
  `imageUrl` varchar (200) NOT NULL,
  `brand` varchar (120) NOT NULL,
  `createdAt` varchar (32) NOT NULL,
  `updatedAt` varchar (32) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
