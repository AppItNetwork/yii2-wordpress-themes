<?php

namespace appitnetwork\wpthemes\components;

use Yii;

class WP_Theme extends \yii\base\Theme
{
    private $_themesBaseUrl;

    public function getThemesBaseUrl()
    {
        return $this->_themesBaseUrl;
    }

    public function setThemesBaseUrl($url)
    {
        $this->_themesBaseUrl = rtrim(Yii::getAlias($url), '/');
    }

    private $_themesBasePath;

    public function getThemesBasePath()
    {
        return $this->_themesBasePath;
    }

    public function setThemesBasePath($path)
    {
        $this->_themesBasePath = Yii::getAlias($path);
    }

    private $_wpThemesBaseUrl;

    public function getWpThemesBaseUrl()
    {
        return $this->_wpThemesBaseUrl;
    }

    public function setWpThemesBaseUrl($url)
    {
        $this->_wpThemesBaseUrl = rtrim(Yii::getAlias($url), '/');
    }

    private $_wpThemesBasePath;

    public function getWpThemesBasePath()
    {
        return $this->_wpThemesBasePath;
    }

    public function setWpThemesBasePath($path)
    {
        $this->_wpThemesBasePath = Yii::getAlias($path);
    }
    

    public $selectedTheme;

    public function getSelectedTheme()
    {
        return $this->selectedTheme;
    }

    public function setSelectedTheme($themeFolder)
    {
        $this->selectedTheme = $themeFolder;
        $this->_baseUrl = $this->_wpThemesBaseUrl . '/' . $themeFolder;
        $this->_basePath = $this->_wpThemesBasePath . DIRECTORY_SEPARATOR . $themeFolder;
    }

    public function init()
    {

    }

}
