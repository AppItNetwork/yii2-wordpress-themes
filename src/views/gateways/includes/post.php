<?php
use appitnetwork\wpthemes\helpers\WP_Post;

function post_type_supports( $post_type, $feature ) {
	global $_wp_post_type_features;

	return ( isset( $_wp_post_type_features[$post_type][$feature] ) );
}

function is_sticky( $post_id = 0 ) {
	$post_id = absint( $post_id );

	if ( ! $post_id )
		$post_id = get_the_ID();

	$stickies = get_option( 'sticky_posts' );

	if ( ! is_array( $stickies ) )
		return false;

	if ( in_array( $post_id, $stickies ) )
		return true;

	return false;
}

function get_post_meta( $post_id, $key = '', $single = false ) {
	return get_metadata('post', $post_id, $key, $single);
}

function _get_custom_object_labels( $object, $nohier_vs_hier_defaults ) {
	$object->labels = (array) $object->labels;

	if ( isset( $object->label ) && empty( $object->labels['name'] ) )
		$object->labels['name'] = $object->label;

	if ( !isset( $object->labels['singular_name'] ) && isset( $object->labels['name'] ) )
		$object->labels['singular_name'] = $object->labels['name'];

	if ( ! isset( $object->labels['name_admin_bar'] ) )
		$object->labels['name_admin_bar'] = isset( $object->labels['singular_name'] ) ? $object->labels['singular_name'] : $object->name;

	if ( !isset( $object->labels['menu_name'] ) && isset( $object->labels['name'] ) )
		$object->labels['menu_name'] = $object->labels['name'];

	if ( !isset( $object->labels['all_items'] ) && isset( $object->labels['menu_name'] ) )
		$object->labels['all_items'] = $object->labels['menu_name'];

	if ( !isset( $object->labels['archives'] ) && isset( $object->labels['all_items'] ) ) {
		$object->labels['archives'] = $object->labels['all_items'];
	}

	$defaults = array();
	foreach ( $nohier_vs_hier_defaults as $key => $value ) {
		$defaults[$key] = $object->hierarchical ? $value[1] : $value[0];
	}
	$labels = array_merge( $defaults, $object->labels );
	$object->labels = (object) $object->labels;

	return (object) $labels;
}

function create_initial_post_types() {
	register_post_type( 'post', array(
		'labels' => array(
			'name_admin_bar' => _x( 'Post', 'add new on admin bar' ),
		),
		'public'  => true,
		'_builtin' => true, /* internal use only. don't use this when registering your own post type. */
		'_edit_link' => 'post.php?post=%d', /* internal use only. don't use this when registering your own post type. */
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'menu_position' => 5,
		'hierarchical' => false,
		'rewrite' => false,
		'query_var' => false,
		'delete_with_user' => true,
		'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'post-formats' ),
	) );

	register_post_type( 'page', array(
		'labels' => array(
			'name_admin_bar' => _x( 'Page', 'add new on admin bar' ),
		),
		'public' => true,
		'publicly_queryable' => false,
		'_builtin' => true, /* internal use only. don't use this when registering your own post type. */
		'_edit_link' => 'post.php?post=%d', /* internal use only. don't use this when registering your own post type. */
		'capability_type' => 'page',
		'map_meta_cap' => true,
		'menu_position' => 20,
		'hierarchical' => true,
		'rewrite' => false,
		'query_var' => false,
		'delete_with_user' => true,
		'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'page-attributes', 'custom-fields', 'comments', 'revisions' ),
	) );

	register_post_type( 'attachment', array(
		'labels' => array(
			'name' => _x('Media', 'post type general name'),
			'name_admin_bar' => _x( 'Media', 'add new from admin bar' ),
			'add_new' => _x( 'Add New', 'add new media' ),
 			'edit_item' => __( 'Edit Media' ),
 			'view_item' => __( 'View Attachment Page' ),
		),
		'public' => true,
		'show_ui' => true,
		'_builtin' => true, /* internal use only. don't use this when registering your own post type. */
		'_edit_link' => 'post.php?post=%d', /* internal use only. don't use this when registering your own post type. */
		'capability_type' => 'post',
		'capabilities' => array(
			'create_posts' => 'upload_files',
		),
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => false,
		'query_var' => false,
		'show_in_nav_menus' => false,
		'delete_with_user' => true,
		'supports' => array( 'title', 'author', 'comments' ),
	) );
	add_post_type_support( 'attachment:audio', 'thumbnail' );
	add_post_type_support( 'attachment:video', 'thumbnail' );

	register_post_type( 'revision', array(
		'labels' => array(
			'name' => __( 'Revisions' ),
			'singular_name' => __( 'Revision' ),
		),
		'public' => false,
		'_builtin' => true, /* internal use only. don't use this when registering your own post type. */
		'_edit_link' => 'revision.php?revision=%d', /* internal use only. don't use this when registering your own post type. */
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => false,
		'query_var' => false,
		'can_export' => false,
		'delete_with_user' => true,
		'supports' => array( 'author' ),
	) );

	register_post_type( 'nav_menu_item', array(
		'labels' => array(
			'name' => __( 'Navigation Menu Items' ),
			'singular_name' => __( 'Navigation Menu Item' ),
		),
		'public' => false,
		'_builtin' => true, /* internal use only. don't use this when registering your own post type. */
		'hierarchical' => false,
		'rewrite' => false,
		'delete_with_user' => false,
		'query_var' => false,
	) );

	register_post_status( 'publish', array(
		'label'       => _x( 'Published', 'post' ),
		'public'      => true,
		'_builtin'    => true, /* internal use only. */
		'label_count' => _n_noop( 'Published <span class="count">(%s)</span>', 'Published <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'future', array(
		'label'       => _x( 'Scheduled', 'post' ),
		'protected'   => true,
		'_builtin'    => true, /* internal use only. */
		'label_count' => _n_noop('Scheduled <span class="count">(%s)</span>', 'Scheduled <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'draft', array(
		'label'       => _x( 'Draft', 'post' ),
		'protected'   => true,
		'_builtin'    => true, /* internal use only. */
		'label_count' => _n_noop( 'Draft <span class="count">(%s)</span>', 'Drafts <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'pending', array(
		'label'       => _x( 'Pending', 'post' ),
		'protected'   => true,
		'_builtin'    => true, /* internal use only. */
		'label_count' => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'private', array(
		'label'       => _x( 'Private', 'post' ),
		'private'     => true,
		'_builtin'    => true, /* internal use only. */
		'label_count' => _n_noop( 'Private <span class="count">(%s)</span>', 'Private <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'trash', array(
		'label'       => _x( 'Trash', 'post' ),
		'internal'    => true,
		'_builtin'    => true, /* internal use only. */
		'label_count' => _n_noop( 'Trash <span class="count">(%s)</span>', 'Trash <span class="count">(%s)</span>' ),
		'show_in_admin_status_list' => true,
	) );

	register_post_status( 'auto-draft', array(
		'label'    => 'auto-draft',
		'internal' => true,
		'_builtin' => true, /* internal use only. */
	) );

	register_post_status( 'inherit', array(
		'label'    => 'inherit',
		'internal' => true,
		'_builtin' => true, /* internal use only. */
		'exclude_from_search' => false,
	) );
}

function register_post_type( $post_type, $args = array() ) {
	global $wp_post_types, $wp_rewrite, $wp;

	if ( ! is_array( $wp_post_types ) ) {
		$wp_post_types = array();
	}

	// Sanitize post type name
	$post_type = sanitize_key( $post_type );
	$args      = wp_parse_args( $args );

	$args = apply_filters( 'register_post_type_args', $args, $post_type );

	$has_edit_link = ! empty( $args['_edit_link'] );

	// Args prefixed with an underscore are reserved for internal use.
	$defaults = array(
		'labels'               => array(),
		'description'          => '',
		'public'               => false,
		'hierarchical'         => false,
		'exclude_from_search'  => null,
		'publicly_queryable'   => null,
		'show_ui'              => null,
		'show_in_menu'         => null,
		'show_in_nav_menus'    => null,
		'show_in_admin_bar'    => null,
		'menu_position'        => null,
		'menu_icon'            => null,
		'capability_type'      => 'post',
		'capabilities'         => array(),
		'map_meta_cap'         => null,
		'supports'             => array(),
		'register_meta_box_cb' => null,
		'taxonomies'           => array(),
		'has_archive'          => false,
		'rewrite'              => true,
		'query_var'            => true,
		'can_export'           => true,
		'delete_with_user'     => null,
		'_builtin'             => false,
		'_edit_link'           => 'post.php?post=%d',
	);
	$args = array_merge( $defaults, $args );
	$args = (object) $args;

	$args->name = $post_type;

	if ( empty( $post_type ) || strlen( $post_type ) > 20 ) {
		_doing_it_wrong( __FUNCTION__, __( 'Post type names must be between 1 and 20 characters in length.' ), '4.2' );
		return new WP_Error( 'post_type_length_invalid', __( 'Post type names must be between 1 and 20 characters in length.' ) );
	}

	// If not set, default to the setting for public.
	if ( null === $args->publicly_queryable )
		$args->publicly_queryable = $args->public;

	// If not set, default to the setting for public.
	if ( null === $args->show_ui )
		$args->show_ui = $args->public;

	// If not set, default to the setting for show_ui.
	if ( null === $args->show_in_menu || ! $args->show_ui )
		$args->show_in_menu = $args->show_ui;

	// If not set, default to the whether the full UI is shown.
	if ( null === $args->show_in_admin_bar )
		$args->show_in_admin_bar = (bool) $args->show_in_menu;

	// If not set, default to the setting for public.
	if ( null === $args->show_in_nav_menus )
		$args->show_in_nav_menus = $args->public;

	// If not set, default to true if not public, false if public.
	if ( null === $args->exclude_from_search )
		$args->exclude_from_search = !$args->public;

	// Back compat with quirky handling in version 3.0. #14122.
	if ( empty( $args->capabilities ) && null === $args->map_meta_cap && in_array( $args->capability_type, array( 'post', 'page' ) ) )
		$args->map_meta_cap = true;

	// If not set, default to false.
	if ( null === $args->map_meta_cap )
		$args->map_meta_cap = false;

	// If there's no specified edit link and no UI, remove the edit link.
	if ( ! $args->show_ui && ! $has_edit_link ) {
		$args->_edit_link = '';
	}

	$args->cap = get_post_type_capabilities( $args );
	unset( $args->capabilities );

	if ( is_array( $args->capability_type ) )
		$args->capability_type = $args->capability_type[0];

	if ( ! empty( $args->supports ) ) {
		add_post_type_support( $post_type, $args->supports );
		unset( $args->supports );
	} elseif ( false !== $args->supports ) {
		// Add default features
		add_post_type_support( $post_type, array( 'title', 'editor' ) );
	}

	if ( false !== $args->query_var ) {
		if ( true === $args->query_var )
			$args->query_var = $post_type;
		else
			$args->query_var = sanitize_title_with_dashes( $args->query_var );

		if ( $wp && is_post_type_viewable( $args ) ) {
			$wp->add_query_var( $args->query_var );
		}
	}

	if ( false !== $args->rewrite && ( is_admin() || '' != get_option( 'permalink_structure' ) ) ) {
		if ( ! is_array( $args->rewrite ) )
			$args->rewrite = array();
		if ( empty( $args->rewrite['slug'] ) )
			$args->rewrite['slug'] = $post_type;
		if ( ! isset( $args->rewrite['with_front'] ) )
			$args->rewrite['with_front'] = true;
		if ( ! isset( $args->rewrite['pages'] ) )
			$args->rewrite['pages'] = true;
		if ( ! isset( $args->rewrite['feeds'] ) || ! $args->has_archive )
			$args->rewrite['feeds'] = (bool) $args->has_archive;
		if ( ! isset( $args->rewrite['ep_mask'] ) ) {
			if ( isset( $args->permalink_epmask ) )
				$args->rewrite['ep_mask'] = $args->permalink_epmask;
			else
				$args->rewrite['ep_mask'] = EP_PERMALINK;
		}

		if ( $args->hierarchical )
			add_rewrite_tag( "%$post_type%", '(.+?)', $args->query_var ? "{$args->query_var}=" : "post_type=$post_type&pagename=" );
		else
			add_rewrite_tag( "%$post_type%", '([^/]+)', $args->query_var ? "{$args->query_var}=" : "post_type=$post_type&name=" );

		if ( $args->has_archive ) {
			$archive_slug = $args->has_archive === true ? $args->rewrite['slug'] : $args->has_archive;
			if ( $args->rewrite['with_front'] )
				$archive_slug = substr( $wp_rewrite->front, 1 ) . $archive_slug;
			else
				$archive_slug = $wp_rewrite->root . $archive_slug;

			add_rewrite_rule( "{$archive_slug}/?$", "index.php?post_type=$post_type", 'top' );
			if ( $args->rewrite['feeds'] && $wp_rewrite->feeds ) {
				$feeds = '(' . trim( implode( '|', $wp_rewrite->feeds ) ) . ')';
				add_rewrite_rule( "{$archive_slug}/feed/$feeds/?$", "index.php?post_type=$post_type" . '&feed=$matches[1]', 'top' );
				add_rewrite_rule( "{$archive_slug}/$feeds/?$", "index.php?post_type=$post_type" . '&feed=$matches[1]', 'top' );
			}
			if ( $args->rewrite['pages'] )
				add_rewrite_rule( "{$archive_slug}/{$wp_rewrite->pagination_base}/([0-9]{1,})/?$", "index.php?post_type=$post_type" . '&paged=$matches[1]', 'top' );
		}

		$permastruct_args = $args->rewrite;
		$permastruct_args['feed'] = $permastruct_args['feeds'];
		add_permastruct( $post_type, "{$args->rewrite['slug']}/%$post_type%", $permastruct_args );
	}

	// Register the post type meta box if a custom callback was specified.
	if ( $args->register_meta_box_cb )
		add_action( 'add_meta_boxes_' . $post_type, $args->register_meta_box_cb, 10, 1 );

	$args->labels = get_post_type_labels( $args );
	$args->label = $args->labels->name;

	$wp_post_types[ $post_type ] = $args;

	add_action( 'future_' . $post_type, '_future_post_hook', 5, 2 );

	foreach ( $args->taxonomies as $taxonomy ) {
		register_taxonomy_for_object_type( $taxonomy, $post_type );
	}

	do_action( 'registered_post_type', $post_type, $args );

	return $args;
}

function get_post_type_capabilities( $args ) {
	if ( ! is_array( $args->capability_type ) )
		$args->capability_type = array( $args->capability_type, $args->capability_type . 's' );

	// Singular base for meta capabilities, plural base for primitive capabilities.
	list( $singular_base, $plural_base ) = $args->capability_type;

	$default_capabilities = array(
		// Meta capabilities
		'edit_post'          => 'edit_'         . $singular_base,
		'read_post'          => 'read_'         . $singular_base,
		'delete_post'        => 'delete_'       . $singular_base,
		// Primitive capabilities used outside of map_meta_cap():
		'edit_posts'         => 'edit_'         . $plural_base,
		'edit_others_posts'  => 'edit_others_'  . $plural_base,
		'publish_posts'      => 'publish_'      . $plural_base,
		'read_private_posts' => 'read_private_' . $plural_base,
	);

	// Primitive capabilities used within map_meta_cap():
	if ( $args->map_meta_cap ) {
		$default_capabilities_for_mapping = array(
			'read'                   => 'read',
			'delete_posts'           => 'delete_'           . $plural_base,
			'delete_private_posts'   => 'delete_private_'   . $plural_base,
			'delete_published_posts' => 'delete_published_' . $plural_base,
			'delete_others_posts'    => 'delete_others_'    . $plural_base,
			'edit_private_posts'     => 'edit_private_'     . $plural_base,
			'edit_published_posts'   => 'edit_published_'   . $plural_base,
		);
		$default_capabilities = array_merge( $default_capabilities, $default_capabilities_for_mapping );
	}

	$capabilities = array_merge( $default_capabilities, $args->capabilities );

	// Post creation capability simply maps to edit_posts by default:
	if ( ! isset( $capabilities['create_posts'] ) )
		$capabilities['create_posts'] = $capabilities['edit_posts'];

	// Remember meta capabilities for future reference.
	if ( $args->map_meta_cap )
		_post_type_meta_capabilities( $capabilities );

	return (object) $capabilities;
}

function _post_type_meta_capabilities( $capabilities = null ) {
	static $meta_caps = array();
	if ( null === $capabilities )
		return $meta_caps;
	foreach ( $capabilities as $core => $custom ) {
		if ( in_array( $core, array( 'read_post', 'delete_post', 'edit_post' ) ) )
			$meta_caps[ $custom ] = $core;
	}
}

function add_post_type_support( $post_type, $feature ) {
	global $_wp_post_type_features;

	$features = (array) $feature;
	foreach ($features as $feature) {
		if ( func_num_args() == 2 )
			$_wp_post_type_features[$post_type][$feature] = true;
		else
			$_wp_post_type_features[$post_type][$feature] = array_slice( func_get_args(), 2 );
	}
}

function get_post_type_labels( $post_type_object ) {
	$nohier_vs_hier_defaults = array(
		'name' => array( _x('Posts', 'post type general name'), _x('Pages', 'post type general name') ),
		'singular_name' => array( _x('Post', 'post type singular name'), _x('Page', 'post type singular name') ),
		'add_new' => array( _x('Add New', 'post'), _x('Add New', 'page') ),
		'add_new_item' => array( __('Add New Post'), __('Add New Page') ),
		'edit_item' => array( __('Edit Post'), __('Edit Page') ),
		'new_item' => array( __('New Post'), __('New Page') ),
		'view_item' => array( __('View Post'), __('View Page') ),
		'search_items' => array( __('Search Posts'), __('Search Pages') ),
		'not_found' => array( __('No posts found.'), __('No pages found.') ),
		'not_found_in_trash' => array( __('No posts found in Trash.'), __('No pages found in Trash.') ),
		'parent_item_colon' => array( null, __('Parent Page:') ),
		'all_items' => array( __( 'All Posts' ), __( 'All Pages' ) ),
		'archives' => array( __( 'Post Archives' ), __( 'Page Archives' ) ),
		'insert_into_item' => array( __( 'Insert into post' ), __( 'Insert into page' ) ),
		'uploaded_to_this_item' => array( __( 'Uploaded to this post' ), __( 'Uploaded to this page' ) ),
		'featured_image' => array( __( 'Featured Image' ), __( 'Featured Image' ) ),
		'set_featured_image' => array( __( 'Set featured image' ), __( 'Set featured image' ) ),
		'remove_featured_image' => array( __( 'Remove featured image' ), __( 'Remove featured image' ) ),
		'use_featured_image' => array( __( 'Use as featured image' ), __( 'Use as featured image' ) ),
		'filter_items_list' => array( __( 'Filter posts list' ), __( 'Filter pages list' ) ),
		'items_list_navigation' => array( __( 'Posts list navigation' ), __( 'Pages list navigation' ) ),
		'items_list' => array( __( 'Posts list' ), __( 'Pages list' ) ),
	);
	$nohier_vs_hier_defaults['menu_name'] = $nohier_vs_hier_defaults['name'];

	$labels = _get_custom_object_labels( $post_type_object, $nohier_vs_hier_defaults );

	$post_type = $post_type_object->name;

	$default_labels = clone $labels;

	$labels = apply_filters( "post_type_labels_{$post_type}", $labels );

	// Ensure that the filtered labels contain all required default values.
	$labels = (object) array_merge( (array) $default_labels, (array) $labels );

	return $labels;
}

function register_post_status( $post_status, $args = array() ) {
	global $wp_post_statuses;

	if (!is_array($wp_post_statuses))
		$wp_post_statuses = array();

	// Args prefixed with an underscore are reserved for internal use.
	$defaults = array(
		'label' => false,
		'label_count' => false,
		'exclude_from_search' => null,
		'_builtin' => false,
		'public' => null,
		'internal' => null,
		'protected' => null,
		'private' => null,
		'publicly_queryable' => null,
		'show_in_admin_status_list' => null,
		'show_in_admin_all_list' => null,
	);
	$args = wp_parse_args($args, $defaults);
	$args = (object) $args;

	$post_status = sanitize_key($post_status);
	$args->name = $post_status;

	// Set various defaults.
	if ( null === $args->public && null === $args->internal && null === $args->protected && null === $args->private )
		$args->internal = true;

	if ( null === $args->public  )
		$args->public = false;

	if ( null === $args->private  )
		$args->private = false;

	if ( null === $args->protected  )
		$args->protected = false;

	if ( null === $args->internal  )
		$args->internal = false;

	if ( null === $args->publicly_queryable )
		$args->publicly_queryable = $args->public;

	if ( null === $args->exclude_from_search )
		$args->exclude_from_search = $args->internal;

	if ( null === $args->show_in_admin_all_list )
		$args->show_in_admin_all_list = !$args->internal;

	if ( null === $args->show_in_admin_status_list )
		$args->show_in_admin_status_list = !$args->internal;

	if ( false === $args->label )
		$args->label = $post_status;

	if ( false === $args->label_count )
		$args->label_count = array( $args->label, $args->label );

	$wp_post_statuses[$post_status] = $args;

	return $args;
}

function get_post_type_object( $post_type ) {
	global $wp_post_types;

	if ( ! is_scalar( $post_type ) || empty( $wp_post_types[ $post_type ] ) ) {
		return null;
	}

	return $wp_post_types[ $post_type ];
}

function get_post_types( $args = array(), $output = 'names', $operator = 'and' ) {
	global $wp_post_types;

	$field = ('names' == $output) ? 'name' : false;

	return wp_filter_object_list($wp_post_types, $args, $operator, $field);
}

function get_post_stati( $args = array(), $output = 'names', $operator = 'and' ) {
	global $wp_post_statuses;

	$field = ('names' == $output) ? 'name' : false;

	return wp_filter_object_list($wp_post_statuses, $args, $operator, $field);
}

function get_post( $post = null, $output = OBJECT, $filter = 'raw' ) {
	if ( empty( $post ) && isset( $GLOBALS['post'] ) )
		$post = $GLOBALS['post'];

// pr($post);die;
	if ( $post instanceof WP_Post ) {
		$_post = $post;
	} elseif ( is_object( $post ) ) {
		if ( empty( $post->filter ) ) {
			$_post = sanitize_post( $post, 'raw' );
			$_post = new WP_Post( $_post );
		} elseif ( 'raw' == $post->filter ) {
			$_post = new WP_Post( $post );
		} else {
			$_post = WP_Post::get_instance( $post->ID );
		}
	} else {
		$_post = WP_Post::get_instance( $post );
	}

// pr($_post);die;
	if ( ! $_post )
		return null;

	$_post = $_post->filter( $filter );

	if ( $output == ARRAY_A )
		return $_post->to_array();
	elseif ( $output == ARRAY_N )
		return array_values( $_post->to_array() );

	return $_post;
}

function sanitize_post( $post, $context = 'display' ) {
	// pr('sanitize_post');pr($post);die;
	if ( is_object($post) ) {
		// Check if post already filtered for this context.
		if ( isset($post->filter) && $context == $post->filter )
			return $post;
		if ( !isset($post->ID) )
			$post->ID = 0;
		foreach ( array_keys(get_object_vars($post)) as $field )
			$post->$field = sanitize_post_field($field, $post->$field, $post->ID, $context);
		$post->filter = $context;
	} elseif ( is_array( $post ) ) {
		// Check if post already filtered for this context.
		if ( isset($post['filter']) && $context == $post['filter'] )
			return $post;
		if ( !isset($post['ID']) )
			$post['ID'] = 0;
		foreach ( array_keys($post) as $field )
			$post[$field] = sanitize_post_field($field, $post[$field], $post['ID'], $context);
		$post['filter'] = $context;
	}
	return $post;
}

function sanitize_post_field( $field, $value, $post_id, $context = 'display' ) {
	$int_fields = array('ID', 'post_parent', 'menu_order');
	if ( in_array($field, $int_fields) )
		$value = (int) $value;

	// Fields which contain arrays of integers.
	$array_int_fields = array( 'ancestors' );
	if ( in_array($field, $array_int_fields) ) {
		$value = array_map( 'absint', $value);
		return $value;
	}

	if ( 'raw' == $context )
		return $value;

	$prefixed = false;
	if ( false !== strpos($field, 'post_') ) {
		$prefixed = true;
		$field_no_prefix = str_replace('post_', '', $field);
	}

	if ( 'edit' == $context ) {
		$format_to_edit = array('post_content', 'post_excerpt', 'post_title', 'post_password');

		if ( $prefixed ) {

			$value = apply_filters( "edit_{$field}", $value, $post_id );

			$value = apply_filters( "{$field_no_prefix}_edit_pre", $value, $post_id );
		} else {
			$value = apply_filters( "edit_post_{$field}", $value, $post_id );
		}

		if ( in_array($field, $format_to_edit) ) {
			if ( 'post_content' == $field )
				$value = format_to_edit($value, user_can_richedit());
			else
				$value = format_to_edit($value);
		} else {
			$value = esc_attr($value);
		}
	} elseif ( 'db' == $context ) {
		if ( $prefixed ) {

			$value = apply_filters( "pre_{$field}", $value );

			$value = apply_filters( "{$field_no_prefix}_save_pre", $value );
		} else {
			$value = apply_filters( "pre_post_{$field}", $value );

			$value = apply_filters( "{$field}_pre", $value );
		}
	} else {

		// Use display filters by default.
		if ( $prefixed ) {

			$value = apply_filters( $field, $value, $post_id, $context );
		} else {
			$value = apply_filters( "post_{$field}", $value, $post_id, $context );
		}
	}

	if ( 'attribute' == $context )
		$value = esc_attr($value);
	elseif ( 'js' == $context )
		$value = esc_js($value);

	return $value;
}

function update_post_caches( &$posts, $post_type = 'post', $update_term_cache = true, $update_meta_cache = true ) {
	// No point in doing all this work if we didn't match any posts.
	if ( !$posts )
		return;

	update_post_cache($posts);

	$post_ids = array();
	foreach ( $posts as $post )
		$post_ids[] = $post->ID;

	if ( ! $post_type )
		$post_type = 'any';

	if ( $update_term_cache ) {
		if ( is_array($post_type) ) {
			$ptypes = $post_type;
		} elseif ( 'any' == $post_type ) {
			$ptypes = array();
			// Just use the post_types in the supplied posts.
			foreach ( $posts as $post ) {
				$ptypes[] = $post->post_type;
			}
			$ptypes = array_unique($ptypes);
		} else {
			$ptypes = array($post_type);
		}

		if ( ! empty($ptypes) )
			update_object_term_cache($post_ids, $ptypes);
	}

	// if ( $update_meta_cache )
	// 	update_postmeta_cache($post_ids);
}

function update_post_cache( &$posts ) {
	if ( ! $posts )
		return;

	// foreach ( $posts as $post )
	// 	wp_cache_add( $post->ID, $post, 'posts' );
}

function is_post_type_viewable( $post_type_object ) {
	return $post_type_object->publicly_queryable || ( $post_type_object->_builtin && $post_type_object->public );
}

function get_page_by_path( $page_path, $output = OBJECT, $post_type = 'page' ) {
	global $wpdb;

	$page_path = rawurlencode(urldecode($page_path));
	$page_path = str_replace('%2F', '/', $page_path);
	$page_path = str_replace('%20', ' ', $page_path);
	$parts = explode( '/', trim( $page_path, '/' ) );
	// $parts = esc_sql( $parts );
	$parts = array_map( 'sanitize_title_for_query', $parts );

	$in_string = "'" . implode( "','", $parts ) . "'";

	if ( is_array( $post_type ) ) {
		$post_types = $post_type;
	} else {
		$post_types = array( $post_type, 'attachment' );
	}

	// $post_types = esc_sql( $post_types );
	$post_type_in_string = "'" . implode( "','", $post_types ) . "'";
	$sql = "
		SELECT ID, post_name, post_parent, post_type
		FROM $wpdb->posts
		WHERE post_name IN ($in_string)
		AND post_type IN ($post_type_in_string)
	";

	$pages = $wpdb->get_results( $sql, OBJECT_K );

	$revparts = array_reverse( $parts );

	$foundid = 0;
	foreach ( (array) $pages as $page ) {
		// pr($page);
		if ( $page->post_name == $revparts[0] ) {
			$count = 0;
			$p = $page;
			// while ( $p->post_parent != 0 && isset( $pages[ $p->post_parent ] ) ) {
			// 	$count++;
			// 	$parent = $pages[ $p->post_parent ];
			// 	if ( ! isset( $revparts[ $count ] ) || $parent->post_name != $revparts[ $count ] )
			// 		break;
			// 	$p = $parent;
			// }

			// if ( $p->post_parent == 0 && $count+1 == count( $revparts ) && $p->post_name == $revparts[ $count ] ) {
			if ($p->post_name == $revparts[0]) {
				$foundid = $page->ID;
				if ( $page->post_type == $post_type )
					break;
			}
		}
	}
// pr($revparts);pr($foundid);pr($output);die;

	if ( $foundid ) {
		return get_post( $foundid, $output );
	}
}

function wp_attachment_is_image( $post = 0 ) {
	return wp_attachment_is( 'image', $post );
}

function wp_attachment_is( $type, $post_id = 0 ) {
	if ( ! $post = get_post( $post_id ) ) {
		return false;
	}

	if ( ! $file = get_attached_file( $post->ID ) ) {
		return false;
	}

	if ( 0 === strpos( $post->post_mime_type, $type . '/' ) ) {
		return true;
	}

	$check = wp_check_filetype( $file );
	if ( empty( $check['ext'] ) ) {
		return false;
	}

	$ext = $check['ext'];

	if ( 'import' !== $post->post_mime_type ) {
		return $type === $ext;
	}

	switch ( $type ) {
	case 'image':
		$image_exts = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png' );
		return in_array( $ext, $image_exts );

	case 'audio':
		return in_array( $ext, wp_get_audio_extensions() );

	case 'video':
		return in_array( $ext, wp_get_video_extensions() );

	default:
		return $type === $ext;
	}
}

function get_attached_file( $attachment_id, $unfiltered = false ) {
	$file = get_post_meta( $attachment_id, '_wp_attached_file', true );
	// If the file is relative, prepend upload dir.
	if ( $file && 0 !== strpos($file, '/') && !preg_match('|^.:\\\|', $file) && ( ($uploads = wp_upload_dir()) && false === $uploads['error'] ) )
		$file = $uploads['basedir'] . "/$file";
	if ( $unfiltered )
		return $file;

	return apply_filters( 'get_attached_file', $file, $attachment_id );
}

