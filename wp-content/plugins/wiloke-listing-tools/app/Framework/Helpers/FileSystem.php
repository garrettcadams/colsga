<?php

namespace WilokeListingTools\Framework\Helpers;

use WilokeListingTools\Frontend\User;

class FileSystem
{
    private static function buildFileDir($fileName, $subFolder = '')
    {
        $aUploadDir = wp_upload_dir();
        if (!empty($subFolder)) {
            $fileDir = $aUploadDir['basedir'].'/'.WILCITY_WHITE_LABEL.'/'.$subFolder.'/'.$fileName;
        } else {
            $fileDir = $aUploadDir['basedir'].'/'.WILCITY_WHITE_LABEL.'/'.$fileName;
        }
        
        return $fileDir;
    }
    
    public static function isWilcityFolderExisted($subFolder = '')
    {
        $aUploadDir = wp_upload_dir();
        $folder     = empty($subFolder) ? $aUploadDir['basedir'].'/'.WILCITY_WHITE_LABEL :
            $aUploadDir['basedir'].'/'.WILCITY_WHITE_LABEL.'/'.$subFolder;
        
        return is_dir($folder);
    }
    
    public static function createWilcityFolder()
    {
        if (self::isWilcityFolderExisted()) {
            return true;
        }
        $aUploadDir = wp_upload_dir();
        if (wp_mkdir_p($aUploadDir['basedir'].'/'.WILCITY_WHITE_LABEL)) {
            return true;
        }
        
        return false;
    }
    
    public static function getWilcityFolderDir()
    {
        self::createWilcityFolder();
        $aUploadDir = wp_upload_dir();
        
        return $aUploadDir['basedir'].'/'.WILCITY_WHITE_LABEL.'/';
    }
    
    public static function createSubFolder($subFolder)
    {
        self::createWilcityFolder();
        if (self::isWilcityFolderExisted($subFolder)) {
            return true;
        }
        
        $aUploadDir = wp_upload_dir();
        if (wp_mkdir_p($aUploadDir['basedir'].'/'.WILCITY_WHITE_LABEL.'/'.$subFolder)) {
            return true;
        }
        
        return false;
    }
    
    public static function createUserFolder($userID)
    {
        self::createWilcityFolder();
        $userFolder = User::getField('user_login', $userID);
        
        return self::createSubFolder($userFolder);
    }
    
    public static function getUserFolderUrl($userID)
    {
        self::createUserFolder($userID);
        
        $userFolder = User::getField('user_login', $userID);
        $aUploadDir = wp_upload_dir();
        
        return $aUploadDir['baseurl'].'/'.WILCITY_WHITE_LABEL.'/'.$userFolder.'/';
    }
    
    public static function getUserFolderDir($userID)
    {
        self::createUserFolder($userID);
        
        $userFolder = User::getField('user_login', $userID);
        $aUploadDir = wp_upload_dir();
        
        return $aUploadDir['basedir'].'/'.WILCITY_WHITE_LABEL.'/'.$userFolder.'/';
    }
    
    public static function getWilcityFolderUrl()
    {
        self::createWilcityFolder();
        $aUploadDir = wp_upload_dir();
        
        return $aUploadDir['baseurl'].'/'.WILCITY_WHITE_LABEL.'/';
    }
    
    public static function getFileURI($fileName = '', $subFolder = '')
    {
        $aUploadDir = wp_upload_dir();
        
        if (!empty($subFolder)) {
            return $aUploadDir['baseurl'].'/'.WILCITY_WHITE_LABEL.'/'.$subFolder.'/'.$fileName;
        } else {
            return $aUploadDir['baseurl'].'/'.WILCITY_WHITE_LABEL.'/'.$fileName;
        }
    }
    
    public static function getFileDir($fileName = '', $subFolder = '')
    {
        return self::buildFileDir($fileName, $subFolder);
    }
    
    public static function deleteFile($fileName, $subFolder = '')
    {
        $fileDir = self::buildFileDir($fileName, $subFolder);
        if (file_exists($fileDir)) {
            wp_delete_file($fileDir);
        }
    }
    
    public static function createFile($fileName = '', $subFolder = '')
    {
        if (!self::createWilcityFolder()) {
            return false;
        }
        
        if (!empty($subFolder)) {
            if (!self::createSubFolder($subFolder)) {
                return false;
            }
        }
        
        $fileDir = self::buildFileDir($fileName, $subFolder);
        
        if (!function_exists('WP_Filesystem')) {
            require_once(ABSPATH.'wp-admin/includes/file.php');
        }
        WP_Filesystem();
        global $wp_filesystem;
        
        $wp_filesystem->put_contents(
            $fileDir,
            '',
            FS_CHMOD_FILE // predefined mode settings for WP files
        );
        
        return true;
    }
    
    public static function isFileExists($fileName, $subFolder = '')
    {
        $fileDir = self::buildFileDir($fileName, $subFolder);
        return file_exists($fileDir);
    }
    
    public static function filePutContents($fileName, $text, $subFolder = '')
    {
        $fileDir = self::buildFileDir($fileName, $subFolder);
        if (!self::isFileExists($fileName, $subFolder)) {
            if (!self::createFile($fileName, $subFolder)) {
                return false;
            }
        }
        
        if (!function_exists('WP_Filesystem')) {
            require_once(ABSPATH.'wp-admin/includes/file.php');
        }
        WP_Filesystem();
        global $wp_filesystem;
        return $wp_filesystem->put_contents($fileDir, $text, FS_CHMOD_FILE);
    }
    
    private static function fileUpdateContent($fileName, $text, $subFolder = '')
    {
        $fileDir = self::buildFileDir($fileName, $subFolder);
        
        if (!self::isFileExists($fileName)) {
            if (!self::createFile($fileName)) {
                return self::createFile($fileName);
            }
        }
        
        if (!function_exists('WP_Filesystem')) {
            require_once(ABSPATH.'wp-admin/includes/file.php');
        }
        WP_Filesystem();
        global $wp_filesystem;
        
        $originalText = $wp_filesystem->get_contents($fileDir);
        $text         = $text.'--'.$originalText;
        
        return $wp_filesystem->put_contents($fileDir, $text, FS_CHMOD_FILE);
    }
    
    public static function logPayment($fileName, $text)
    {
        $status = GetWilokeSubmission::getField('toggle_debug');
        if ($status == 'enable') {
            self::fileUpdateContent($fileName, $text);
        }
    }
    
    public static function fileGetContents($fileName, $isCreatedIfNotExists = true, $subFolder = '')
    {
        $fileDir = self::buildFileDir($fileName, $subFolder);
        
        if (!file_exists($fileDir)) {
            if ($isCreatedIfNotExists) {
                self::createFile($fileName);
                
                return false;
            }
        }
        
        if (!function_exists('WP_Filesystem')) {
            require_once(ABSPATH.'wp-admin/includes/file.php');
        }
        WP_Filesystem();
        global $wp_filesystem;
        
        return $wp_filesystem->get_contents($fileDir);
    }
}
