<?php
namespace Td\Thdfilemanager\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use FluidTYPO3\Vhs\ViewHelpers\Media;

/**
 * Returns an array of files found in the provided path
 *
 */
class FilesViewHelper extends \FluidTYPO3\Vhs\ViewHelpers\Media\FilesViewHelper {
	
	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('path', 'string', 'Path to the folder containing the files to be listed.', TRUE);
		$this->registerArgument('extensionList', 'string', 'A comma seperated list of file extensions to pick up.', FALSE, '');
		$this->registerArgument('prependPath', 'boolean', 'If set to TRUE the path will be prepended to file names.', FALSE, FALSE);
		$this->registerArgument('order', 'string', 'If set to "mtime" sorts files by modification time or alphabetically otherwise.', FALSE, '');
		$this->registerArgument('excludePattern', 'string', 'A comma seperated list of filenames to exclude, no wildcards.', FALSE, '');
		$this->registerArgument('directdownloadext', 'string', 'A comma seperated list of file extensions to test', FALSE, '');

	
	}

	/**
	 * @return array
	 */
	public function render() {
		$path = $this->arguments['path'];

		if (NULL === $path) {
			$path = $this->renderChildren();
			if (NULL === $path) {
				return array();
			}
		}

		$extensionList  = $this->arguments['extensionList'];
		//$prependPath    = $this->arguments['prependPath'];
		$prependPath = 1; // always full path
		$order          = $this->arguments['order'];
		$excludePattern = $this->arguments['excludePattern'];
		$directdownloadext  = ',' . $this->arguments['directdownloadext'] . ',';

		$files = GeneralUtility::getFilesInDir($path, $extensionList, $prependPath, $order, $excludePattern);
        $fileinfoarr = array();
        foreach ($files as $fpth) {
        	$fname=\TYPO3\CMS\Core\Utility\PathUtility::basename($fpth);
        	$fileinfoarr[$fname]['filesize']=filesize($fpth);
        	$fileinfoarr[$fname]['filepath']=$fpth;
        	$parts = explode('.', basename($fname));
        	// file has no extension
			if (1 === count($parts)) {
				$fileinfoarr[$fname]['ext'] = '';
				}
			else {
				$fileinfoarr[$fname]['ext'] = strtolower(array_pop($parts));
			}
			$dirdwnl = false;
			$fileinfoarr[$fname]['dirdwnl'] = ($directdownloadext === ',,' || stripos($directdownloadext, ',' . pathinfo($fname, PATHINFO_EXTENSION) . ',') !== FALSE);
        	 
        }
        
        
        
        
		//return $files;
		return $fileinfoarr;
	}
	/*
	
	*
    * @param string $filepath
    *
    * return STRING
    */ 
     
	private function filesize($filepath) {
		
		$file = GeneralUtility::getFileAbsFileName($fname);
		if (FALSE === file_exists($file) || TRUE === is_dir($file)) {
			throw new Exception('Cannot determine size of "' . $file . '". File does not exist or is a directory.', 1356953963);
		}
		$size = filesize($file);

		if (FALSE === $size) {
			throw new Exception('Cannot determine size of "' . $file . '".', 1356954032);
		}
		return $size;
	
	}

}
