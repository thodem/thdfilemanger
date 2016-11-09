<?php
namespace Td\Thdfilemanager\Tests\Unit\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

/**
 * Test case for class Td\Thdfilemanager\Controller\TxthdfilemanagerController.
 *
 */
class TxthdfilemanagerControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \Td\Thdfilemanager\Controller\TxthdfilemanagerController
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = $this->getMock('Td\\Thdfilemanager\\Controller\\TxthdfilemanagerController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllTxthdfilemanagersFromRepositoryAndAssignsThemToView() {

		$allTxthdfilemanagers = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$txthdfilemanagerRepository = $this->getMock('Td\\Thdfilemanager\\Domain\\Repository\\TxthdfilemanagerRepository', array('findAll'), array(), '', FALSE);
		$txthdfilemanagerRepository->expects($this->once())->method('findAll')->will($this->returnValue($allTxthdfilemanagers));
		$this->inject($this->subject, 'txthdfilemanagerRepository', $txthdfilemanagerRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('txthdfilemanagers', $allTxthdfilemanagers);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function showActionAssignsTheGivenTxthdfilemanagerToView() {
		$txthdfilemanager = new \Td\Thdfilemanager\Domain\Model\Txthdfilemanager();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('txthdfilemanager', $txthdfilemanager);

		$this->subject->showAction($txthdfilemanager);
	}

	/**
	 * @test
	 */
	public function newActionAssignsTheGivenTxthdfilemanagerToView() {
		$txthdfilemanager = new \Td\Thdfilemanager\Domain\Model\Txthdfilemanager();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('newTxthdfilemanager', $txthdfilemanager);
		$this->inject($this->subject, 'view', $view);

		$this->subject->newAction($txthdfilemanager);
	}

	/**
	 * @test
	 */
	public function createActionAddsTheGivenTxthdfilemanagerToTxthdfilemanagerRepository() {
		$txthdfilemanager = new \Td\Thdfilemanager\Domain\Model\Txthdfilemanager();

		$txthdfilemanagerRepository = $this->getMock('Td\\Thdfilemanager\\Domain\\Repository\\TxthdfilemanagerRepository', array('add'), array(), '', FALSE);
		$txthdfilemanagerRepository->expects($this->once())->method('add')->with($txthdfilemanager);
		$this->inject($this->subject, 'txthdfilemanagerRepository', $txthdfilemanagerRepository);

		$this->subject->createAction($txthdfilemanager);
	}

	/**
	 * @test
	 */
	public function editActionAssignsTheGivenTxthdfilemanagerToView() {
		$txthdfilemanager = new \Td\Thdfilemanager\Domain\Model\Txthdfilemanager();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('txthdfilemanager', $txthdfilemanager);

		$this->subject->editAction($txthdfilemanager);
	}

	/**
	 * @test
	 */
	public function updateActionUpdatesTheGivenTxthdfilemanagerInTxthdfilemanagerRepository() {
		$txthdfilemanager = new \Td\Thdfilemanager\Domain\Model\Txthdfilemanager();

		$txthdfilemanagerRepository = $this->getMock('Td\\Thdfilemanager\\Domain\\Repository\\TxthdfilemanagerRepository', array('update'), array(), '', FALSE);
		$txthdfilemanagerRepository->expects($this->once())->method('update')->with($txthdfilemanager);
		$this->inject($this->subject, 'txthdfilemanagerRepository', $txthdfilemanagerRepository);

		$this->subject->updateAction($txthdfilemanager);
	}

	/**
	 * @test
	 */
	public function deleteActionRemovesTheGivenTxthdfilemanagerFromTxthdfilemanagerRepository() {
		$txthdfilemanager = new \Td\Thdfilemanager\Domain\Model\Txthdfilemanager();

		$txthdfilemanagerRepository = $this->getMock('Td\\Thdfilemanager\\Domain\\Repository\\TxthdfilemanagerRepository', array('remove'), array(), '', FALSE);
		$txthdfilemanagerRepository->expects($this->once())->method('remove')->with($txthdfilemanager);
		$this->inject($this->subject, 'txthdfilemanagerRepository', $txthdfilemanagerRepository);

		$this->subject->deleteAction($txthdfilemanager);
	}
}
