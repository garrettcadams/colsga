<?php

namespace WILCITY_APP\Controllers;

use WilokeListingTools\Framework\Helpers\FileSystem;

trait Caching
{
    protected function buildCachingFile($fileName)
    {
        return $fileName.'.json';
    }
    
    protected function getCaching($fileName)
    {
        $fileName = $this->buildCachingFile($fileName);
        if (FileSystem::isFileExists($fileName, 'wilcity-mobile-app')) {
            $content = FileSystem::fileGetContents($fileName, 'wilcity-mobile-app');
            if (!empty($content)) {
                return json_decode($content, true);
            }
        }
        
        return '';
    }
    
    protected function writeCaching($content, $fileName)
    {
        $fileName = $this->buildCachingFile($fileName);
        $content = is_array($content) ? json_encode($content) : $content;
        FileSystem::filePutContents($fileName, $content, 'wilcity-mobile-app');
    }
    
    protected function deleteCaching($fileName)
    {
        FileSystem::deleteFile($fileName, 'wilcity-mobile-app');
    }
}
