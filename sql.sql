SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for message
-- ----------------------------
DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` int(11) DEFAULT NULL,
  `to` int(11) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `time` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `msg` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20)  NOT NULL ,
  `password` varchar(50)  NOT NULL ,
  `sex` tinyint DEFAULT 0 NOT NULL ,
  `age` SMALLINT DEFAULT 0 NOT NULL ,
  `created_at` datetime NOT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL ,
  `access_token` varchar(100) DEFAULT NULL,
  `access_ip` varchar(50) DEFAULT NULL,
  `token_expired` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB
  AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8
  COMMENT '用户表'
;

-- ----------------------------
-- Table structure for friend
-- ----------------------------
DROP TABLE  IF EXISTS `user_chat_list`;
CREATE TABLE `user_chat_list`(
  `id` int(11) not null AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL COMMENT '用户id',
  `friend_id` INT(11) NOT NULL COMMENT '好友Id或群组id',
  `type` TINYINT NOT NULL null DEFAULT 0 COMMENT '类型，0好友，1群组',
  `last_message_time` DATETIME DEFAULT NULL COMMENT '最后消息时间',
  PRIMARY KEY (`id`),
  INDEX (`user_id`),
  INDEX (`friend_id`)
)ENGINE =Innodb
 AUTO_INCREMENT=1
 DEFAULT CHARSET=utf8
 COMMENT '聊天对象表';

-- ----------------------------
-- Table structure for group
-- ----------------------------
DROP Table if EXISTS `group`;
CREATE TABLE `group`(
  `id` INT not NULL AUTO_INCREMENT,
  `sponsor_id` INT NOT NULL COMMENT '创建人id',
  `user_ids` VARCHAR(255) NOT NULL  COMMENT '群组userID数组',
  `created_at` DATETIME not NULL ,
  PRIMARY KEY (`id`)
)ENGINE =Innodb
 AUTO_INCREMENT=1
 DEFAULT CHARSET=utf8
 COMMENT '群组表';

-- ----------------------------
-- Table structure for user_friend
-- ----------------------------
DROP TABLE if EXISTS `user_friend`;
CREATE TABLE `user_friend`(
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL ,
  `friend_id` INT NOT NULL ,
  `add_time` DATETIME NOT NULL,
  `sponsor_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`user_id`),
  INDEX (`friend_id`)
) ENGINE =innodb
  AUTO_INCREMENT=1
  DEFAULT CHARSET =utf8
  COMMENT '好友表';
