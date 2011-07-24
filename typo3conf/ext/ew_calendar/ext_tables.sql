#
# Table structure for table 'tx_ewcalendar_dates'
#
CREATE TABLE tx_ewcalendar_dates (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumtext,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	title tinytext,
	production int(11) DEFAULT '0' NOT NULL,
	highlight tinyint(3) DEFAULT '0' NOT NULL,
	info tinytext,
	info_size tinyint(3) DEFAULT '0' NOT NULL,
	date int(11) DEFAULT '0' NOT NULL,
	only_month tinyint(3) DEFAULT '0' NOT NULL,
	dont_teaser tinyint(3) DEFAULT '0' NOT NULL,
    image int(11) unsigned DEFAULT '0' NOT NULL,
	facts text,
	link_title tinytext,
	link_text tinytext,
	link tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_ewcalendar_productions'
#
CREATE TABLE tx_ewcalendar_productions (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumtext,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	title tinytext,
	link tinytext,
    image int(11) unsigned DEFAULT '0' NOT NULL,
	facts text,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
