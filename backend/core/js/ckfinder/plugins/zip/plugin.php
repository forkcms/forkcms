<?php
/*
* CKFinder
* ========
* http://ckfinder.com
* Copyright (C) 2007-2012, CKSource - Frederico Knabben. All rights reserved.
*
* The software, this file and its contents are subject to the CKFinder
* License. Please read the license.txt file before using, installing, copying,
* modifying or distribute this file or part of its contents. The contents of
* this file is part of the Source Code of CKFinder.
*
* CKFinder extension: provides commands to add files into a zip archive, or extract contents from a zip.
*/
if (!defined('IN_CKFINDER')) exit;

/**
 * Include base XML command handler
 */
require_once CKFINDER_CONNECTOR_LIB_DIR . "/CommandHandler/XmlCommandHandlerBase.php";

class CKFinder_Connector_CommandHandler_Unzip extends CKFinder_Connector_CommandHandler_XmlCommandHandlerBase
{
  protected $filePath;
  protected $skippedFilesNode;
  protected $_config;

  /**
   * Handle request and build XML
   */
  protected function buildXml()
  {
    if (empty($_POST['CKFinderCommand']) || $_POST['CKFinderCommand'] != 'true') {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
    }

    if (!extension_loaded('zip')) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_COMMAND);
    }

    $this->checkConnector();
    $this->checkRequest();

    if ( !$this->_currentFolder->checkAcl(CKFINDER_CONNECTOR_ACL_FILE_UPLOAD)) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED);
    }

    if (!isset($_POST["fileName"])) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_NAME);
    }

    $fileName = CKFinder_Connector_Utils_FileSystem::convertToFilesystemEncoding($_POST["fileName"]);
    $resourceTypeInfo = $this->_currentFolder->getResourceTypeConfig();

    if (!$resourceTypeInfo->checkExtension($fileName)) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_EXTENSION);
    }

    if (!CKFinder_Connector_Utils_FileSystem::checkFileName($fileName) || $resourceTypeInfo->checkIsHiddenFile($fileName)) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
    }

    $filePath = CKFinder_Connector_Utils_FileSystem::combinePaths($this->_currentFolder->getServerPath(), $fileName);

    if (!file_exists($filePath) || !is_file($filePath)) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_FILE_NOT_FOUND);
    }

    if (!is_writable(dirname($filePath))) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_ACCESS_DENIED);
    }

    if ( strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) !== 'zip'){
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_EXTENSION);
    }

    $zip = new ZipArchive();
    $result = $zip->open($filePath);
    if ($result !== TRUE) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UNKNOWN);
    }
    $this->zip = $zip;
    $this->filePath = $filePath;
    $this->_config =& CKFinder_Connector_Core_Factory::getInstance("Core_Config");

    // list of unzipped nodes
    $this->unzippedNodes = new CKFinder_Connector_Utils_XmlNode("UnzippedFiles");

    // list of files which could not be unzipped
    $this->skippedFilesNode = new CKFinder_Connector_Utils_XmlNode("Errors");
    $this->errorCode = CKFINDER_CONNECTOR_ERROR_NONE;
  }

  /**
   * Check one file for security reasons
   *
   * @param object $filePathInfo
   * @param string $originalFileName
   * @return mixed bool(false) - if security checks fails. Otherwise string - ralative zip archive path with secured filename.
   */
  protected function checkOneFile($filePathInfo, $originalFileName )
  {
    $resourceTypeInfo = $this->_currentFolder->getResourceTypeConfig();

    // checked if it is a folder
    $fileStat = $this->zip->statName($originalFileName);
    if ( empty($filePathInfo['extension']) && empty($fileStat['size']) ){
      $sNewFolderName = CKFinder_Connector_Utils_FileSystem::convertToFilesystemEncoding(rtrim($fileStat['name'],'/'));
      if ($this->_config->forceAscii()) {
        $sNewFolderName = CKFinder_Connector_Utils_FileSystem::convertToAscii($sNewFolderName);
      }
      if (!CKFinder_Connector_Utils_FileSystem::checkFolderPath($sNewFolderName) || $resourceTypeInfo->checkIsHiddenFolder($sNewFolderName)) {
        $this->errorCode = CKFINDER_CONNECTOR_ERROR_INVALID_NAME;
        $this->appendErrorNode($this->skippedFilesNode, $this->errorCode, $originalFileName);
        return false;
      }

      if (!is_writeable($this->_currentFolder->getServerPath())) {
        $this->errorCode = CKFINDER_CONNECTOR_ERROR_ACCESS_DENIED;
        $this->appendErrorNode($this->skippedFilesNode, $this->errorCode, $originalFileName);
        return false;
      }

      return $originalFileName;
    }

    $fileName = CKFinder_Connector_Utils_FileSystem::convertToFilesystemEncoding($filePathInfo['basename']);
    $sFileName = CKFinder_Connector_Utils_FileSystem::secureFileName($fileName);

    // max file size
    $maxSize = $resourceTypeInfo->getMaxSize();
    if ( $maxSize && $fileStat['size'] > $maxSize )
    {
      $this->errorCode = CKFINDER_CONNECTOR_ERROR_UPLOADED_TOO_BIG;
      $this->appendErrorNode($this->skippedFilesNode, $this->errorCode, $originalFileName);
      return false;
    }
    // extension
    if ( !$resourceTypeInfo->checkExtension($sFileName) )
    {
      $this->errorCode = CKFINDER_CONNECTOR_ERROR_INVALID_EXTENSION;
      $this->appendErrorNode($this->skippedFilesNode, $this->errorCode, $originalFileName);
      return false;
    }
    // hidden file
    if ( !CKFinder_Connector_Utils_FileSystem::checkFileName($sFileName) || $resourceTypeInfo->checkIsHiddenFile($sFileName) ){
      $this->errorCode = CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST;
      $this->appendErrorNode($this->skippedFilesNode, $this->errorCode, $originalFileName);
      return false;
    }

    // unpack file to tmp dir for detecting html and valid image
    $dir = CKFinder_Connector_Utils_FileSystem::getTmpDir().'/';
    if ( file_exists($dir.$sFileName) && !CKFinder_Connector_Utils_FileSystem::unlink($dir.$sFileName) ){
      $this->errorCode = CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST;
      $this->appendErrorNode($this->skippedFilesNode, $this->errorCode, $originalFileName);
      return false;
    }
    if ( copy('zip://'.$this->filePath.'#'.$originalFileName, $dir.$sFileName) )
    {
      // html extensions
      $htmlExtensions = $this->_config->getHtmlExtensions();
      $sExtension = CKFinder_Connector_Utils_FileSystem::getExtension( $dir.$sFileName );
      if ( $htmlExtensions
        && !CKFinder_Connector_Utils_Misc::inArrayCaseInsensitive( $sExtension, $htmlExtensions )
        && CKFinder_Connector_Utils_FileSystem::detectHtml($dir.$sFileName) === true )
      {
        $this->errorCode = CKFINDER_CONNECTOR_ERROR_UPLOADED_INVALID;
        $this->appendErrorNode($this->skippedFilesNode, $this->errorCode, $originalFileName);
        return false;
      }

      // proper image
      $secureImageUploads = $this->_config->getSecureImageUploads();
      if ( $secureImageUploads
        && ( $isImageValid = CKFinder_Connector_Utils_FileSystem::isImageValid($dir.$sFileName, $sExtension) ) === false )
      {
        $this->errorCode = CKFINDER_CONNECTOR_ERROR_UPLOADED_INVALID;
        $this->appendErrorNode($this->skippedFilesNode, $this->errorCode, $originalFileName);
        return false;
      }
    }
    $sDirName = ($filePathInfo['dirname'] != '.')? $filePathInfo['dirname'].'/' : '';

    return $sDirName.$sFileName;
  }

  /**
   * Add error node to the list
   * @param obj $oErrorsNode
   * @param int $errorCode
   * @param string $name
   * @param string $type
   * @param string $path
   */
  protected function appendErrorNode($oErrorsNode, $errorCode=0, $name, $type=null, $path=null)
  {
    $oErrorNode = new CKFinder_Connector_Utils_XmlNode("Error");
    $oErrorNode->addAttribute("code", $errorCode);
    $oErrorNode->addAttribute("name", CKFinder_Connector_Utils_FileSystem::convertToConnectorEncoding($name));
    if ( $type ){
      $oErrorNode->addAttribute("type", $type);
    }
    if ( $path ){
      $oErrorNode->addAttribute("folder", $path);
    }
    $oErrorsNode->addChild($oErrorNode);
  }

  /**
   * Add unzipped node to the list
   * @param obj $oUnzippedNodes
   * @param string $name
   * @param string $action
   */
  protected function appendUnzippedNode($oUnzippedNodes, $name, $action='ok')
  {
    $oUnzippedNode = new CKFinder_Connector_Utils_XmlNode("File");
    $oUnzippedNode->addAttribute("name", CKFinder_Connector_Utils_FileSystem::convertToConnectorEncoding($name));
    $oUnzippedNode->addAttribute("action", $action );
    $oUnzippedNodes->addChild($oUnzippedNode);
  }

  /**
   * Extract one file from zip archive
   *
   * @param string $extractPath
   * @param string $extractClientPath
   * @param array  $filePathInfo
   * @param string $sFileName
   * @param string $originalFileName
   */
  protected function extractTo($extractPath, $extractClientPath, $filePathInfo, $sFileName, $originalFileName)
  {
    $sfilePathInfo = pathinfo($extractPath.$sFileName);
    $extractClientPathDir = $filePathInfo['dirname'];
    if ( $filePathInfo['dirname'] == '.' ){
      $extractClientPathDir = '';
    }
    $folderPath = CKFinder_Connector_Utils_FileSystem::combinePaths($extractClientPath,$extractClientPathDir);

    $_aclConfig = $this->_config->getAccessControlConfig();
    $aclMask = $_aclConfig->getComputedMask($this->_currentFolder->getResourceTypeName(),$folderPath);
    $canCreateFolder = (($aclMask & CKFINDER_CONNECTOR_ACL_FOLDER_CREATE ) == CKFINDER_CONNECTOR_ACL_FOLDER_CREATE );
    // create sub-directory of zip archive
    if ( empty($sfilePathInfo['extension']) )
    {
      $fileStat = $this->zip->statName($originalFileName);
      $isDir = false;
      if ( $fileStat && empty($fileStat['size']) ){
        $isDir = true;
      }
      if( !empty($sfilePathInfo['dirname']) && !empty($sfilePathInfo['basename']) && !file_exists($sfilePathInfo['dirname'].'/'.$sfilePathInfo['basename']) )
      {
        if ( !$canCreateFolder ){
          return;
        }
        if ( $isDir ) {
          CKFinder_Connector_Utils_FileSystem::createDirectoryRecursively( $sfilePathInfo['dirname'].'/'.$sfilePathInfo['basename'] );
          return;
        } else {
          CKFinder_Connector_Utils_FileSystem::createDirectoryRecursively( $sfilePathInfo['dirname']);
        }
      } else {
        return;
      }
    }

    // extract file
    if ( !file_exists($sfilePathInfo['dirname']) ){
      if ( !$canCreateFolder ){
        $this->errorCode = CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED;
        $this->appendErrorNode($this->skippedFilesNode, $this->errorCode, $originalFileName );
        return;
      }
      CKFinder_Connector_Utils_FileSystem::createDirectoryRecursively($sfilePathInfo['dirname']);
    }
    $isAuthorized = (($aclMask & CKFINDER_CONNECTOR_ACL_FILE_UPLOAD ) == CKFINDER_CONNECTOR_ACL_FILE_UPLOAD );
    if ( !$isAuthorized ){
      $this->errorCode = CKFINDER_CONNECTOR_ERROR_COPY_FAILED;
      $this->appendErrorNode($this->skippedFilesNode, $this->errorCode, $originalFileName);
      return;
    }
    if ( copy('zip://'.$this->filePath.'#'.$originalFileName, $extractPath.$sFileName) )
    {
      $this->appendUnzippedNode($this->unzippedNodes,$originalFileName);
      // chmod extracted file
      if ( is_file($extractPath.$sFileName) && ( $perms = $this->_config->getChmodFiles()) )
      {
        $oldumask = umask(0);
        chmod( $extractPath.$sFileName, $perms );
        umask( $oldumask );
      }
    }
    // file extraction failed, add to skipped
    else
    {
      $this->errorCode = CKFINDER_CONNECTOR_ERROR_COPY_FAILED;
      $this->appendErrorNode($this->skippedFilesNode, $this->errorCode, $originalFileName);
    }
  }

} // end of CKFinder_Connector_CommandHandler_Unzip class

class CKFinder_Connector_CommandHandler_UnzipHere extends CKFinder_Connector_CommandHandler_Unzip
{
  /**
   * Handle request and build XML
   */
  protected function buildXml()
  {
    parent::buildXml();

   $checkedFiles = array();
   if ( !empty($_POST['files']) && is_array($_POST['files']) ){
     foreach ( $_POST['files'] as $file){
       $checkedFiles[$file['name']] = $file;
     }
   }

   for ($i = 0; $i < $this->zip->numFiles; $i++)
    {
      $fileName = $this->zip->getNameIndex($i);
      if ( !empty($checkedFiles[$fileName]) && $checkedFiles[$fileName]['options'] == 'ok' )
      {
        // file was sucessfully unzipped before
        $this->appendUnzippedNode($this->unzippedNodes,$fileName);
        continue;
      }

      $filePathInfo = pathinfo($fileName);
      $fileType = 'File';
      $fileStat = $this->zip->statName($i);
      if ( empty($filePathInfo['extension']) && empty($fileStat['size']) ){
        $fileType = 'Folder';
        // check if we can create subfolder
        if ( !$this->_currentFolder->checkAcl( CKFINDER_CONNECTOR_ACL_FOLDER_CREATE ) ){
          $this->errorCode = CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED;
          $this->appendErrorNode($this->skippedFilesNode, $this->errorCode, $fileName, $fileType);
          continue;
        }
      }
      $extractPath = $this->_currentFolder->getServerPath();
      $extractClientPath = $this->_currentFolder->getClientPath();

      $sFileName = $this->checkOneFile( $filePathInfo, $fileName );
      // security test failed, add to skipped
      if ( false !== $sFileName )
      {
        if ( file_exists($extractPath.$sFileName) )
        {
          if ( !is_dir($extractPath.$sFileName) )
          {
            // file was checked before
            if ( !empty($checkedFiles[$fileName]['options']) )
            {
              if ( $checkedFiles[$fileName]['options'] == 'autorename')
              {
                $sFileName = CKFinder_Connector_Utils_FileSystem::autoRename($extractPath,$sFileName,$sFileName);
                $this->extractTo($extractPath,$extractClientPath,$filePathInfo,$sFileName,$fileName);
              }
              elseif ( $checkedFiles[$fileName]['options'] == 'overwrite')
              {
                if ( !$this->_currentFolder->checkAcl( CKFINDER_CONNECTOR_ACL_FILE_DELETE ) ){
                  $this->errorCode = CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED;
                  $this->appendErrorNode($this->skippedFilesNode, $this->errorCode, $fileName, $fileType);
                  continue;
                }
                if (!CKFinder_Connector_Utils_FileSystem::unlink($extractPath.$sFileName))
                {
                  $this->errorCode = CKFINDER_CONNECTOR_ERROR_ACCESS_DENIED;
                  $this->appendErrorNode($this->skippedFilesNode, $this->errorCode, $fileName, $fileType);
                }
                else
                {
                  $this->extractTo($extractPath,$extractClientPath,$filePathInfo,$sFileName,$fileName);
                }
              }
              else
              {
                // add to skipped files
                $this->appendUnzippedNode($this->unzippedNodes,$fileName,'skip');
              }
            }
            else
            {
              $this->errorCode = CKFINDER_CONNECTOR_ERROR_ALREADY_EXIST;
              $this->appendErrorNode($this->skippedFilesNode, $this->errorCode, $fileName, $fileType);
            }
          }
        }
        // file doesn't exist yet
        else
        {
          $this->extractTo($extractPath,$extractClientPath,$filePathInfo,$sFileName,$fileName);
        }
      }
    }
    $this->zip->close();

    $this->_connectorNode->addChild($this->unzippedNodes);

    if ($this->errorCode != CKFINDER_CONNECTOR_ERROR_NONE) {
      $this->_connectorNode->addChild($this->skippedFilesNode);
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_ZIP_FAILED);
    }
  }

  public function onBeforeExecuteCommand( &$command )
  {
      if ( $command == 'ExtractHere' )
      {
          $this->sendResponse();
          return false;
      }
      return true ;
  }

} // end of CKFinder_Connector_CommandHandler_UnzipHere class

class CKFinder_Connector_CommandHandler_UnzipTo extends CKFinder_Connector_CommandHandler_Unzip
{
  /**
   * Handle request and build XML
   */
  protected function buildXml()
  {
    parent::buildXml();

    $extractDir = ( !empty($_POST['extractDir']) ) ? ltrim($_POST['extractDir'],'/') : '';
    $extractDir = CKFinder_Connector_Utils_FileSystem::convertToFilesystemEncoding($extractDir);
    if ( preg_match(CKFINDER_REGEX_INVALID_PATH, $extractDir) ){
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
    }
    $extractPath = CKFinder_Connector_Utils_FileSystem::combinePaths($this->_currentFolder->getServerPath(), $extractDir.'/');
    $extractClientPath = CKFinder_Connector_Utils_FileSystem::combinePaths($this->_currentFolder->getClientPath(),$extractDir);
    // acl for upload dir
    $_aclConfig = $this->_config->getAccessControlConfig();
    $aclMask = $_aclConfig->getComputedMask($this->_currentFolder->getResourceTypeName(),$extractDir);

    if ( !(($aclMask & CKFINDER_CONNECTOR_ACL_FOLDER_CREATE ) == CKFINDER_CONNECTOR_ACL_FOLDER_CREATE ) ){
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED);
    }
    if ( empty( $_POST['force']) && file_exists($extractPath) && is_dir($extractPath) && !CKFinder_Connector_Utils_FileSystem::isEmptyDir($extractPath) )
    {
      $dirExists = new CKFinder_Connector_Utils_XmlNode("FolderExists");
      $oErrorNode = new CKFinder_Connector_Utils_XmlNode("Folder");
      $oErrorNode->addAttribute("name", $extractDir);
      $dirExists->addChild($oErrorNode);
      $this->_connectorNode->addChild($dirExists);
      return;
    }
    elseif ( !empty( $_POST['force']) && $_POST['force'] =='overwrite' )
    {
      if ( !(($aclMask &  CKFINDER_CONNECTOR_ACL_FILE_UPLOAD | CKFINDER_CONNECTOR_ACL_FILE_DELETE ) ==  CKFINDER_CONNECTOR_ACL_FILE_UPLOAD | CKFINDER_CONNECTOR_ACL_FILE_DELETE ) ){
        $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED);
      }
      if ( $extractDir && file_exists($extractPath) && is_dir($extractPath) )
      {
        if ( !(($aclMask &  CKFINDER_CONNECTOR_ACL_FOLDER_CREATE | CKFINDER_CONNECTOR_ACL_FOLDER_DELETE ) ==  CKFINDER_CONNECTOR_ACL_FOLDER_CREATE | CKFINDER_CONNECTOR_ACL_FOLDER_DELETE ) ){
          $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED);
        }
        if (!CKFinder_Connector_Utils_FileSystem::unlink($extractPath))
        {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_ACCESS_DENIED);
        }
      }
    }
    else if ( !empty( $_POST['force']) && $_POST['force'] !== 'merge' )
    {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
    }

    for ($i = 0; $i < $this->zip->numFiles; $i++)
    {
      $fileName = $this->zip->getNameIndex($i);
      $filePathInfo = pathinfo($fileName);

      $sFileName = $this->checkOneFile( $filePathInfo, $fileName );
      // security test failed, add to skipped
      if ( $sFileName )
      {
        $this->extractTo($extractPath,$extractClientPath,$filePathInfo,$sFileName,$fileName);
      }
    }
    $this->zip->close();


    $this->_connectorNode->addChild($this->unzippedNodes);

    if ($this->errorCode != CKFINDER_CONNECTOR_ERROR_NONE) {
      $this->_connectorNode->addChild($this->skippedFilesNode);
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_ZIP_FAILED);
    }
  }

  public function onBeforeExecuteCommand( &$command )
  {
    if ( $command == 'ExtractTo'){
      $this->sendResponse();
      return false;
    }
    return true ;
  }

} // end of CKFinder_Connector_CommandHandler_UnzipTo class


class CKFinder_Connector_CommandHandler_CreateZip extends CKFinder_Connector_CommandHandler_XmlCommandHandlerBase
{
  protected $_config;

  /**
   * Get private zip plugin config
   *
   * @access protected
   * @return array
   */
  protected function getConfig(){
    $config = array();

    $config['zipMaxSize'] = 'default';
    if (isset($GLOBALS['config']['ZipMaxSize']) && (string)$GLOBALS['config']['ZipMaxSize']!='default' ){
      $config['zipMaxSize'] = CKFinder_Connector_Utils_Misc::returnBytes((string)$GLOBALS['config']['ZipMaxSize']);
    }

    return $config;
  }

  /**
   * Checks given file for security
   *
   * @param  SplFileInfo $file
   * @access protected
   * @return bool
   */
  protected function checkOneFile($file)
  {
    $resourceTypeInfo = $this->_currentFolder->getResourceTypeConfig();
    $_aclConfig = $this->_config->getAccessControlConfig();
    $directory = str_replace('\\','/', $resourceTypeInfo->getDirectory());
    $fileName = CKFinder_Connector_Utils_FileSystem::convertToFilesystemEncoding($file->getFilename());

    if ($this->_config->forceAscii()) {
      $fileName = CKFinder_Connector_Utils_FileSystem::convertToAscii($fileName);
    }
    $pathName = str_replace('\\','/', pathinfo($file->getPathname(), PATHINFO_DIRNAME) );
    $pathName = CKFinder_Connector_Utils_FileSystem::convertToFilesystemEncoding($pathName);

    // acl
    $aclMask = $_aclConfig->getComputedMask($this->_currentFolder->getResourceTypeName(), str_ireplace($directory,'',$pathName));
    $isAuthorized = (($aclMask & CKFINDER_CONNECTOR_ACL_FILE_VIEW) == CKFINDER_CONNECTOR_ACL_FILE_VIEW);
    if ( !$isAuthorized ){
      return false;
    }

    // if it is a folder fileName represents the dir
    if ( $file->isDir() && ( !CKFinder_Connector_Utils_FileSystem::checkFolderPath($fileName) || $resourceTypeInfo->checkIsHiddenPath($fileName) ) ){
      return false;
    }
    // folder name
    if ( !CKFinder_Connector_Utils_FileSystem::checkFolderPath($pathName) ){
      return false;
    }

    // is hidden
    if ( $resourceTypeInfo->checkIsHiddenPath($pathName) || $resourceTypeInfo->checkIsHiddenFile($fileName) ){
      return false;
    }

    // extension
    if ( !$resourceTypeInfo->checkExtension($fileName) || !CKFinder_Connector_Utils_FileSystem::checkFileName($fileName) ){
      return false;
    }

    return true;
  }

  /**
   * Get list of all files in given directory, including sub-directories
   *
   * @param string $directory
   * @param int $zipMaxSize Maximum zip file size
   * @return array $allFiles
   */
  protected function getFilesRecursively( $directory, $zipMaxSize )
  {
    $allFiles = array();
    $_zipFilesSize = 0;
    $serverPath = str_replace('\\','/',$directory);

    foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory), RecursiveIteratorIterator::CHILD_FIRST) as $file ) {
      if ( !$this->checkOneFile($file) ){
        continue;
      }
      if ( !empty($zipMaxSize) ){
        clearstatcache();
        $_zipFilesSize += $file->getSize();
        if ( $_zipFilesSize > $zipMaxSize ) {
          $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_CREATED_FILE_TOO_BIG);
        }
      }
      $pathName = str_replace('\\','/',$file->getPathname());
      if ( $file->isDir() ){
        // skip dot folders on unix systems ( do not try to use isDot() as $file is not a  DirectoryIterator obj )
        if ( in_array($file->getFilename(),array('..','.')) ){
          continue;
        }
        if ($pathName != rtrim($serverPath,'/')){
          $allFiles[ ltrim(str_ireplace(rtrim($serverPath,'/'),'',$pathName),'/') ] = '';
        }
      } else {
        $allFiles[$pathName] = str_ireplace($serverPath,'',$pathName);
      }
    }

    return $allFiles;
  }

  /**
   * Handle request and build XML
   */
  public function buildXml()
  {
    if (!extension_loaded('zip')) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_COMMAND);
    }

    $this->checkConnector();
    $this->checkRequest();

    if ( !$this->_currentFolder->checkAcl(CKFINDER_CONNECTOR_ACL_FILE_UPLOAD)) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED);
    }

    $this->_config =& CKFinder_Connector_Core_Factory::getInstance("Core_Config");
    $currentResourceTypeConfig = $this->_currentFolder->getResourceTypeConfig();
    $_sServerDir = $this->_currentFolder->getServerPath();

    $files = array();

    $_zipFilesSize = 0;
    $config = $this->getConfig();
    $zipMaxSize = $config['zipMaxSize'];
    if ( !empty($zipMaxSize) && $zipMaxSize == 'default' ){
      $zipMaxSize = $currentResourceTypeConfig->getMaxSize();
    }

    $_isBasket = ( isset($_POST['basket']) && $_POST['basket'] == 'true' )? true : false;

    if ( !empty($_POST['files']))
    {

      $_aclConfig = $this->_config->getAccessControlConfig();
      $aclMasks = array();
      $_resourceTypeConfig = array();

      foreach ( $_POST['files'] as $arr ){
        if ( empty($arr['name']) || empty($arr['type']) || empty($arr['folder']) ) {
          continue;
        }
        // file name
        $name = CKFinder_Connector_Utils_FileSystem::convertToFilesystemEncoding($arr['name']);
        // resource type
        $type = $arr['type'];
        // client path
        $path = CKFinder_Connector_Utils_FileSystem::convertToFilesystemEncoding($arr['folder']);

        // check #1 (path)
        if (!CKFinder_Connector_Utils_FileSystem::checkFileName($name) || preg_match(CKFINDER_REGEX_INVALID_PATH, $path)) {
          continue;
        }

        // get resource type config for current file
        if (!isset($_resourceTypeConfig[$type])) {
          $_resourceTypeConfig[$type] = $this->_config->getResourceTypeConfig($type);
        }

        // check #2 (resource type)
        if (is_null($_resourceTypeConfig[$type])) {
          continue;
        }

        // check #3 (extension)
        if (!$_resourceTypeConfig[$type]->checkExtension($name, false)) {
          continue;
        }

        // check #4 (extension) - when moving to another resource type, double check extension
        if ($currentResourceTypeConfig->getName() != $type && !$currentResourceTypeConfig->checkExtension($name, false)) {
          continue;
        }

        // check #5 (hidden folders)
        // cache results
        if (empty($checkedPaths[$path])) {
          $checkedPaths[$path] = true;

          if ($_resourceTypeConfig[$type]->checkIsHiddenPath($path)) {
            continue;
          }
        }

        // check #6 (hidden file name)
        if ($currentResourceTypeConfig->checkIsHiddenFile($name)) {
          continue;
        }

        // check #7 (Access Control, need file view permission to source files)
        if (!isset($aclMasks[$type."@".$path])) {
          $aclMasks[$type."@".$path] = $_aclConfig->getComputedMask($type, $path);
        }

        $isAuthorized = (($aclMasks[$type."@".$path] & CKFINDER_CONNECTOR_ACL_FILE_VIEW) == CKFINDER_CONNECTOR_ACL_FILE_VIEW);
        if (!$isAuthorized) {
          continue;
        }

        $sourceFilePath = CKFinder_Connector_Utils_FileSystem::combinePaths($_resourceTypeConfig[$type]->getDirectory().$path,$name);
        // check #8 (invalid file name)
        if (!file_exists($sourceFilePath) || !is_file($sourceFilePath)) {
          continue;
        }

        // check #9 - max file size
        if ( !empty($zipMaxSize) ){
          clearstatcache();
          $_zipFilesSize += filesize($sourceFilePath);
          if ( $_zipFilesSize > $zipMaxSize ) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_CREATED_FILE_TOO_BIG);
          }
        }

        $zipPathPart = ( $_isBasket ) ? CKFinder_Connector_Utils_FileSystem::combinePaths($type,$path) : '';

        $files[$sourceFilePath] = $zipPathPart.pathinfo($sourceFilePath,PATHINFO_BASENAME);
      }
    }
    else
    {
      if (!is_dir($_sServerDir)) {
        $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_FOLDER_NOT_FOUND);
      }
      $files = $this->getFilesRecursively($_sServerDir,$zipMaxSize);
    }
    if ( sizeof($files)<1) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_FILE_NOT_FOUND);
    }
    // default destination dir - temp
    $dest_dir = CKFinder_Connector_Utils_FileSystem::getTmpDir();
    $resourceTypeInfo = $this->_currentFolder->getResourceTypeConfig();

    // default file name - hash
    $zip_filename = substr(md5(serialize($files)), 0, 16).$resourceTypeInfo->getHash().'.zip';

    // compress files - do not download them
    // change destination and name
    if ( isset($_POST['download']) && $_POST['download'] == 'false'){
      $dest_dir = $_sServerDir;
      if ( isset($_POST['zipName']) && !empty($_POST['zipName'])){
        $zip_filename = CKFinder_Connector_Utils_FileSystem::convertToFilesystemEncoding($_POST['zipName']);
        if (!$resourceTypeInfo->checkExtension($zip_filename)) {
          $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_EXTENSION);
        }
      }
    }
    if (!CKFinder_Connector_Utils_FileSystem::checkFileName($zip_filename) || $resourceTypeInfo->checkIsHiddenFile($zip_filename)) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_NAME);
    }
    if ($this->_config->forceAscii()) {
      $zip_filename = CKFinder_Connector_Utils_FileSystem::convertToAscii($zip_filename);
    }

    $zipFilePath = CKFinder_Connector_Utils_FileSystem::combinePaths($dest_dir, $zip_filename);

    if (!is_writable(dirname($zipFilePath))) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_ACCESS_DENIED);
    }

    // usually we would need to create zip?
    $createZip = true;

    // only if file already exists and we want download it
    // do not create new one - because hash of previously created is the same - existing archive is ok
    if ( file_exists($zipFilePath) && isset($_POST['download']) && $_POST['download'] == 'true' ){
      $createZip = false;
    }
    // if we only want to create archive
    else
    {
      if ( file_exists($zipFilePath) && ( !isset($_POST['fileExistsAction']) || !in_array($_POST['fileExistsAction'], array('autorename','overwrite')) ) ){
        $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_ALREADY_EXIST);
      }

      if ( !$this->_currentFolder->checkAcl( CKFINDER_CONNECTOR_ACL_FILE_UPLOAD )) {
        $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED);
      }
      // check how to deal with existing file
      if ( isset($_POST['fileExistsAction']) && $_POST['fileExistsAction'] == 'autorename' )
      {
        if ( !$this->_currentFolder->checkAcl(CKFINDER_CONNECTOR_ACL_FILE_UPLOAD | CKFINDER_CONNECTOR_ACL_FILE_RENAME )) {
          $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED);
        }
        $zip_filename = CKFinder_Connector_Utils_FileSystem::autoRename($dest_dir, $zip_filename, $zip_filename);
        $zipFilePath = CKFinder_Connector_Utils_FileSystem::combinePaths($dest_dir, $zip_filename);
      }
      elseif ( isset($_POST['fileExistsAction']) && $_POST['fileExistsAction'] == 'overwrite' )
      {
        if ( !$this->_currentFolder->checkAcl(CKFINDER_CONNECTOR_ACL_FILE_RENAME | CKFINDER_CONNECTOR_ACL_FILE_DELETE)) {
          $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED);
        }
        if (!CKFinder_Connector_Utils_FileSystem::unlink($zipFilePath)){
          $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_ACCESS_DENIED);
        }
      }
    }

    if ( $createZip ){
      $zip = new ZipArchive();
      $result = $zip->open( $zipFilePath, ZIPARCHIVE::CREATE);
      if ( $result !== TRUE ) {
        $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UNKNOWN);
      }
      foreach ( $files as $pathname => $filename ){
        if ( !empty($filename) ){
          if ( file_exists($pathname) && is_readable($pathname) ){
            $zip->addFile( $pathname, $filename );
          }
        } else {
          $zip->addEmptyDir( $pathname );
        }
      }
      $zip->close();
    }

    $file = new CKFinder_Connector_Utils_XmlNode("ZipFile");
    $file->addAttribute("name", $zip_filename);
    $this->_connectorNode->addChild($file);
  }

  public function onBeforeExecuteCommand( &$command )
  {
    if ( $command == 'CreateZip'){
      $this->sendResponse();
      return false;
    }
    return true ;
  }

} // end of CKFinder_Connector_CommandHandler_DownloadZip class

class CKFinder_Connector_CommandHandler_DownloadZip extends CKFinder_Connector_CommandHandler_CreateZip
{
  /**
   * Sends generated zip file to the user
   */
  protected function sendZipFile()
  {
    if (!function_exists('ob_list_handlers') || ob_list_handlers()) {
      @ob_end_clean();
    }
    header("Content-Encoding: none");

    $this->checkConnector();
    $this->checkRequest();

    // empty wystarczy
    if ( empty($_GET['FileName']) ){
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_FILE_NOT_FOUND);
    }

    $resourceTypeInfo = $this->_currentFolder->getResourceTypeConfig();
    $hash = $resourceTypeInfo->getHash();
    if ( $hash !== $_GET['hash'] || $hash !== substr($_GET['FileName'],16,16) ){
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
    }

    if (!$this->_currentFolder->checkAcl(CKFINDER_CONNECTOR_ACL_FILE_VIEW)) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED);
    }

    $fileName = CKFinder_Connector_Utils_FileSystem::convertToFilesystemEncoding(trim($_GET['FileName']));

    if (!CKFinder_Connector_Utils_FileSystem::checkFileName($fileName)) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
    }

    if ( strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) !== 'zip'){
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_EXTENSION);
    }

    $dest_dir = CKFinder_Connector_Utils_FileSystem::getTmpDir();
    $filePath = CKFinder_Connector_Utils_FileSystem::combinePaths($dest_dir,$fileName);
    if ( !file_exists($filePath) || !is_file($filePath)) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_FILE_NOT_FOUND);
    }
    if (!is_readable($filePath)) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_ACCESS_DENIED);
    }

    $zipFileName = CKFinder_Connector_Utils_FileSystem::convertToFilesystemEncoding(trim($_GET['ZipName']));
    if (!CKFinder_Connector_Utils_FileSystem::checkFileName($zipFileName)) {
      $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_REQUEST);
    }
    $fileFilename = pathinfo($zipFileName,PATHINFO_BASENAME );

    header("Content-Encoding: none");
    header("Cache-Control: cache, must-revalidate");
    header("Pragma: public");
    header("Expires: 0");
    $user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
    $encodedName = str_replace("\"", "\\\"", $fileFilename);
    if (strpos($user_agent, "MSIE") !== false) {
      $encodedName = str_replace(array("+", "%2E"), array(" ", "."), urlencode($encodedName));
    }
    header("Content-type: application/octet-stream; name=\"" . $fileFilename . "\"");
    header("Content-Disposition: attachment; filename=\"" . $encodedName. "\"");
    header("Content-Length: " . filesize($filePath));
    CKFinder_Connector_Utils_FileSystem::sendFile($filePath);
    exit;
  }

  public function onBeforeExecuteCommand( &$command )
  {
    if ( $command == 'DownloadZip'){
      $this->sendZipFile();
      return false;
    }
    return true ;
  }

} // end of CKFinder_Connector_CommandHandler_DownloadZip

if (extension_loaded('zip'))
{
  $CommandHandler_UnzipHere = new CKFinder_Connector_CommandHandler_UnzipHere();
  $CommandHandler_UnzipTo = new CKFinder_Connector_CommandHandler_UnzipTo();
  $CommandHandler_CreateZip = new CKFinder_Connector_CommandHandler_CreateZip();
  $CommandHandler_DownloadZip = new CKFinder_Connector_CommandHandler_DownloadZip();
  $config['Hooks']['BeforeExecuteCommand'][] = array($CommandHandler_UnzipHere, "onBeforeExecuteCommand");
  $config['Hooks']['BeforeExecuteCommand'][] = array($CommandHandler_UnzipTo, "onBeforeExecuteCommand");
  $config['Hooks']['BeforeExecuteCommand'][] = array($CommandHandler_CreateZip, "onBeforeExecuteCommand");
  $config['Hooks']['BeforeExecuteCommand'][] = array($CommandHandler_DownloadZip, "onBeforeExecuteCommand");
  $config['Plugins'][] = 'zip';
}
