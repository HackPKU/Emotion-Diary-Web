-- Emotion Diary initial database structure

-- Table structure for table `user`

CREATE TABLE `user` (
  `userid` VARCHAR(16) NOT NULL,
  `name` VARCHAR(16) NOT NULL,
  `sex` VARCHAR(16) NOT NULL,
  `email` VARCHAR(128) NOT NULL,
  `icon` VARCHAR(128) NOT NULL,
  `register_time` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `latest_time` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`userid`)
);

-- Table structure for table `token`

CREATE TABLE `token` (
  `token` VARCHAR(128) NOT NULL,
  `userid` VARCHAR(16) NOT NULL,
  `expire_time` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`token`),
  FOREIGN KEY (`userid`) REFERENCES user(`userid`)
);

-- Table structure for table `diary`

CREATE TABLE `diary` (
  `diaryid` VARCHAR(16) NOT NULL,
  `userid` VARCHAR(16) NOT NULL,
  `emotion` SMALLINT NOT NULL,
  `selfie` VARCHAR(128) NOT NULL,
  `text` VARCHAR(2000) NOT NULL,
  `create_time` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  `edit_time` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`diaryid`),
  FOREIGN KEY (`userid`) REFERENCES user(`userid`)
);

