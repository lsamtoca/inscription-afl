/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  lsantoca
 * Created: 20 janv. 2017
 */


DROP DATABASE IF EXISTS `{db}`;
CREATE DATABASE `{db}`;
DROP USER IF EXISTS '{user}'@'localhost';
CREATE USER '{user}'@'localhost' IDENTIFIED BY '{pwd}';
GRANT ALL PRIVILEGES ON `{db}` . * TO '{user}'@'localhost';
FLUSH PRIVILEGES;
