CREATE DATABASE ari_dream;

USE ari_dream;

CREATE TABLE users (
    userID INT AUTO_INCREMENT NOT NULL PRIMARY KEY ,
    username CHAR(16) UNIQUE NOT NULL ,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE content(
    contentID INT AUTO_INCREMENT NOT NULL PRIMARY KEY ,
    userID INT NOT NULL ,
    caption LONGTEXT ,
    imagePath VARCHAR(255) ,
    timestamp DATETIME NOT NULL ,
    FOREIGN KEY (userID) REFERENCES users(userID)
);
