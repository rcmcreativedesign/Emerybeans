<?php
    $create_entry = "CREATE TABLE `entry` (`id` int(11) NOT NULL AUTO_INCREMENT,`uploadTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,`comments` text,`imageBinary` longblob NOT NULL,`fileExtension` varchar(50) NOT NULL,`imageType` int(11) NOT NULL,`type` varchar(128) DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;";
    $create_user = "CREATE TABLE `user` (`id` int(11) NOT NULL AUTO_INCREMENT,`emailAddress` text NOT NULL,`displayName` varchar(50) DEFAULT NULL,`pwHash` text NOT NULL,`hashSeed` text NOT NULL,`lastLoginTimestamp` timestamp NULL DEFAULT NULL,`createdTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,`enabled` bit(1) NOT NULL,`inviteAuthorized` bit(1) NOT NULL DEFAULT b'0',PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;";
    $create_entryview = "CREATE TABLE `entryview` (`id` int(11) NOT NULL AUTO_INCREMENT,`entryId` int(11) NOT NULL,`userId` int(11) NOT NULL,`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
    $create_entrylike = "CREATE TABLE `entrylike` (`id` int(11) NOT NULL AUTO_INCREMENT,`entryId` int(11) NOT NULL,`userId` int(11) NOT NULL,`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;";
?>