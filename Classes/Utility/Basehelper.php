<?php
namespace Td\Thdfilemanager\Utility;


use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use \TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use \TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use \TYPO3\CMS\Core\Mail\MailMessage;
use \TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use \Td\Thdfilemanager\Domain\Model\Txthdfilemanager;
use \Td\Thdfilemanager\Domain\Model\FEgroups;
use \TYPO3\CMS\Extbase\Reflection\Exception\PropertyNotAccessibleException;
use \TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use \TYPO3\CMS\Saltedpasswords\Salt\SaltFactory;
use \TYPO3\CMS\Saltedpasswords\Utility\SaltedPasswordsUtility;


/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Thomas Demel <thdemel@anima-media.de>
 *   some code from femanager, (c) 2013 Alex Kellner <alexander.kellner@in2code.de>, in2code
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

/**
 * Misc Functions
 *
 * @package Thdfilemanager
 * @license http://www.gnu.org/licenses/gpl.html
 * 			GNU General Public License, version 3 or later
 */
class Basehelper  {
	/**
	 * configurationManager
	 *
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
	 * @inject
	 */
	protected $configurationManager;
	
	/**
	 * txthdfilemanagerRepository
	 *
	 * @var \Td\Thdfilemanager\Domain\Repository\TxthdfilemanagerRepository
	 * @inject
	 */
	protected $txthdfilemanagerRepository = NULL;
    
     /**
	 * objectManager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;
	
	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
	 * @inject
	 */
	protected $frontendUserRepository = NULL;

	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository
	 * @inject
	 */
	protected $userGroupRepository = NULL;
    
    
    /**
	 * set usergroups to group from settings
	 *
	 * @param Txthdfilemanager $userobject
	 * @param array $settings
	 * @return Txthdfilemanager $userobject
	 */
    public function setFileUsergrp($userobject, $settings) {
    	$usergroupUid = $settings['dateiusergrp'];
    	$usergroup = $this->userGroupRepository->findByUid($usergroupUid);
    	$userobject->addUsergroup($usergroup);
    	return $userobject;
    }
    
    /**
	 * test filepath userrights
	 *
	 * @param Txthdfilemanager $userobject
	 * @param string $filepath
	 * @param array $settings
	 * @param string $basepath
	 * @return boolean
	 */
    public function filePathAllowed($userobject, $filepath, $settings, $basepath  = NULL) {
    	$useright = false;
    	$fileusertest = false;
    	if ((!$basepath) || ($basepath == ""))  {
    		$basepath = $this->getBasisverz($settings);
    	}
    	$usergrps = $userobject->getUsergroup()->toArray();
    	// test if user is in fileadmin or fileuser group
    	foreach ($usergrps as $key => $akgrp) {
			$tgrpuid = $akgrp->getUid();
			if (($tgrpuid == $setting['dateiusergrp']) || ($tgrpuid == $setting['dateiadmingrp'])) {
				$fileusertest = true;
			}
		}
    	if ($fileusetest) {
    		
    	
    		}
    	return $useright;
    }
    
    /*
    * test to path upper of settings rootpath
    * returns true if not allowed!
    * 
    *
    * @param string $testpath
    * @param array $settings
    * @param string $basepath
    * @return boolean
	 */
	 public function pathUpperthenRoot($testpath, $settings, $basepath) {
	 	$isupperroot=true;
	 	if ($testpath != "") {
			$testpath = ltrim($testpath, '/');
			if (stripos($testpath, "../") !== false) { 
			    // path should point directly and must not contain "../"!
				return true;
			} else {
				
				$isupperootp = stripos($testpath, \TYPO3\CMS\Core\Utility\PathUtility::basename($basepath)); // pfad oberhalb definierten Bereich
		        
		
			}
	 	
	 	
	 	}
	 	return $isupperroot;
	 }
    /**
	 * Hash a password from $user->getPassword()
	 *
	 * @param FrontendUser $user
	 * @param string $method "md5" or "sha1"
	 * @return void
	 */
	public static function hashPassword(&$user, $method) {
		switch ($method) {
			case 'md5':
				$user->setPassword(md5($user->getPassword()));
				break;

			case 'sha1':
				$user->setPassword(sha1($user->getPassword()));
				break;

			default:
				if (ExtensionManagementUtility::isLoaded('saltedpasswords')) {
					if (SaltedPasswordsUtility::isUsageEnabled('FE')) {
						$objInstanceSaltedPw = SaltFactory::getSaltingInstance();
						$user->setPassword($objInstanceSaltedPw->getHashedPassword($user->getPassword()));
					}
				}
		}
	}
	
	/**
	 * send Mail to recipient
	 *
	 * @param Txthdfilemanager $user
	 * @param string $clearpw
	 * @param array $settings
	 * @return boolean 
	 */
	public function sendeMail($user, $clearpw, $settings) {
		$email = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
		$receiver = $this->makeEmailArray($user->getEmail(), $user->getUsername());
		print_r($settings["mailsender"]);
		$sender = $this->makeEmailArray($settings["mailsender"]["email"], $settings["mailsender"]["name"]);
		$subject = "Für Sie wurde ein neuer Account angelegt";
		$email
			->setTo($receiver)
			->setFrom($sender)
			->setSubject($subject)
			->setCharset($GLOBALS['TSFE']->metaCharset)
			->setBody($this->getMailBody($user, $clearpw, $settings), 'text/html');
		$email->send();
		return $email->isSent();
	}
	/**
	 * Create array for swiftmailer
	 * 		sender and receiver mail/name combination with fallback
	 *
	 * @param string $emailString String with separated emails (splitted by \n)
	 * @param string $name Name for every email name combination
	 * @return array $mailArray
	 */
	public static function makeEmailArray($emailString, $name = 'femanager') {
		$emails = GeneralUtility::trimExplode("\n", $emailString, 1);
		$mailArray = array();
		foreach ($emails as $email) {
			if (!GeneralUtility::validEmail($email)) {
				continue;
			}
			$mailArray[$email] = $name;
		}
		return $mailArray;
	}
    
    /**
	 * Generate Email Body
	 *
	 * @param Txthdfilemanager $user
	 * @param string $clearpw
	 * @param array $settings
	 * @return string
	 */
	 protected function getMailBody($user, $clearpw, $settings) {
	 	$mailbody ="Für Sie wurde ein neuer Dateiverwaltungszugang auf tomgeist eingerichtet<br>";
	 	$mailbody .="Die Zugangsdaten sind:<br> Benutzer: ".$user->getUsername()."<br>Passwort: ".$clearpw;
	 	$mailbody .="<br>Mit freundlichen Grüßen\n";
	 	return $mailbody;
	 }
	 
	  /*
	 * 
	 * @return string
	 */
	public static function getBasisverz($settings = NULL) {
			$basepath = "";
			$startfolder= "fileadmin/";
			if ($settings == NULL) {
				$settings = $this->getSetting();
				}
			$basepath = $startfolder.$settings['dateiverzeichnis'];
			$basepath = rtrim($basepath, '/');
			return $basepath;
	}
	
	 /*
	 * 
	 * @return array
	 */
    public static function getSetting($settingparameter = "") {
    		$setting = array();
    		$settings = $this->configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS);
    		if ($settingparameter == "") {
    			return $settings;
    		} else {
    			return $settings[$settingparameter];
    		}
    } 
	 

}