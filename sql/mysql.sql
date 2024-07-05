CREATE TABLE `tad_player` (
  `psn` smallint(5) unsigned NOT NULL auto_increment,
  `pcsn` smallint(5) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `creator` varchar(255) NOT NULL default '',
  `location` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `info` varchar(255) NOT NULL default '',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `post_date` varchar(255) NOT NULL default '',
  `enable_group` varchar(255) NOT NULL default '',
  `counter` smallint(5) unsigned NOT NULL default '0',
  `width` smallint(5) unsigned NOT NULL default '0',
  `height` smallint(5) unsigned NOT NULL default '0',
  `sort` smallint(5) unsigned NOT NULL default '0',
  `content` text NOT NULL,
  `youtube` varchar(255) NOT NULL default '',
  `logo` varchar(255) NOT NULL default '',
  PRIMARY KEY (`psn`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8;

CREATE TABLE `tad_player_cate` (
  `pcsn` smallint(5) unsigned NOT NULL auto_increment,
  `of_csn` smallint(5) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `enable_group` varchar(255) NOT NULL default '',
  `enable_upload_group` varchar(255) NOT NULL default '',
  `sort` smallint(5) unsigned NOT NULL default '0',
  `width` smallint(5) unsigned NOT NULL default '0',
  `height` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY (`pcsn`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8;

CREATE TABLE `tad_player_rank` (
  `col_name` varchar(50) NOT NULL,
  `col_sn` smallint(5) unsigned NOT NULL,
  `rank` tinyint(3) unsigned NOT NULL,
  `uid` mediumint(8) unsigned NOT NULL,
  `rank_date` datetime NOT NULL,
  PRIMARY KEY (`col_name`, `col_sn`, `uid`)
) ENGINE = MYISAM;