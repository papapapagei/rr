#
# Table structure for table 'tx_formhandler_log'
#
CREATE TABLE tx_formhandler_log (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	ip tinytext,
	params text,
	is_spam int(11) unsigned DEFAULT '0',
	key_hash tinytext,
	PRIMARY KEY (uid),
	KEY parent (pid)
);
