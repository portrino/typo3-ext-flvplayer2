<?php

########################################################################
# Extension Manager/Repository config file for ext "flvplayer2".
#
# Auto generated 20-08-2010 14:37
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Flash Video Player v2 (FLV, MP4, Youtube and Vimeo)',
	'description' => 'This Video Player allows you to show your MP4 or FLV videos to a broader audience as with Quicktime, Windows Media or Real Media. This extension is based and backwards-compatible with extension flvplayer, adding standards compliance, an updated player (JW Player or Flowplayer) and improved support for YouTube and Vimeo.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '1.4.0',
	'conflicts' => 'flvplayer',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => 'uploads/tx_flvplayer/',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Jose Antonio Guerra',
	'author_email' => 'jaguerra@icti.es',
	'author_company' => 'ICTI Internet Passion',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5.0-6.2.99',
		),
		'conflicts' => array(
			'flvplayer' => '0.0.0-0.9.0',
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => '',
);

?>