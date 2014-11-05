<?php

/******************************************************/
/*                     THUMBNAILS                     */
/******************************************************/

/**
 *
 * Add more post thumbnail sizes
 *
 * @link http://codex.wordpress.org/Function_Reference/add_image_size
 *
 */
add_image_size( 'square-mini', 50, 50 );

/**
 *
 * Customize custom posts thumbnails
 *
 * @link http://themergency.com/featured-image-metabox-customization
 *
 */

// The title
function custom_post_thumbnail_title() {
	$post_type = get_post_type();
	switch( $post_type ) {
		case 'release':
			remove_meta_box( 'postimagediv', 'release', 'side' );
			add_meta_box( 'postimagediv', __( 'Cover Artwork' ), 'post_thumbnail_meta_box', 'release', 'side', 'low' );
			break;
		case 'band':
			remove_meta_box( 'postimagediv', 'band', 'side' );
			add_meta_box( 'postimagediv', __( 'Band Logo' ), 'post_thumbnail_meta_box', 'band', 'side', 'low' );
			break;
		default:
			break;
	}
}
add_action( 'add_meta_boxes', 'custom_post_thumbnail_title' );

// The link
function custom_post_thumbnail_content( $content ) {
	$post_type = get_post_type();
	switch( $post_type ) {
		case 'release':
			if( has_post_thumbnail() ) {
				return $content = str_replace( __( 'Remove featured image' ), __( 'Remove cover artwork' ), $content );
			} else {
				return $content = str_replace( __( 'Set featured image' ), __( 'Set cover artwork' ), $content );
			}
			break;
		case 'band':
			if( has_post_thumbnail() ) {
				return $content = str_replace( __( 'Remove featured image' ), __( 'Remove band logo' ), $content );
			} else {
				return $content = str_replace( __( 'Set featured image' ), __( 'Set band logo' ), $content );
			}
			break;
		default:
			return $content;
			break;
	}
}
add_filter( 'admin_post_thumbnail_html', 'custom_post_thumbnail_content' );

// The media manager title and button
function custom_post_thumbnail_media_manager( $strings ) {
	$post_type = get_post_type();
	switch( $post_type ) {
		case 'release':
			$strings[ 'setFeaturedImage' ] = __( 'Set cover artwork' );
			$strings[ 'setFeaturedImageTitle' ] = __( 'Set cover artwork' );
			return $strings;
			break;
		case 'band':
			$strings[ 'setFeaturedImage' ] = __( 'Set band logo' );
			$strings[ 'setFeaturedImageTitle' ] = __( 'Set band logo' );
			return $strings;
			break;
		default:
			return $strings;
			break;
	}
}
add_filter( 'media_view_strings', 'custom_post_thumbnail_media_manager' );

/************************************************/
/*                     BAND                     */
/************************************************/

/**
 *
 * Change default title for band post types
 *
 * @link http://wp-snippets.com/change-enter-title-here-text-for-custom-post-type/
 *
 */

function band_default_title( $title ) {
	$screen = get_current_screen();
	if( 'band' == $screen->post_type ) {
		$title = 'Enter band name';
	}
	return $title;
}
add_filter( 'enter_title_here', 'band_default_title' );

/***************************************************/
/*                     RELEASE                     */
/***************************************************/

/**
 *
 * Change default title for release post type
 *
 * @link http://wp-snippets.com/change-enter-title-here-text-for-custom-post-type/
 *
 */

function release_default_title( $title ) {
	$screen = get_current_screen();
	if( 'release' == $screen->post_type ) {
		$title = 'Enter release title';
	}
	return $title;
}
add_filter( 'enter_title_here', 'release_default_title' );

/**
 *
 * Register custom columns for release post type
 *
 * @link http://codex.wordpress.org/Plugin_API/Action_Reference/manage_posts_custom_column
 *
 */
function release_edit_columns( $columns ) {
	$columns = array(
		'cb'              => '<input type=\"checkbox\" />',
		'title'           => __( 'Release' ),
		'cover'           => __( 'Cover' ),
		'catalog_number'  => __( 'Catalog Number' ),
		'release_date'    => __( 'Release Date' ),
		'release_date_us' => __( 'Release Date US' ),
		'date'            => __( 'Date' ),
	);
	return $columns;
}
add_filter( 'manage_edit-release_columns', 'release_edit_columns' );

function release_custom_columns( $columns ) {
	global $post;
	switch( $columns ) {
		case 'cover':
			if( has_post_thumbnail() ) {
				the_post_thumbnail( 'square-mini' );
			}
			break;
		case 'catalog_number':
			if( get_field( 'catalog_number' ) ) {
				the_field( 'catalog_number' );
			}
			break;
		case 'release_date':
			if( get_field( 'release_date' ) ) {
				the_field( 'release_date' );
			}
			break;
		case 'release_date_us':
			if( get_field( 'release_date_us' ) ) {
				the_field( 'release_date_us' );
			}
			break;
		default:
			break;
	}
}
add_filter( 'manage_posts_custom_column', 'release_custom_columns' );

?>