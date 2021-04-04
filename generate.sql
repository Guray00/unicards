
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

PRIMARY KEY (`mail`)
);







-- ************************************ 	TEST
insert into User values ("test@test.it", "test", "098f6bcd4621d373cade4e832627b4f6");