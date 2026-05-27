USE `{$NAMESPACE}_phalanx`;

CREATE TABLE `phalanx_thread` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `objectPHID` varbinary(64) NOT NULL,
  `externalThreadID` varchar(64) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `title` varchar(255) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `status` varchar(32) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `agentLabel` varchar(64) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `backendKind` varchar(64) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} DEFAULT NULL,
  `backendThreadID` varchar(64) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} DEFAULT NULL,
  `externalResumeRef` varchar(255) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} DEFAULT NULL,
  `properties` longtext CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `dateCreated` int(10) unsigned NOT NULL,
  `dateModified` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_external` (`externalThreadID`),
  KEY `key_object` (`objectPHID`,`status`,`dateModified`)
) ENGINE=InnoDB DEFAULT CHARSET={$CHARSET} COLLATE={$COLLATE_TEXT};

CREATE TABLE `phalanx_artifact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `threadID` int(10) unsigned NOT NULL,
  `externalArtifactID` varchar(64) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `artifactKind` varchar(64) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `label` varchar(128) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `summary` longtext CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT},
  `filePHID` varbinary(64) DEFAULT NULL,
  `objectPHID` varbinary(64) DEFAULT NULL,
  `externalRef` varchar(255) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} DEFAULT NULL,
  `payload` longtext CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `dateCreated` int(10) unsigned NOT NULL,
  `dateModified` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_external` (`threadID`,`externalArtifactID`),
  KEY `key_thread` (`threadID`,`dateModified`)
) ENGINE=InnoDB DEFAULT CHARSET={$CHARSET} COLLATE={$COLLATE_TEXT};

CREATE TABLE `phalanx_question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `threadID` int(10) unsigned NOT NULL,
  `isActive` tinyint(1) NOT NULL,
  `questionKind` varchar(64) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `prompt` longtext CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `primaryContactPHID` varbinary(64) DEFAULT NULL,
  `responseChannel` varchar(32) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `requiredActionKind` varchar(64) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `requiredAction` longtext CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT},
  `defaultAction` varchar(64) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} DEFAULT NULL,
  `properties` longtext CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `dateCreated` int(10) unsigned NOT NULL,
  `dateModified` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key_thread` (`threadID`,`isActive`,`dateModified`)
) ENGINE=InnoDB DEFAULT CHARSET={$CHARSET} COLLATE={$COLLATE_TEXT};
