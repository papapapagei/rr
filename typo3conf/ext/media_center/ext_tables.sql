#
# Table structure for table 'tx_mediacenter_item'
#
CREATE TABLE tx_mediacenter_item (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	description tinytext NOT NULL,
	author tinytext NOT NULL,
	file blob NOT NULL,
	file_url tinytext NOT NULL,
	captions tinytext NOT NULL,
	duration int(11) DEFAULT '0' NOT NULL,
	link tinytext NOT NULL,
	image blob NOT NULL,
	start int(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY pid (pid)
);


