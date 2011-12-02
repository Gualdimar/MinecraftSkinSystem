SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for errorlogin
-- ----------------------------
DROP TABLE IF EXISTS `errorlogin`;
CREATE TABLE `errorlogin` (
  `ip` varchar(39) NOT NULL,
  `date` datetime NOT NULL,
  `num` int(1) NOT NULL,
  PRIMARY KEY  (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records 
-- ----------------------------
