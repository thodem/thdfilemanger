<?php
namespace Td\Thdfilemanager\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Resource\Driver\LocalDriver;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use FluidTYPO3\Vhs\ViewHelpers\Media;

/**
 * Returns an array of files found in the provided path
 *
 */
class FolderViewHelper extends \FluidTYPO3\Vhs\ViewHelpers\Media\FilesViewHelper {

	

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('path', 'string', 'Path to the new folder to be shown.', TRUE);
		$this->registerArgument('upperpath', 'string', 'Path to the folder containing the folder.', '');
		$this->registerArgument('prependPath', 'boolean', 'If set to TRUE the path will be prepended to file names.', FALSE, FALSE);
		$this->registerArgument('order', 'string', 'If set to "mtime" sorts files by modification time or alphabetically otherwise.', FALSE, '');
     	}
	
	/**
	 * @return array
	 */
	public function render() {
		$fileinfoarr = array();
		$path = $this->arguments['path'];
		$upperpath = $this->arguments['upperpath'];
		if (NULL === $path) {
			$path = $this->renderChildren();
			if (NULL === $path) {
				return array();
			}
		}
		
		$prependPath    = $this->arguments['prependPath'];
		//$prependPath = 1; // always full path
		$order          = $this->arguments['order'];
		$excludePattern = $this->arguments['excludePattern'];
        //$filarr = array();
		//$files = GeneralUtility::getAllFilesAndFoldersInPath($filarr, $path, $extensionList, $prependPath, 99, $excludePattern);
        
        /*
        foreach ($files as $fpth) {
        	$fname=\TYPO3\CMS\Core\Utility\PathUtility::basename($fpth);

        	$fileinfoarr[$fname]['filesize']=filesize($fpth);
        	$fileinfoarr[$fname]['filepath']=$fpth;
        	//$fileinfoarr[$fname]['filesize']=\FluidTYPO3\Vhs\ViewHelpers\Media\SizeViewHelper::render('path:' =>$fname);
        	//$fileinfoarr[$fname]['filesize']="test";
            //$fileinfoarr[$fname]
        
        }
        */
        $files=GeneralUtility::get_dirs($path);
        foreach ($files as $fpth) {
        	$fname=\TYPO3\CMS\Core\Utility\PathUtility::basename($fpth);
        	$fileinfoarr[$fname]['filepath']=$upperpath."/".$fpth;
        	$fileinfoarr[$fname]['filarr'] = array();
        	$files2 = GeneralUtility::getFilesInDir($path."/".$fname, $extensionList, $prependPath);
        	$folders2= GeneralUtility::get_dirs($path."/".$fname);
        	//$nrin = (count($files2)+count($folders2)) > 0;
        	//$fileinfoarr[$fname]['files'] = GeneralUtility::getAllFilesAndFoldersInPath($fileinfoarr[$fname]['filarr'], ($path."/".$fname), $extensionList, $prependPath, 99, $excludePattern);
			$fileinfoarr[$fname]['files2'] = $files2;
        	$fileinfoarr[$fname]['dir'] = $folders2;
        	$fileinfoarr[$fname]['notempty'] = !(count($files2)+count($folders2)) > 0;
        	$fileinfoarr[$fname]['emptyclass'] = $fileinfoarr[$fname]['notempty']?  "aktiviert" : "notempty";
        	//$fileinfoarr[$fname]['filesize']=\FluidTYPO3\Vhs\ViewHelpers\Media\SizeViewHelper::render('path:' =>$fname);
        	//$fileinfoarr[$fname]['filesize']="test";
            //$fileinfoarr[$fname]
        
        }
		return $fileinfoarr;
	}
	/**
	 * @parameter string $aktuFolder
	 * @return string
	 */
	public function extractAktUserfolder($aktuFolder) {
	      // test
	      return $aktuFolder;
	}


}
