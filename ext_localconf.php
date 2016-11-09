<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Td.' . $_EXTKEY,
	'Useradmin',
	array(
		'Txthdfilemanager' => 'list, show, new, create, edit, update, delete, filedelete, newfiles, diropen, dirdelete, createfile, newfolder, createnewfolder, download',
		
	),
	// non-cacheable actions
	array(
		'FileUserAdmin' => 'create, delete, edit, filedelete, newfiles, diropen, dirdelete, createfile, newfolder, createnewfolder, download',
		
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Td.' . $_EXTKEY,
	'Folderadmin',
	array(
		'Folderadmin' => 'create, delete, edit',
		
	),
	// non-cacheable actions
	array(
		'Folderadmin' => 'create, delete, edit',
		
	)
);
