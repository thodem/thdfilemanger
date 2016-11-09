<?php
namespace Td\Thdfilemanager\Controller;

use TYPO3\CMS\Core\Resource\Driver\LocalDriver;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use FluidTYPO3\Vhs\ViewHelpers\Media;
use Td\Thdfilemanager\Utility\Basehelper;

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

class TxthdfilemanagerController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * txthdfilemanagerRepository
	 *
	 * @var \Td\Thdfilemanager\Domain\Repository\TxthdfilemanagerRepository
	 * @inject
	 */
	protected $txthdfilemanagerRepository = NULL;

	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
	 * @inject
	 */
	protected $frontendUserRepository = NULL;

	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository
	 * @inject
	 */
	protected $FrontendUserGroup = NULL;
    
    /**
	 * Helper Functions
	 *
	 * @var \Td\Thdfilemanager\Utility\Basehelper
	 * @inject
	 */
	 
	protected $basehelper;
	/**
	 * @var Array
	 */
	protected static $txfolders = array();

	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
	 */
	protected $user = NULL;

	/**
	 * @var String
	 */
	protected $aktuserpath;
    
    /**
	 * @var String
	 */
	protected $aktbasisverzeichnis;
	
	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$basehelperobj = $this->objectManager->get('\Td\Thdfilemanager\Utility\Basehelper');
	    $settings = $this->settings;
	    
	    $this->aktbasisverzeichnis = $basehelperobj->getBasisverz($settings);
		$this->returnUserAction();
		$isFileadmin = $this->user->checkFileadmin();
		$this->getActualUserFolder();
		$excludePattern = '\\.(D|h).*';
		
		$excludePattern = $settings['anzexcludePattern'];
		$extList = $settings['anzextlist'];
		$directdownload = $settings['directdownloadext'];
		$upperfolder = $this->upperfolder($this->aktuserpath, 1);
		
		$txafolderscont = array();
		//$aktfoldercont = $this->returnFolderContentAction($txafolderscont, $path, $extList, $excludePattern);
		//$fldlst = $this->returnFolderListAction($path, $extList, $excludePattern);
		$txthdfilemanagers = $this->txthdfilemanagerRepository->findAll();
		$txthdusers = $this->frontendUserRepository->findAll();
		$aktuser = $this->user;
		$this->view->assign('txthdfilemanagers', $txthdfilemanagers);
		$this->view->assignMultiple(array(
				'aktUser' => $this->user,
				'aktUserverzeichnis' => $this->aktuserpath,
				'aktDateiverzeichnis' => $aktfoldercont,
				'aktbasisverzeichnis' => $this->aktbasisverzeichnis,
				'upperfolder' => $upperfolder,
				'excludePattern' => $excludePattern,
				'extensionList' => $extList,
				'settings' => $settings
			));
		
	}

	/**
	 * action show
	 *
	 * @param \Td\Thdfilemanager\Domain\Model\Txthdfilemanager $txthdfilemanager
	 * @return void
	 */
	public function showAction(\Td\Thdfilemanager\Domain\Model\Txthdfilemanager $txthdfilemanager) {
		$this->view->assign('txthdfilemanager', $txthdfilemanager);
	}

	/**
	 * action new
	 *
	 * @param string $aktfolder
	 * @param \Td\Thdfilemanager\Domain\Model\Txthdfilemanager $newTxthdfilemanager
	 * @ignorevalidation $newTxthdfilemanager
	 * @return void
	 */
	public function newAction($aktfolder, \Td\Thdfilemanager\Domain\Model\Txthdfilemanager $newTxthdfilemanager = NULL) {
		//$this->view->assign('newTxthdfilemanager', $newTxthdfilemanager);
		$this->view->assignMultiple(array(
				'newTxthdfilemanager', $newTxthdfilemanager,
				'aktUserverzeichnis' => $aktfolder
			));
	}

	/**
	 * action create
	 *
	 * @param \Td\Thdfilemanager\Domain\Model\Txthdfilemanager $newTxthdfilemanager
	 * @return void
	 */
	public function createAction(\Td\Thdfilemanager\Domain\Model\Txthdfilemanager $newTxthdfilemanager) {
		$settings = $this->settings;
		$basehelperobj = $this->objectManager->get('\Td\Thdfilemanager\Utility\Basehelper');
		$newTxthdfilemanager =  $basehelperobj->setFileUsergrp($newTxthdfilemanager, $settings);
		$clearpw = $newTxthdfilemanager->getPassword();
		//print("clearpw: ".$clearpw);
		//print("email: ".$newTxthdfilemanager->getEmail());
		$basehelperobj->hashPassword($newTxthdfilemanager);
		$this->txthdfilemanagerRepository->add($newTxthdfilemanager);
		$this->addFlashMessage(LocalizationUtility::translate('createFrontendFileUserInfo', 'thdfilemanager').$newTxthdfilemanager->getUsername(), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		$newFoldername = $newTxthdfilemanager->getUserFolder();
		
		$nfoldern = $this->createFolder("", $newFoldername);
		//print("korr foldername: ".$nfoldern);
		$newTxthdfilemanager->setFoldername($nfoldern);
		$mailsend = $basehelperobj->sendeMail($newTxthdfilemanager, $clearpw, $settings);
		if (!$mailsend) {
			$this->addFlashMessage(LocalizationUtility::translate('mailSendEror', 'thdfilemanager'), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
		$this->redirect('list');
	}
    
    /**
	 * action newfolder
	 *
	 * @param string $aktfolder
	 * @param array $newFoldername
	 * @ignorevalidation $newFoldername
	 * @return void
	 */
	public function newfolderAction($aktfolder, $newFoldername = NULL) {
		//$this->view->assign('newFoldername', $newFoldername);
		$this->view->assignMultiple(array(
				'newFoldername' => $newFoldername,
				'aktUserverzeichnis' => $aktfolder
			));
	}
    
    
    /**
	 * action createnewfolder
	 *
	 * @param string $aktfolder
	 * @param array $newFoldername
	 * 
	 * @return void
	 */
    public function createnewfolderAction($aktfolder, $newFoldername) {
    	$this->aktuserpath = $aktfolder;
		$arguments = array();
    	$arguments['aktpath'] = $this->aktuserpath;
        $this->createFolder($aktfolder, $newFoldername['Foldername']);
    	$this->redirect('list', NULL, NULL, $arguments);
    }
    
    /**
	 * createfolder
	 *
	 * @param string $aktfolder
	 * @param array $newFoldername
	 * 
	 * @return string
	 */
    function createFolder($aktfolder, $newFoldername) {
    	$settings = $this->settings;
    	$basehelperobj = $this->objectManager->get('\Td\Thdfilemanager\Utility\Basehelper');
    	$this->aktbasisverzeichnis = $basehelperobj->getBasisverz($settings);
    	$localsavefolder = $this->aktbasisverzeichnis.$aktfolder;
    	$nfolderdriver = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\Driver\LocalDriver');
        $nfoldername = $nfolderdriver->sanitizeFileName($newFoldername);
        $folderexist = $nfolderdriver->folderExistsInFolder($nfoldername, $localsavefolder);
        if ($folderexist) {
        	$this->addFlashMessage(LocalizationUtility::translate('folderalreadyExistInfo', 'thdfilemanager')." ".$newFoldername, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
        }
        else {
			$newfolder = $nfolderdriver->createFolder($newFoldername, $localsavefolder);
			if ($newfolder == NULL) {
				$this->addFlashMessage(LocalizationUtility::translate('folderCreateFailureInfo', 'thdfilemanager')." ".$newFoldername, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
			else {
				$this->addFlashMessage(LocalizationUtility::translate('folderCreateSuccInfo', 'thdfilemanager')." ".$nfoldername, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
    	}
        return $nfoldername;
    }
    
    
	/**
	 * action edit
	 *
	 * @param \Td\Thdfilemanager\Domain\Model\Txthdfilemanager $txthdfilemanager
	 * @ignorevalidation $txthdfilemanager
	 * @return void
	 */
	public function editAction(\Td\Thdfilemanager\Domain\Model\Txthdfilemanager $txthdfilemanager) {
		$this->view->assign('txthdfilemanager', $txthdfilemanager);
	}

	/**
	 * action update
	 *
	 * @param \Td\Thdfilemanager\Domain\Model\Txthdfilemanager $txthdfilemanager
	 * @return void
	 */
	public function updateAction(\Td\Thdfilemanager\Domain\Model\Txthdfilemanager $txthdfilemanager) {
		$this->addFlashMessage('The object was updated', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		$this->txthdfilemanagerRepository->update($txthdfilemanager);
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param \Td\Thdfilemanager\Domain\Model\Txthdfilemanager $txthdfilemanager
	 * @return void
	 */
	public function deleteAction(\Td\Thdfilemanager\Domain\Model\Txthdfilemanager $txthdfilemanager) {
		$this->addFlashMessage(LocalizationUtility::translate('deleteFrontendFileUserInfo', 'thdfilemanager').$txthdfilemanager->getUsername(), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		$this->txthdfilemanagerRepository->remove($txthdfilemanager);
		$this->redirect('list');
	}
     /**
	 * action newfiles
	 *
	 * @param string $aktfolder
	 * @param array $newfiles
	 * @ignorevalidation $newFiles
	 
	 * @return void
	 */
	public function newfilesAction($aktfolder, $newFiles = array()) {
		$this->view->assignMultiple(array(
				'newFiles', $newFiles,
				'aktUserverzeichnis' => $aktfolder
			));
			
	}
	
	/**
	 * action createfile
	 * 
	 * @param string $aktfolder
	 * @param array $newFiles
	 * @return void
	 */
	public function createFileAction($aktfolder, $newFiles) {
		$this->aktuserpath = $aktfolder;
		$arguments = array();
		$arguments['aktpath'] = $this->aktuserpath;
		$settings = $this->settings; 
		$this->returnUserAction();
		$user = $GLOBALS['TSFE']->fe_user->user;
		$feUserId = $user['uid'];
        $storageRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\StorageRepository');
        $storage           = $storageRepository->findByUid(1); # Fileadmin = 1
        $localsavefolder = $this->settings['dateiverzeichnis'].$aktfolder;
        $saveFolder        = $storage->getFolder($localsavefolder);
        foreach ($newFiles['file'] as $fnr => $filedat) {
            if ($filedat['name'] !="") {
				if (!GeneralUtility::verifyFilenameAgainstDenyPattern($filedat['name'])) {
					$this->addFlashMessage(LocalizationUtility::translate('uploadWrongExtInfo', 'thdfilemanager')." ".$filedat['name'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				}
				else {
					$fileObject = $storage->addFile($filedat['tmp_name'], $saveFolder, $filedat['name']);
					if ($fileObject) {
						$this->addFlashMessage(LocalizationUtility::translate('uploadAccessInfo', 'thdfilemanager')." ".$filedat['name'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
						}
					else {
						$this->addFlashMessage(LocalizationUtility::translate('uploadFailureInfo', 'thdfilemanager')." ".$filedat['name'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
					}	

				}
			}
		}
		$this->redirect('list', NULL, NULL, $arguments);
	}
	
	/**
	 * Moves a file from the local filesystem to this storage.
	 * @param array $localFilePath The file on the server's hard disk to add.
	 ** @param \TYPO3\CMS\Core\Resource\Folder $targetFolder The target path, without the fileName
	 * @return \TYPO3\CMS\Core\Resource\FileInterface
	 
	 */
	public function fileupload($localFile, \TYPO3\CMS\Core\Resource\Folder $saveFolder) {
		$fileObject = $storage->addFile($localFile['tmp_name'], $saveFolder, $localFile['name']);
	    return $fileObject;
	}
	
	/**
	 * action filedelete
	 * @param string $aktfolder
	 * @param string $aktfile
	 * @return void
	 */
	public function filedeleteAction($aktfolder, $aktfile) {
		$basehelperobj = $this->objectManager->get('\Td\Thdfilemanager\Utility\Basehelper');
		$settings = $this->settings;
		$this->aktbasisverzeichnis = $basehelperobj->getBasisverz($settings);
		$this->aktuserpath = $aktfolder;
		$arguments = array();
		$arguments['aktpath'] = $this->aktuserpath;
		$filepath = GeneralUtility::getFileAbsFileName($aktfile, 0);
		$aktsuc = unlink(realpath($aktfile));
		if ($aktsuc) {
			$this->addFlashMessage(LocalizationUtility::translate('fileDeleteSuccInfo', 'thdfilemanager')." ".$aktfile, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		} else {
			$this->addFlashMessage(LocalizationUtility::translate('fileDeleteFailureInfo', 'thdfilemanager')." ".$aktfile, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}
		$this->redirect('list', NULL, NULL, $arguments);
	}
    
    /**
     * action download
     * @param string $aktfolder
	 * @param string $fileName
     * @return void
     */  
    public function downloadAction($aktfolder, $fileName) {
        $basehelperobj = $this->objectManager->get('\Td\Thdfilemanager\Utility\Basehelper');
        $settings = $this->settings;
        $this->aktbasisverzeichnis = $basehelperobj->getBasisverz($settings);
		//$this->aktbasisverzeichnis = $basehelperobj->getBasisverz($settings);
        $file = $this->aktbasisverzeichnis.$aktfolder."/".$fileName;        
        //print($file);
        if(is_file($file)) {

            $fileLen    = filesize($file);          
            $ext        = strtolower(substr(strrchr($fileName, '.'), 1));

            switch($ext) {
                case 'txt':
                    $cType = 'text/plain'; 
                break;              
                case 'pdf':
                    $cType = 'application/pdf'; 
                break;
                case 'exe':
                    $cType = 'application/octet-stream';
                break;
                case 'zip':
                    $cType = 'application/zip';
                break;
                case 'doc':
                    $cType = 'application/msword';
                break;
                case 'xls':
                    $cType = 'application/vnd.ms-excel';
                break;
                case 'ppt':
                    $cType = 'application/vnd.ms-powerpoint';
                break;
                case 'gif':
                    $cType = 'image/gif';
                break;
                case 'png':
                    $cType = 'image/png';
                break;
                case 'jpeg':
                case 'jpg':
                    $cType = 'image/jpg';
                break;
                case 'mp3':
                    $cType = 'audio/mpeg';
                break;
                case 'wav':
                    $cType = 'audio/x-wav';
                break;
                case 'mpeg':
                case 'mpg':
                case 'mpe':
                    $cType = 'video/mpeg';
                break;
                case 'mov':
                    $cType = 'video/quicktime';
                break;
                case 'avi':
                    $cType = 'video/x-msvideo';
                break;

                //forbidden filetypes
                case 'inc':
                case 'conf':
                case 'sql':                 
                case 'cgi':
                case 'htaccess':
                case 'php':
                case 'php3':
                case 'php4':                        
                case 'php5':
                exit;

                default:
                    $cType = 'application/force-download';
                break;
            }
            
            
			set_time_limit(0);
			ignore_user_abort(false);
			ini_set('output_buffering', 0);
            ini_set('zlib.output_compression', 0);
            
            
            $size=filesize($file);
			
			$begin=0;
  			$end=$size;
			
			if(isset($_SERVER['HTTP_RANGE']))
			  { if(preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches))
				{ $begin=intval($matches[0]);
				  if(!empty($matches[1]))
					$end=intval($matches[1]);
				}
			  }
 
			  if($begin>0||$end<$size)
				header('HTTP/1.0 206 Partial Content');
			  else
				header('HTTP/1.0 200 OK');  
			$headers = array(
                'Pragma'                    => 'public', 
                'Expires'                   => 0, 
                'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'             => 'public',
                'Content-Description'       => 'File Transfer',
                'Content-Type'              => $cType,
                'Content-Disposition'       => 'attachment; filename="'. $fileName .'"',
                'Content-Transfer-Encoding' => 'binary', 
                'Content-Length'            => ($end-$begin),
                'Content-Range'            => ('bytes '.$begin-$end/$size)         
            );
			
            foreach($headers as $header => $data) {
                $this->response->setHeader($header, $data); 
			}
						set_time_limit(0);
			ignore_user_abort(false);
			ini_set('output_buffering', 0);
			ini_set('zlib.output_compression', 0);
			$fm=@fopen($file,'rb');
			$this->response->sendHeaders();
           	$cur=$begin;
  			fseek($fm,$begin,0);
            while(!feof($fm)&&$cur<$end&&(connection_status()==0)) { 
            	print fread($fm,min(1024*16,$end-$cur));
    			$cur+=1024*16;
    			ob_flush();
    			flush();
  				}
			
        }
        else { 
        	header ("HTTP/1.0 404 Not Found");
    		return;
  		} 
        exit;   
    }
    
    
	/**
	 * action diropen
	 *
	 * @param string $aktfolder
	 * @return void
	 */
	public function diropenAction($aktfolder) {
		//print_r($aktfolder);
		//print("<br>aktDateiverzeichnis");
		//print_r($aktDateiverzeichnis);
		if($aktfolder !="" ) {
			$aktfolder = "/".ltrim($aktfolder, '/');
			}
		$settings = $this->settings;
		$this->returnUserAction();
		$basehelperobj = $this->objectManager->get('\Td\Thdfilemanager\Utility\Basehelper');
		$this->aktuserpath = $aktfolder;
		$this->aktbasisverzeichnis = $basehelperobj->getBasisverz($settings);
		$test = $basehelperobj ->filePathAllowed($this->user, $filepath, $settings, $this->aktbasisverzeichnis);
		$arguments = array();
		$arguments['aktpath'] = $this->aktuserpath;
		//$this->redirect('list');
		
		$this->redirect('list', NULL, NULL, $arguments);
	}

	/**
	 * action dirdelete
	 *
	 * @param string $aktfolder
	 * @param string $delfolder
	 * @return void
	 */
	public function dirdeleteAction($aktfolder, $delfolder) {
		$basehelperobj = $this->objectManager->get('\Td\Thdfilemanager\Utility\Basehelper');
		$settings = $this->settings;
		$this->aktbasisverzeichnis = $basehelperobj->getBasisverz($settings);
		$this->aktuserpath = $aktfolder;
		$filepath = $this->aktbasisverzeichnis.$delfolder;
		//print_r(realpath($filepath));
		$aktsuc = GeneralUtility::rmdir(realpath($filepath));
		$arguments = array();
		$arguments['aktpath'] = $this->aktuserpath;
		if ($aktsuc) {
			$this->addFlashMessage(LocalizationUtility::translate('folderDeleteSuccInfo', 'thdfilemanager')." ".$delfolder, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::INFO);
		} else {
			$this->addFlashMessage(LocalizationUtility::translate('folderDeleteFailureInfo', 'thdfilemanager')." ".$delfolder, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}
		$this->redirect('list', NULL, NULL, $arguments);
	}
    
	/**
	 * action returnUser
	 *
	 * @return void
	 */
	public function returnUserAction() {
		$user = $GLOBALS['TSFE']->fe_user->user;
		if ($user) {
			$this->user = $this->frontendUserRepository->findByUid($user['uid']);
		} else {
			$this->user = null;
		}
	}


	/**
	 * action returnFolderContent
	 *
	 * @param array $filarr
	 * @param string $path
	 * @param $extList
	 * @param $excludePattern
	 * @return array
	 */
	public function returnFolderContentAction($filarr, $path, $extList = '', $excludePattern = '') {
		//$filarr = array();
		$aktfolders = array();
		$aktfolders = GeneralUtility::getAllFilesAndFoldersInPath($filarr, $path, $extList, 1, 99, $excludePattern);
		//$files = GeneralUtility::getFilesInDir($path, $extensionList, $prependPath, $order, $excludePattern);
		//print 'aktfolders \n';
		//print_r($aktfolders);
		//print_r($filarr);
		return $aktfolders;
	}

	/**
	 * action returnFolderList
	 *
	 * @param string $path
	 * @param $extList
	 * @param $excludePattern
	 * @return array
	 */
	public function returnFolderListAction($path, $extList = '', $excludePattern = '') {
		$files = GeneralUtility::getFilesInDir($path, $extList, true, '', $excludePattern);
		//$files = GeneralUtility::getFilesInDir($path, $extensionList, $prependPath, $order, $excludePattern);
		//print_r($files);
		return $files;
	}

	/**
	 * getActualFolder
	 * @param boolean $isAdmin
	 */
	private function getActualUserFolder() {
		$userpath = $this->user->getUserFolder(); 
		if ($this->request->hasArgument('aktpath')) {
			$this->aktuserpath = $this->request->getArgument('aktpath');
		}
		if ($this->aktuserpath == "") { //keinPfad übergeben
			$this->aktuserpath = "/".$userpath; // mit User-Pfad starten
			if($this->user->checkFileadmin()) { //Admin startet mit Basispfad
				$this->aktuserpath="";
			}
		}
	}
	
	/**
	 * upperfolder return info about upperfolder
	 * 
	 * @param string $path
	 * @param boolean $revert = false 
	 * @return array
	 *
	 */
	public function upperfolder($path, $revert) {
		$pathinfo = array();
		$pathinfo['allowed']= false;
	    $basepath = $this->aktbasisverzeichnis;
	    $showrootnusrtoo = $this->settings['showrootnusrtoo'];
	    $pathinfo['basepath']=$basepath;
		$user = $this->user;
		$tempuserpath=$user->getUserFolder();
		$pathinfo['newpath']=$tempuserpath; //defaul: userpath
		$isadmin=$user->checkFileadmin();
		$pathinfo['oldpath']=$path;
		If (($basepath != "") && ($user)) { //path basepath and user is defined!
		    $isupperootp = stripos($path, \TYPO3\CMS\Core\Utility\PathUtility::basename($basepath)); // pfad oberhalb definierten Bereich
		    $pathinfo['isupperootp']=$isupperootp;
			if ($revert && !$isupperootp) {
				$pathinfo['callpath2a']=$path;
				$path = \TYPO3\CMS\Core\Utility\PathUtility::dirname($path);
				if (($path=="/") || ($path==".")) {
					$path="";
				}
				$pathinfo['callpath2']=$path;
			}
			if (!$isupperootp) {  //$basepath at beginning of path !!!
				if ($path == "") {  // jump to basepath?
					$pathinfo['root']=1;
					if ($pathinfo['oldpath'] != "")	{
						if (!(!$isadmin && !$showrootnusrtoo)) {
							$pathinfo['allowed']= true;
							}
						}
				}
				else {
					$temppartpath=preg_split("/\//",ltrim($path, '/'));
					//$pathinfo['temppartpath-split']=$temppartpath;
					if ((count($temppartpath) > 0) && !$isadmin){
						
						$pathinfo['tempuserpath']= $tempuserpath;
						$pathinfo['temppartpath']= $temppartpath;
						$temppartcount = $tempuserpath == $temppartpath[0];
						$pathinfo['temppartcount']= $temppartcount;
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
        if ($pathinfo['allowed']) {
            $pathinfo['newpath']=$path;
            }
		return $pathinfo;
	}  
		 /*
	
	 * 
	 * @return array
	 */
    public static function getSetting($settingparameter = "") {
    		$setting = array();
    		$settings = $this->settings;
    		if ($settingparameter == "") {
    			return $settings;
    		} else {
    			return $settings[$settingparameter];
    		}
    }   
}