USE `{$NAMESPACE}_phalanx`;

CREATE TABLE `phalanx_reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `threadID` int(10) unsigned NOT NULL,
  `questionID` int(10) unsigned DEFAULT NULL,
  `authorPHID` varbinary(64) NOT NULL,
  `actionKind` varchar(64) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `messageText` longtext CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT},
  `properties` longtext CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `dateCreated` int(10) unsigned NOT NULL,
  `dateModified` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key_thread` (`threadID`,`dateModified`)
) ENGINE=InnoDB DEFAULT CHARSET={$CHARSET} COLLATE={$COLLATE_TEXT};
