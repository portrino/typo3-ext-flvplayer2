<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}

	// Plugin options
	$TCA['tt_content']['types']['list']['subtypes_excludelist']['flvplayer_pi1']='layout,select_key,pages,recursive';
	
	// Add flexform field to plugin options
	$TCA['tt_content']['types']['list']['subtypes_addlist']['flvplayer_pi1']='pi_flexform';
	
	// Add flexform DataStructure
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('flvplayer_pi1', 'FILE:EXT:' . $_EXTKEY . '/flexform_ds_pi1.xml');
	
	// Add plugin
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(Array('LLL:EXT:flvplayer2/locallang_db.php:tt_content.list_type_pi1', 'flvplayer_pi1'),'list_type');
	
	// Wizard icon
	if (TYPO3_MODE=='BE') {
		$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_flvplayer2_pi1_wizicon'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'pi1/class.tx_flvplayer2_pi1_wizicon.php';
	}
?>