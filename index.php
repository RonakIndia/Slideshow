<?php

/*
Plugin Name: Site Specific Plugin | Ronak Mehta
Plugin Author: Ronak Mehta
Plugin URI: 
Description: This is a site specific plugin built for ronak mehta
*/

//define( 'DONOTCACHEPAGE', true );

if ( file_exists( dirname( __FILE__ ) . '/inc/cmb2/init.php' ) ) {
	require_once 'inc/cmb2/init.php';
} else {
	echo "CMB2 missing";
}

require_once ( plugin_dir_path( __FILE__ ) . '/inc/metaboxes.php' );

add_action( 'cmb2_admin_init', 'dcwd_photoshoots_carousel_images' );
function dcwd_photoshoots_carousel_images() {
	$cmb_options = new_cmb2_box( array(
		'id'            => 'photoshoots_carousel',
		'title'         => 'Photoshoots Carousel',
		'object_types' => array( 'options-page' ),

		// The following parameters are specific to the options-page box.
		'option_key'      => 'photoshoots_carousel', // The option key and admin menu page slug.
		'icon_url'        => 'dashicons-camera-alt', // Menu icon. Only applicable if 'parent_slug' is left empty.
		'capability'      => 'edit_posts', // Capability required to view this options page.
		'position'        => 2, // Menu position. Only applicable if 'parent_slug' is left empty.
		'save_button'     => 'Save',
	) );

	$photoshoots_group_id = $cmb_options->add_field( array(
		'id' => 'photoshoots_group',
		'type' => 'group',
		'repeatable'  => true,
		'options'     => array(
			'group_title'   => 'Photoshoot {#}',
			'add_button'    => 'Add another photoshoot group',
			'remove_button' => 'Remove this photoshoot group',
			'closed'        => false,  // Repeater fields open by default so that the first repeater field get set.
		),
	) );

	// TODO: Add regex or other protection to ensure that the photoshoot_name can be used as an array element name.
	$cmb_options->add_group_field( $photoshoots_group_id, array(
		'name' => 'Photoshoot ID',
		'id'   => 'photoshoot_name',
		'desc' => 'A word to identify this photoshoot. It will not be displayed, it is only for internal organisation.',
		'type' => 'text_small',
		'attributes' => array(
			'required' => 'required',
		),
	) );
	// Use 'file_list' as 'file' is not repeatable.
	$cmb_options->add_group_field( $photoshoots_group_id, array(
		'id'   => 'photos',
		'type' => 'file_list',
		'preview_size' => array( 100, 100 ),
		'query_args' => array( 'type' => 'image' ),
	) );
}


// Return the markup for the [photoshoots_carousel] shortcode.
add_shortcode( 'photoshoots_carousel', 'dcwd_photoshoots_carousel' );
function dcwd_photoshoots_carousel() {
	// Add the Slick files to the footer.
	wp_enqueue_script( 'slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array( 'jquery' ) );
	wp_enqueue_style( 'slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.css' );
	add_action( 'wp_footer', 'dcwd_photoshoots_carousel_slick_init' );

	$photoshoots_carousel = get_option( 'photoshoots_carousel' );

	// Convert the data into an array of photoshoot names that point to a list of image urls.
	$images = array();
	foreach ( $photoshoots_carousel['photoshoots_group'] as $shoot ) {
		//echo '<pre>', var_export( $shoot['photoshoot_name'], true ), '</pre>';
		foreach ( $shoot[ 'photos' ] as $id => $photo_url ) {
			$images[ $shoot['photoshoot_name'] ][] = $photo_url;
		}
	}

	// Shuffle the top level keys to randomise the groups.
	$shuffled = array_keys( $images );
	//shuffle( $shuffled );

	ob_start();
?>
<div id="slider-wrap">
	<div id="photoshoots_carousel">
<?php
	foreach ( $shuffled as $group ) {
		foreach ( $images[ $group ] as $image ) {
			printf( '<div><img src=%s / style="width:200px; height:200px;"></div>%s',$image, "\n" );
		}
	}
?>
	</div>
</div>
<?php
	return ob_get_clean();
}


// Initialise the carousel
function dcwd_photoshoots_carousel_slick_init() {
?>
<script>
jQuery(document).ready(function( $ ) {
	$('#photoshoots_carousel').slick({
	  centerMode: true,
	  arrows: false,
	  autoplay: true,
	  autoplaySpeed: 3000,
	  slidesToShow: 3,
	  responsive: [
		{
		  breakpoint: 1200,
		  settings: {
			slidesToShow: 10,
		  }
		},
		{
		  breakpoint: 768,
		  settings: {
			slidesToShow: 3,
		  }
		},
		{
		  breakpoint: 480,
		  settings: {
			slidesToShow: 1,
		  }
		}
	  ]
	});
});
</script>
<?php
}