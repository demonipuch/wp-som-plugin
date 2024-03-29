<?php
/*
Plugin Name: Advanced Custom Fields Pro
Plugin URI: http://www.advancedcustomfields.com/
Description: Fully customise WordPress edit screens with powerful fields. Boasting a professional interface and a powerful API, it’s a must have for any web developer working with WordPress. Field types include: Wysiwyg, text, textarea, image, file, select, checkbox, page link, post object, date picker, color picker, repeater, flexible content, gallery and more!
Version: 5.0.0
Author: elliot condon
Author URI: http://www.elliotcondon.com/
Copyright: Elliot Condon
Text Domain: acf
Domain Path: /lang
*/

// Current with acf v4 as of 27th Feb 1dde13a0bb8af763ef086f2ca0be4553ac955346

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('acf') ) :

class acf {

	// vars
	var $settings;


	/*
	*  __construct
	*
	*  A dummy constructor to ensure ACF is only initialized once
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	5.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function __construct() {

		/* Do nothing here */

	}


	/*
	*  initialize
	*
	*  The real constructor to initialize ACF
	*
	*  @type	function
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function initialize() {

		// vars
		$this->settings = array(

			// basic
			'name'			=> __('Advanced Custom Fields', 'acf'),
			'version'		=> '5.0.0',

			// urls
			'basename'		=> plugin_basename( __FILE__ ),
			'path'			=> plugin_dir_path( __FILE__ ),
			'dir'			=> plugin_dir_url( __FILE__ ),

			// options
			'show_admin'	=> true,
			'stripslashes'	=> true,
			'local'			=> true,
			'json'			=> true,
			'save_json'		=> '',
			'load_json'		=> array()
		);


		// include helpers
		include_once('api/api-helpers.php');


		// set text domain
		load_textdomain( 'acf', acf_get_path( 'lang/acf-' . get_locale() . '.mo' ) );


		// api
		acf_include('api/api-value.php');
		acf_include('api/api-field.php');
		acf_include('api/api-field-group.php');
		acf_include('api/api-template.php');


		// core
		acf_include('core/field.php');
		acf_include('core/input.php');
		acf_include('core/json.php');
		acf_include('core/local.php');
		acf_include('core/location.php');
		acf_include('core/revisions.php');
		acf_include('core/compatibility.php');
		acf_include('core/third_party.php');


		// forms
		acf_include('forms/attachment.php');
		acf_include('forms/comment.php');
		acf_include('forms/post.php');
		acf_include('forms/taxonomy.php');
		acf_include('forms/user.php');
		acf_include('forms/widget.php');


		// admin
		if( is_admin() ) {

			acf_include('admin/admin.php');
			acf_include('admin/field-group.php');
			acf_include('admin/field-groups.php');
			acf_include('admin/update.php');
			acf_include('admin/settings-export.php');
			//acf_include('admin/settings-addons.php');
			acf_include('admin/settings-info.php');

		}


		// fields
		acf_include('fields/text.php');
		acf_include('fields/textarea.php');
		acf_include('fields/number.php');
		acf_include('fields/email.php');
		acf_include('fields/password.php');
		acf_include('fields/wysiwyg.php');
		acf_include('fields/oembed.php');
		acf_include('fields/image.php');
		acf_include('fields/file.php');
		acf_include('fields/select.php');
		acf_include('fields/checkbox.php');
		acf_include('fields/radio.php');
		acf_include('fields/true_false.php');
		acf_include('fields/post_object.php');
		acf_include('fields/page_link.php');
		acf_include('fields/relationship.php');
		acf_include('fields/taxonomy.php');
		acf_include('fields/user.php');
		acf_include('fields/google-map.php');
		acf_include('fields/date_picker.php');
		acf_include('fields/color_picker.php');
		acf_include('fields/message.php');
		acf_include('fields/tab.php');


		// pro
		acf_include('pro/acf-pro.php');


		// actions
		add_action('init',			array($this, 'wp_init'), 5);
		add_filter('posts_where',	array($this, 'wp_posts_where'), 10, 2 );

	}


	/*
	*  complete
	*
	*  This function will ensure all files are included
	*
	*  @type	function
	*  @date	10/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function complete() {

		// bail early if actions have not passed 'plugins_loaded'
		if( ! did_action('plugins_loaded') ) {

			return;

		}


		// once run once
		if( acf_get_setting('complete') ) {

			return;

		}


		// update setting
		acf_update_setting('complete', true);


		// wpml
		if( defined('ICL_SITEPRESS_VERSION') ) {

			acf_include('core/wpml.php');

		}


		// action for 3rd party customization
		do_action('acf/include_field_types', 5);
		do_action('acf/include_fields', 5);

	}


	/*
	*  wp_init
	*
	*  This function will run on the WP init action and setup many things
	*
	*  @type	action (init)
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function wp_init() {

		// complete loading of ACF files
		$this->complete();


		// Create post type 'acf-field-group'
		register_post_type( 'acf-field-group', array(
			'labels'			=> array(
			    'name'					=> __( 'Field&nbsp;Groups', 'acf' ),
				'singular_name'			=> __( 'Field Group', 'acf' ),
			    'add_new'				=> __( 'Add New' , 'acf' ),
			    'add_new_item'			=> __( 'Add New Field Group' , 'acf' ),
			    'edit_item'				=> __( 'Edit Field Group' , 'acf' ),
			    'new_item'				=> __( 'New Field Group' , 'acf' ),
			    'view_item'				=> __( 'View Field Group', 'acf' ),
			    'search_items'			=> __( 'Search Field Groups', 'acf' ),
			    'not_found'				=> __( 'No Field Groups found', 'acf' ),
			    'not_found_in_trash'	=> __( 'No Field Groups found in Trash', 'acf' ), 
			),
			'public'			=> false,
			'show_ui'			=> true,
			'_builtin'			=> false,
			'capability_type'	=> 'page',
			'hierarchical'		=> true,
			'rewrite'			=> false,
			'query_var'			=> false,
			'supports' 			=> array( 'title' ),
			'show_in_menu'		=> false,
		));


		// Create post type 'acf-field'
		register_post_type( 'acf-field', array(
			'labels'			=> array(
			    'name'					=> __( 'Fields', 'acf' ),
				'singular_name'			=> __( 'Field', 'acf' ),
			    'add_new'				=> __( 'Add New' , 'acf' ),
			    'add_new_item'			=> __( 'Add New Field' , 'acf' ),
			    'edit_item'				=> __( 'Edit Field' , 'acf' ),
			    'new_item'				=> __( 'New Field' , 'acf' ),
			    'view_item'				=> __( 'View Field', 'acf' ),
			    'search_items'			=> __( 'Search Fields', 'acf' ),
			    'not_found'				=> __( 'No Fields found', 'acf' ),
			    'not_found_in_trash'	=> __( 'No Fields found in Trash', 'acf' ), 
			),
			'public'			=> false,
			'show_ui'			=> false,
			'_builtin'			=> false,
			'capability_type'	=> 'page',
			'hierarchical'		=> true,
			'rewrite'			=> false,
			'query_var'			=> false,
			'supports' 			=> array( 'title' ),
			'show_in_menu'		=> false,
		));


		// min
		//$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$min = '';


		// register scripts
		$scripts = array(

			array(
				'handle'	=> 'select2',
				'src'		=> acf_get_dir( "inc/select2/select2{$min}.js" ),
				'deps'		=> array('jquery'),
			),

			array(
				'handle'	=> 'acf-input',
				'src'		=> acf_get_dir( "js/input{$min}.js" ),
				'deps'		=> array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'underscore', 'select2'),
			),

			array(
				'handle'	=> 'acf-field-group',
				'src'		=> acf_get_dir( "js/field-group{$min}.js"),
				'deps'		=> array('acf-input'),
			)

		);

		foreach( $scripts as $script ) {

			wp_register_script( $script['handle'], $script['src'], $script['deps'], acf_get_setting('version') );

		}


		// register styles
		$styles = array(

			array(
				'handle'	=> 'select2',
				'src'		=> acf_get_dir( 'inc/select2/select2.css' ),
				'deps'		=> array(),
			),

			array(
				'handle'	=> 'acf-datepicker',
				'src'		=> acf_get_dir( 'inc/datepicker/jquery-ui-1.10.4.custom.min.css' ),
				'deps'		=> array(),
			),

			array(
				'handle'	=> 'acf-global',
				'src'		=> acf_get_dir( 'css/global.css' ),
				'deps'		=> array(),
			),

			array(
				'handle'	=> 'acf-field-group',
				'src'		=> acf_get_dir( 'css/field-group.css' ),
				'deps'		=> array(),
			),

			array(
				'handle'	=> 'acf-input',
				'src'		=> acf_get_dir( 'css/input.css' ),
				'deps'		=> array('acf-datepicker', 'select2'),
			)

		);

		foreach( $styles as $style ) {

			wp_register_style( $style['handle'], $style['src'], $style['deps'], acf_get_setting('version') ); 

		}

	}


	/*
	*  wp_posts_where
	*
	*  This function will add in some new parameters to the WP_Query args allowing fields to be found via key / name
	*
	*  @type	filter
	*  @date	5/12/2013
	*  @since	5.0.0
	*
	*  @param	$where (string)
	*  @param	$wp_query (object)
	*  @return	$where (string)
	*/

	function wp_posts_where( $where, $wp_query ) {

		// global
		global $wpdb;


		// acf_field_key
		if( $field_key = $wp_query->get('acf_field_key') ) {

			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_name = %s", $field_key );

	    }


	    // acf_field_name
	    if( $field_name = $wp_query->get('acf_field_name') ) {

			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_excerpt = %s", $field_name );

			// acf_post_id
		    if( $post_id = $wp_query->get('acf_post_id') ) {

				$where .= $wpdb->prepare(" AND {$wpdb->postmeta}.post_id = %d", $post_id );

			}

	    }


	    // acf_group_key
		if( $group_key = $wp_query->get('acf_group_key') ) {

			$where .= $wpdb->prepare(" AND {$wpdb->posts}.post_name = %s", $group_key );

	    }


	    return $where;

	}


	/*
	*  debug SQL
	*
	*  description
	*
	*  @type	function
	*  @date	27/02/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function wp_posts_join( $join, $wp_query ) {

		/*
// acf_field_name
		if( $post_id = $wp_query->get('acf_post_id') )
		{
			$join = str_replace('.ID', '.post_name', $join);
			$join = str_replace('.post_id', '.meta_value', $join);
	   }
*/

	   return $join;


	}


	function posts_request( $thing ) {
		/*

		echo '<pre>';
			print_r($thing );
		echo '</pre>';
		die;
*/

		return $thing;
	}

}


/*
*  acf
*
*  The main function responsible for returning the one true acf Instance to functions everywhere.
*  Use this function like you would a global variable, except without needing to declare the global.
*
*  Example: <?php $acf = acf(); ?>
*
*  @type	function
*  @date	4/09/13
*  @since	4.3.0
*
*  @param	N/A
*  @return	(object)
*/

function acf() {

	global $acf;

	if( !isset($acf) ) {

		$acf = new acf();

		$acf->initialize();

	}

	return $acf;
}


// initialize
acf();


endif; // class_exists check

?>
