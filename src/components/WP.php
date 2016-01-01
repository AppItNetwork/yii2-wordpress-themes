<?php

namespace appitnetwork\wpthemes\components;

use Yii;
use yii\base\Component;
use yii\web\Application;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;

use appitnetwork\wpthemes\helpers\WP_Page;

// class WP extends Application
class WP extends Component
{

	private $_wordpressLocation;
	private $_assetClass = 'appitnetwork\wpthemes\assets\WP_Asset';
	private $_themeAssetClass = 'appitnetwork\wpthemes\assets\WP_ThemeAsset';

	public function __construct( $config = [] )
	{	

		$path = '@appitnetwork/wpthemes/wordpress';
		$this->setWordpressLocation($path);
		$this->_setView();
		$this->_registerAsset();

        parent::__construct($config);
    }

	public function init()
	{ 
        parent::init();
        // pr(Yii::$app->view);die;
    }


	private function _setView()
	{
		// $selectedTheme = ArrayHelper::getValue( $config, 'selectedTheme', 'twentythirteen' );
		$selectedTheme = ArrayHelper::getValue( $config, 'selectedTheme', 'twentyfourteen' );
		// $selectedTheme = ArrayHelper::getValue( $config, 'selectedTheme', 'twentyfifteen' );
		// $selectedTheme = ArrayHelper::getValue( $config, 'selectedTheme', 'twentysixteen' );

		$themesBaseUrl = ArrayHelper::getValue( $config, 'themesBaseUrl', '@web/../vendor/appitnetwork/yii2-wordpress-themes/src/wordpress/wp-content/themes' );
		$wpThemesBaseUrl = ArrayHelper::getValue( $config, 'wpThemesBaseUrl', $themesBaseUrl );

		$themesBasePath = ArrayHelper::getValue( $config, 'themesBasePath', '@appitnetwork/wpthemes/wordpress/wp-content/themes' );
		$wpThemesBasePath = ArrayHelper::getValue( $config, 'wpThemesBasePath', $themesBasePath );

		Yii::$app->set('view', Yii::createObject([
			'class' => 'appitnetwork\wpthemes\components\WP_View',
			    'theme' => [
	                'class' => 'appitnetwork\wpthemes\components\WP_Theme',
	                // 'pathMap' => [
	                //     '@app/views' => '@themesBasePath/shopperacks',
	                // ],
	                'baseUrl' => $themesBaseUrl . '/' . $selectedTheme,
	                'basePath' => $themesBasePath . '/' . $selectedTheme,
	                'selectedTheme' => $selectedTheme,
	                'themesBaseUrl' => $themesBaseUrl,
	                'themesBasePath' => $themesBasePath,
	                'wpThemesBaseUrl' => $wpThemesBaseUrl,
	                'wpThemesBasePath' => $wpThemesBasePath,
	            ],
		]) );

		$wpThemesLayout = '@appitnetwork/wpthemes/views/layouts/main';
		Yii::$app->layout = $wpThemesLayout;
		// pr(Yii::$app->layout);die;
	}

	private function _registerAsset()
	{
		Yii::$app->view->registerAssetBundle( $this->_assetClass );
		Yii::$app->view->registerAssetBundle( $this->_themeAssetClass );
	}

    public function getWordpressLocation()
    {
        return $this->_wordpressLocation;
    }

	public function setWordpressLocation($path)
	{
        $this->_wordpressLocation = Yii::getAlias($path);
	}

	public function getAssetUrl()
	{
		return Yii::$app->view->assetBundles[$this->_assetClass]->baseUrl;
	}

	public function getBaseThemeAssetUrl()
	{
		return Yii::$app->view->assetBundles[$this->_themeAssetClass]->baseUrl;
	}

	public function getThemeAssetUrl()
	{
		$selectedTheme = Yii::$app->view->theme->selectedTheme;
		return $this->getBaseThemeAssetUrl() . '/' . $selectedTheme;
	}

	public function getAllPages()
	{
		$controllerMethods = get_class_methods(Yii::$app->controller);
		$pages = [];
		foreach ( $controllerMethods as $method ) {
			if (StringHelper::startsWith($method, 'action') && $method != 'actions') {
				$page = StringHelper::byteSubstr($method, 6);
				$pages[] = new WP_Page( $page );
				// pr($pages);die;
			}
		}

		return $pages;
	}

}
