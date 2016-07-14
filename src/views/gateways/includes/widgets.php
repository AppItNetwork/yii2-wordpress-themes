<?php
global $wp_registered_sidebars, $wp_registered_widgets, $wp_registered_widget_controls, $wp_registered_widget_updates;

$wp_registered_sidebars = array();
$wp_registered_widgets = array();
$wp_registered_widget_controls = array();
$wp_registered_widget_updates = array();
$_wp_sidebars_widgets = array();

$GLOBALS['_wp_deprecated_widgets_callbacks'] = array(
	'wp_widget_pages',
	'wp_widget_pages_control',
	'wp_widget_calendar',
	'wp_widget_calendar_control',
	'wp_widget_archives',
	'wp_widget_archives_control',
	'wp_widget_links',
	'wp_widget_meta',
	'wp_widget_meta_control',
	'wp_widget_search',
	'wp_widget_recent_entries',
	'wp_widget_recent_entries_control',
	'wp_widget_tag_cloud',
	'wp_widget_tag_cloud_control',
	'wp_widget_categories',
	'wp_widget_categories_control',
	'wp_widget_text',
	'wp_widget_text_control',
	'wp_widget_rss',
	'wp_widget_rss_control',
	'wp_widget_recent_comments',
	'wp_widget_recent_comments_control'
);


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
	register_widget('WP_Widget_Pages');

	// register_widget('WP_Widget_Calendar');

	register_widget('WP_Widget_Archives');

	// if ( get_option( 'link_manager_enabled' ) )
	// 	register_widget('WP_Widget_Links');

	register_widget('WP_Widget_Meta');

	register_widget('WP_Widget_Search');

	register_widget('WP_Widget_Text');

	register_widget('WP_Widget_Categories');

	register_widget('WP_Widget_Recent_Posts');

	register_widget('WP_Widget_Recent_Comments');

	// register_widget('WP_Widget_RSS');

	register_widget('WP_Widget_Tag_Cloud');

	register_widget('WP_Nav_Menu_Widget');

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

function _get_widget_id_base($id) {
	return preg_replace( '/-[0-9]+$/', '', $id );
}

function is_active_widget($callback = false, $widget_id = false, $id_base = false, $skip_inactive = true) {
	global $wp_registered_widgets;

	$sidebars_widgets = wp_get_sidebars_widgets();

	if ( is_array($sidebars_widgets) ) {
		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			if ( $skip_inactive && ( 'wp_inactive_widgets' === $sidebar || 'orphaned_widgets' === substr( $sidebar, 0, 16 ) ) ) {
				continue;
			}

			if ( is_array($widgets) ) {
				foreach ( $widgets as $widget ) {
					if ( ( $callback && isset($wp_registered_widgets[$widget]['callback']) && $wp_registered_widgets[$widget]['callback'] == $callback ) || ( $id_base && _get_widget_id_base($widget) == $id_base ) ) {
						if ( !$widget_id || $widget_id == $wp_registered_widgets[$widget]['id'] )
							return $sidebar;
					}
				}
			}
		}
	}
	return false;
}

function wp_register_sidebar_widget( $id, $name, $output_callback, $options = array() ) {
	global $wp_registered_widgets, $wp_registered_widget_controls, $wp_registered_widget_updates, $_wp_deprecated_widgets_callbacks;

	$id = strtolower($id);

	if ( empty($output_callback) ) {
		unset($wp_registered_widgets[$id]);
		return;
	}

	$id_base = _get_widget_id_base($id);
	if ( in_array($output_callback, $_wp_deprecated_widgets_callbacks, true) && !is_callable($output_callback) ) {
		unset( $wp_registered_widget_controls[ $id ] );
		unset( $wp_registered_widget_updates[ $id_base ] );
		return;
	}

	$defaults = array('classname' => $output_callback);
	$options = wp_parse_args($options, $defaults);
	$widget = array(
		'name' => $name,
		'id' => $id,
		'callback' => $output_callback,
		'params' => array_slice(func_get_args(), 4)
	);
	$widget = array_merge($widget, $options);

	if ( is_callable($output_callback) && ( !isset($wp_registered_widgets[$id]) || did_action( 'widgets_init' ) ) ) {

		do_action( 'wp_register_sidebar_widget', $widget );
		$wp_registered_widgets[$id] = $widget;
	}
}

function _register_widget_update_callback($id_base, $update_callback, $options = array()) {
	global $wp_registered_widget_updates;

	if ( isset($wp_registered_widget_updates[$id_base]) ) {
		if ( empty($update_callback) )
			unset($wp_registered_widget_updates[$id_base]);
		return;
	}

	$widget = array(
		'callback' => $update_callback,
		'params' => array_slice(func_get_args(), 3)
	);

	$widget = array_merge($widget, $options);
	$wp_registered_widget_updates[$id_base] = $widget;
}

function _register_widget_form_callback($id, $name, $form_callback, $options = array()) {
	global $wp_registered_widget_controls;

	$id = strtolower($id);

	if ( empty($form_callback) ) {
		unset($wp_registered_widget_controls[$id]);
		return;
	}

	if ( isset($wp_registered_widget_controls[$id]) && !did_action( 'widgets_init' ) )
		return;

	$defaults = array('width' => 250, 'height' => 200 );
	$options = wp_parse_args($options, $defaults);
	$options['width'] = (int) $options['width'];
	$options['height'] = (int) $options['height'];

	$widget = array(
		'name' => $name,
		'id' => $id,
		'callback' => $form_callback,
		'params' => array_slice(func_get_args(), 4)
	);
	$widget = array_merge($widget, $options);

	$wp_registered_widget_controls[$id] = $widget;
}

function dynamic_sidebar( $index = 1 ) {
	global $wp_registered_sidebars, $wp_registered_widgets;

	if ( is_int( $index ) ) {
		$index = "sidebar-$index";
	} else {
		$sanitized_index = sanitize_title( $index );
		foreach ( (array) $wp_registered_sidebars as $key => $value ) {
			if ( sanitize_title( $value['name'] ) == $sanitized_index ) {
				$index = $key;
				break;
			}
		}
	}

	$sidebars_widgets = wp_get_sidebars_widgets();
	if ( empty( $wp_registered_sidebars[ $index ] ) || empty( $sidebars_widgets[ $index ] ) || ! is_array( $sidebars_widgets[ $index ] ) ) {
		/** This action is documented in wp-includes/widget.php */
		do_action( 'dynamic_sidebar_before', $index, false );
		/** This action is documented in wp-includes/widget.php */
		do_action( 'dynamic_sidebar_after',  $index, false );
		/** This filter is documented in wp-includes/widget.php */
		return apply_filters( 'dynamic_sidebar_has_widgets', false, $index );
	}

	do_action( 'dynamic_sidebar_before', $index, true );
	$sidebar = $wp_registered_sidebars[$index];

	$did_one = false;
	foreach ( (array) $sidebars_widgets[$index] as $id ) {

		if ( !isset($wp_registered_widgets[$id]) ) continue;

		$params = array_merge(
			array( array_merge( $sidebar, array('widget_id' => $id, 'widget_name' => $wp_registered_widgets[$id]['name']) ) ),
			(array) $wp_registered_widgets[$id]['params']
		);

		// Substitute HTML id and class attributes into before_widget
		$classname_ = '';
		foreach ( (array) $wp_registered_widgets[$id]['classname'] as $cn ) {
			if ( is_string($cn) )
				$classname_ .= '_' . $cn;
			elseif ( is_object($cn) )
				$classname_ .= '_' . get_class($cn);
		}
		$classname_ = ltrim($classname_, '_');
		$params[0]['before_widget'] = sprintf($params[0]['before_widget'], $id, $classname_);

		$params = apply_filters( 'dynamic_sidebar_params', $params );

		$callback = $wp_registered_widgets[$id]['callback'];

		do_action( 'dynamic_sidebar', $wp_registered_widgets[ $id ] );

		if ( is_callable($callback) ) {
			call_user_func_array($callback, $params);
			$did_one = true;
		}
	}

	do_action( 'dynamic_sidebar_after', $index, true );

	return apply_filters( 'dynamic_sidebar_has_widgets', $did_one, $index );
}

