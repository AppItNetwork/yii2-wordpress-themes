<?php

namespace appitnetwork\wpthemes\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

use appitnetwork\wpthemes\models\WP_Query;
use appitnetwork\wpthemes\helpers\WP_Post;
use appitnetwork\wpthemes\helpers\WP_Page;
use appitnetwork\wpthemes\helpers\WP_Term;
use appitnetwork\wpthemes\helpers\WP_Comment;

class WP extends Component
{
	private $_wordpressLocation;
	private $_assetClass = 'appitnetwork\wpthemes\assets\WP_Asset';
	private $_adminAssetClass = 'appitnetwork\wpthemes\assets\WP_AdminAsset';
	private $_themeAssetClass = 'appitnetwork\wpthemes\assets\WP_ThemeAsset';

	public function __construct( $config = [] )
	{	
		$path = '@appitnetwork/wpthemes/wordpress';
		$this->setWordpressLocation($path);
		$config = $this->_setView($config);
		$this->_registerAsset();

        parent::__construct($config);
    }

	public function init()
	{ 
        parent::init();
    }

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
                'wp_query' => new WP_Query,
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

    // public function getPages()
	// {
	// 	$pages = $this->getAllPages();
	// 	$page = Yii::$app->controller->action->id;
	// 	// if ($page != 'index') {
	// 		foreach ($pages as $key => $value) {
	// 			if ($value->guid == Url::to(['site/'.strtolower($page)], true)) {
	// 				// pr($value->guid .' == '. Url::to(['site/'.strtolower($page)], true));die;
	// 				$value->post_content = Yii::$app->view->content;
	// 				$this->posts = [$value];
	// 				$this->post_count = 1;
	// 				// $this->current_post = 0;
	// 			}
	// 		}
	// 	// }

	// }

	// public function getAllPages()
	// {
	// 	$controllerMethods = get_class_methods(Yii::$app->controller);
	// 	$pages = [];
	// 	foreach ( $controllerMethods as $method ) {
	// 		if (StringHelper::startsWith($method, 'action') && $method != 'actions') {
	// 			$page = StringHelper::byteSubstr($method, 6);
	// 			$pages[] = new WP_Page( $page );
	// 		}
	// 	}
	
	// 	return $pages;
	// }

	// public $current_post = -1;
	// public $post_count = 0;
	// public $posts;
	// public $post;
	// public $in_the_loop = false;
	// public $comment_count = 0;
	// public $current_comment = -1;
	// public $found_posts = 0;

	// public function have_posts()
	// {
	// 	// pr( $this->current_post + 1 .' < '. $this->post_count );
	// 	if ( $this->current_post + 1 < $this->post_count ) {
	// 		return true;
	// 	} elseif ( $this->current_post + 1 == $this->post_count && $this->post_count > 0 ) {
	// 		do_action_ref_array( 'loop_end', array( &$this ) );
	// 		// Do some cleaning up after the loop
	// 		$this->rewind_posts();
	// 	}

	// 	$this->in_the_loop = false;
	// 	return false;

	// }

	// public function rewind_posts() {
	// 	$this->current_post = -1;
	// 	if ( $this->post_count > 0 ) {
	// 		$this->post = $this->posts[0];
	// 	}
	// }

	// public function the_post() {
	// 	$this->in_the_loop = true;

	// 	if ( $this->current_post == -1 ) // loop has just started
	// 		do_action_ref_array( 'loop_start', array( &$this ) );

	// 	$this->post = $this->next_post();
	// 	$this->setup_postdata( $this->post );
	// }

	// public function next_post() {

	// 	$this->current_post++;

	// 	$this->post = $this->posts[$this->current_post];
	// 	return $this->post;
	// }

	// public function setup_postdata( $post ) {
	// 	// global $id, $authordata, $currentday, $currentmonth, $page, $pages, $multipage, $more, $numpages;
	// 	// pr($post);die;
	// 	// if ( ! ( $post instanceof WP_Post ) ) {
	// 	// 	$post = get_post( $post );
	// 	// }

	// 	// if ( ! $post ) {
	// 	// 	return;
	// 	// }

	// 	// $id = (int) $post->ID;

	// 	// $authordata = get_userdata($post->post_author);

	// 	// $currentday = mysql2date('d.m.y', $post->post_date, false);
	// 	// $currentmonth = mysql2date('m', $post->post_date, false);
	// 	// $numpages = 1;
	// 	// $multipage = 0;
	// 	// $page = $this->get( 'page' );
	// 	// if ( ! $page )
	// 	// 	$page = 1;

	// 	// /*
	// 	//  * Force full post content when viewing the permalink for the $post,
	// 	//  * or when on an RSS feed. Otherwise respect the 'more' tag.
	// 	//  */
	// 	// if ( $post->ID === get_queried_object_id() && ( $this->is_page() || $this->is_single() ) ) {
	// 	// if ( $this->is_page() || $this->is_single() ) {
	// 		$more = 1;
	// 	// } elseif ( $this->is_feed() ) {
	// 	// 	$more = 1;
	// 	// } else {
	// 	// 	$more = 0;
	// 	// }

	// 	$content = $post->post_content;
	// 	if ( false !== strpos( $content, '<!--nextpage-->' ) ) {
	// 		$content = str_replace( "\n<!--nextpage-->\n", '<!--nextpage-->', $content );
	// 		$content = str_replace( "\n<!--nextpage-->", '<!--nextpage-->', $content );
	// 		$content = str_replace( "<!--nextpage-->\n", '<!--nextpage-->', $content );

	// 		// Ignore nextpage at the beginning of the content.
	// 		if ( 0 === strpos( $content, '<!--nextpage-->' ) )
	// 			$content = substr( $content, 15 );

	// 		$pages = explode('<!--nextpage-->', $content);
	// 	} else {
	// 		$pages = array( $post->post_content );
	// 	}

	// 	$pages = apply_filters( 'content_pagination', $this->pages, $post );

	// 	$numpages = count( $pages );

	// 	if ( $numpages > 1 ) {
	// 		if ( $page > 1 ) {
	// 			$more = 1;
	// 		}
	// 		$multipage = 1;
	// 	} else {
	//  		$multipage = 0;
	//  	}

	// 	do_action_ref_array( 'the_post', array( &$post, &$this ) );

	// 	return true;
	// }

	// public function reset_postdata() {
	// 	if ( ! empty( $this->post ) ) {
	// 		$GLOBALS['post'] = $this->post;
	// 		$this->setup_postdata( $this->post );
	// 	}
	// }

}

function pr( $data = null ) {
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}
