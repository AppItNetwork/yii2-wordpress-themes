<?php
 use appitnetwork\wpthemes\helpers\wpdb;

function is_admin() {
	// if ( isset( $GLOBALS['current_screen'] ) )
	// 	return $GLOBALS['current_screen']->in_admin();
	// elseif ( defined( 'WP_ADMIN' ) )
	// 	return WP_ADMIN;

	return false;
}

function get_current_blog_id() {
	global $blog_id;
	return absint($blog_id);
}

function is_multisite() {
	// if ( defined( 'MULTISITE' ) )
	// 	return MULTISITE;

	// if ( defined( 'SUBDOMAIN_INSTALL' ) || defined( 'VHOST' ) || defined( 'SUNRISE' ) )
	// 	return true;

	return false;
}

function wp_installing( $is_installing = null ) {
	return false;	
}

function wp_using_ext_object_cache( $using = null ) {
	global $_wp_using_ext_object_cache;
	$current_using = $_wp_using_ext_object_cache;
	if ( null !== $using )
		$_wp_using_ext_object_cache = $using;
	return $current_using;
}

function wp_get_active_and_valid_plugins() {
	$plugins = array();
	$active_plugins = (array) get_option( 'active_plugins', array() );

	// Check for hacks file if the option is enabled
	if ( get_option( 'hack_file' ) && file_exists( ABSPATH . 'my-hacks.php' ) ) {
		_deprecated_file( 'my-hacks.php', '1.5' );
		array_unshift( $plugins, ABSPATH . 'my-hacks.php' );
	}

	if ( empty( $active_plugins ) || wp_installing() )
		return $plugins;

	$network_plugins = is_multisite() ? wp_get_active_network_plugins() : false;

	foreach ( $active_plugins as $plugin ) {
		if ( ! validate_file( $plugin ) // $plugin must validate as file
			&& '.php' == substr( $plugin, -4 ) // $plugin must end with '.php'
			&& file_exists( WP_PLUGIN_DIR . '/' . $plugin ) // $plugin must exist
			// not already included as a network plugin
			&& ( ! $network_plugins || ! in_array( WP_PLUGIN_DIR . '/' . $plugin, $network_plugins ) )
			)
		$plugins[] = WP_PLUGIN_DIR . '/' . $plugin;
	}
	return $plugins;
}

function wp_set_internal_encoding() {
	if ( function_exists( 'mb_internal_encoding' ) ) {
		$charset = get_option( 'blog_charset' );
		if ( ! $charset || ! @mb_internal_encoding( $charset ) )
			mb_internal_encoding( 'UTF-8' );
	}
}

function wp_magic_quotes() {
	// If already slashed, strip.
	if ( get_magic_quotes_gpc() ) {
		$_GET    = stripslashes_deep( $_GET    );
		$_POST   = stripslashes_deep( $_POST   );
		$_COOKIE = stripslashes_deep( $_COOKIE );
	}

	// Escape with wpdb.
	$_GET    = add_magic_quotes( $_GET    );
	$_POST   = add_magic_quotes( $_POST   );
	$_COOKIE = add_magic_quotes( $_COOKIE );
	$_SERVER = add_magic_quotes( $_SERVER );

	// Force REQUEST to be GET + POST.
	$_REQUEST = array_merge( $_GET, $_POST );
}

function wp_get_mu_plugins() {
	$mu_plugins = array();

	if ( !is_dir( WPMU_PLUGIN_DIR ) )
		return $mu_plugins;
	if ( ! $dh = opendir( WPMU_PLUGIN_DIR ) )
		return $mu_plugins;
	while ( ( $plugin = readdir( $dh ) ) !== false ) {
		if ( substr( $plugin, -4 ) == '.php' )
			$mu_plugins[] = WPMU_PLUGIN_DIR . '/' . $plugin;
	}
	closedir( $dh );
	sort( $mu_plugins );

	return $mu_plugins;
}

function wp_get_server_protocol() {
	$protocol = $_SERVER['SERVER_PROTOCOL'];
	if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0' ) ) ) {
		$protocol = 'HTTP/1.0';
	}
	return $protocol;
}

function require_wp_db() {
	global $wpdb;

	// require_once( ABSPATH . WPINC . '/wp-db.php' );
	// if ( file_exists( WP_CONTENT_DIR . '/db.php' ) )
	// 	require_once( WP_CONTENT_DIR . '/db.php' );

	if ( isset( $wpdb ) )
		return;

	$wpdb = new wpdb( );
}

function wp_set_wpdb_vars() {
	global $wpdb, $table_prefix;
	if ( !empty( $wpdb->error ) )
		dead_db();

	$wpdb->field_types = array( 'post_author' => '%d', 'post_parent' => '%d', 'menu_order' => '%d', 'term_id' => '%d', 'term_group' => '%d', 'term_taxonomy_id' => '%d',
		'parent' => '%d', 'count' => '%d','object_id' => '%d', 'term_order' => '%d', 'ID' => '%d', 'comment_ID' => '%d', 'comment_post_ID' => '%d', 'comment_parent' => '%d',
		'user_id' => '%d', 'link_id' => '%d', 'link_owner' => '%d', 'link_rating' => '%d', 'option_id' => '%d', 'blog_id' => '%d', 'meta_id' => '%d', 'post_id' => '%d',
		'user_status' => '%d', 'umeta_id' => '%d', 'comment_karma' => '%d', 'comment_count' => '%d',
		// multisite:
		'active' => '%d', 'cat_id' => '%d', 'deleted' => '%d', 'lang_id' => '%d', 'mature' => '%d', 'public' => '%d', 'site_id' => '%d', 'spam' => '%d',
	);

	$prefix = $wpdb->set_prefix( $table_prefix );

	// if ( is_wp_error( $prefix ) ) {
	// 	wp_load_translations_early();
	// 	wp_die(
	// 		/* translators: 1: $table_prefix 2: wp-config.php */
	// 		sprintf( __( '<strong>ERROR</strong>: %1$s in %2$s can only contain numbers, letters, and underscores.' ),
	// 			'<code>$table_prefix</code>',
	// 			'<code>wp-config.php</code>'
	// 		)
	// 	);
	// }
}

