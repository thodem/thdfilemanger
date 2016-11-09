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
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use FluidTYPO3\Vhs\ViewHelpers\Media;
use Td\Thdfilemanager\Utility\Basehelper;

/**
 * Returns an array of files found in the provided path
 *
 */
class FolderLinkViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('path', 'string', 'Path to the new folder to be shown.', TRUE);
		$this->registerArgument('basepath', 'string', 'basepath to the frontend-file-root', TRUE);
		$this->registerArgument('txfeuser', '\Td\Thdfilemanager\Domain\Model\Txthdfilemanager', 'Frontend-FileUser', TRUE);
		$this->registerArgument('revert', 'boolean', 'should it go backwards', FALSE, FALSE);
		}
	
	/**
	 * @return array
	 */
	public function render() {
		$pathinfo = array();
		$pathinfo['allowed']= false;
		$path = $this->arguments['path'];
		$basepath = $this->arguments['basepath'];
		$user = $this->arguments['txfeuser'];
		//$pathinfo['path']=$path;
		//$pathinfo['basepath']=$basepath;
		$isadmin=$user->checkFileadmin();
		//$pathinfo['basepathtest'] = \TYPO3\CMS\Core\Utility\PathUtility::basename($basepath);
		If (($basepath != "") && ($user)) { //path basepath and user is defined!
		    $isupperpath = stripos($path, \TYPO3\CMS\Core\Utility\PathUtility::basename($basepath)); // pfad oberhalb definierten Bereich
		    //$pathinfo['oldpath']=$path;
		    $pathinfo['isupperpath']=$isupperpath;
			if (($this->arguments['revert'])&& !$isupperpath) {
				$path = \TYPO3\CMS\Core\Utility\PathUtility::dirname($path);
				if (($path=="/") || ($path==".")) {
					$path="";
				}
				//$pathinfo['newpathx']=$path;
				
			}
			if (!$isupperpath) {  //$basepath at beginning of path !!!
				if ($path == "") {  // jump to basepath?
					$pathinfo['root']=1;
					$pathinfo['allowed']= true;
				}
				else {
					$temppartpath=preg_split("/\//",$path);
					//$pathinfo['temppartpath-split']=$temppartpath;
					if ((count($temppartpath) > 0) && !$isadmin){
						$tempuserpath=$user->getUserFolder();
						//$pathinfo['tempuserpath']= $tempuserpath;
						$temppartcount = $tempuserpath == $temppartpath[0];
						//$pathinfo['temppartcount']= $temppartcount;
						if ($tempuserpath == $temppartpath[0]) {
							$pathinfo['allowed']= true;
							}
						else {
							$path=$tempuserpath;
							}
						}
					else {
						$pathinfo['allowed']= true;
						
					}	
				}
			}
        }
        if($pathinfo['allowed']) {
		return $path;}
		
	}
}
