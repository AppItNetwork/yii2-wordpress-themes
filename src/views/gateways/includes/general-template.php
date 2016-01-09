<?php
use yii\helpers\Html;

function get_header( $name = null ) {
	do_action( 'get_header', $name );

	$templates = array();
	$name = (string) $name;
	if ( '' !== $name )
		$templates[] = "header-{$name}.php";

	$templates[] = 'header.php';

	// Backward compat code will be removed in a future release
	if ('' == locate_template($templates, true))
		load_template( ABSPATH . WPINC . DS . 'theme-compat' . DS . 'header.php');
}

function get_sidebar( $name = null ) {
	do_action( 'get_sidebar', $name );

	$templates = array();
	$name = (string) $name;
	if ( '' !== $name )
		$templates[] = "sidebar-{$name}.php";

	$templates[] = 'sidebar.php';

	// Backward compat code will be removed in a future release
	if ('' == locate_template($templates, true))
		load_template( ABSPATH . WPINC . '/theme-compat/sidebar.php');
}

function get_footer( $name = null ) {
	do_action( 'get_footer', $name );

	$templates = array();
	$name = (string) $name;
	if ( '' !== $name )
		$templates[] = "footer-{$name}.php";

	$templates[] = 'footer.php';

	// Backward compat code will be removed in a future release
	if ('' == locate_template($templates, true))
		load_template( ABSPATH . WPINC . '/theme-compat/footer.php');
}

function get_search_form( $echo = true ) {
	do_action( 'pre_get_search_form' );

	$format = current_theme_supports( 'html5', 'search-form' ) ? 'html5' : 'xhtml';

	$format = apply_filters( 'search_form_format', $format );

	$search_form_template = locate_template( 'searchform.php' );
	if ( '' != $search_form_template ) {
		ob_start();
		require( $search_form_template );
		$form = ob_get_clean();
	} else {
		if ( 'html5' == $format ) {
			$form = '<form role="search" method="get" class="search-form" action="' . esc_url( home_url( '/' ) ) . '">
				<label>
					<span class="screen-reader-text">' . _x( 'Search for:', 'label' ) . '</span>
					<input type="search" class="search-field" placeholder="' . esc_attr_x( 'Search &hellip;', 'placeholder' ) . '" value="' . get_search_query() . '" name="s" title="' . esc_attr_x( 'Search for:', 'label' ) . '" />
				</label>
				<input type="submit" class="search-submit" value="'. esc_attr_x( 'Search', 'submit button' ) .'" />
			</form>';
		} else {
			$form = '<form role="search" method="get" id="searchform" class="searchform" action="' . esc_url( home_url( '/' ) ) . '">
				<div>
					<label class="screen-reader-text" for="s">' . _x( 'Search for:', 'label' ) . '</label>
					<input type="text" value="' . get_search_query() . '" name="s" id="s" />
					<input type="submit" id="searchsubmit" value="'. esc_attr_x( 'Search', 'submit button' ) .'" />
				</div>
			</form>';
		}
	}

	$result = apply_filters( 'get_search_form', $form );

	if ( null === $result )
		$result = $form;

	if ( $echo )
		echo $result;
	else
		return $result;
}

function language_attributes( $doctype = 'html' ) {
	echo get_language_attributes( $doctype );
}

function get_language_attributes( $doctype = 'html' ) {
	$attributes = array();

	if ( function_exists( 'is_rtl' ) && is_rtl() )
		$attributes[] = 'dir="rtl"';

	if ( $lang = get_bloginfo('language') ) {
		if ( get_option('html_type') == 'text/html' || $doctype == 'html' )
			$attributes[] = "lang=\"$lang\"";

		if ( get_option('html_type') != 'text/html' || $doctype == 'xhtml' )
			$attributes[] = "xml:lang=\"$lang\"";
	}

	$output = implode(' ', $attributes);

	return apply_filters( 'language_attributes', $output, $doctype );
}

function bloginfo( $show='' ) {
	echo get_bloginfo( $show, 'display' );
}

function get_bloginfo( $show = '', $filter = 'raw' ) {
	switch( $show ) {
		case 'home' : // DEPRECATED
		case 'siteurl' : // DEPRECATED
			_deprecated_argument( __FUNCTION__, '2.2', sprintf(
				/* translators: 1: 'siteurl'/'home' argument, 2: bloginfo() function name, 3: 'url' argument */
				__( 'The %1$s option is deprecated for the family of %2$s functions. Use the %3$s option instead.' ),
				'<code>' . $show . '</code>',
				'<code>bloginfo()</code>',
				'<code>url</code>'
			) );
		case 'url' :
			$output = home_url();
			break;
		case 'wpurl' :
			$output = site_url();
			break;
		case 'description':
			$output = get_option('blogdescription');
			break;
		case 'rdf_url':
			$output = get_feed_link('rdf');
			break;
		case 'rss_url':
			$output = get_feed_link('rss');
			break;
		case 'rss2_url':
			$output = get_feed_link('rss2');
			break;
		case 'atom_url':
			$output = get_feed_link('atom');
			break;
		case 'comments_atom_url':
			$output = get_feed_link('comments_atom');
			break;
		case 'comments_rss2_url':
			$output = get_feed_link('comments_rss2');
			break;
		case 'pingback_url':
			$output = site_url( 'xmlrpc.php' );
			break;
		case 'stylesheet_url':
			$output = get_stylesheet_uri();
			break;
		case 'stylesheet_directory':
			$output = get_stylesheet_directory_uri();
			break;
		case 'template_directory':
		case 'template_url':
			$output = get_template_directory_uri();
			break;
		case 'admin_email':
			$output = get_option('admin_email');
			break;
		case 'charset':
			$output = get_option('blog_charset');
			if ('' == $output) $output = 'UTF-8';
			break;
		case 'html_type' :
			$output = get_option('html_type');
			break;
		case 'version':
			global $wp_version;
			$output = $wp_version;
			break;
		case 'language':
			$output = get_locale();
			$output = str_replace('_', '-', $output);
			break;
		case 'text_direction':
			_deprecated_argument( __FUNCTION__, '2.2', sprintf(
				/* translators: 1: 'text_direction' argument, 2: bloginfo() function name, 3: is_rtl() function name */
				__( 'The %1$s option is deprecated for the family of %2$s functions. Use the %3$s function instead.' ),
				'<code>' . $show . '</code>',
				'<code>bloginfo()</code>',
				'<code>is_rtl()</code>'
			) );
			if ( function_exists( 'is_rtl' ) ) {
				$output = is_rtl() ? 'rtl' : 'ltr';
			} else {
				$output = 'ltr';
			}
			break;
		case 'name':
		default:
			$output = get_option('blogname');
			break;
	}

	$url = true;
	if (strpos($show, 'url') === false &&
		strpos($show, 'directory') === false &&
		strpos($show, 'home') === false)
		$url = false;

	// pr($url);pr($show);pr($output);pr($filter);die;
	if ( 'display' == $filter ) {
		if ( $url ) {
			$output = apply_filters( 'bloginfo_url', $output, $show );
		} else {
			// $output = apply_fwp_titleilters( 'bloginfo', $output, $show );
		}
	}

	return $output;
}

function wp_title( $sep = '&raquo;', $display = true, $seplocation = '' ) {
	global $wp_locale;

	$title    = '';

	$t_sep = '%WP_TITILE_SEP%'; // Temporary separator, for accurate flipping, if necessary

	// If there is a post
	if ( is_single() || ( is_home() && ! is_front_page() ) || ( is_page() && ! is_front_page() ) ) {
		$title = single_post_title( '', false );
	}

	// If there's a post type archive
	if ( is_post_type_archive() ) {
		$post_type = get_query_var( 'post_type' );
		if ( is_array( $post_type ) ) {
			$post_type = reset( $post_type );
		}
		$post_type_object = get_post_type_object( $post_type );
		if ( ! $post_type_object->has_archive ) {
			$title = post_type_archive_title( '', false );
		}
	}

	// If there's a category or tag
	if ( is_category() || is_tag() ) {
		$title = single_term_title( '', false );
	}

	// If there's a taxonomy
	if ( is_tax() ) {
		$term = get_queried_object();
		if ( $term ) {
			$tax   = get_taxonomy( $term->taxonomy );
			$title = single_term_title( $tax->labels->name . $t_sep, false );
		}
	}

	// If there's an author
	if ( is_author() && ! is_post_type_archive() ) {
		$author = get_queried_object();
		if ( $author ) {
			$title = $author->display_name;
		}
	}

	// Post type archives with has_archive should override terms.
	if ( is_post_type_archive() && $post_type_object->has_archive ) {
		$title = post_type_archive_title( '', false );
	}

	// If it's a search
	if ( is_search() ) {
		/* translators: 1: separator, 2: search phrase */
		$title = sprintf( __( 'Search Results %1$s %2$s' ), $t_sep, strip_tags( $search ) );
	}

	// If it's a 404 page
	if ( is_404() ) {
		$title = __( 'Page not found' );
	}

	$prefix = '';
	if ( ! empty( $title ) ) {
		$prefix = " $sep ";
	}

	$title_array = apply_filters( 'wp_title_parts', explode( $t_sep, $title ) );

	// Determines position of the separator and direction of the breadcrumb
	if ( 'right' == $seplocation ) { // sep on right, so reverse the order
		$title_array = array_reverse( $title_array );
		$title       = implode( " $sep ", $title_array ) . $prefix;
	} else {
		$title = $prefix . implode( " $sep ", $title_array );
	}

	$title = apply_filters( 'wp_title', $title, $sep, $seplocation );

	if ( !empty(Yii::$app->view->title) ) {
		$title = __( Yii::$app->view->title );
	}

	// Send it out
	if ( $display ) {
		echo $title;
	} else {
		return $title;
	}
}

function wp_head() {
	echo Html::csrfMetaTags();
	Yii::$app->view->head();
	do_action( 'wp_head' );

	Yii::$app->view->beginBody();
}

function get_search_query( $escaped = true ) {
	$query = apply_filters( 'get_search_query', get_query_var( 's' ) );

	if ( $escaped )
		$query = esc_attr( $query );
	return $query;
}

function wp_footer() {
	do_action( 'wp_footer' );
	Yii::$app->view->endBody();
}

function _wp_render_title_tag() {
	if ( ! current_theme_supports( 'title-tag' ) ) {
		return;
	}

	echo '<title>' . wp_get_document_title() . '</title>' . "\n";
}

function noindex() {
	// If the blog is not public, tell robots to go away.
	if ( '0' == get_option('blog_public') )
		wp_no_robots();
}

function wp_no_robots() {
	echo "<meta name='robots' content='noindex,follow' />\n";
}

function feed_links( $args = array() ) {
	if ( !current_theme_supports('automatic-feed-links') )
		return;

	$defaults = array(
		/* translators: Separator between blog name and feed type in feed links */
		'separator'	=> _x('&raquo;', 'feed link'),
		/* translators: 1: blog title, 2: separator (raquo) */
		'feedtitle'	=> __('%1$s %2$s Feed'),
		/* translators: 1: blog title, 2: separator (raquo) */
		'comstitle'	=> __('%1$s %2$s Comments Feed'),
	);

	$args = wp_parse_args( $args, $defaults );

	if ( apply_filters( 'feed_links_show_posts_feed', true ) ) {
		echo '<link rel="alternate" type="' . feed_content_type() . '" title="' . esc_attr( sprintf( $args['feedtitle'], get_bloginfo( 'name' ), $args['separator'] ) ) . '" href="' . esc_url( get_feed_link() ) . "\" />\n";
	}

	if ( apply_filters( 'feed_links_show_comments_feed', true ) ) {
		echo '<link rel="alternate" type="' . feed_content_type() . '" title="' . esc_attr( sprintf( $args['comstitle'], get_bloginfo( 'name' ), $args['separator'] ) ) . '" href="' . esc_url( get_feed_link( 'comments_' . get_default_feed() ) ) . "\" />\n";
	}
}

function feed_links_extra( $args = array() ) {
	$defaults = array(
		/* translators: Separator between blog name and feed type in feed links */
		'separator'   => _x('&raquo;', 'feed link'),
		/* translators: 1: blog name, 2: separator(raquo), 3: post title */
		'singletitle' => __('%1$s %2$s %3$s Comments Feed'),
		/* translators: 1: blog name, 2: separator(raquo), 3: category name */
		'cattitle'    => __('%1$s %2$s %3$s Category Feed'),
		/* translators: 1: blog name, 2: separator(raquo), 3: tag name */
		'tagtitle'    => __('%1$s %2$s %3$s Tag Feed'),
		/* translators: 1: blog name, 2: separator(raquo), 3: author name  */
		'authortitle' => __('%1$s %2$s Posts by %3$s Feed'),
		/* translators: 1: blog name, 2: separator(raquo), 3: search phrase */
		'searchtitle' => __('%1$s %2$s Search Results for &#8220;%3$s&#8221; Feed'),
		/* translators: 1: blog name, 2: separator(raquo), 3: post type name */
		'posttypetitle' => __('%1$s %2$s %3$s Feed'),
	);

	$args = wp_parse_args( $args, $defaults );

	if ( is_singular() ) {
		$id = 0;
		$post = get_post( $id );

		if ( comments_open() || pings_open() || $post->comment_count > 0 ) {
			$title = sprintf( $args['singletitle'], get_bloginfo('name'), $args['separator'], the_title_attribute( array( 'echo' => false ) ) );
			$href = get_post_comments_feed_link( $post->ID );
		}
	} elseif ( is_post_type_archive() ) {
		$post_type = get_query_var( 'post_type' );
		if ( is_array( $post_type ) )
			$post_type = reset( $post_type );

		$post_type_obj = get_post_type_object( $post_type );
		$title = sprintf( $args['posttypetitle'], get_bloginfo( 'name' ), $args['separator'], $post_type_obj->labels->name );
		$href = get_post_type_archive_feed_link( $post_type_obj->name );
	} elseif ( is_category() ) {
		$term = get_queried_object();

		if ( $term ) {
			$title = sprintf( $args['cattitle'], get_bloginfo('name'), $args['separator'], $term->name );
			$href = get_category_feed_link( $term->term_id );
		}
	} elseif ( is_tag() ) {
		$term = get_queried_object();

		if ( $term ) {
			$title = sprintf( $args['tagtitle'], get_bloginfo('name'), $args['separator'], $term->name );
			$href = get_tag_feed_link( $term->term_id );
		}
	} elseif ( is_author() ) {
		$author_id = intval( get_query_var('author') );

		$title = sprintf( $args['authortitle'], get_bloginfo('name'), $args['separator'], get_the_author_meta( 'display_name', $author_id ) );
		$href = get_author_feed_link( $author_id );
	} elseif ( is_search() ) {
		$title = sprintf( $args['searchtitle'], get_bloginfo('name'), $args['separator'], get_search_query( false ) );
		$href = get_search_feed_link();
	} elseif ( is_post_type_archive() ) {
		$title = sprintf( $args['posttypetitle'], get_bloginfo('name'), $args['separator'], post_type_archive_title( '', false ) );
		$post_type_obj = get_queried_object();
		if ( $post_type_obj )
			$href = get_post_type_archive_feed_link( $post_type_obj->name );
	}

	if ( isset($title) && isset($href) )
		echo '<link rel="alternate" type="' . feed_content_type() . '" title="' . esc_attr( $title ) . '" href="' . esc_url( $href ) . '" />' . "\n";
}

function rsd_link() {
	echo '<link rel="EditURI" type="application/rsd+xml" title="RSD" href="' . esc_url( site_url( 'xmlrpc.php?rsd', 'rpc' ) ) . '" />' . "\n";
}

function wlwmanifest_link() {
	echo '<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="',
		includes_url( 'wlwmanifest.xml' ), '" /> ', "\n";
}

function wp_generator() {
	the_generator( apply_filters( 'wp_generator_type', 'xhtml' ) );
}

function the_generator( $type ) {
	echo apply_filters( 'the_generator', get_the_generator($type), $type ) . "\n";
}

function get_the_generator( $type = '' ) {
	if ( empty( $type ) ) {

		$current_filter = current_filter();
		if ( empty( $current_filter ) )
			return;

		switch ( $current_filter ) {
			case 'rss2_head' :
			case 'commentsrss2_head' :
				$type = 'rss2';
				break;
			case 'rss_head' :
			case 'opml_head' :
				$type = 'comment';
				break;
			case 'rdf_header' :
				$type = 'rdf';
				break;
			case 'atom_head' :
			case 'comments_atom_head' :
			case 'app_head' :
				$type = 'atom';
				break;
		}
	}

	switch ( $type ) {
		case 'html':
			$gen = '<meta name="generator" content="' . Yii::$app->name . '">';
			break;
		case 'xhtml':
			$gen = '<meta name="generator" content="' . Yii::$app->name . '" />';
			break;
		case 'atom':
			$gen = '<generator uri="https://wordpress.org/" version="' . get_bloginfo_rss( 'version' ) . '">' . Yii::$app->name . '</generator>';
			break;
		case 'rss2':
			$gen = '<generator>https://wordpress.org/?v=' . get_bloginfo_rss( 'version' ) . '</generator>';
			break;
		case 'rdf':
			$gen = '<admin:generatorAgent rdf:resource="https://wordpress.org/?v=' . get_bloginfo_rss( 'version' ) . '" />';
			break;
		case 'comment':
			$gen = '<!-- generator="' . Yii::$app->name . '" -->';
			break;
		case 'export':
			$gen = '<!-- generator="' . Yii::$app->name . '" created="'. date('Y-m-d H:i') . '" -->';
			break;
	}

	return apply_filters( "get_the_generator_{$type}", $gen, $type );
}

function wp_site_icon() {
	if ( ! has_site_icon() && ! is_customize_preview() ) {
		return;
	}

	$meta_tags = array(
		sprintf( '<link rel="icon" href="%s" sizes="32x32" />', esc_url( get_site_icon_url( 32 ) ) ),
		sprintf( '<link rel="icon" href="%s" sizes="192x192" />', esc_url( get_site_icon_url( 192 ) ) ),
		sprintf( '<link rel="apple-touch-icon-precomposed" href="%s" />', esc_url( get_site_icon_url( 180 ) ) ),
		sprintf( '<meta name="msapplication-TileImage" content="%s" />', esc_url( get_site_icon_url( 270 ) ) ),
	);

	$meta_tags = apply_filters( 'site_icon_meta_tags', $meta_tags );
	$meta_tags = array_filter( $meta_tags );

	foreach ( $meta_tags as $meta_tag ) {
		echo "$meta_tag\n";
	}
}

function has_site_icon( $blog_id = 0 ) {
	return (bool) get_site_icon_url( 512, '', $blog_id );
}

function get_site_icon_url( $size = 512, $url = '', $blog_id = 0 ) {
	if ( is_multisite() && (int) $blog_id !== get_current_blog_id() ) {
		switch_to_blog( $blog_id );
	}

	$site_icon_id = get_option( 'site_icon' );

	if ( $site_icon_id ) {
		if ( $size >= 512 ) {
			$size_data = 'full';
		} else {
			$size_data = array( $size, $size );
		}
		$url = wp_get_attachment_image_url( $site_icon_id, $size_data );
	}

	if ( is_multisite() && ms_is_switched() ) {
		restore_current_blog();
	}

	return apply_filters( 'get_site_icon_url', $url, $size, $blog_id );
}

function get_template_part( $slug, $name = null ) {
	do_action( "get_template_part_{$slug}", $slug, $name );

	$templates = array();
	$name = (string) $name;
	if ( '' !== $name )
		$templates[] = "{$slug}-{$name}.php";

	$templates[] = "{$slug}.php";

	locate_template($templates, true, false);
}

function wp_get_document_title() {

	$title = apply_filters( 'pre_get_document_title', '' );
	if ( ! empty( $title ) ) {
		return $title;
	}

	global $page, $paged;

	$title = array(
		'title' => '',
	);

	// If it's a 404 page, use a "Page not found" title.
	if ( is_404() ) {
		$title['title'] = __( 'Page not found' );

	// If it's a search, use a dynamic search results title.
	} elseif ( is_search() ) {
		/* translators: %s: search phrase */
		$title['title'] = sprintf( __( 'Search Results for &#8220;%s&#8221;' ), get_search_query() );

	// If on the home or front page, use the site title.
	} elseif ( is_home() && is_front_page() ) {
		$title['title'] = get_bloginfo( 'name', 'display' );

	// If on a post type archive, use the post type archive title.
	} elseif ( is_post_type_archive() ) {
		$title['title'] = post_type_archive_title( '', false );

	// If on a taxonomy archive, use the term title.
	} elseif ( is_tax() ) {
		$title['title'] = single_term_title( '', false );

	/*
	 * If we're on the blog page and that page is not the homepage or a single
	 * page that is designated as the homepage, use the container page's title.
	 */
	} elseif ( ( is_home() && ! is_front_page() ) || ( ! is_home() && is_front_page() ) ) {
		$title['title'] = single_post_title( '', false );

	// If on a single post of any post type, use the post title.
	} elseif ( is_singular() ) {
		$title['title'] = single_post_title( '', false );

	// If on a category or tag archive, use the term title.
	} elseif ( is_category() || is_tag() ) {
		$title['title'] = single_term_title( '', false );

	// If on an author archive, use the author's display name.
	} elseif ( is_author() && $author = get_queried_object() ) {
		$title['title'] = $author->display_name;

	// If it's a date archive, use the date as the title.
	} elseif ( is_year() ) {
		$title['title'] = get_the_date( _x( 'Y', 'yearly archives date format' ) );

	} elseif ( is_month() ) {
		$title['title'] = get_the_date( _x( 'F Y', 'monthly archives date format' ) );

	} elseif ( is_day() ) {
		$title['title'] = get_the_date();
	}

	// Add a page number if necessary.
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
		$title['page'] = sprintf( __( 'Page %s' ), max( $paged, $page ) );
	}

	// Append the description or site title to give context.
	if ( is_home() && is_front_page() ) {
		$title['tagline'] = get_bloginfo( 'description', 'display' );
	} else {
		$title['site'] = get_bloginfo( 'name', 'display' );
	}

	$sep = apply_filters( 'document_title_separator', '-' );

	$title = apply_filters( 'document_title_parts', $title );

	$title = implode( " $sep ", array_filter( $title ) );
	$title = wptexturize( $title );
	$title = convert_chars( $title );
	$title = esc_html( $title );
	$title = capital_P_dangit( $title );

	return $title;
}

function single_post_title( $prefix = '', $display = true ) {
	$_post = get_queried_object();

	if ( !isset($_post->post_title) )
		return;

	$title = apply_filters( 'single_post_title', $_post->post_title, $_post );
	if ( $display )
		echo $prefix . $title;
	else
		return $prefix . $title;
}

function wp_get_archives( $args = '' ) {
	return false;
	global $wpdb, $wp_locale;

	$defaults = array(
		'type' => 'monthly', 'limit' => '',
		'format' => 'html', 'before' => '',
		'after' => '', 'show_post_count' => false,
		'echo' => 1, 'order' => 'DESC',
		'post_type' => 'post'
	);

	$r = wp_parse_args( $args, $defaults );

	$post_type_object = get_post_type_object( $r['post_type'] );
	if ( ! is_post_type_viewable( $post_type_object ) ) {
		return;
	}
	$r['post_type'] = $post_type_object->name;

	if ( '' == $r['type'] ) {
		$r['type'] = 'monthly';
	}

	if ( ! empty( $r['limit'] ) ) {
		$r['limit'] = absint( $r['limit'] );
		$r['limit'] = ' LIMIT ' . $r['limit'];
	}

	$order = strtoupper( $r['order'] );
	if ( $order !== 'ASC' ) {
		$order = 'DESC';
	}

	// this is what will separate dates on weekly archive links
	$archive_week_separator = '&#8211;';

	// over-ride general date format ? 0 = no: use the date format set in Options, 1 = yes: over-ride
	$archive_date_format_over_ride = 0;

	// options for daily archive (only if you over-ride the general date format)
	$archive_day_date_format = 'Y/m/d';

	// options for weekly archive (only if you over-ride the general date format)
	$archive_week_start_date_format = 'Y/m/d';
	$archive_week_end_date_format	= 'Y/m/d';

	if ( ! $archive_date_format_over_ride ) {
		$archive_day_date_format = get_option( 'date_format' );
		$archive_week_start_date_format = get_option( 'date_format' );
		$archive_week_end_date_format = get_option( 'date_format' );
	}

	$sql_where = $wpdb->prepare( "WHERE post_type = %s AND post_status = 'publish'", $r['post_type'] );

	$where = apply_filters( 'getarchives_where', $sql_where, $r );

	$join = apply_filters( 'getarchives_join', '', $r );

	$output = '';

	$last_changed = wp_cache_get( 'last_changed', 'posts' );
	if ( ! $last_changed ) {
		$last_changed = microtime();
		wp_cache_set( 'last_changed', $last_changed, 'posts' );
	}

	$limit = $r['limit'];

	if ( 'monthly' == $r['type'] ) {
		$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date $order $limit";
		$key = md5( $query );
		$key = "wp_get_archives:$key:$last_changed";
		if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
			$results = $wpdb->get_results( $query );
			wp_cache_set( $key, $results, 'posts' );
		}
		if ( $results ) {
			$after = $r['after'];
			foreach ( (array) $results as $result ) {
				$url = get_month_link( $result->year, $result->month );
				if ( 'post' !== $r['post_type'] ) {
					$url = add_query_arg( 'post_type', $r['post_type'], $url );
				}
				/* translators: 1: month name, 2: 4-digit year */
				$text = sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $result->month ), $result->year );
				if ( $r['show_post_count'] ) {
					$r['after'] = '&nbsp;(' . $result->posts . ')' . $after;
				}
				$output .= get_archives_link( $url, $text, $r['format'], $r['before'], $r['after'] );
			}
		}
	} elseif ( 'yearly' == $r['type'] ) {
		$query = "SELECT YEAR(post_date) AS `year`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date) ORDER BY post_date $order $limit";
		$key = md5( $query );
		$key = "wp_get_archives:$key:$last_changed";
		if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
			$results = $wpdb->get_results( $query );
			wp_cache_set( $key, $results, 'posts' );
		}
		if ( $results ) {
			$after = $r['after'];
			foreach ( (array) $results as $result) {
				$url = get_year_link( $result->year );
				if ( 'post' !== $r['post_type'] ) {
					$url = add_query_arg( 'post_type', $r['post_type'], $url );
				}
				$text = sprintf( '%d', $result->year );
				if ( $r['show_post_count'] ) {
					$r['after'] = '&nbsp;(' . $result->posts . ')' . $after;
				}
				$output .= get_archives_link( $url, $text, $r['format'], $r['before'], $r['after'] );
			}
		}
	} elseif ( 'daily' == $r['type'] ) {
		$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date) ORDER BY post_date $order $limit";
		$key = md5( $query );
		$key = "wp_get_archives:$key:$last_changed";
		if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
			$results = $wpdb->get_results( $query );
			wp_cache_set( $key, $results, 'posts' );
		}
		if ( $results ) {
			$after = $r['after'];
			foreach ( (array) $results as $result ) {
				$url  = get_day_link( $result->year, $result->month, $result->dayofmonth );
				if ( 'post' !== $r['post_type'] ) {
					$url = add_query_arg( 'post_type', $r['post_type'], $url );
				}
				$date = sprintf( '%1$d-%2$02d-%3$02d 00:00:00', $result->year, $result->month, $result->dayofmonth );
				$text = mysql2date( $archive_day_date_format, $date );
				if ( $r['show_post_count'] ) {
					$r['after'] = '&nbsp;(' . $result->posts . ')' . $after;
				}
				$output .= get_archives_link( $url, $text, $r['format'], $r['before'], $r['after'] );
			}
		}
	} elseif ( 'weekly' == $r['type'] ) {
		$week = _wp_mysql_week( '`post_date`' );
		$query = "SELECT DISTINCT $week AS `week`, YEAR( `post_date` ) AS `yr`, DATE_FORMAT( `post_date`, '%Y-%m-%d' ) AS `yyyymmdd`, count( `ID` ) AS `posts` FROM `$wpdb->posts` $join $where GROUP BY $week, YEAR( `post_date` ) ORDER BY `post_date` $order $limit";
		$key = md5( $query );
		$key = "wp_get_archives:$key:$last_changed";
		if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
			$results = $wpdb->get_results( $query );
			wp_cache_set( $key, $results, 'posts' );
		}
		$arc_w_last = '';
		if ( $results ) {
			$after = $r['after'];
			foreach ( (array) $results as $result ) {
				if ( $result->week != $arc_w_last ) {
					$arc_year       = $result->yr;
					$arc_w_last     = $result->week;
					$arc_week       = get_weekstartend( $result->yyyymmdd, get_option( 'start_of_week' ) );
					$arc_week_start = date_i18n( $archive_week_start_date_format, $arc_week['start'] );
					$arc_week_end   = date_i18n( $archive_week_end_date_format, $arc_week['end'] );
					$url            = sprintf( '%1$s/%2$s%3$sm%4$s%5$s%6$sw%7$s%8$d', home_url(), '', '?', '=', $arc_year, '&amp;', '=', $result->week );
					if ( 'post' !== $r['post_type'] ) {
						$url = add_query_arg( 'post_type', $r['post_type'], $url );
					}
					$text           = $arc_week_start . $archive_week_separator . $arc_week_end;
					if ( $r['show_post_count'] ) {
						$r['after'] = '&nbsp;(' . $result->posts . ')' . $after;
					}
					$output .= get_archives_link( $url, $text, $r['format'], $r['before'], $r['after'] );
				}
			}
		}
	} elseif ( ( 'postbypost' == $r['type'] ) || ('alpha' == $r['type'] ) ) {
		$orderby = ( 'alpha' == $r['type'] ) ? 'post_title ASC ' : 'post_date DESC, ID DESC ';
		$query = "SELECT * FROM $wpdb->posts $join $where ORDER BY $orderby $limit";
		$key = md5( $query );
		$key = "wp_get_archives:$key:$last_changed";
		if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
			$results = $wpdb->get_results( $query );
			wp_cache_set( $key, $results, 'posts' );
		}
		if ( $results ) {
			foreach ( (array) $results as $result ) {
				if ( $result->post_date != '0000-00-00 00:00:00' ) {
					$url = get_permalink( $result );
					if ( $result->post_title ) {
						/** This filter is documented in wp-includes/post-template.php */
						$text = strip_tags( apply_filters( 'the_title', $result->post_title, $result->ID ) );
					} else {
						$text = $result->ID;
					}
					$output .= get_archives_link( $url, $text, $r['format'], $r['before'], $r['after'] );
				}
			}
		}
	}
	if ( $r['echo'] ) {
		echo $output;
	} else {
		return $output;
	}
}

function wp_register( $before = '<li>', $after = '</li>', $echo = true ) {
	if ( ! is_user_logged_in() ) {
		if ( get_option('users_can_register') )
			$link = $before . '<a href="' . esc_url( wp_registration_url() ) . '">' . __('Register') . '</a>' . $after;
		else
			$link = '';
	} elseif ( current_user_can( 'read' ) ) {
		$link = $before . '<a href="' . admin_url() . '">' . __('Site Admin') . '</a>' . $after;
	} else {
		$link = '';
	}

	$link = apply_filters( 'register', $link );

	if ( $echo ) {
		echo $link;
	} else {
		return $link;
	}
}

function wp_loginout($redirect = '', $echo = true) {
	if ( ! is_user_logged_in() )
		$link = '<a href="' . esc_url( wp_login_url($redirect) ) . '">' . __('Log in') . '</a>';
	else {
		$link = Html::beginForm( esc_url( wp_logout_url($redirect) ) , 'post')
	    . Html::submitButton(
	        __('Logout').' (' . Yii::$app->user->identity->username . ')'
	    )
	    . Html::endForm();
	}

	if ( $echo ) {
		echo apply_filters( 'loginout', $link );
	} else {
		/** This filter is documented in wp-includes/general-template.php */
		return apply_filters( 'loginout', $link );
	}
}

function wp_logout_url($redirect = '') {
	$logout_url = !empty(Yii::$app->user->logoutUrl) ? Yii::$app->urlManager->createUrl(Yii::$app->user->logoutUrl) : Yii::$app->urlManager->createUrl(['site/logout']);

	return apply_filters( 'logout_url', $logout_url, $redirect );
}

function wp_login_url($redirect = '', $force_reauth = false) {
	$login_url = !empty(Yii::$app->user->loginUrl) ? Yii::$app->urlManager->createUrl(Yii::$app->user->loginUrl) : Yii::$app->urlManager->createUrl(['site/login']);

	return apply_filters( 'login_url', $login_url, $redirect, $force_reauth );
}

function wp_meta() {
	do_action( 'wp_meta' );
}

