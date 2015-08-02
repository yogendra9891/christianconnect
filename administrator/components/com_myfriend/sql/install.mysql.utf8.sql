CREATE TABLE IF NOT EXISTS `#__christianconnection` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`connectfrom` INT(11)  NOT NULL ,
`connectto` INT(11)  NOT NULL ,
`status` TINYINT(4)  NOT NULL ,
`msg` TEXT NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

