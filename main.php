<?php
/*
Plugin Name: Display Opencart Category
Plugin URI: http://anybuy.vn/
Description: Display OpenCart Category is a WordPress plugin that allows you to show categories from your separate OpenCart store as primary menu in your WordPress site.
Version: 1.0.0
Author: ANYVIETNAM.,JSC
Author URI: http://anybuy.vn/
*/
global $ocdb, $ocdb_prefix, $oc_url;
$ocdb_host = 'localhost';
$ocdb_name = 'opencart';
$ocdb_user = 'root';
$ocdb_pass = '';
$ocdb_prefix = 'oc_';
$oc_url = 'http://localhost/opencart/';
$oc_seo_enable = 0;
$ocdb = new wpdb($ocdb_user,$ocdb_pass, $ocdb_name, $ocdb_host);
add_filter( 'wp_nav_menu_items', 'oc_category_display', 10, 2 );

function oc_category_display($menu, $args) {
	$args = (array)$args;
	if ('primary' == $args['theme_location']){
		$categories = oc_get_categories();
		foreach ($categories as $category) {
			if ($category['category_id'] == $category_id) {
				$menu .= '<li class="menu-item menu-item-type-taxonomy menu-item-object-category menu-item-has-children"><a href="'.$category['href'].'" class="active">'.$category['name'].'</a>';
			} else {
				$menu .= '<li class="menu-item menu-item-type-taxonomy menu-item-object-category menu-item-has-children"><a href="'.$category['href'].'">'.$category['name'].'</a>';
			}
			if ($category['children']){
				$menu .= '<ul class="sub-menu">';
				foreach ($category['children'] as $child) {
				   if ($child['category_id'] == $child_id) {
						$menu .= '<li class="menu-item menu-item-type-taxonomy menu-item-object-category"><a href="'.$child['href'].'" class="active">'.$child['name'].'</a></li>';
				   } else {
						$menu .= '<li class="menu-item menu-item-type-taxonomy menu-item-object-category"><a href="'.$child['href'].'">'.$child['name'].'</a></li>';
				   }
				}
				$menu .= '</ul>';
			}
			$menu .= '</li>';
		 }
	}
	return $menu;
}

function oc_get_categories(){
	$categories = oc_get_child_categories(0);
	foreach ($categories as $category) {
		$children_data = array();
		$children = oc_get_child_categories($category['category_id']);
		foreach ($children as $child) {
			$data = array(
				'filter_category_id'  => $child['category_id'],
				'filter_sub_category' => true
			);
			$children_data[] = array(
				'category_id' => $child['category_id'],
				'name'        => $child['name'],
				'href'        => oc_get_category_link($child['category_id'])	
			);		
		}
		$data_categories[] = array(
			'category_id' => $category['category_id'],
			'name'        => $category['name'],
			'children'    => $children_data,
			'href'        => oc_get_category_link($category['category_id'])
		);	
	}
	return($data_categories);
}

function oc_get_child_categories($parent_id = 0) {
	global $ocdb, $ocdb_prefix;
	$categories = $ocdb->get_results("SELECT * FROM " . $ocdb_prefix . "category c LEFT JOIN " . $ocdb_prefix . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . $ocdb_prefix . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)",ARRAY_A);
	return $categories;
}

function oc_get_category_link($category_id) {
	global $ocdb, $ocdb_prefix, $oc_url, $oc_seo_enable;
	$route = 'category_id=' . $category_id;
	$url = 	$oc_url;
	if($oc_seo_enable){
		$keyword = $ocdb->get_var("SELECT keyword FROM " . $ocdb_prefix . "url_alias WHERE query='".$route."'");
		if($keyword){
			$url.= $keyword;
		}else{
			$url .= 'index.php?route=product/category&path=' . $category_id;
		}	
	}
	else{
		$url .= 'index.php?route=product/category&path=' . $category_id;
	}			
	return $url;
}
?>
