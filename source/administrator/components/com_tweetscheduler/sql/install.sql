
CREATE TABLE IF NOT EXISTS `#__tweetscheduler_tweets` (
  `id` int(11) NOT NULL auto_increment,
  `category_id` int(11) NOT NULL default '0',
  `message` varchar(140) NOT NULL,
  `post_date` tinyint(1) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;


CREATE TABLE IF NOT EXISTS `#__tweetscheduler_accounts` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(140) NOT NULL,
  `username` varchar(140) NOT NULL,
  `password` varchar(140) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

