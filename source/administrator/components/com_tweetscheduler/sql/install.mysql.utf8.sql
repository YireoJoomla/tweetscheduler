CREATE TABLE IF NOT EXISTS `#__tweetscheduler_tweets` (
  `id` int(11) NOT NULL auto_increment,
  `account_id` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL default '0',
  `title` varchar(70) NOT NULL,
  `message` varchar(140) NOT NULL,
  `post_date` datetime NOT NULL,
  `post_state` tinyint(1) NOT NULL,
  `post_id` varchar(32) NOT NULL,
  `post_error` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__tweetscheduler_categories` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(140) NOT NULL,
  `url` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__tweetscheduler_accounts` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(140) NOT NULL,
  `type` varchar(50) NOT NULL,
  `consumer_key` varchar(255) NOT NULL,
  `consumer_secret` varchar(255) NOT NULL,
  `oauth_token` varchar(255) NOT NULL,
  `oauth_token_secret` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
