#
# Add field to table 'be_groups'
#
CREATE TABLE be_groups (
	tx_dam_mountpoints tinytext
);


#
# Add field to table 'be_users'
#
CREATE TABLE be_users (
	tx_dam_mountpoints tinytext
);



#
# Add fields to table 'tt_content'
#
CREATE TABLE tt_content (
    tx_dam_images int(11) unsigned DEFAULT '0' NOT NULL,
    tx_dam_files int(11) unsigned DEFAULT '0' NOT NULL,
    ce_flexform mediumtext
);



#
# Table structure for table 'tx_dam'
#
CREATE TABLE tx_dam (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,
  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,

  # defines the file as a child of a group
  parent_id int(11) DEFAULT '0' NOT NULL,

  # might be handy to reconnect a record to a reuploaded file
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,




  # like hidden but not active means: don`t use in element browser (insert content in page)
 #TODO remove?!
  active tinyint(4) unsigned DEFAULT '0' NOT NULL,

#TODO remove?!
  sorting int(10) unsigned DEFAULT '0' NOT NULL,







  # could be usefull for frontend applications which use the dam data
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,
  fe_group varchar(100) DEFAULT '0' NOT NULL,

  # languages
  sys_language_uid int(11) DEFAULT '0' NOT NULL,
  l18n_parent int(11) DEFAULT '0' NOT NULL,
  l18n_diffsource mediumblob,

  # workspaces
  t3ver_oid int(11) DEFAULT '0' NOT NULL,
  t3ver_id int(11) DEFAULT '0' NOT NULL,
  t3ver_wsid int(11) DEFAULT '0' NOT NULL,
  t3ver_label varchar(30) DEFAULT '' NOT NULL,
  t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
  t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
  t3ver_count int(11) DEFAULT '0' NOT NULL,
  t3ver_tstamp int(11) DEFAULT '0' NOT NULL,

  # Inspired by dublin core
  #
  # Image, Text, Sound, Dataset (cvs, xml),
  # Interactive (flash), Software (exe, zip),
  # Collection (div data in zip),
  # Service (uri,...), Anything/Sonstiges
  media_type tinyint(4) unsigned DEFAULT '0' NOT NULL,

  title tinytext,
  category int(11) DEFAULT '0' NOT NULL,


  # man(ual), auto, cron
  index_type varchar(4) DEFAULT '' NOT NULL,


  # unix 'file' command gives mime type
  # needed because of the lack of file extensions on mac
  file_mime_type varchar(45) DEFAULT '' NOT NULL,
  file_mime_subtype varchar(45) DEFAULT '' NOT NULL,

  # psd,doc,tif,class
  file_type varchar(5) DEFAULT '' NOT NULL,

  # pdf: 1.3
  file_type_version varchar(9) DEFAULT '' NOT NULL,

  file_name varchar(255) DEFAULT '' NOT NULL,
  file_path text,
  file_size int(11) unsigned DEFAULT '0' NOT NULL,
  file_mtime int(11) unsigned DEFAULT '0' NOT NULL,
  file_inode int(11) DEFAULT '0' NOT NULL,

  # date of file creation
  file_ctime int(11) unsigned DEFAULT '0' NOT NULL,

  # hex md5 of file content using file_md5()
  # to be able to identify the file exactly
  file_hash varchar(32) DEFAULT '' NOT NULL,

  # 0=ok, 1=needs refresh etc.
  file_status tinyint(4) unsigned DEFAULT '0' NOT NULL,

  # index a cd:
  # create usable images in size 600x600 and copy to folder
  # or copy the files from a location (network) to folder
  # the location is then: "photo stock picture cd 8"
  file_orig_location varchar(255) DEFAULT '' NOT NULL,
  file_orig_loc_desc varchar(45) DEFAULT '' NOT NULL,

  # pdf: Acrobat Distiller
  file_creator varchar(45) DEFAULT '' NOT NULL,

  # name that should be used for download
  file_dl_name varchar(255) DEFAULT '' NOT NULL,


  # dummy field for relation to content elements
  file_usage int(11) unsigned DEFAULT '0' NOT NULL,



  # xml / array
  meta text,


  # sku / bestell nr
  # for custom use. DAM don`t use this
  ident varchar(45) DEFAULT '' NOT NULL,

  # photographer
  creator varchar(45) DEFAULT '' NOT NULL,

  keywords tinytext,
  description text,

  # for web or FE applications
  alt_text varchar(255) DEFAULT '' NOT NULL,
  caption text,


  # if not set by hand, the first kb of a pdf, doc file for example
  abstract text,

  # for searching and non editable: the first 60 kb of a pdf, doc file for example
  search_content text,

  # document language
  language char(3) DEFAULT '' NOT NULL,

  # text document include x pages
  pages int(4) unsigned DEFAULT '0' NOT NULL,

  # vendor?
  publisher varchar(45) DEFAULT '' NOT NULL,
  copyright varchar(128) DEFAULT '' NOT NULL,

  # instructions and notes
  instructions tinytext,

  # created, modified
  # date_cr don`t have to be the file creation time. It is the time a photo is shooten not the time the slide is scanned and saved
  date_cr int(11) unsigned DEFAULT '0' NOT NULL,
  # last modification. Not neccessarily the file modification time
  date_mod int(11) unsigned DEFAULT '0' NOT NULL,

  # location: what do you see on the picture?
  loc_desc varchar(45) DEFAULT '' NOT NULL,
  loc_country char(3) DEFAULT '' NOT NULL,
  loc_city varchar(45) DEFAULT '' NOT NULL,

  # dpi
  hres int(11) unsigned DEFAULT '0' NOT NULL,
  vres int(11) unsigned DEFAULT '0' NOT NULL,

  # size in pixel
  hpixels int(11) unsigned DEFAULT '0' NOT NULL,
  vpixels int(11) unsigned DEFAULT '0' NOT NULL,

  # RGB,sRGB,YUV, ...
  color_space varchar(4) DEFAULT '' NOT NULL,

  # 21 cm, 29.7 cm: A4
  width float unsigned DEFAULT '0' NOT NULL,
  height float unsigned DEFAULT '0' NOT NULL,
  # px,mm,cm,m,p, ...
  height_unit char(3) DEFAULT '' NOT NULL,


#  FULLTEXT  (title),
#  FULLTEXT  (keywords),
#  FULLTEXT  (description),
#  FULLTEXT  (abstract),
#  FULLTEXT  (creator),
#  FULLTEXT  (search_content),
#  FULLTEXT  (instructions),
#  FULLTEXT  (file_orig_loc_desc),

  PRIMARY KEY (uid),
  KEY parent (pid),
  KEY media_type (media_type),
  KEY t3ver_oid (t3ver_oid),
  KEY file_type (file_type),
  KEY file_hash (file_hash),
  KEY file_name (file_name)
);




#
# Table structure for table 'tx_dam_cat'
#
CREATE TABLE tx_dam_cat (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,
  parent_id int(11) DEFAULT '0' NOT NULL,

  # languages
  sys_language_uid int(11) DEFAULT '0' NOT NULL,
  l18n_parent int(11) DEFAULT '0' NOT NULL,
  l18n_diffsource mediumblob,

  # workspaces
  t3ver_oid int(11) DEFAULT '0' NOT NULL,
  t3ver_id int(11) DEFAULT '0' NOT NULL,
  t3ver_wsid int(11) DEFAULT '0' NOT NULL,
  t3ver_label varchar(30) DEFAULT '' NOT NULL,
  t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
  t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
  t3ver_count int(11) DEFAULT '0' NOT NULL,
  t3ver_tstamp int(11) DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,

  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  fe_group varchar(100) DEFAULT '0' NOT NULL,

  title tinytext,
  nav_title tinytext,
  subtitle tinytext,
  keywords text,
  description text,
  PRIMARY KEY (uid),
  KEY parent (pid),
  KEY parent_id (parent_id)
);



#
# Table structure for table 'tx_dam_mm_cat'
#
CREATE TABLE tx_dam_mm_cat (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  sorting_foreign int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);


#
# Table structure for table 'tx_dam_types_avail'
#
CREATE TABLE tx_dam_metypes_avail (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,
  parent_id int(11) unsigned DEFAULT '0' NOT NULL,
  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  title varchar(30) DEFAULT '' NOT NULL,
  type tinyint(4) unsigned DEFAULT '0' NOT NULL,
  PRIMARY KEY (uid),
  KEY parent (pid),
  KEY parent_id (parent_id)
);


#
# Table structure for table 'tx_dam_mm_ref'
#
CREATE TABLE tx_dam_mm_ref (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(90) DEFAULT '' NOT NULL,
  ident text,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_dam_log_index'
#
CREATE TABLE tx_dam_log_index (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,
  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,

  # this is the time stamp of the index run
  # multiple log entries can be in the log table with that time stamp
  crdate int(11) unsigned DEFAULT '0' NOT NULL,

  # if is "1" this entry describes an error
  # error entries are for sinlge files/errors while non-error entries just describe the index run with item_count
  error tinyint(4) unsigned DEFAULT '0' NOT NULL,

  # man(ual), auto, cron
  type varchar(4) DEFAULT '' NOT NULL,

  # number of elements indexed (is 1 for error entry)
  item_count int(11) unsigned DEFAULT '0' NOT NULL,

  # short description
  message tinytext,

  PRIMARY KEY (uid),
  KEY parent (pid)
);


#
# Table structure for table 'tx_dam_file_tracking'
#
CREATE TABLE tx_dam_file_tracking (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,
  tstamp int(11) unsigned DEFAULT '0' NOT NULL,

  file_name varchar(255) DEFAULT '' NOT NULL,
  file_path text,
  file_size int(11) unsigned DEFAULT '0' NOT NULL,

  # date of file creation
  file_ctime int(11) unsigned DEFAULT '0' NOT NULL,

  # hex md5 of file content using file_md5()
  # to be able to identify the file exactly
  file_hash varchar(32) DEFAULT '' NOT NULL,

  PRIMARY KEY (uid),
  KEY parent (pid)
);




#
# Table structure for table 'tx_dam_selection'
#
CREATE TABLE tx_dam_selection (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	type int(11) DEFAULT '0' NOT NULL,
	title tinytext,
	definition text,

	PRIMARY KEY (uid),
	KEY parent (pid)
);
