<?php

use appitnetwork\wpthemes\helpers\WP;
use appitnetwork\wpthemes\helpers\WP_Query;
use appitnetwork\wpthemes\helpers\WP_Rewrite;
use appitnetwork\wpthemes\helpers\WP_Widget_Factory;
use appitnetwork\wpthemes\helpers\WP_Http;

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

define( 'AUTOSAVE_INTERVAL', 60 );
define( 'OBJECT', 'OBJECT' );
define( 'object', 'OBJECT' ); // Back compat.

define( 'AUTOSAVE_INTERVAL', 60 );
define( 'EMPTY_TRASH_DAYS', 30 );
define( 'WP_POST_REVISIONS', true );
define( 'WP_CRON_LOCK_TIMEOUT', 60 );  // In seconds

define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' ); // full path, no trailing slash
define( 'WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins' ); // full url, no trailing slash
define( 'PLUGINDIR', 'wp-content/plugins' ); // Relative to ABSPATH. For back compat.
define( 'WPMU_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins' ); // full path, no trailing slash
define( 'WPMU_PLUGIN_URL', WP_CONTENT_URL . '/mu-plugins' ); // full url, no trailing slash
define( 'MUPLUGINDIR', 'wp-content/mu-plugins' ); // Relative to ABSPATH. For back compat.$GLOBALS['wp_version'] = '4.4';

$GLOBALS['wp_db_version'] = 35700;
$GLOBALS['tinymce_version'] = '4208-20151113';
$GLOBALS['required_php_version'] = '5.4.16';
$GLOBALS['required_mysql_version'] = '5.0';
$GLOBALS['table_prefix'] = Yii::$app->db->tablePrefix;

	require( 'includes/I10n.php' );
	require( 'includes/option.php' );

	require( 'includes/load.php' );

	// require( 'includes/compat.php' );
	require( 'includes/functions.php' );
	// require( 'includes/class-wp.php' );
	require( 'includes/class-wp-error.php' );
	require( 'includes/plugin.php' );
	// require( 'includes/pomo/mo.php' );

require_wp_db();

// Set the database table prefix and the format specifiers for database table columns.
$GLOBALS['table_prefix'] = $table_prefix;
wp_set_wpdb_vars();

	require( 'includes/default-filters.php' );

// require( ABSPATH . WPINC . '/class-wp-walker.php' );
// require( ABSPATH . WPINC . '/class-wp-ajax-response.php' );
	require( 'includes/formatting.php' );
	require( 'includes/capabilities.php' );
// require( ABSPATH . WPINC . '/class-wp-roles.php' );
// require( ABSPATH . WPINC . '/class-wp-role.php' );
// require( ABSPATH . WPINC . '/class-wp-user.php' );
	require( 'includes/query.php' );
// require( ABSPATH . WPINC . '/date.php' );
	require( 'includes/theme.php' );
// require( ABSPATH . WPINC . '/class-wp-theme.php' );
	require( 'includes/template.php' );
	require( 'includes/user.php' );
// require( ABSPATH . WPINC . '/class-wp-user-query.php' );
// require( ABSPATH . WPINC . '/session.php' );
	require( 'includes/meta.php' );
// require( ABSPATH . WPINC . '/class-wp-meta-query.php' );
	require( 'includes/general-template.php' );
	require( 'includes/link-template.php' );
	require( 'includes/author-template.php' );
	require( 'includes/post.php' );
// require( ABSPATH . WPINC . '/class-walker-page.php' );
// require( ABSPATH . WPINC . '/class-walker-page-dropdown.php' );
// require( ABSPATH . WPINC . '/class-wp-post.php' );
	require( 'includes/post-template.php' );
	require( 'includes/revision.php' );
	require( 'includes/post-formats.php' );
	require( 'includes/post-thumbnail-template.php' );
// require( ABSPATH . WPINC . '/category.php' );
// require( ABSPATH . WPINC . '/class-walker-category.php' );
// require( ABSPATH . WPINC . '/class-walker-category-dropdown.php' );
// require( ABSPATH . WPINC . '/category-template.php' );
	require( 'includes/comment.php' );
// require( ABSPATH . WPINC . '/class-wp-comment.php' );
// require( ABSPATH . WPINC . '/class-wp-comment-query.php' );
// require( ABSPATH . WPINC . '/class-walker-comment.php' );
	require( 'includes/comment-template.php' );
	require( 'includes/rewrite.php' );
// require( ABSPATH . WPINC . '/class-wp-rewrite.php' );
	require( 'includes/feed.php' );
// require( ABSPATH . WPINC . '/bookmark.php' );
// require( ABSPATH . WPINC . '/bookmark-template.php' );
	require( 'includes/kses.php' );
	require( 'includes/cron.php' );
// require( ABSPATH . WPINC . '/deprecated.php' );
	require( 'includes/script-loader.php' );
	require( 'includes/taxonomy.php' );
// require( ABSPATH . WPINC . '/class-wp-term.php' );
// require( ABSPATH . WPINC . '/class-wp-tax-query.php' );
// require( ABSPATH . WPINC . '/update.php' );
// require( ABSPATH . WPINC . '/canonical.php' );
	require( 'includes/shortcodes.php' );
	require( 'includes/embed.php' );
	require( 'includes/class-wp-embed.php' );
// require( ABSPATH . WPINC . '/class-wp-oembed-controller.php' );
	require( 'includes/media.php' );
	require( 'includes/http.php' );
// require( ABSPATH . WPINC . '/class-wp-http-streams.php' );
// require( ABSPATH . WPINC . '/class-wp-http-cookie.php' );
// require( ABSPATH . WPINC . '/class-wp-http-encoding.php' );
// require( ABSPATH . WPINC . '/class-wp-http-response.php' );
	require( 'includes/widgets.php' );
	require( 'includes/class-wp-widget.php' );
// require( ABSPATH . WPINC . '/class-wp-widget-factory.php' );
	require( 'includes/nav-menu.php' );
	require( 'includes/nav-menu-template.php' );
	require( 'includes/admin-bar.php' );
	require( 'includes/rest-api.php' );
// require( ABSPATH . WPINC . '/rest-api/class-wp-rest-server.php' );
// require( ABSPATH . WPINC . '/rest-api/class-wp-rest-response.php' );
// require( ABSPATH . WPINC . '/rest-api/class-wp-rest-request.php' );

$GLOBALS['wp_plugin_paths'] = array();

// Load must-use plugins.
foreach ( wp_get_mu_plugins() as $mu_plugin ) {
	include_once( $mu_plugin );
}
unset( $mu_plugin );

// Load network activated plugins.
if ( is_multisite() ) {
	foreach ( wp_get_active_network_plugins() as $network_plugin ) {
		wp_register_plugin_realpath( $network_plugin );
		include_once( $network_plugin );
	}
	unset( $network_plugin );
}

do_action( 'muplugins_loaded' );

	require( 'includes/vars.php' );

create_initial_taxonomies();
create_initial_post_types();

// Register the default theme directory root
register_theme_directory( get_theme_root() );

// Load active plugins.
foreach ( wp_get_active_and_valid_plugins() as $plugin ) {
	wp_register_plugin_realpath( $plugin );
	include_once( $plugin );
}
unset( $plugin );

// Load pluggable functions.
	require( 'includes/pluggable.php' );
// require( ABSPATH . WPINC . '/pluggable-deprecated.php' );

// Set internal encoding.
wp_set_internal_encoding();

// Run wp_cache_postload() if object cache is enabled and the function exists.
if ( WP_CACHE && function_exists( 'wp_cache_postload' ) )
	wp_cache_postload();

do_action( 'plugins_loaded' );

// Define constants which affect functionality if not already defined.
// wp_functionality_constants();

// Add magic quotes and set up $_REQUEST ( $_GET + $_POST )
wp_magic_quotes();

do_action( 'sanitize_comment_cookies' );

$GLOBALS['wp_the_query'] = new WP_Query();
$GLOBALS['wp_query'] = $GLOBALS['wp_the_query'];
$GLOBALS['wp_rewrite'] = new WP_Rewrite();
$GLOBALS['wp'] = new WP();
$GLOBALS['wp_widget_factory'] = new WP_Widget_Factory();
// $GLOBALS['wp_roles'] = new WP_Roles();

do_action( 'setup_theme' );

// Define the template related constants.
// wp_templating_constants(  );

// Load the default text localization domain.
// load_default_textdomain();

// $locale = get_locale();
// $locale_file = WP_LANG_DIR . "/$locale.php";
// if ( ( 0 === validate_file( $locale ) ) && is_readable( $locale_file ) )
// 	require( $locale_file );
// unset( $locale_file );

// Pull in locale data after loading text domain.
// require_once( ABSPATH . WPINC . '/locale.php' );

// $GLOBALS['wp_locale'] = new WP_Locale();

// Load the functions for the active theme, for both parent and child theme if applicable.
	if ( TEMPLATEPATH !== STYLESHEETPATH && file_exists( STYLESHEETPATH . DS . 'functions.php' ) )
		include( STYLESHEETPATH . DS . 'functions.php' );
	if ( file_exists( TEMPLATEPATH . DS . 'functions.php' ) )
		include( TEMPLATEPATH . DS . 'functions.php' );

do_action( 'after_setup_theme' );

// Set up current user.
$GLOBALS['wp']->init();
do_action( 'init' );
do_action( 'wp_loaded' );

	ob_start();
    ob_implicit_flush(false);
		$path = Yii::getAlias( '@app/views/' . $this->context->id . '/' . $this->context->action->id . '.php' );
		if (!is_file($path)) {
			$path = Yii::getAlias( '@app/views/' . $this->context->id . '/error.php' );
			$this->context->action->id = 'error';
		}
		include( $path );
	$this->content = ob_get_clean();

	wp();

	require( 'includes/functions.wp-scripts.php' );
	require( 'includes/functions.wp-styles.php' );

	require( 'includes/class.wp-dependencies.php' );
	require( 'includes/class.wp-scripts.php' );
	require( 'includes/class.wp-styles.php' );
	require( 'includes/class-wp-customize-manager.php' );

	// pr($this->context->action->id);die;
	// $this->wp_query->getPages();
	if ( $this->context->action->id != 'error' ) {
		include( TEMPLATEPATH . DS . 'page.php' );
	} else {
		include( TEMPLATEPATH . DS . '404.php' );
	}

	// global $wp_filter;
	// pr($wp_filter);die;
