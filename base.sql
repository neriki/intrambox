# phpMyAdmin SQL Dump
# version 2.5.6
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: Feb 25, 2005 at 09:04 PM
# Server version: 4.0.15
# PHP Version: 4.3.3
# 
# Database : `intrambox2`
# 

# --------------------------------------------------------

#
# Table structure for table `chanson_playlists`
#

CREATE TABLE `chanson_playlists` (
  `idchanson` bigint(20) NOT NULL auto_increment,
  `chanson` varchar(255) NOT NULL default '',
  `idplaylists` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`idchanson`)
) TYPE=MyISAM AUTO_INCREMENT=204 ;

#
# Table structure for table `lecteur`
#

CREATE TABLE `lecteur` (
  `cle_lecteur` bigint(20) NOT NULL auto_increment,
  `commande` varchar(255) NOT NULL default '',
  `extension` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`cle_lecteur`)
) TYPE=MyISAM AUTO_INCREMENT=12 ;

#
# Dumping data for table `lecteur`
#

INSERT INTO `lecteur` VALUES (1, '/usr/bin/ogg123', 'ogg');
INSERT INTO `lecteur` VALUES (2, '/usr/bin/mpg123', 'mp3');
INSERT INTO `lecteur` VALUES (3, '/usr/local/bin/mplayer', 'wma');
INSERT INTO `lecteur` VALUES (4, '/usr/bin/play', 'wav');
INSERT INTO `lecteur` VALUES (5, '/usr/local/bin/xmp', 's3m');
INSERT INTO `lecteur` VALUES (6, '/usr/local/bin/xmp', 'mod');
INSERT INTO `lecteur` VALUES (7, '/usr/local/bin/xmp', 'med');
INSERT INTO `lecteur` VALUES (8, '/usr/local/bin/xmp', 'xm');
INSERT INTO `lecteur` VALUES (9, '/usr/local/bin/xmp', 'it');
INSERT INTO `lecteur` VALUES (10, '/usr/local/bin/xmp', '669');
INSERT INTO `lecteur` VALUES (11, '/usr/local/bin/xmp', 'mtm');

# --------------------------------------------------------

#
# Table structure for table `lecture`
#

CREATE TABLE `lecture` (
  `cle_lecture` bigint(20) NOT NULL default '0',
  `repetition` tinyint(4) default NULL,
  `pause` tinyint(4) default NULL,
  PRIMARY KEY  (`cle_lecture`)
) TYPE=MyISAM;

#
# Dumping data for table `lecture`
#

INSERT INTO `lecture` VALUES (-1, 1, 0);

# --------------------------------------------------------

#
# Table structure for table `playlist`
#

CREATE TABLE `playlist` (
  `cle_playlist` bigint(20) NOT NULL auto_increment,
  `chanson` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`cle_playlist`)
) TYPE=MyISAM AUTO_INCREMENT=52 ;

# --------------------------------------------------------

#
# Table structure for table `playlists`
#

CREATE TABLE `playlists` (
  `idplaylists` bigint(20) NOT NULL auto_increment,
  `libplaylists` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`idplaylists`)
) TYPE=MyISAM AUTO_INCREMENT=16 ;


