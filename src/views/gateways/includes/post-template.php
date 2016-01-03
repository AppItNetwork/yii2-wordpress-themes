<?php

function body_class( $class = '' ) {
	// Separates classes with a single space, collates classes for body element
	echo 'class="' . join( ' ', get_body_class( $class ) ) . '"';
}

/**
 * Retrieve the classes for the body element as an array.
 *
 * @since 2.8.0
 *
 * @global WP_Query $wp_query
 *
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
function get_body_class( $class = '' ) {
	global $wp_query;

	$classes = array();

	if ( function_exists('is_rtl') && is_rtl() )
		$classes[] = 'rtl';

	// if ( is_front_page() )
		$classes[] = 'home';
	// if ( is_home() )
	// 	$classes[] = 'blog';
	// if ( is_archive() )
	// 	$classes[] = 'archive';
	// if ( is_date() )
	// 	$classes[] = 'date';
	// if ( is_search() ) {
	// 	$classes[] = 'search';
	// 	$classes[] = $wp_query->posts ? 'search-results' : 'search-no-results';
	// }
	// if ( is_paged() )
	// 	$classes[] = 'paged';
	// if ( is_attachment() )
	// 	$classes[] = 'attachment';
	// if ( is_404() )
	// 	$classes[] = 'error404';

	// if ( is_single() ) {
	// 	$post_id = $wp_query->get_queried_object_id();
	// 	$post = $wp_query->get_queried_object();

	// 	$classes[] = 'single';
	// 	if ( isset( $post->post_type ) ) {
	// 		$classes[] = 'single-' . sanitize_html_class($post->post_type, $post_id);
	// 		$classes[] = 'postid-' . $post_id;

	// 		// Post Format
	// 		if ( post_type_supports( $post->post_type, 'post-formats' ) ) {
	// 			$post_format = get_post_format( $post->ID );

	// 			if ( $post_format && !is_wp_error($post_format) )
	// 				$classes[] = 'single-format-' . sanitize_html_class( $post_format );
	// 			else
	// 				$classes[] = 'single-format-standard';
	// 		}
	// 	}

	// 	if ( is_attachment() ) {
	// 		$mime_type = get_post_mime_type($post_id);
	// 		$mime_prefix = array( 'application/', 'image/', 'text/', 'audio/', 'video/', 'music/' );
	// 		$classes[] = 'attachmentid-' . $post_id;
	// 		$classes[] = 'attachment-' . str_replace( $mime_prefix, '', $mime_type );
	// 	}
	// } elseif ( is_archive() ) {
	// 	if ( is_post_type_archive() ) {
	// 		$classes[] = 'post-type-archive';
	// 		$post_type = get_query_var( 'post_type' );
	// 		if ( is_array( $post_type ) )
	// 			$post_type = reset( $post_type );
	// 		$classes[] = 'post-type-archive-' . sanitize_html_class( $post_type );
	// 	} elseif ( is_author() ) {
	// 		$author = $wp_query->get_queried_object();
	// 		$classes[] = 'author';
	// 		if ( isset( $author->user_nicename ) ) {
	// 			$classes[] = 'author-' . sanitize_html_class( $author->user_nicename, $author->ID );
	// 			$classes[] = 'author-' . $author->ID;
	// 		}
	// 	} elseif ( is_category() ) {
	// 		$cat = $wp_query->get_queried_object();
	// 		$classes[] = 'category';
	// 		if ( isset( $cat->term_id ) ) {
	// 			$cat_class = sanitize_html_class( $cat->slug, $cat->term_id );
	// 			if ( is_numeric( $cat_class ) || ! trim( $cat_class, '-' ) ) {
	// 				$cat_class = $cat->term_id;
	// 			}

	// 			$classes[] = 'category-' . $cat_class;
	// 			$classes[] = 'category-' . $cat->term_id;
	// 		}
	// 	} elseif ( is_tag() ) {
	// 		$tag = $wp_query->get_queried_object();
	// 		$classes[] = 'tag';
	// 		if ( isset( $tag->term_id ) ) {
	// 			$tag_class = sanitize_html_class( $tag->slug, $tag->term_id );
	// 			if ( is_numeric( $tag_class ) || ! trim( $tag_class, '-' ) ) {
	// 				$tag_class = $tag->term_id;
	// 			}

	// 			$classes[] = 'tag-' . $tag_class;
	// 			$classes[] = 'tag-' . $tag->term_id;
	// 		}
	// 	} elseif ( is_tax() ) {
	// 		$term = $wp_query->get_queried_object();
	// 		if ( isset( $term->term_id ) ) {
	// 			$term_class = sanitize_html_class( $term->slug, $term->term_id );
	// 			if ( is_numeric( $term_class ) || ! trim( $term_class, '-' ) ) {
	// 				$term_class = $term->term_id;
	// 			}

	// 			$classes[] = 'tax-' . sanitize_html_class( $term->taxonomy );
	// 			$classes[] = 'term-' . $term_class;
	// 			$classes[] = 'term-' . $term->term_id;
	// 		}
	// 	}
	// } elseif ( is_page() ) {
	// 	$classes[] = 'page';

	// 	$page_id = $wp_query->get_queried_object_id();

	// 	$post = get_post($page_id);

	// 	$classes[] = 'page-id-' . $page_id;

	// 	if ( get_pages( array( 'parent' => $page_id, 'number' => 1 ) ) ) {
	// 		$classes[] = 'page-parent';
	// 	}

	// 	if ( $post->post_parent ) {
	// 		$classes[] = 'page-child';
	// 		$classes[] = 'parent-pageid-' . $post->post_parent;
	// 	}
	// 	if ( is_page_template() ) {
	// 		$classes[] = 'page-template';

	// 		$template_slug  = get_page_template_slug( $page_id );
	// 		$template_parts = explode( '/', $template_slug );

	// 		foreach ( $template_parts as $part ) {
	// 			$classes[] = 'page-template-' . sanitize_html_class( str_replace( array( '.', '/' ), '-', basename( $part, '.php' ) ) );
	// 		}
	// 		$classes[] = 'page-template-' . sanitize_html_class( str_replace( '.', '-', $template_slug ) );
	// 	} else {
	// 		$classes[] = 'page-template-default';
	// 	}
	// }

	// if ( is_user_logged_in() )
	// 	$classes[] = 'logged-in';

	// if ( is_admin_bar_showing() ) {
	// 	$classes[] = 'admin-bar';
	// 	$classes[] = 'no-customize-support';
	// }

	// if ( get_background_color() !== get_theme_support( 'custom-background', 'default-color' ) || get_background_image() )
	// 	$classes[] = 'custom-background';

	// $page = $wp_query->get( 'page' );
	$page = Yii::$app->wpthemes->getAllPages();

	if ( ! $page || $page < 2 )
		$page = $wp_query->get( 'paged' );

	// if ( $page && $page > 1 && ! is_404() ) {
	// 	$classes[] = 'paged-' . $page;

	// 	if ( is_single() )
	// 		$classes[] = 'single-paged-' . $page;
	// 	elseif ( is_page() )
	// 		$classes[] = 'page-paged-' . $page;
	// 	elseif ( is_category() )
	// 		$classes[] = 'category-paged-' . $page;
	// 	elseif ( is_tag() )
	// 		$classes[] = 'tag-paged-' . $page;
	// 	elseif ( is_date() )
	// 		$classes[] = 'date-paged-' . $page;
	// 	elseif ( is_author() )
	// 		$classes[] = 'author-paged-' . $page;
	// 	elseif ( is_search() )
	// 		$classes[] = 'search-paged-' . $page;
	// 	elseif ( is_post_type_archive() )
	// 		$classes[] = 'post-type-paged-' . $page;
	// }

	if ( ! empty( $class ) ) {
		if ( !is_array( $class ) )
			$class = preg_split( '#\s+#', $class );
		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	$classes = array_map( 'esc_attr', $classes );

	// $classes = apply_filters( 'body_class', $classes, $class );

	return array_unique( $classes );
}

function the_ID() {
	echo get_the_ID();
}

function get_the_ID() {
	$post = Yii::$app->wpthemes->post;
	return ! empty( $post ) ? $post->ID : false;
}

function post_class( $class = '', $post_id = null ) {
	// Separates classes with a single space, collates classes for post DIV
	echo 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
}

function get_post_class( $class = '', $post_id = null ) {
	$post = Yii::$app->wpthemes->post;

	$classes = array();

	if ( $class ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_map( 'esc_attr', $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	if ( ! $post ) {
		return $classes;
	}

	$classes[] = 'post-' . $post->ID;
	if ( ! is_admin() )
		$classes[] = $post->post_type;
	$classes[] = 'type-' . $post->post_type;
	$classes[] = 'status-' . $post->post_status;

	// Post Format
	if ( post_type_supports( $post->post_type, 'post-formats' ) ) {
		$post_format = get_post_format( $post->ID );

		if ( $post_format && !is_wp_error($post_format) )
			$classes[] = 'format-' . sanitize_html_class( $post_format );
		else
			$classes[] = 'format-standard';
	}

	$post_password_required = post_password_required( $post->ID );

	// Post requires password.
	if ( $post_password_required ) {
		$classes[] = 'post-password-required';
	} elseif ( ! empty( $post->post_password ) ) {
		$classes[] = 'post-password-protected';
	}

	// Post thumbnails.
	if ( current_theme_supports( 'post-thumbnails' ) && has_post_thumbnail( $post->ID ) && ! is_attachment( $post ) && ! $post_password_required ) {
		$classes[] = 'has-post-thumbnail';
	}

	// sticky for Sticky Posts
	if ( is_sticky( $post->ID ) ) {
		if ( is_home() && ! is_paged() ) {
			$classes[] = 'sticky';
		} elseif ( is_admin() ) {
			$classes[] = 'status-sticky';
		}
	}

	// hentry for hAtom compliance
	$classes[] = 'hentry';

	// All public taxonomies
	$taxonomies = get_taxonomies( array( 'public' => true ) );
	foreach ( (array) $taxonomies as $taxonomy ) {
		if ( is_object_in_taxonomy( $post->post_type, $taxonomy ) ) {
			foreach ( (array) get_the_terms( $post->ID, $taxonomy ) as $term ) {
				if ( empty( $term->slug ) ) {
					continue;
				}

				$term_class = sanitize_html_class( $term->slug, $term->term_id );
				if ( is_numeric( $term_class ) || ! trim( $term_class, '-' ) ) {
					$term_class = $term->term_id;
				}

				// 'post_tag' uses the 'tag' prefix for backward compatibility.
				if ( 'post_tag' == $taxonomy ) {
					$classes[] = 'tag-' . $term_class;
				} else {
					$classes[] = sanitize_html_class( $taxonomy . '-' . $term_class, $taxonomy . '-' . $term->term_id );
				}
			}
		}
	}

	$classes = array_map( 'esc_attr', $classes );

	$classes = apply_filters( 'post_class', $classes, $class, $post->ID );

	return array_unique( $classes );
}

function post_password_required( $post = null ) {
	$post = Yii::$app->wpthemes->post;

	if ( empty( $post->post_password ) )
		return false;

	if ( ! isset( $_COOKIE['wp-postpass_' . COOKIEHASH] ) )
		return true;

	require_once ABSPATH . WPINC . '/class-phpass.php';
	$hasher = new PasswordHash( 8, true );

	$hash = wp_unslash( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] );
	if ( 0 !== strpos( $hash, '$P$B' ) )
		return true;

	return ! $hasher->CheckPassword( $post->post_password, $hash );
}

function the_title( $before = '', $after = '', $echo = true ) {
	$title = get_the_title();

	if ( strlen($title) == 0 )
		return;

	$title = $before . $title . $after;

	if ( $echo )
		echo $title;
	else
		return $title;
}

function get_the_title( $post = 0 ) {
	$post = Yii::$app->wpthemes->post;

	$title = isset( $post->post_title ) ? $post->post_title : '';
	$id = isset( $post->ID ) ? $post->ID : 0;

	if ( ! is_admin() ) {
		if ( ! empty( $post->post_password ) ) {

			$protected_title_format = apply_filters( 'protected_title_format', __( 'Protected: %s' ), $post );
			$title = sprintf( $protected_title_format, $title );
		} elseif ( isset( $post->post_status ) && 'private' == $post->post_status ) {

			$private_title_format = apply_filters( 'private_title_format', __( 'Private: %s' ), $post );
			$title = sprintf( $private_title_format, $title );
		}
	}

	return apply_filters( 'the_title', $title, $id );
}

function the_content( $more_link_text = null, $strip_teaser = false) {
	// $content = get_the_content( $more_link_text, $strip_teaser );
	$content = Yii::$app->wpthemes->post->post_content;

	// $content = apply_filters( 'the_content', $content );
	// $content = str_replace( ']]>', ']]&gt;', $content );
	echo $content;
}

function get_the_content( $more_link_text = null, $strip_teaser = false ) {
	global $page, $more, $preview, $pages, $multipage;

	$post = Yii::$app->wpthemes->post;

	if ( null === $more_link_text )
		$more_link_text = __( '(more&hellip;)' );

	$output = '';
	$has_teaser = false;

	// If post password required and it doesn't match the cookie.
	if ( post_password_required( $post ) )
		return get_the_password_form( $post );

	if ( $page > count( $pages ) ) // if the requested page doesn't exist
		$page = count( $pages ); // give them the highest numbered page that DOES exist

	$content = $pages[$page - 1];
	if ( preg_match( '/<!--more(.*?)?-->/', $content, $matches ) ) {
		$content = explode( $matches[0], $content, 2 );
		if ( ! empty( $matches[1] ) && ! empty( $more_link_text ) )
			$more_link_text = strip_tags( wp_kses_no_null( trim( $matches[1] ) ) );

		$has_teaser = true;
	} else {
		$content = array( $content );
	}

	if ( false !== strpos( $post->post_content, '<!--noteaser-->' ) && ( ! $multipage || $page == 1 ) )
		$strip_teaser = true;

	$teaser = $content[0];

	if ( $more && $strip_teaser && $has_teaser )
		$teaser = '';

	$output .= $teaser;

	if ( count( $content ) > 1 ) {
		if ( $more ) {
			$output .= '<span id="more-' . $post->ID . '"></span>' . $content[1];
		} else {
			if ( ! empty( $more_link_text ) )

				$output .= apply_filters( 'the_content_more_link', ' <a href="' . get_permalink() . "#more-{$post->ID}\" class=\"more-link\">$more_link_text</a>", $more_link_text );
			$output = force_balance_tags( $output );
		}
	}

	if ( $preview ) // Preview fix for JavaScript bug with foreign languages.
		$output =	preg_replace_callback( '/\%u([0-9A-F]{4})/', '_convert_urlencoded_to_entities', $output );

	return $output;
}

function prepend_attachment($content) {
	$post = Yii::$app->wpthemes->post;

	if ( empty($post->post_type) || $post->post_type != 'attachment' )
		return $content;

	if ( wp_attachment_is( 'video', $post ) ) {
		$meta = wp_get_attachment_metadata( get_the_ID() );
		$atts = array( 'src' => wp_get_attachment_url() );
		if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
			$atts['width'] = (int) $meta['width'];
			$atts['height'] = (int) $meta['height'];
		}
		if ( has_post_thumbnail() ) {
			$atts['poster'] = wp_get_attachment_url( get_post_thumbnail_id() );
		}
		$p = wp_video_shortcode( $atts );
	} elseif ( wp_attachment_is( 'audio', $post ) ) {
		$p = wp_audio_shortcode( array( 'src' => wp_get_attachment_url() ) );
	} else {
		$p = '<p class="attachment">';
		// show the medium sized image representation of the attachment if available, and link to the raw file
		$p .= wp_get_attachment_link(0, 'medium', false);
		$p .= '</p>';
	}

	$p = apply_filters( 'prepend_attachment', $p );

	return "$p\n$content";
}

function wp_link_pages( $args = '' ) {
	global $page, $numpages, $multipage, $more;

	$defaults = array(
		'before'           => '<p>' . __( 'Pages:' ),
		'after'            => '</p>',
		'link_before'      => '',
		'link_after'       => '',
		'next_or_number'   => 'number',
		'separator'        => ' ',
		'nextpagelink'     => __( 'Next page' ),
		'previouspagelink' => __( 'Previous page' ),
		'pagelink'         => '%',
		'echo'             => 1
	);

	$params = wp_parse_args( $args, $defaults );

	$r = apply_filters( 'wp_link_pages_args', $params );

	$output = '';
	if ( $multipage ) {
		if ( 'number' == $r['next_or_number'] ) {
			$output .= $r['before'];
			for ( $i = 1; $i <= $numpages; $i++ ) {
				$link = $r['link_before'] . str_replace( '%', $i, $r['pagelink'] ) . $r['link_after'];
				if ( $i != $page || ! $more && 1 == $page ) {
					$link = _wp_link_page( $i ) . $link . '</a>';
				}
				$link = apply_filters( 'wp_link_pages_link', $link, $i );

				// Use the custom links separator beginning with the second link.
				$output .= ( 1 === $i ) ? ' ' : $r['separator'];
				$output .= $link;
			}
			$output .= $r['after'];
		} elseif ( $more ) {
			$output .= $r['before'];
			$prev = $page - 1;
			if ( $prev > 0 ) {
				$link = _wp_link_page( $prev ) . $r['link_before'] . $r['previouspagelink'] . $r['link_after'] . '</a>';

				/** This filter is documented in wp-includes/post-template.php */
				$output .= apply_filters( 'wp_link_pages_link', $link, $prev );
			}
			$next = $page + 1;
			if ( $next <= $numpages ) {
				if ( $prev ) {
					$output .= $r['separator'];
				}
				$link = _wp_link_page( $next ) . $r['link_before'] . $r['nextpagelink'] . $r['link_after'] . '</a>';

				/** This filter is documented in wp-includes/post-template.php */
				$output .= apply_filters( 'wp_link_pages_link', $link, $next );
			}
			$output .= $r['after'];
		}
	}

	$html = apply_filters( 'wp_link_pages', $output, $args );

	if ( $r['echo'] ) {
		echo $html;
	}
	return $html;
}

