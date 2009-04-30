# Sequel Pro dump
# Version 0.9
# http://code.google.com/p/sequel-pro
#
# Host: 127.0.0.1 (MySQL 5.0.51b)
# Database: simplypost
# Generation Time: 2009-04-30 15:08:53 +0200
# ************************************************************

# Dump of table forum_tree
# ------------------------------------------------------------

CREATE TABLE `forum_tree` (
  `materialized_path` varchar(225) NOT NULL,
  `node_id` int(11) unsigned NOT NULL,
  `node_type` varchar(10) default NULL,
  PRIMARY KEY  (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table forum_tree_meta
# ------------------------------------------------------------

CREATE TABLE `forum_tree_meta` (
  `meta_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(50) default NULL,
  `description` varchar(225) default NULL,
  `url_title` varchar(50) default NULL,
  `permission_flag` int(5) unsigned default NULL,
  `permission_id` int(11) unsigned default NULL,
  `restrict_child_type` varchar(10) default NULL,
  PRIMARY KEY  (`meta_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;



# Dump of table general
# ------------------------------------------------------------

CREATE TABLE `general` (
  `id` tinyint(2) unsigned default '1',
  `title` varchar(50) default NULL,
  `locked` tinyint(2) unsigned default '0',
  `language` varchar(50) default NULL,
  `template` varchar(50) default NULL,
  `version` varchar(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table groups
# ------------------------------------------------------------

CREATE TABLE `groups` (
  `group_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(50) default NULL,
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;



# Dump of table members
# ------------------------------------------------------------

CREATE TABLE `members` (
  `member_id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(10) unsigned default '0',
  `username` varchar(20) default NULL,
  `email` varchar(100) default NULL,
  `password` varchar(60) default NULL,
  `join_date` int(10) unsigned default '0',
  `banned` tinyint(1) unsigned default '0',
  `post_count` int(11) default '0',
  `rem_data` text,
  PRIMARY KEY  (`member_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;



# Dump of table permissions
# ------------------------------------------------------------

CREATE TABLE `permissions` (
  `permission_id` int(11) default NULL,
  `type` int(5) default NULL,
  `data` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table sp_sessions
# ------------------------------------------------------------

CREATE TABLE `sp_sessions` (
  `session_id` varchar(40) NOT NULL default '0',
  `ip_address` varchar(16) NOT NULL default '0',
  `user_agent` varchar(50) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL default '0',
  `user_data` text NOT NULL,
  PRIMARY KEY  (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table tracker
# ------------------------------------------------------------

CREATE TABLE `tracker` (
  `ip_address` varchar(16) default '0',
  `attempts` tinyint(2) unsigned default '0',
  `last_attempt` int(10) unsigned default NULL,
  `banned` tinyint(1) unsigned default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



