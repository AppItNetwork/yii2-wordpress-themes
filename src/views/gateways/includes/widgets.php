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

function wp_widgets_init() {
	if ( !is_blog_installed() )
		return;

	// register_widget('WP_Widget_Pages');

	// register_widget('WP_Widget_Calendar');

	// register_widget('WP_Widget_Archives');

	// if ( get_option( 'link_manager_enabled' ) )
	// 	register_widget('WP_Widget_Links');

	// register_widget('WP_Widget_Meta');

	// register_widget('WP_Widget_Search');

	// register_widget('WP_Widget_Text');

	// register_widget('WP_Widget_Categories');

	// register_widget('WP_Widget_Recent_Posts');

	// register_widget('WP_Widget_Recent_Comments');

	// register_widget('WP_Widget_RSS');

	// register_widget('WP_Widget_Tag_Cloud');

	// register_widget('WP_Nav_Menu_Widget');

	do_action( 'widgets_init' );
}

function register_widget($widget_class) {
	global $wp_widget_factory;

	$wp_widget_factory->register($widget_class);
}

function register_sidebar($args = array()) {
	global $wp_registered_sidebars;

	$i = count($wp_registered_sidebars) + 1;

	$id_is_empty = empty( $args['id'] );

	$defaults = array(
		'name' => sprintf(__('Sidebar %d'), $i ),
		'id' => "sidebar-$i",
		'description' => '',
		'class' => '',
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => "</li>\n",
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => "</h2>\n",
	);

	$sidebar = wp_parse_args( $args, $defaults );

	if ( $id_is_empty ) {
		/* translators: 1: the id argument, 2: sidebar name, 3: recommended id value */
		_doing_it_wrong( __FUNCTION__, sprintf( __( 'No %1$s was set in the arguments array for the "%2$s" sidebar. Defaulting to "%3$s". Manually set the %1$s to "%3$s" to silence this notice and keep existing sidebar content.' ), '<code>id</code>', $sidebar['name'], $sidebar['id'] ), '4.2.0' );
	}

	$wp_registered_sidebars[$sidebar['id']] = $sidebar;

	add_theme_support('widgets');

	do_action( 'register_sidebar', $sidebar );

	return $sidebar['id'];
}

