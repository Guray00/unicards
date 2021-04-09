
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
CONSTRAINT `user_account` FOREIGN KEY `fkIdx_75` (`mail`) REFERENCES `User` (`mail`) ON DELETE CASCADE ON UPDATE CASCADE
);



-- ************************************** `Card`
CREATE TABLE `Card`
(
 `id`       integer unsigned NOT NULL AUTO_INCREMENT ,
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
 `name` varchar(45) NOT NULL ,

PRIMARY KEY (`name`)
);


-- ************************************** `Deck`
CREATE TABLE `Deck`
(
 `id`     integer unsigned NOT NULL AUTO_INCREMENT ,
 `user`   varchar(300) NOT NULL ,
 `name`   varchar(45) NOT NULL ,
 `school` varchar(45) NULL ,
 `degree` varchar(45) NULL ,
 `public` bit NOT NULL DEFAULT 0 ,

PRIMARY KEY (`id`, `user`),
KEY `fkIdx_21` (`user`),
CONSTRAINT `deck_user` FOREIGN KEY `fkIdx_21` (`user`) REFERENCES `User` (`mail`) ON DELETE CASCADE ON UPDATE CASCADE,
KEY `fkIdx_39` (`school`),
CONSTRAINT `deck_school` FOREIGN KEY `fkIdx_39` (`school`) REFERENCES `school` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
KEY `fkIdx_83` (`degree`),
CONSTRAINT `FK_82` FOREIGN KEY `fkIdx_83` (`degree`) REFERENCES `degree` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
);


-- ************************************** `Tag`

CREATE TABLE `Tag`
(
 `name` varchar(45) NOT NULL ,

PRIMARY KEY (`name`)
);



-- ************************************** `deck_tag`
CREATE TABLE `deck_tag`
(
 `deck` integer unsigned NOT NULL ,
 `user` varchar(300) NOT NULL ,
 `name` varchar(45) NOT NULL ,

PRIMARY KEY (`deck`, `user`, `name`),
KEY `fkIdx_46` (`deck`, `user`),
CONSTRAINT `FK_45` FOREIGN KEY `fkIdx_46` (`deck`, `user`) REFERENCES `Deck` (`id`, `user`) ON DELETE CASCADE ON UPDATE CASCADE,
KEY `fkIdx_51` (`name`),
CONSTRAINT `FK_50` FOREIGN KEY `fkIdx_51` (`name`) REFERENCES `Tag` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
);


-- ************************************** `Section`
CREATE TABLE `Section`
(
 `deck_id` integer unsigned NOT NULL ,
 `user`    varchar(300) NOT NULL ,
 `card_id` integer unsigned NOT NULL ,
 `name`    varchar(45) NOT NULL ,

PRIMARY KEY (`deck_id`, `user`, `card_id`),
KEY `fkIdx_92` (`deck_id`, `user`),
CONSTRAINT `FK_91` FOREIGN KEY `fkIdx_92` (`deck_id`, `user`) REFERENCES `Deck` (`id`, `user`),
KEY `fkIdx_98` (`card_id`),
CONSTRAINT `FK_97` FOREIGN KEY `fkIdx_98` (`card_id`) REFERENCES `Card` (`id`)
);

-- ************************************** `Session`
CREATE TABLE `Session`
(
 `id`   integer unsigned NOT NULL ,
 `mail` varchar(300) NOT NULL ,
 `time` timestamp NOT NULL ,

PRIMARY KEY (`id`, `mail`),
KEY `fkIdx_58` (`mail`),
CONSTRAINT `username_session` FOREIGN KEY `fkIdx_58` (`mail`) REFERENCES `User` (`mail`) ON DELETE CASCADE ON UPDATE CASCADE
);




-- ************************************ 	TEST
insert into User values ("test@test.it", "test", "$2y$10$/MUUE/wL3CrUIxtmr0.EOO1nIAU6t9DY9ijuBPtfS0rXoUkJkEvFu", "it", "light");

insert into deck (id, user, name, public) values (1, 	"test@test.it", 	"deck test", 	1);

insert into school values ("Liceo Pontormo");
insert into school values ("Universita di Pisa");

insert into card (question, answer) values ("domanda di test", "risposta di test");
INSERT INTO `card` (`id`, `question`, `answer`) VALUES (NULL, 'domanda 2', 'risposta 2');
insert into Section values (1, "test@test.it", 1, "sezione 1");
insert into Section values (1, "test@test.it", 2, "sezione 2");
