<?php

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

