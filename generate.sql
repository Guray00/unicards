
-- generando il database
DROP DATABASE IF EXISTS unicards;
CREATE DATABASE unicards 
	CHARACTER SET utf8 COLLATE utf8_general_ci; -- imposta la codifica dei caratteri a utf8

-- ************************************** `User`
CREATE TABLE `User`
(
 `mail`     varchar(300) NOT NULL ,
 `username` varchar(45) NOT NULL ,
 `password` varchar(256) NOT NULL ,
 `lang`     varchar(6) NOT NULL ,
 `theme`    varchar(15) NOT NULL ,

PRIMARY KEY (`mail`)
);




-- ************************************** `Account`
CREATE TABLE `Account`
(
 `mail`    varchar(300) NOT NULL ,
 `name`    varchar(45) NOT NULL ,
 `surname` varchar(45) NOT NULL ,
 `job`     varchar(45) NOT NULL ,

PRIMARY KEY (`mail`),
KEY `fkIdx_75` (`mail`),
CONSTRAINT `user_account` FOREIGN KEY `fkIdx_75` (`mail`) REFERENCES `User` (`mail`)
);


-- ************************************** `Card`
CREATE TABLE `Card`
(
 `id`       integer NOT NULL AUTO_INCREMENT,
 `question` text NOT NULL ,
 `answer`   text NOT NULL ,

PRIMARY KEY (`id`)
);



-- ************************************** `degree`
CREATE TABLE `degree`
(
 `name` varchar(45) NOT NULL ,
  PRIMARY KEY (`name`)
);


-- ************************************** `school`
CREATE TABLE `school`
(
 `id`   integer NOT NULL ,
 `name` varchar(45) NOT NULL ,

PRIMARY KEY (`id`)
);

-- ************************************** `Deck`
CREATE TABLE `Deck`
(
 `id`     integer NOT NULL AUTO_INCREMENT,
 `user`   varchar(300) NOT NULL ,
 `name`   varchar(45) NOT NULL ,
 `school` integer NOT NULL ,
 `degree` varchar(45) NOT NULL ,

PRIMARY KEY (`id`, `user`),
KEY `fkIdx_21` (`user`),
CONSTRAINT `deck_user` FOREIGN KEY `fkIdx_21` (`user`) REFERENCES `User` (`mail`),
KEY `fkIdx_39` (`school`),
CONSTRAINT `deck_school` FOREIGN KEY `fkIdx_39` (`school`) REFERENCES `school` (`id`),
KEY `fkIdx_83` (`degree`),
CONSTRAINT `FK_82` FOREIGN KEY `fkIdx_83` (`degree`) REFERENCES `degree` (`name`)
);

-- ************************************ 	TEST
insert into User values ("test@test.it", "test", "$2y$10$/MUUE/wL3CrUIxtmr0.EOO1nIAU6t9DY9ijuBPtfS0rXoUkJkEvFu", "it", "light");


/*modificati a manina
	- auto_increment su deck - id
	- auto_increment su card - id
*/