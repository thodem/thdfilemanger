<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Useradmin',
	'Frontend-Dateiverwaltung'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Folderadmin',
	'Dateiverzeichnis'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Dateimanager');

if (!isset($GLOBALS['TCA']['fe_users']['ctrl']['type'])) {
	if (file_exists($GLOBALS['TCA']['fe_users']['ctrl']['dynamicConfigFile'])) {
		require_once($GLOBALS['TCA']['fe_users']['ctrl']['dynamicConfigFile']);
	}
	// no type field defined, so we define it here. This will only happen the first time the extension is installed!!
	$GLOBALS['TCA']['fe_users']['ctrl']['type'] = 'tx_extbase_type';
	$tempColumns = array();
	$tempColumns[$GLOBALS['TCA']['fe_users']['ctrl']['type']] = array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:thdfilemanager/Resources/Private/Language/locallang_db.xlf:tx_thdfilemanager.tx_extbase_type',
		'config' => array(
			'type' => 'select',
			'items' => array(),
			'size' => 1,
			'maxitems' => 1,
		)
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tempColumns, 1);
}

if (!isset($GLOBALS['TCA']['fe_groups']['ctrl']['type'])) {
	if (file_exists($GLOBALS['TCA']['fe_groups']['ctrl']['dynamicConfigFile'])) {
		require_once($GLOBALS['TCA']['fe_groups']['ctrl']['dynamicConfigFile']);
	}
	// no type field defined, so we define it here. This will only happen the first time the extension is installed!!
	$GLOBALS['TCA']['fe_groups']['ctrl']['type'] = 'tx_extbase_type';
	$tempColumns = array();
	$tempColumns[$GLOBALS['TCA']['fe_groups']['ctrl']['type']] = array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:thdfilemanager/Resources/Private/Language/locallang_db.xlf:tx_thdfilemanager.tx_extbase_type',
		'config' => array(
			'type' => 'select',
			'items' => array(),
			'size' => 1,
			'maxitems' => 1,
		)
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_groups', $tempColumns, 1);
}

$tmp_thdfilemanager_columns = array(

	'thdfilemangaeradmin' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:thdfilemanager/Resources/Private/Language/locallang_db.xlf:tx_thdfilemanager_domain_model_fegroups.thdfilemangaeradmin',
		'config' => array(
			'type' => 'check',
			'default' => 0
		)
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_groups',$tmp_thdfilemanager_columns);

$GLOBALS['TCA']['fe_groups']['types']['Tx_Thdfilemanager_FEgroups']['showitem'] = $TCA['fe_groups']['types']['0']['showitem'];
$GLOBALS['TCA']['fe_groups']['types']['Tx_Thdfilemanager_FEgroups']['showitem'] .= ',--div--;LLL:EXT:thdfilemanager/Resources/Private/Language/locallang_db.xlf:tx_thdfilemanager_domain_model_fegroups,';
$GLOBALS['TCA']['fe_groups']['types']['Tx_Thdfilemanager_FEgroups']['showitem'] .= 'thdfilemangaeradmin';

$GLOBALS['TCA']['fe_groups']['columns'][$TCA['fe_groups']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:thdfilemanager/Resources/Private/Language/locallang_db.xlf:fe_groups.tx_extbase_type.Tx_Thdfilemanager_FEgroups','Tx_Thdfilemanager_FEgroups');

$tmp_thdfilemanager_columns = array(

	'foldername' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:thdfilemanager/Resources/Private/Language/locallang_db.xlf:tx_thdfilemanager_domain_model_txthdfilemanager.foldername',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users',$tmp_thdfilemanager_columns);

$GLOBALS['TCA']['fe_users']['types']['Tx_Thdfilemanager_Txthdfilemanager']['showitem'] = $TCA['fe_users']['types']['0']['showitem'];
$GLOBALS['TCA']['fe_users']['types']['Tx_Thdfilemanager_Txthdfilemanager']['showitem'] .= ',--div--;LLL:EXT:thdfilemanager/Resources/Private/Language/locallang_db.xlf:tx_thdfilemanager_domain_model_txthdfilemanager,';
$GLOBALS['TCA']['fe_users']['types']['Tx_Thdfilemanager_Txthdfilemanager']['showitem'] .= 'foldername';

$GLOBALS['TCA']['fe_users']['columns'][$TCA['fe_users']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:thdfilemanager/Resources/Private/Language/locallang_db.xlf:fe_users.tx_extbase_type.Tx_Thdfilemanager_Txthdfilemanager','Tx_Thdfilemanager_Txthdfilemanager');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', $GLOBALS['TCA']['fe_users']['ctrl']['type'],'','after:' . $TCA['fe_users']['ctrl']['label']);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_groups', $GLOBALS['TCA']['fe_groups']['ctrl']['type'],'','after:' . $TCA['fe_groups']['ctrl']['label']);
