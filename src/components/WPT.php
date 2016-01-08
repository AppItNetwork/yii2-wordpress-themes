<?php

namespace appitnetwork\wpthemes\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

use appitnetwork\wpthemes\helpers\WP;
use appitnetwork\wpthemes\helpers\WP_Query;
use appitnetwork\wpthemes\helpers\WP_Post;
use appitnetwork\wpthemes\helpers\WP_Page;
use appitnetwork\wpthemes\helpers\WP_Term;
use appitnetwork\wpthemes\helpers\WP_Comment;

class WPT extends Component
{
	private $_wordpressLocation;
	private $_assetClass = 'appitnetwork\wpthemes\assets\WP_Asset';
	private $_adminAssetClass = 'appitnetwork\wpthemes\assets\WP_AdminAsset';
	private $_themeAssetClass = 'appitnetwork\wpthemes\assets\WP_ThemeAsset';

	// public function __construct($subject, $matches) {
	// 	$this->_subject = $subject;
	// 	$this->_matches = $matches;
	// 	$this->output = $this->_map();
	// }

	public function __construct( $config = [] )
	{	
		$path = '@appitnetwork/wpthemes/wordpress';
		$this->setWordpressLocation($path);
		$config = $this->_setView($config);
		$this->_registerAsset();

        parent::__construct($config);
    }

	public function main($query_args = '') {
		$this->init();
		$this->parse_request($query_args);
		$this->send_headers();
		$this->query_posts();
		$this->handle_404();
		$this->register_globals();

		do_action_ref_array( 'wp', array( &$this ) );
	}

	public function init()
	{ 
        parent::init();
  //       $user = Yii::$app->user->identity;
		// return $user;
    }

	// public function init() {
	// 	wp_get_current_user();
	// }

	private function _setView($config)
	{
		// $selectedTheme = ArrayHelper::remove( $config, 'selectedTheme', 'twentythirteen' );
		// $selectedTheme = ArrayHelper::remove( $config, 'selectedTheme', 'twentyfourteen' );
		// $selectedTheme = ArrayHelper::remove( $config, 'selectedTheme', 'twentyfifteen' );
		$selectedTheme = ArrayHelper::remove( $config, 'selectedTheme', 'twentysixteen' );

		$themesBaseUrl = ArrayHelper::remove( $config, 'themesBaseUrl', '@web/../vendor/appitnetwork/yii2-wordpress-themes/src/wordpress/wp-content/themes' );
		$wpThemesBaseUrl = ArrayHelper::remove( $config, 'wpThemesBaseUrl', $themesBaseUrl );

		$themesBasePath = ArrayHelper::remove( $config, 'themesBasePath', '@appitnetwork/wpthemes/wordpress/wp-content/themes' );
		$wpThemesBasePath = ArrayHelper::remove( $config, 'wpThemesBasePath', $themesBasePath );

		$originalTheme = Yii::$app->view->theme;
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
                'originalTheme' => $originalTheme
		]) );

		$wpThemesLayout = '@appitnetwork/wpthemes/views/layouts/main';
		Yii::$app->layout = $wpThemesLayout;
		// pr(Yii::$app->layout);die;
		return $config;
	}

	private function _registerAsset()
	{
		Yii::$app->view->registerAssetBundle( $this->_assetClass );
		Yii::$app->view->registerAssetBundle( $this->_adminAssetClass );
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

	public function getAdminAssetUrl()
	{
		return Yii::$app->view->assetBundles[$this->_adminAssetClass]->baseUrl;
	}

	public function getBaseThemeAssetUrl()
	{
		return Yii::$app->view->assetBundles[$this->_themeAssetClass]->baseUrl;
	}

	public function getThemeAssetUrl()
	{
		$selectedTheme = Yii::$app->view->theme->selectedTheme;
		return $this->baseThemeAssetUrl . '/' . $selectedTheme;
	}

	public function getThemeBasePath()
	{
		$selectedTheme = Yii::$app->view->theme->selectedTheme;
		return Yii::$app->view->theme->wpThemesBasePath . DIRECTORY_SEPARATOR . $selectedTheme;
	}

}

function pr( $data = null ) {
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}
