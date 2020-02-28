<?php

namespace WILCITY_APP\Controllers;

class GlobalSettingController
{
    public function __construct()
    {
        add_filter('wilcity/theme-options/configurations', [$this, 'addMobileSettings']);
    }
    
    public function addMobileSettings($aOptions)
    {
        $aThemeOptions = require_once WILCITY_APP_PATH . 'configs/themeoptions.php';
        $aOptions[] = $aThemeOptions;
        return $aOptions;
    }
}
