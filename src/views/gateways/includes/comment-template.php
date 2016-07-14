<?php

function comments_open( $post_id = null ) {

	$_post = get_post($post_id);
	// $_post = Yii::$app->wpthemes->post;

	$open = ( 'open' == $_post->comment_status );

	return apply_filters( 'comments_open', $open, $post_id );
}

function comments_template( $file = '/comments.php', $separate_comments = false ) {
	global $wp_query, $withcomments, $post, $wpdb, $id, $comment, $user_login, $user_ID, $user_identity, $overridden_cpage;

	if ( !(is_single() || is_page() || $withcomments) || empty($post) )
		return;

	if ( empty($file) )
		$file = '/comments.php';

	$req = get_option('require_name_email');

	/*
	 * Comment author information fetched from the comment cookies.
	 */
	$commenter = wp_get_current_commenter();

	/*
	 * The name of the current comment author escaped for use in attributes.
	 * Escaped by sanitize_comment_cookies().
	 */
	$comment_author = $commenter['comment_author'];

	/*
	 * The email address of the current comment author escaped for use in attributes.
	 * Escaped by sanitize_comment_cookies().
	 */
	$comment_author_email = $commenter['comment_author_email'];

	/*
	 * The url of the current comment author escaped for use in attributes.
	 */
	$comment_author_url = esc_url($commenter['comment_author_url']);

	$comment_args = array(
		'orderby' => 'comment_date_gmt',
		'order' => 'ASC',
		'status'  => 'approve',
		'post_id' => $post->ID,
		'hierarchical' => 'threaded',
		'no_found_rows' => false,
		'update_comment_meta_cache' => false, // We lazy-load comment meta for performance.
	);

	if ( $user_ID ) {
		$comment_args['include_unapproved'] = array( $user_ID );
	} elseif ( ! empty( $comment_author_email ) ) {
		$comment_args['include_unapproved'] = array( $comment_author_email );
	}

	$per_page = 0;
	if ( get_option( 'page_comments' ) ) {
		$per_page = (int) get_query_var( 'comments_per_page' );
		if ( 0 === $per_page ) {
			$per_page = (int) get_option( 'comments_per_page' );
		}

		$comment_args['number'] = $per_page;
		$page = (int) get_query_var( 'cpage' );

		if ( $page ) {
			$comment_args['offset'] = ( $page - 1 ) * $per_page;
		} elseif ( 'oldest' === get_option( 'default_comments_page' ) ) {
			$comment_args['offset'] = 0;
		} else {
			// If fetching the first page of 'newest', we need a top-level comment count.
			$top_level_query = new WP_Comment_Query();
			$top_level_count = $top_level_query->query( array(
				'count'   => true,
				'orderby' => false,
				'post_id' => $post->ID,
				'parent'  => 0,
			) );

			$comment_args['offset'] = ( ceil( $top_level_count / $per_page ) - 1 ) * $per_page;
		}
	}

	$comment_query = new WP_Comment_Query( $comment_args );
	$_comments = $comment_query->comments;

	// Trees must be flattened before they're passed to the walker.
	$comments_flat = array();
	foreach ( $_comments as $_comment ) {
		$comments_flat = array_merge( $comments_flat, array( $_comment ), $_comment->get_children( array(
			'format' => 'flat',
			'status' => $comment_args['status'],
			'orderby' => $comment_args['orderby']
		) ) );
	}

	$wp_query->comments = apply_filters( 'comments_array', $comments_flat, $post->ID );

	// Set up lazy-loading for comment metadata.
	add_action( 'get_comment_metadata', array( $wp_query, 'lazyload_comment_meta' ), 10, 2 );

	$comments = &$wp_query->comments;
	$wp_query->comment_count = count($wp_query->comments);
	$wp_query->max_num_comment_pages = $comment_query->max_num_pages;

	if ( $separate_comments ) {
		$wp_query->comments_by_type = separate_comments($comments);
		$comments_by_type = &$wp_query->comments_by_type;
	} else {
		$wp_query->comments_by_type = array();
	}

	$overridden_cpage = false;
	if ( '' == get_query_var( 'cpage' ) && $wp_query->max_num_comment_pages > 1 ) {
		set_query_var( 'cpage', 'newest' == get_option('default_comments_page') ? get_comment_pages_count() : 1 );
		$overridden_cpage = true;
	}

	if ( !defined('COMMENTS_TEMPLATE') )
		define('COMMENTS_TEMPLATE', true);

	$theme_template = STYLESHEETPATH . $file;
	$include = apply_filters( 'comments_template', $theme_template );
	if ( file_exists( $include ) )
		require( $include );
	elseif ( file_exists( TEMPLATEPATH . $file ) )
		require( TEMPLATEPATH . $file );
	else // Backward compat code will be removed in a future release
		require( ABSPATH . WPINC . '/theme-compat/comments.php');
}

function get_comments_number( $post_id = 0 ) {
	$post = get_post( $post_id );

	if ( ! $post ) {
		$count = 0;
	} else {
		$count = $post->comment_count;
		$post_id = $post->ID;
	}

	return apply_filters( 'get_comments_number', $count, $post_id );
}

function pings_open( $post_id = null ) {

	$_post = get_post($post_id);

	$open = ( 'open' == $_post->ping_status );

	return apply_filters( 'pings_open', $open, $post_id );
}

