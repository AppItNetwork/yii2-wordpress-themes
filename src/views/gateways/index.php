<?php

if (!defined( 'DS' ))
	define( 'DS', DIRECTORY_SEPARATOR );

define( 'ABSPATH', Yii::$app->wpthemes->getWordpressLocation() . DS );
define( 'WPINC', 'wp-includes' );
define( 'TEMPLATEPATH', Yii::$app->wpthemes->getThemeBasePath() );
// this should be a child theme folder else the active theme folder if it is not a child theme
define( 'STYLESHEETPATH', Yii::$app->wpthemes->getThemeBasePath() );

define( 'WP_DEFAULT_THEME', Yii::$app->view->theme->selectedTheme );
define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
define( 'WP_SITEURL', Yii::$app->request->hostInfo );
define( 'WP_CONTENT_URL', WP_SITEURL . Yii::$app->wpthemes->baseThemeAssetUrl );
define( 'WP_DEBUG', false );

$GLOBALS['wp_version'] = 4.4;

	require_once( 'includes/functions.php' );
	require_once( 'includes/rest-api.php' );
	require_once( 'includes/query.php' );
	require_once( 'includes/load.php' );
	require_once( 'includes/kses.php' );
	require_once( 'includes/I10n.php' );
	require_once( 'includes/formatting.php' );
	require_once( 'includes/option.php' );
	require_once( 'includes/template.php' );
	require_once( 'includes/general-template.php' );
	require_once( 'includes/link-template.php' );
	require_once( 'includes/post-template.php' );
	require_once( 'includes/nav-menu-template.php' );
	require_once( 'includes/nav-menu.php' );
	require_once( 'includes/plugin.php' );
	require_once( 'includes/theme.php' );
	require_once( 'includes/widgets.php' );
	require_once( 'includes/embed.php' );
	require_once( 'includes/default-filters.php' );
	require_once( 'includes/class.wp-dependencies.php' );
	require_once( 'includes/class.wp-scripts.php' );
	require_once( 'includes/class.wp-styles.php' );
	require_once( 'includes/functions.wp-scripts.php' );
	require_once( 'includes/functions.wp-styles.php' );
	require_once( 'includes/script-loader.php' );
	require_once( 'includes/admin-bar.php' );
	require_once( 'includes/pluggable.php' );
	require_once( 'includes/user.php' );
	require_once( 'includes/capabilities.php' );
	require_once( 'includes/taxonomy.php' );
	require_once( 'includes/post.php' );
	require_once( 'includes/post-thumbnail-template.php' );
	require_once( 'includes/meta.php' );
	require_once( 'includes/media.php' );
	require_once( 'includes/shortcodes.php' );
	require_once( 'includes/comment-template.php' );
	require_once( 'includes/comment.php' );
	require_once( 'includes/class-wp-error.php' );

	include_once( TEMPLATEPATH . DS . 'functions.php' );

	ob_start();
    ob_implicit_flush(false);
		include( Yii::getAlias( '@app/views/' . $this->context->id . '/' . $this->context->action->id . '.php' ) );
	$this->content = ob_get_clean();

	Yii::$app->wpthemes->getPages();
	if ( $this->context->action->id !== 'error' ) {
		include_once( TEMPLATEPATH . DS . 'page.php' );
	} else {
		include_once( TEMPLATEPATH . DS . '404.php' );
	}

	// global $wp_filter;
	// pr($wp_filter);die;
