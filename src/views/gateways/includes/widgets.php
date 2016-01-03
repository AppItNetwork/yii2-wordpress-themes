<?php

function is_active_sidebar( $index ) {
	$index = ( is_int($index) ) ? "sidebar-$index" : sanitize_title($index);
	$sidebars_widgets = wp_get_sidebars_widgets();
	$is_active_sidebar = ! empty( $sidebars_widgets[$index] );

	return apply_filters( 'is_active_sidebar', $is_active_sidebar, $index );
}

function wp_get_sidebars_widgets( $deprecated = true ) {
	if ( $deprecated !== true )
		_deprecated_argument( __FUNCTION__, '2.8.1' );

	global $_wp_sidebars_widgets, $sidebars_widgets;

	// If loading from front page, consult $_wp_sidebars_widgets rather than options
	// to see if wp_convert_widget_settings() has made manipulations in memory.
	if ( !is_admin() ) {
		if ( empty($_wp_sidebars_widgets) )
			$_wp_sidebars_widgets = get_option('sidebars_widgets', array());

		$sidebars_widgets = $_wp_sidebars_widgets;
	} else {
		$sidebars_widgets = get_option('sidebars_widgets', array());
	}

	if ( is_array( $sidebars_widgets ) && isset($sidebars_widgets['array_version']) )
		unset($sidebars_widgets['array_version']);

	return apply_filters( 'sidebars_widgets', $sidebars_widgets );
}

