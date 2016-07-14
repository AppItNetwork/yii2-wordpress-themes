<?php

use appitnetwork\wpthemes\helpers\WP_Term;
use yii\helpers\Inflector;

function wp_get_nav_menu_object( $menu ) {
	$menu_obj = false;

	if ( is_object( $menu ) ) {
		$menu_obj = $menu;
	}

	if ( $menu && ! $menu_obj ) {
		// if (strtolower($menu) == 'primary-menu' || strtolower($menu) == 'primary_menu') {
			$menu_obj = [
				'term_id' => $menu,
			    'name' => Inflector::camel2words($menu),
			    'slug' => $menu,
			    'term_group' => '0',
			    'term_taxonomy_id' => $menu,
			    'taxonomy' => 'nav_menu',
			    'description' => '',
			    'parent' => '0',
			    'count' => '1',
			    'filter' => 'raw'
		    ];
		    $menu_obj = json_decode(json_encode($menu_obj, false));
			$menu_obj = new WP_Term( $menu_obj );
		// }
		// $menu_obj = get_term( $menu, 'nav_menu' );

		// if ( ! $menu_obj ) {
		// 	$menu_obj = get_term_by( 'slug', $menu, 'nav_menu' );
		// }

		// if ( ! $menu_obj ) {
		// 	$menu_obj = get_term_by( 'name', $menu, 'nav_menu' );
		// }
	}

	if ( ! $menu_obj || is_wp_error( $menu_obj ) ) {
		$menu_obj = false;
	}

	return apply_filters( 'wp_get_nav_menu_object', $menu_obj, $menu );
}

function get_nav_menu_locations() {
	$locations = get_theme_mod( 'nav_menu_locations' );
	return ( is_array( $locations ) ) ? $locations : array();
}

function has_nav_menu( $location ) {
	$has_nav_menu = false;

	$registered_nav_menus = get_registered_nav_menus();
	if ( isset( $registered_nav_menus[ $location ] ) ) {
		// $locations = get_nav_menu_locations();
		$has_nav_menu = $location;
	}
// pr($registered_nav_menus);pr($locations);pr($location);die('has_nav_menu');
// pr(apply_filters( 'has_nav_menu', $has_nav_menu, $location ));die;
	return apply_filters( 'has_nav_menu', $has_nav_menu, $location );
}

function get_registered_nav_menus() {
	global $_wp_registered_nav_menus;
	if ( isset( $_wp_registered_nav_menus ) )
		return $_wp_registered_nav_menus;
	return array();
}

function register_nav_menu( $location, $description ) {
	register_nav_menus( array( $location => $description ) );
}

function register_nav_menus( $locations = array() ) {
	global $_wp_registered_nav_menus;

	add_theme_support( 'menus' );

	$_wp_registered_nav_menus = array_merge( (array) $_wp_registered_nav_menus, $locations );
}

function wp_get_nav_menu_items( $menu, $args = array() ) {
	// $menu = wp_get_nav_menu_object( $menu );
	$selected_menu_items = new \ArrayObject;
	if (isset(Yii::$app->wpthemes->menu[$menu])) {
		foreach (Yii::$app->wpthemes->menu[$menu] as $key => $item) {
			$selected_menu_items[$key] = generateNavMenuItemObject($item, $key);
			if (isset($item['items'])) {
				$parent_id = $key;
				extractNavMenuItemChild($item['items'], $parent_id, $selected_menu_items);
			}
		}
	}
	return $selected_menu_items->getArrayCopy();
	// return apply_filters( 'wp_get_nav_menu_items', $items, $menu, $args );
}

function extractNavMenuItemChild($items, $parent_id, $selected_menu_items) {
	foreach ($items as $key => $item) {
		$selected_menu_items[$parent_id.'-'.$key] = generateNavMenuItemObject($item, $parent_id.'-'.$key, $parent_id);
		if (isset($item['items'])) {
			extractNavMenuItemChild($item['items'], $parent_id.'-'.$key, $selected_menu_items);
		}
	}
}

function generateNavMenuItemObject($item, $key, $parent_id = 0) {
	$itemObj = new \stdClass;
	$itemObj->db_id = $key;
	$itemObj->menu_item_parent = $parent_id;
	$itemObj->object_id = $item['label'];
	$itemObj->object = 'custom';
	$itemObj->type = 'custom';
	$itemObj->type_label = 'Custom Link';
	$itemObj->title = $item['label'];
	$itemObj->url = Yii::$app->urlManager->createUrl($item['url']);
	$itemObj->target = '';
	$itemObj->attr_title = '';
	$itemObj->description = '';
	$itemObj->classes = [''];
	$itemObj->xfn = '';
	$itemObj->menu_order = $key;

	return $itemObj;
}

