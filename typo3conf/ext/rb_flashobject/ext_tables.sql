#
# Table structure for table 'tx_rbflashobject_movie'
#
CREATE TABLE tx_rbflashobject_movie (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	description tinytext NOT NULL,
	flashmovie blob NOT NULL,
	width tinytext NOT NULL,
	height tinytext NOT NULL,
	requiredversion tinytext NOT NULL,
	quality int(11) unsigned DEFAULT '0' NOT NULL,
	displaymenu tinyint(3) unsigned DEFAULT '0' NOT NULL,
	alternativecontent blob NOT NULL,
	redirecturl tinytext NOT NULL,
	backgroundcolor tinytext NOT NULL,
	additionalparams text NOT NULL,
	additionalvars text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);