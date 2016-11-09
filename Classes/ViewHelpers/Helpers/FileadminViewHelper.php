<?php
namespace Td\Thdfilemanager\ViewHelpers\Helpers;

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
 * Returns info abot td-FE-Fileuser Txthdfilemanager
 *
 */
class FileadminViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * fegroupsrepository
	 *
	 * @var \Td\Thdfilemanager\Domain\Repository\FEgroupsRepository
	 * @inject
	 */
	protected $fegroupsrepository = NULL;
	
	/**
	 * txthdfilemanagerRepository
	 *
	 * @var \Td\Thdfilemanager\Domain\Repository\TxthdfilemanagerRepository
	 * @inject
	 */
	protected $txthdfilemanagerRepository = NULL;
    /**
	 * Initialize arguments.
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('txfeuser', '\Td\Thdfilemanager\Domain\Model\Txthdfilemanager', 'Frontend-FileUser', TRUE);
		//$this->registerArgument('txfeuserid', 'integer', 'Frontend-FileUser-ID', TRUE) 
	}
	
	/**
	 * @return boolean
	 */
	public function render() {
	    $requser = $this->arguments['txfeuser'];
		$userinfo = array();
		//$userinfo['user']=$requser;
		$userinfo['foldername']=$requser->getFoldername();
		//$userinfo['usergroup']=$requser->getUsergroup();
		$userinfo['frtfldmntest']=$requser->checkFileadmin();
		return $userinfo['frtfldmntest'];
	}

}
