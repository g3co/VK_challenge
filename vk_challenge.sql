-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_name` varchar(128) DEFAULT NULL,
  `task_descr` longtext,
  `author_id` int(11) DEFAULT NULL,
  `dev_id` int(11) DEFAULT NULL,
  `change_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`),
  KEY `dev_id` (`dev_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tasks` (`id`, `task_name`, `task_descr`, `author_id`, `dev_id`, `change_date`) VALUES
(1,	'Test',	'asdasda',	1,	NULL,	NULL),
(2,	'Test',	'asdasda',	1,	NULL,	NULL),
(3,	'new task',	'short description for task',	NULL,	NULL,	NULL),
(4,	'new task',	'short description for task',	14,	NULL,	NULL),
(5,	'new task',	'short description for task',	14,	NULL,	NULL),
(6,	'new task',	'short description for task',	14,	NULL,	NULL),
(7,	'new task',	'short description for task',	14,	NULL,	NULL),
(8,	'new task',	'&lt;a href=\'x\'&gt;short description for task&lt;/a&gt;',	14,	NULL,	NULL),
(9,	'new task',	'short description for task',	14,	NULL,	NULL);

DROP TABLE IF EXISTS `tasks_operations`;
CREATE TABLE `tasks_operations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `task_id` int(11) DEFAULT NULL,
  `operation_type` int(11) DEFAULT NULL,
  `operation_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nick_name` varchar(45) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `salt` varchar(128) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `nick_name` (`nick_name`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`, `nick_name`, `email`, `password`, `salt`, `type`) VALUES
(1,	'valera',	'kabisov@mail.ru',	'c8b2f17833a4c73bb20f88876219ddcd',	'2',	NULL),
(5,	'xxx',	'asx@mail.ru',	'7d290c289226724affde01d7d559c18a',	'42935582559a44a415476b5.48557838',	1),
(8,	'a1',	'5asx@mail.ru',	'd457a29d66022273c0a468d07d48546d',	'199386748559a44b1183a786.97248872',	1),
(10,	'a17',	'5a6sx@mail.ru',	'1d5e42a825c6a89dd852cd6eca35c9b8',	'201609556559a44b97bbe2f6.16000070',	1),
(11,	'a174',	'5a6sx5@mail.ru',	'4d7aec5501b87dbaa86c1a551e982c1b',	'55230874159a44bc6267870.68971529',	1),
(13,	'a1746',	'5a6sx55@mail.ru',	'0adc75e11c2a0ccdce49b89243f72b8e',	'117095114559a44c12ee3708.44389831',	1),
(14,	'b1xza',	'b1@mail.ru',	'23a37217a652ea274dfbf0c62dc7191b',	'85538993659a44c2fb98559.39098598',	1),
(21,	'b2',	'b2@mail.ru',	'b4e0c28c98037a8af116201d152c0e5f',	'174941321359a53f1b575563.92749914',	1);

DROP TABLE IF EXISTS `users_accounts`;
CREATE TABLE `users_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `current_account` float DEFAULT '0',
  `account_change` float DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2017-08-29 17:49:12
