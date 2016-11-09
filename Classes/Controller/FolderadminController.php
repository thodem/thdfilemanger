<?php
namespace Td\Thdfilemanager\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class FolderadminController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	
	/**
	 * txthdfilemanagerRepository
	 *
	 * @var \Td\Thdfilemanager\Domain\Repository\TxthdfilemanagerRepository
	 * @inject
	 */
	protected $txthdfilemanagerRepository = NULL;
	
	/**
	 * @var Array
	 */
	protected static $folders = array();
	
	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
	
	}
	
		/**
	 * action show
	 *
	 * @param \Td\Thdfilemanager\Domain\Model\FeUserfolder $FeUserfolder
	 * @return void
	 */
	public function showAction(\Td\Thdfilemanager\Domain\Model\FeUserfolder $FeUserfolder) {
	
	}
	/**
	 * action new
	 *
	 * @param \Td\Thdfilemanager\Domain\Model\FeUserfolder $newFeUserfolder
	 * @ignorevalidation $newFeUserfolder
	 * @return void
	 */
	public function newAction(\Td\Thdfilemanager\Domain\Model\FeUserfolder $newFeUserfolder = NULL) {
		$this->view->assign('newTxthdfilemanager', $newTxthdfilemanager);
	}
	
	/**
	 * action create
	 *
	 * @param \Td\Thdfilemanager\Domain\Model\FeUserfolder $newFeUserfolder
	 * @return void
	 */
	public function createAction(\Td\Thdfilemanager\Domain\Model\FeUserfolder $newFeUserfolder) {
	
	}
	 
	
}