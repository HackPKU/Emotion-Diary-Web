-- Emotion Diary initial database structure

-- Table structure for table `user`

CREATE TABLE IF NOT EXISTS `user` (
  `userid` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(32) NOT NULL,
  `password` VARCHAR(32) NOT NULL,
  `sex` VARCHAR(16) NOT NULL,
  `email` VARCHAR(128) NOT NULL,
  `icon` VARCHAR(8) NOT NULL,
  `faceid` VARCHAR(64) NOT NULL,
  `register_time` TIMESTAMP NOT NULL DEFAULT NOW(),
  `latest_time` TIMESTAMP NOT NULL DEFAULT NOW() ON UPDATE NOW()
);

-- Table structure for table `token`

CREATE TABLE IF NOT EXISTS `token` (
  `token` VARCHAR(16) NOT NULL PRIMARY KEY,
  `userid` INTEGER NOT NULL,
  `type` VARCHAR(16) NOT NULL,
  `latest_time` TIMESTAMP NOT NULL DEFAULT NOW(),
  FOREIGN KEY (`userid`) REFERENCES user(`userid`)
);

-- Token clearing schedule

SET GLOBAL event_scheduler = 1;
CREATE EVENT IF NOT EXISTS `clear_token` ON SCHEDULE EVERY 1 DAY
DO DELETE FROM token WHERE TO_DAYS(NOW()) - TO_DAYS(latest_time) > 30;

-- Table structure for table `diary`

CREATE TABLE IF NOT EXISTS `diary` (
  `diaryid` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `userid` INTEGER NOT NULL,
  `share_key` VARCHAR(16) DEFAULT NULL,
  `emotion` SMALLINT NOT NULL,
  `selfie` VARCHAR(8) NOT NULL,
  `images` VARCHAR(128) NOT NULL,
  `tags` VARCHAR(512) NOT NULL,
  `text` TEXT NOT NULL,
  `place_name` VARCHAR(128) NOT NULL,
  `place_long` DOUBLE NOT NULL,
  `place_lat` DOUBLE NOT NULL,
  `weather` VARCHAR(32) NOT NULL,
  `create_time` TIMESTAMP NOT NULL DEFAULT NOW(),
  FOREIGN KEY (`userid`) REFERENCES user(`userid`)
);
