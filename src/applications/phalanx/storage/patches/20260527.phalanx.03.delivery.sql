USE `{$NAMESPACE}_phalanx`;

ALTER TABLE `phalanx_reply`
  ADD `deliveryStatus` varchar(32) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL
  DEFAULT 'recorded'
  AFTER `messageText`;

CREATE TABLE `phalanx_delivery` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `threadID` int(10) unsigned NOT NULL,
  `replyID` int(10) unsigned NOT NULL,
  `deliveryURI` varchar(255) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `status` varchar(32) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `retryMode` varchar(32) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `lastRequestResult` varchar(32) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `lastRequestEpoch` int(10) unsigned DEFAULT NULL,
  `errorType` varchar(32) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} DEFAULT NULL,
  `errorCode` varchar(64) CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} DEFAULT NULL,
  `attemptCount` int(10) unsigned NOT NULL,
  `properties` longtext CHARACTER SET {$CHARSET} COLLATE {$COLLATE_TEXT} NOT NULL,
  `dateCreated` int(10) unsigned NOT NULL,
  `dateModified` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key_reply` (`replyID`,`dateModified`),
  KEY `key_status` (`status`,`dateModified`)
) ENGINE=InnoDB DEFAULT CHARSET={$CHARSET} COLLATE={$COLLATE_TEXT};
