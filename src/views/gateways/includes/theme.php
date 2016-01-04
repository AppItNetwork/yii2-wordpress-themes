<?php

function get_template_directory_uri() {
	$template = str_replace( '%2F', '/', rawurlencode( get_template() ) );
	$theme_root_uri = get_theme_root_uri( $template );
	$template_dir_uri = "$theme_root_uri/$template";

	return apply_filters( 'template_directory_uri', $template_dir_uri, $template, $theme_root_uri );
}

function get_template() {
	// return apply_filters( 'template', get_option( 'template' ) );
	return Yii::$app->view->theme->selectedTheme;
}

function get_theme_root_uri( $stylesheet_or_template = false, $theme_root = false ) {
	if ( $stylesheet_or_template && ! $theme_root )
		$theme_root = get_raw_theme_root( $stylesheet_or_template );

	if ( $stylesheet_or_template && $theme_root ) {
		if ( in_array( $theme_root, (array) $wp_theme_directories ) ) {
			// Absolute path. Make an educated guess. YMMV -- but note the filter below.
			if ( 0 === strpos( $theme_root, WP_CONTENT_DIR ) )
				$theme_root_uri = content_url( str_replace( WP_CONTENT_DIR, '', $theme_root ) );
			elseif ( 0 === strpos( $theme_root, ABSPATH ) )
				$theme_root_uri = site_url( str_replace( ABSPATH, '', $theme_root ) );
			elseif ( 0 === strpos( $theme_root, WP_PLUGIN_DIR ) || 0 === strpos( $theme_root, WPMU_PLUGIN_DIR ) )
				$theme_root_uri = plugins_url( basename( $theme_root ), $theme_root );
			else
				$theme_root_uri = $theme_root;
		} else {
			$theme_root_uri = content_url( $theme_root );
		}
	} else {
		$theme_root_uri = content_url(  );
	}

	return apply_filters( 'theme_root_uri', $theme_root_uri, get_option( 'siteurl' ), $stylesheet_or_template );
}

function get_raw_theme_root( $stylesheet_or_template, $skip_cache = false ) {
	return '';
}

function get_header_image() {
	// die('get_header_image');
	// return false;
	$url = get_theme_mod( 'header_image', get_theme_support( 'custom-header', 'default-image' ) );

	if ( 'remove-header' == $url )
		return false;

	if ( is_random_header_image() )
		$url = get_random_header_image();

	return esc_url_raw( set_url_scheme( $url ) );
}

function current_theme_supports( $feature ) {
	global $_wp_theme_features;

	if ( 'custom-header-uploads' == $feature )
		return current_theme_supports( 'custom-header', 'uploads' );

	if ( !isset( $_wp_theme_features[$feature] ) )
		return false;

	// If no args passed then no extra checks need be performed
	if ( func_num_args() <= 1 )
		return true;

	$args = array_slice( func_get_args(), 1 );

	switch ( $feature ) {
		case 'post-thumbnails':
			// post-thumbnails can be registered for only certain content/post types by passing
			// an array of types to add_theme_support(). If no array was passed, then
			// any type is accepted
			if ( true === $_wp_theme_features[$feature] )  // Registered for all types
				return true;
			$content_type = $args[0];
			return in_array( $content_type, $_wp_theme_features[$feature][0] );

		case 'html5':
		case 'post-formats':
			// specific post formats can be registered by passing an array of types to
			// add_theme_support()

			// Specific areas of HTML5 support *must* be passed via an array to add_theme_support()

			$type = $args[0];
			return in_array( $type, $_wp_theme_features[$feature][0] );

		case 'custom-header':
		case 'custom-background' :
			// specific custom header and background capabilities can be registered by passing
			// an array to add_theme_support()
			$header_support = $args[0];
			return ( isset( $_wp_theme_features[$feature][0][$header_support] ) && $_wp_theme_features[$feature][0][$header_support] );
	}

	return apply_filters( "current_theme_supports-{$feature}", true, $args, $_wp_theme_features[$feature] );
}

function get_template_directory() {
	$template = get_template();
	$theme_root = get_theme_root( $template );
	$template_dir = "$theme_root/$template";

	return apply_filters( 'template_directory', $template_dir, $template, $theme_root );
}

function get_theme_root( $stylesheet_or_template = false ) {
	global $wp_theme_directories;

	if ( $stylesheet_or_template && $theme_root = get_raw_theme_root( $stylesheet_or_template ) ) {
		// Always prepend WP_CONTENT_DIR unless the root currently registered as a theme directory.
		// This gives relative theme roots the benefit of the doubt when things go haywire.
		if ( ! in_array( $theme_root, (array) $wp_theme_directories ) )
			$theme_root = WP_CONTENT_DIR . $theme_root;
	} else {
		$theme_root = WP_CONTENT_DIR . '/themes';
	}

	return apply_filters( 'theme_root', $theme_root );
}

function locale_stylesheet() {
	$stylesheet = get_locale_stylesheet_uri();
	if ( empty($stylesheet) )
		return;
	echo '<link rel="stylesheet" href="' . $stylesheet . '" type="text/css" media="screen" />';
}

function get_locale_stylesheet_uri() {
	global $wp_locale;
	$stylesheet_dir_uri = get_stylesheet_directory_uri();
	$dir = get_stylesheet_directory();
	$locale = get_locale();
	if ( file_exists("$dir/$locale.css") )
		$stylesheet_uri = "$stylesheet_dir_uri/$locale.css";
	elseif ( !empty($wp_locale->text_direction) && file_exists("$dir/{$wp_locale->text_direction}.css") )
		$stylesheet_uri = "$stylesheet_dir_uri/{$wp_locale->text_direction}.css";
	else
		$stylesheet_uri = '';

	return apply_filters( 'locale_stylesheet_uri', $stylesheet_uri, $stylesheet_dir_uri );
}

function get_stylesheet_directory_uri() {
	$stylesheet = str_replace( '%2F', '/', rawurlencode( get_stylesheet() ) );
	$theme_root_uri = get_theme_root_uri( $stylesheet );
	$stylesheet_dir_uri = "$theme_root_uri/$stylesheet";

	return apply_filters( 'stylesheet_directory_uri', $stylesheet_dir_uri, $stylesheet, $theme_root_uri );
}

function get_stylesheet() {
	return apply_filters( 'stylesheet', get_option( 'stylesheet' ) );
}

function get_stylesheet_directory() {
	$stylesheet = get_stylesheet();
	$theme_root = get_theme_root( $stylesheet );
	$stylesheet_dir = "$theme_root/$stylesheet";

	return apply_filters( 'stylesheet_directory', $stylesheet_dir, $stylesheet, $theme_root );
}

function is_customize_preview() {
	global $wp_customize;

	return ( $wp_customize instanceof WP_Customize_Manager ) && $wp_customize->is_preview();
}

function get_stylesheet_uri() {
	$stylesheet_dir_uri = get_stylesheet_directory_uri();
	$stylesheet_uri = $stylesheet_dir_uri . '/style.css';

	return apply_filters( 'stylesheet_uri', $stylesheet_uri, $stylesheet_dir_uri );
}

function get_theme_mod( $name, $default = false ) {
	$mods = get_theme_mods();

	if ( isset( $mods[$name] ) ) {
		return apply_filters( "theme_mod_{$name}", $mods[$name] );
	}

	if ( is_string( $default ) )
		$default = sprintf( $default, get_template_directory_uri(), get_stylesheet_directory_uri() );

	return apply_filters( "theme_mod_{$name}", $default );
}

function get_theme_mods() {
	$theme_slug = get_option( 'stylesheet' );
	$mods = get_option( "theme_mods_$theme_slug" );
	if ( false === $mods ) {
		$theme_name = get_option( 'current_theme' );
		if ( false === $theme_name )
			$theme_name = wp_get_theme()->get('Name');
		$mods = get_option( "mods_$theme_name" ); // Deprecated location.
		if ( is_admin() && false !== $mods ) {
			update_option( "theme_mods_$theme_slug", $mods );
			delete_option( "mods_$theme_name" );
		}
	}
	return $mods;
}

function get_background_image() {
	return get_theme_mod('background_image', get_theme_support( 'custom-background', 'default-image' ) );
}

function background_image() {
	echo get_background_image();
}

function get_theme_support( $feature ) {
	global $_wp_theme_features;
	if ( ! isset( $_wp_theme_features[ $feature ] ) )
		return false;

	if ( func_num_args() <= 1 )
		return $_wp_theme_features[ $feature ];

	$args = array_slice( func_get_args(), 1 );
	switch ( $feature ) {
		case 'custom-header' :
		case 'custom-background' :
			if ( isset( $_wp_theme_features[ $feature ][0][ $args[0] ] ) )
				return $_wp_theme_features[ $feature ][0][ $args[0] ];
			return false;

		default :
			return $_wp_theme_features[ $feature ];
	}
}

function is_random_header_image( $type = 'any' ) {
	$header_image_mod = get_theme_mod( 'header_image', get_theme_support( 'custom-header', 'default-image' ) );

	if ( 'any' == $type ) {
		if ( 'random-default-image' == $header_image_mod || 'random-uploaded-image' == $header_image_mod || ( '' != get_random_header_image() && empty( $header_image_mod ) ) )
			return true;
	} else {
		if ( "random-$type-image" == $header_image_mod )
			return true;
		elseif ( 'default' == $type && empty( $header_image_mod ) && '' != get_random_header_image() )
			return true;
	}

	return false;
}

function get_random_header_image() {
	$random_image = _get_random_header_data();
	// pr($random_image);die;
	if ( empty( $random_image->url ) )
		return '';
	return $random_image->url;
}

function _get_random_header_data() {
	static $_wp_random_header = null;

	if ( empty( $_wp_random_header ) ) {
		global $_wp_default_headers;
		$header_image_mod = get_theme_mod( 'header_image', '' );
		$headers = array();

		if ( 'random-uploaded-image' == $header_image_mod )
			$headers = get_uploaded_header_images();
		elseif ( ! empty( $_wp_default_headers ) ) {
			if ( 'random-default-image' == $header_image_mod ) {
				$headers = $_wp_default_headers;
			} else {
				if ( current_theme_supports( 'custom-header', 'random-default' ) )
					$headers = $_wp_default_headers;
			}
		}

		if ( empty( $headers ) )
			return new stdClass;

		$_wp_random_header = (object) $headers[ array_rand( $headers ) ];

		$_wp_random_header->url =  sprintf( $_wp_random_header->url, get_template_directory_uri(), get_stylesheet_directory_uri() );
		$_wp_random_header->thumbnail_url =  sprintf( $_wp_random_header->thumbnail_url, get_template_directory_uri(), get_stylesheet_directory_uri() );
	}
	return $_wp_random_header;
}

function add_theme_support( $feature ) {
	global $_wp_theme_features;

	if ( func_num_args() == 1 )
		$args = true;
	else
		$args = array_slice( func_get_args(), 1 );

	switch ( $feature ) {
		case 'post-formats' :
			if ( is_array( $args[0] ) ) {
				$post_formats = get_post_format_slugs();
				unset( $post_formats['standard'] );

				$args[0] = array_intersect( $args[0], array_keys( $post_formats ) );
			}
			break;

		case 'html5' :
			// You can't just pass 'html5', you need to pass an array of types.
			if ( empty( $args[0] ) ) {
				// Build an array of types for back-compat.
				$args = array( 0 => array( 'comment-list', 'comment-form', 'search-form' ) );
			} elseif ( ! is_array( $args[0] ) ) {
				_doing_it_wrong( "add_theme_support( 'html5' )", __( 'You need to pass an array of types.' ), '3.6.1' );
				return false;
			}

			// Calling 'html5' again merges, rather than overwrites.
			if ( isset( $_wp_theme_features['html5'] ) )
				$args[0] = array_merge( $_wp_theme_features['html5'][0], $args[0] );
			break;

		case 'custom-header-uploads' :
			return add_theme_support( 'custom-header', array( 'uploads' => true ) );

		case 'custom-header' :
			if ( ! is_array( $args ) )
				$args = array( 0 => array() );

			$defaults = array(
				'default-image' => '',
				'random-default' => false,
				'width' => 0,
				'height' => 0,
				'flex-height' => false,
				'flex-width' => false,
				'default-text-color' => '',
				'header-text' => true,
				'uploads' => true,
				'wp-head-callback' => '',
				'admin-head-callback' => '',
				'admin-preview-callback' => '',
			);

			$jit = isset( $args[0]['__jit'] );
			unset( $args[0]['__jit'] );

			// Merge in data from previous add_theme_support() calls.
			// The first value registered wins. (A child theme is set up first.)
			if ( isset( $_wp_theme_features['custom-header'] ) )
				$args[0] = wp_parse_args( $_wp_theme_features['custom-header'][0], $args[0] );

			// Load in the defaults at the end, as we need to insure first one wins.
			// This will cause all constants to be defined, as each arg will then be set to the default.
			if ( $jit )
				$args[0] = wp_parse_args( $args[0], $defaults );

			// If a constant was defined, use that value. Otherwise, define the constant to ensure
			// the constant is always accurate (and is not defined later,  overriding our value).
			// As stated above, the first value wins.
			// Once we get to wp_loaded (just-in-time), define any constants we haven't already.
			// Constants are lame. Don't reference them. This is just for backwards compatibility.

			if ( defined( 'NO_HEADER_TEXT' ) )
				$args[0]['header-text'] = ! NO_HEADER_TEXT;
			elseif ( isset( $args[0]['header-text'] ) )
				define( 'NO_HEADER_TEXT', empty( $args[0]['header-text'] ) );

			if ( defined( 'HEADER_IMAGE_WIDTH' ) )
				$args[0]['width'] = (int) HEADER_IMAGE_WIDTH;
			elseif ( isset( $args[0]['width'] ) )
				define( 'HEADER_IMAGE_WIDTH', (int) $args[0]['width'] );

			if ( defined( 'HEADER_IMAGE_HEIGHT' ) )
				$args[0]['height'] = (int) HEADER_IMAGE_HEIGHT;
			elseif ( isset( $args[0]['height'] ) )
				define( 'HEADER_IMAGE_HEIGHT', (int) $args[0]['height'] );

			if ( defined( 'HEADER_TEXTCOLOR' ) )
				$args[0]['default-text-color'] = HEADER_TEXTCOLOR;
			elseif ( isset( $args[0]['default-text-color'] ) )
				define( 'HEADER_TEXTCOLOR', $args[0]['default-text-color'] );

			if ( defined( 'HEADER_IMAGE' ) )
				$args[0]['default-image'] = HEADER_IMAGE;
			elseif ( isset( $args[0]['default-image'] ) )
				define( 'HEADER_IMAGE', $args[0]['default-image'] );

			if ( $jit && ! empty( $args[0]['default-image'] ) )
				$args[0]['random-default'] = false;

			// If headers are supported, and we still don't have a defined width or height,
			// we have implicit flex sizes.
			if ( $jit ) {
				if ( empty( $args[0]['width'] ) && empty( $args[0]['flex-width'] ) )
					$args[0]['flex-width'] = true;
				if ( empty( $args[0]['height'] ) && empty( $args[0]['flex-height'] ) )
					$args[0]['flex-height'] = true;
			}

			break;

		case 'custom-background' :
			if ( ! is_array( $args ) )
				$args = array( 0 => array() );

			$defaults = array(
				'default-image'          => '',
				'default-repeat'         => 'repeat',
				'default-position-x'     => 'left',
				'default-attachment'     => 'scroll',
				'default-color'          => '',
				'wp-head-callback'       => '_custom_background_cb',
				'admin-head-callback'    => '',
				'admin-preview-callback' => '',
			);

			$jit = isset( $args[0]['__jit'] );
			unset( $args[0]['__jit'] );

			// Merge in data from previous add_theme_support() calls. The first value registered wins.
			if ( isset( $_wp_theme_features['custom-background'] ) )
				$args[0] = wp_parse_args( $_wp_theme_features['custom-background'][0], $args[0] );

			if ( $jit )
				$args[0] = wp_parse_args( $args[0], $defaults );

			if ( defined( 'BACKGROUND_COLOR' ) )
				$args[0]['default-color'] = BACKGROUND_COLOR;
			elseif ( isset( $args[0]['default-color'] ) || $jit )
				define( 'BACKGROUND_COLOR', $args[0]['default-color'] );

			if ( defined( 'BACKGROUND_IMAGE' ) )
				$args[0]['default-image'] = BACKGROUND_IMAGE;
			elseif ( isset( $args[0]['default-image'] ) || $jit )
				define( 'BACKGROUND_IMAGE', $args[0]['default-image'] );

			break;

		// Ensure that 'title-tag' is accessible in the admin.
		case 'title-tag' :
			// Can be called in functions.php but must happen before wp_loaded, i.e. not in header.php.
			if ( did_action( 'wp_loaded' ) ) {
				/* translators: 1: Theme support 2: hook name */
				_doing_it_wrong( "add_theme_support( 'title-tag' )", sprintf( __( 'Theme support for %1$s should be registered before the %2$s hook.' ),
					'<code>title-tag</code>', '<code>wp_loaded</code>' ), '4.1' );

				return false;
			}
	}

	$_wp_theme_features[ $feature ] = $args;
}

function register_theme_directory( $directory ) {
	global $wp_theme_directories;

	if ( ! file_exists( $directory ) ) {
		// Try prepending as the theme directory could be relative to the content directory
		$directory = WP_CONTENT_DIR . '/' . $directory;
		// If this directory does not exist, return and do not register
		if ( ! file_exists( $directory ) ) {
			return false;
		}
	}

	if ( ! is_array( $wp_theme_directories ) ) {
		$wp_theme_directories = array();
	}

	$untrailed = untrailingslashit( $directory );
	if ( ! empty( $untrailed ) && ! in_array( $untrailed, $wp_theme_directories ) ) {
		$wp_theme_directories[] = $untrailed;
	}

	return true;
}

function _wp_customize_include() {
	if ( ! ( ( isset( $_REQUEST['wp_customize'] ) && 'on' == $_REQUEST['wp_customize'] )
		|| ( is_admin() && 'customize.php' == basename( $_SERVER['PHP_SELF'] ) )
	) ) {
		return;
	}

	require_once ABSPATH . WPINC . '/class-wp-customize-manager.php';
	$GLOBALS['wp_customize'] = new WP_Customize_Manager();
}

function add_editor_style( $stylesheet = 'editor-style.css' ) {
	add_theme_support( 'editor-style' );

	if ( ! is_admin() )
		return;

	global $editor_styles;
	$editor_styles = (array) $editor_styles;
	$stylesheet    = (array) $stylesheet;
	if ( is_rtl() ) {
		$rtl_stylesheet = str_replace('.css', '-rtl.css', $stylesheet[0]);
		$stylesheet[] = $rtl_stylesheet;
	}

	$editor_styles = array_merge( $editor_styles, $stylesheet );
}

function register_default_headers( $headers ) {
	global $_wp_default_headers;

	$_wp_default_headers = array_merge( (array) $_wp_default_headers, (array) $headers );
}

function check_theme_switched() {
	if ( $stylesheet = get_option( 'theme_switched' ) ) {
		$old_theme = wp_get_theme( $stylesheet );

		// Prevent retrieve_widgets() from running since Customizer already called it up front
		if ( get_option( 'theme_switched_via_customizer' ) ) {
			remove_action( 'after_switch_theme', '_wp_sidebars_changed' );
			update_option( 'theme_switched_via_customizer', false );
		}

		if ( $old_theme->exists() ) {
			do_action( 'after_switch_theme', $old_theme->get( 'Name' ), $old_theme );
		} else {
			/** This action is documented in wp-includes/theme.php */
			do_action( 'after_switch_theme', $stylesheet );
		}
		flush_rewrite_rules();

		update_option( 'theme_switched', false );
	}
}

function _custom_header_background_just_in_time() {
	global $custom_image_header, $custom_background;

	if ( current_theme_supports( 'custom-header' ) ) {
		// In case any constants were defined after an add_custom_image_header() call, re-run.
		add_theme_support( 'custom-header', array( '__jit' => true ) );

		$args = get_theme_support( 'custom-header' );
		if ( $args[0]['wp-head-callback'] )
			add_action( 'wp_head', $args[0]['wp-head-callback'] );

		if ( is_admin() ) {
			require_once( ABSPATH . 'wp-admin/custom-header.php' );
			$custom_image_header = new Custom_Image_Header( $args[0]['admin-head-callback'], $args[0]['admin-preview-callback'] );
		}
	}

	if ( current_theme_supports( 'custom-background' ) ) {
		// In case any constants were defined after an add_custom_background() call, re-run.
		add_theme_support( 'custom-background', array( '__jit' => true ) );

		$args = get_theme_support( 'custom-background' );
		add_action( 'wp_head', $args[0]['wp-head-callback'] );

		if ( is_admin() ) {
			require_once( ABSPATH . 'wp-admin/custom-background.php' );
			$custom_background = new Custom_Background( $args[0]['admin-head-callback'], $args[0]['admin-preview-callback'] );
		}
	}
}

function get_header_textcolor() {
	return get_theme_mod('header_textcolor', get_theme_support( 'custom-header', 'default-text-color' ) );
}

function header_image() {
	$image = get_header_image();
	if ( $image ) {
		echo esc_url( $image );
	}
}

function display_header_text() {
	if ( ! current_theme_supports( 'custom-header', 'header-text' ) )
		return false;

	$text_color = get_theme_mod( 'header_textcolor', get_theme_support( 'custom-header', 'default-text-color' ) );
	return 'blank' !== $text_color;
}

