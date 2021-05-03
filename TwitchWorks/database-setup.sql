CREATE DATABASE twitchworks;

USE twitchworks;

CREATE TABLE TwitchUsers (
    `ID` int NOT NULL AUTO_INCREMENT,
    `Token` varchar(255) NOT NULL,
    `Key` varchar(255) NOT NULL,
    `Username` varchar(255),
    `OAuthToken` varchar(255),
    `RefreshToken` varchar(255),
    PRIMARY KEY(`ID`),
    UNIQUE (`Token`)
);