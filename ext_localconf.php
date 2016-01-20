<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}
	
		// Add plugin
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43('flvplayer','pi1/class.tx_flvplayer2_pi1.php','_pi1','list_type',1);
?>
