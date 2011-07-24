#
# Add fields to table 'tt_content'
#
CREATE TABLE tt_content (
    tx_ewgallery_type tinyint(3) DEFAULT '0' NOT NULL,
    tx_ewgallery_smallimage int(11) unsigned DEFAULT '0' NOT NULL,
    tx_ewgallery_bigimage int(11) unsigned DEFAULT '0' NOT NULL,
    tx_ewgallery_video int(11) unsigned DEFAULT '0' NOT NULL,
    tx_ewgallery_video_button int(11) unsigned DEFAULT '0' NOT NULL,
    tx_ewgallery_video_title tinytext,
    tx_ewgallery_video_autostart tinyint(3) DEFAULT '0' NOT NULL,
);

#
# Add fields to table 'pages'
#
CREATE TABLE pages (
    tx_ewgallery_video int(11) unsigned DEFAULT '0' NOT NULL,
    tx_ewgallery_image int(11) unsigned DEFAULT '0' NOT NULL,
    tx_ewgallery_image_x int(11) unsigned DEFAULT '0' NOT NULL,
    tx_ewgallery_image_y int(11) unsigned DEFAULT '0' NOT NULL,
);
