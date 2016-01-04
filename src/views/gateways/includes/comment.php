<?php

function _close_comments_for_old_post( $open, $post_id ) {
	if ( ! $open )
		return $open;

	if ( !get_option('close_comments_for_old_posts') )
		return $open;

	$days_old = (int) get_option('close_comments_days_old');
	if ( !$days_old )
		return $open;

	$post = get_post($post_id);

	/** This filter is documented in wp-includes/comment.php */
	$post_types = apply_filters( 'close_comments_for_post_types', array( 'post' ) );
	if ( ! in_array( $post->post_type, $post_types ) )
		return $open;

	// Undated drafts should not show up as comments closed.
	if ( '0000-00-00 00:00:00' === $post->post_date_gmt ) {
		return $open;
	}

	if ( time() - strtotime( $post->post_date_gmt ) > ( $days_old * DAY_IN_SECONDS ) )
		return false;

	return $open;
}

function sanitize_comment_cookies() {
	if ( isset( $_COOKIE['comment_author_' . COOKIEHASH] ) ) {
		$comment_author = apply_filters( 'pre_comment_author_name', $_COOKIE['comment_author_' . COOKIEHASH] );
		$comment_author = wp_unslash($comment_author);
		$comment_author = esc_attr($comment_author);
		$_COOKIE['comment_author_' . COOKIEHASH] = $comment_author;
	}

	if ( isset( $_COOKIE['comment_author_email_' . COOKIEHASH] ) ) {
		$comment_author_email = apply_filters( 'pre_comment_author_email', $_COOKIE['comment_author_email_' . COOKIEHASH] );
		$comment_author_email = wp_unslash($comment_author_email);
		$comment_author_email = esc_attr($comment_author_email);
		$_COOKIE['comment_author_email_'.COOKIEHASH] = $comment_author_email;
	}

	if ( isset( $_COOKIE['comment_author_url_' . COOKIEHASH] ) ) {
		$comment_author_url = apply_filters( 'pre_comment_author_url', $_COOKIE['comment_author_url_' . COOKIEHASH] );
		$comment_author_url = wp_unslash($comment_author_url);
		$_COOKIE['comment_author_url_'.COOKIEHASH] = $comment_author_url;
	}
}

