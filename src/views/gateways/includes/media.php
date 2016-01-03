<?php

function wp_make_content_images_responsive( $content ) {
	if ( ! preg_match_all( '/<img [^>]+>/', $content, $matches ) ) {
		return $content;
	}

	$selected_images = $attachment_ids = array();

	foreach( $matches[0] as $image ) {
		if ( false === strpos( $image, ' srcset=' ) && preg_match( '/wp-image-([0-9]+)/i', $image, $class_id ) &&
			( $attachment_id = absint( $class_id[1] ) ) ) {

			/*
			 * If exactly the same image tag is used more than once, overwrite it.
			 * All identical tags will be replaced later with 'str_replace()'.
			 */
			$selected_images[ $image ] = $attachment_id;
			// Overwrite the ID when the same image is included more than once.
			$attachment_ids[ $attachment_id ] = true;
		}
	}

	if ( count( $attachment_ids ) > 1 ) {
		/*
		 * Warm object cache for use with 'get_post_meta()'.
		 *
		 * To avoid making a database call for each image, a single query
		 * warms the object cache with the meta information for all images.
		 */
		update_meta_cache( 'post', array_keys( $attachment_ids ) );
	}

	foreach ( $selected_images as $image => $attachment_id ) {
		$image_meta = get_post_meta( $attachment_id, '_wp_attachment_metadata', true );
		$content = str_replace( $image, wp_image_add_srcset_and_sizes( $image, $image_meta, $attachment_id ), $content );
	}

	return $content;
}

