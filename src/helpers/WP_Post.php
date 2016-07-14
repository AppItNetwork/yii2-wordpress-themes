<?php

namespace appitnetwork\wpthemes\helpers;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

class WP_Post extends Component
{
	public $ID;

	public $post_author = 0;

	public $post_date = '0000-00-00 00:00:00';

	public $post_date_gmt = '0000-00-00 00:00:00';

	public $post_content = '';

	public $post_title = '';

	public $post_excerpt = '';

	public $post_status = 'publish';

	public $comment_status = 'open';

	public $ping_status = 'open';

	public $post_password = '';

	public $post_name = '';

	public $to_ping = '';

	public $pinged = '';

	public $post_modified = '0000-00-00 00:00:00';

	public $post_modified_gmt = '0000-00-00 00:00:00';

	public $post_content_filtered = '';

	public $post_parent = 0;

	public $guid = '';

	public $menu_order = 0;

	public $post_type = 'post';

	public $post_mime_type = '';

	public $comment_count = 0;

	public $filter;

	public static function get_instance( $post_id ) {
		global $wpdb;

		// $post_id = (int) $post_id;
// pr($post_id);die;
		if ( ! $post_id )
			return false;

		// $_post = wp_cache_get( $post_id, 'posts' );

		if ( ! $_post ) {
			// $_post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d LIMIT 1", $post_id ) );
			$_post = [
				'ID' => $post_id,
			    'post_author' => '1',
			    'post_date' => '2016-01-07 15:33:41',
			    'post_date_gmt' => '2016-01-07 15:33:41',
			    'post_content' => Yii::$app->view->content,
			    'post_title' => Yii::$app->view->title,
			    'post_excerpt' => '',
			    'post_status' => 'publish',
			    'comment_status' => 'closed',
			    'ping_status' => 'closed',
			    'post_password' => '',
			    'post_name' => 'test-page',
			    'to_ping' => '',
			    'pinged' => '',
			    'post_modified' => '2016-01-07 15:33:41',
			    'post_modified_gmt' => '2016-01-07 15:33:41',
			    'post_content_filtered' => '',
			    'post_parent' => '0',
			    'guid' => 'http://portal.silvercoins.col/?page_id=18',
			    'menu_order' => '0',
			    'post_type' => 'page',
			    'post_mime_type' => '',
			    'comment_count' => '0',
			];
			$_post = json_decode(json_encode($_post, false));
// pr($_post);die;
			if ( ! $_post )
				return false;

			$_post = sanitize_post( $_post, 'raw' );
			// wp_cache_add( $_post->ID, $_post, 'posts' );
		} elseif ( empty( $_post->filter ) ) {
			$_post = sanitize_post( $_post, 'raw' );
		}

		return new WP_Post( $_post );
	}

	public function __construct( $post ) {
	// public function __construct( $post = null ) {
		if ( is_object($post) ) {
			foreach ( get_object_vars( $post ) as $key => $value )
				$this->$key = $value;
		// } else {
		// 	$this->initEmptyObject( $post );
		}

	}

	public function initEmptyObject( $post = null ) {
		// pr(get_class_methods(Yii::$app->controller));die;
		if (is_array($post)) {
			foreach ( $post as $key => $value ) {
				if (isset($this->$key)) {
					$this->$key = $value;
				}
			}
		} else {
			$this->post_type = 'post';
			$this->post_status = 'publish';
			$this->post_title = 'Empty Post';
		}
	}

	public function __isset( $key ) {
		if ( 'ancestors' == $key )
			return true;

		if ( 'page_template' == $key )
			return ( 'page' == $this->post_type );

		if ( 'post_category' == $key )
		   return true;

		if ( 'tags_input' == $key )
		   return true;

		return metadata_exists( 'post', $this->ID, $key );
	}

	public function __get( $key ) {
		if ( 'page_template' == $key && $this->__isset( $key ) ) {
			return get_post_meta( $this->ID, '_wp_page_template', true );
		}

		if ( 'post_category' == $key ) {
			if ( is_object_in_taxonomy( $this->post_type, 'category' ) )
				$terms = get_the_terms( $this, 'category' );

			if ( empty( $terms ) )
				return array();

			return wp_list_pluck( $terms, 'term_id' );
		}

		if ( 'tags_input' == $key ) {
			if ( is_object_in_taxonomy( $this->post_type, 'post_tag' ) )
				$terms = get_the_terms( $this, 'post_tag' );

			if ( empty( $terms ) )
				return array();

			return wp_list_pluck( $terms, 'name' );
		}

		// Rest of the values need filtering.
		if ( 'ancestors' == $key )
			$value = get_post_ancestors( $this );
		else
			$value = get_post_meta( $this->ID, $key, true );

		if ( $this->filter )
			$value = sanitize_post_field( $key, $value, $this->ID, $this->filter );

		return $value;
	}

	public function filter( $filter ) {
		if ( $this->filter == $filter )
			return $this;

		if ( $filter == 'raw' )
			return self::get_instance( $this->ID );

		return sanitize_post( $this, $filter );
	}

	public function to_array() {
		$post = get_object_vars( $this );

		foreach ( array( 'ancestors', 'page_template', 'post_category', 'tags_input' ) as $key ) {
			if ( $this->__isset( $key ) )
				$post[ $key ] = $this->__get( $key );
		}

		return $post;
	}
}
