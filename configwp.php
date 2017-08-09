<?php
	add_action('wp_head', array(&$gwoPackage, 'printHeader'));
//	add_action('wp_footer',array(&$gwoPackage, 'printFooter'));
//	add_action('edit_form_advanced', array(&$gwoPackage, 'printPagePostOptionArea'));
//	add_action('edit_page_form', array(&$gwoPackage, 'printPagePostOptionArea'));
	add_action('save_post',array(&$gwoPackage,'addGWOTest'));//'addGWOTest');// 
	add_action('init', 'add_gwo_plugin_tiny_mce_buttons');
	add_filter('the_content',array(&$gwoPackage,'create_gwo_sections'));
	add_filter('the_content_rss',array(&$gwoPackage,'remove_gwo_sections')); 
	add_action('admin_menu', 'gwo_meta_box_add');
    
    /*
     * Functions related to TinyMCE
     * The following code adds a plugin to TinyMCE
     */
    function add_gwo_plugin_tiny_mce_buttons() {
   		// Don't bother doing this stuff if the current user lacks permissions
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
			return; 
	   // Add only in Rich Editor mode
		if ( get_user_option('rich_editing') == 'true') {
			add_filter("mce_external_plugins", "add_gwo_tinymce_plugin");
			add_filter('mce_buttons_3', 'register_gwo_tinymce_plugin_buttons');
		}
	}
	
	function gwo_meta_box_add() {
		if ( function_exists('add_meta_box') ) {
			add_meta_box('gwo',__('GWO', 'cs_gwo_plugin'),'gwo_meta','post','advanced');
			add_meta_box('gwo',__('GWO', 'cs_gwo_plugin'),'gwo_meta','page','advanced');
		}
	}
	function gwo_meta(){
		global $gwoPackage;
		$gwoPackage->printPagePostOptionArea();
	}
	function register_gwo_tinymce_plugin_buttons($buttons) {
		array_push($buttons, "CsGWOSection");
		array_push($buttons, "CsGWOAddConversionLink");
		array_push($buttons, "CsGWORemoveConversionLink");
		return $buttons;
	}

	function add_gwo_tinymce_plugin($plugin_array) {
	   $plugin_array['GWO4WP'] = WP_PLUGIN_URL."/gwo4wp/editor_plugin.js"; //"../../../wp-content/plugins/cs-gwo-plugin/editor_plugin.js";
	   return $plugin_array;
	}


/*
	function testTitle($title) {
		global $wp_query;
		$post = $wp_query->get_queried_object();
		$csgwo_enable=(bool)get_post_meta($post->ID, '_csgwo_enable', true);
		if($csgwo_enable){
			$csgwo_title=(bool)get_post_meta($post->ID, '_csgwo_title', true);
			if($csgwo_title)
				$title="<script>utmx_section(\"Title\")</script>".$title."</noscript>";
		}
		return $title;
	}
*/ 