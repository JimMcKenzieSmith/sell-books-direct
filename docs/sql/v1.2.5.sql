CREATE TABLE IF NOT EXISTS `invoiceStatus` (
  `id` tinyint(4) unsigned NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `invoiceStatus`
--

INSERT INTO `invoiceStatus` (`id`, `description`) VALUES
(0, 'Quote'),
(1, 'Pending Approval'),
(2, 'Approved'),
(3, 'Received'),
(4, 'Processed'),
(5, 'Paid'),
(6, 'Cancelled');



CREATE TABLE IF NOT EXISTS `invoiceWhat` (
  `id` tinyint(4) unsigned NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `invoiceWhat`
--

INSERT INTO `invoiceWhat` (`id`, `description`) VALUES
(1, 'Created Manual'),
(2, 'Created Auto'),
(3, 'Approved'),
(4, 'Received'),
(5, 'Processed'),
(6, 'Paid'),
(7, 'Cancelled'),
(8, 'Note');

ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_3` FOREIGN KEY (`invoiceStatus`) REFERENCES `invoiceStatus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `invoiceAction`
  ADD CONSTRAINT `invoiceAction_ibfk_2` FOREIGN KEY (`what`) REFERENCES `invoiceWhat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;