CREATE TABLE IF NOT EXISTS `clickwrap` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `agreement` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

ALTER TABLE `seller`
  ADD `isClickwrap` BOOLEAN NOT NULL ,
  ADD `clickwrapId` INT UNSIGNED NULL ;

ALTER TABLE `seller`
  ADD FOREIGN KEY ( `clickwrapId` )
    REFERENCES `bluepointweb`.`clickwrap` ( `id` )
    ON DELETE SET NULL ON UPDATE CASCADE ;
