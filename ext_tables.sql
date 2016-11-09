#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (

	foldername varchar(255) DEFAULT '' NOT NULL,

	tx_extbase_type varchar(255) DEFAULT '' NOT NULL,

);

#
# Table structure for table 'fe_groups'
#
CREATE TABLE fe_groups (

	thdfilemangaeradmin tinyint(1) unsigned DEFAULT '0' NOT NULL,

	tx_extbase_type varchar(255) DEFAULT '' NOT NULL,

);
