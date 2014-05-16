<?php
function my_mce_buttons_2($buttons) {	
	/**
	 * Add in a core button that's disabled by default
	 */
	$buttons[] = 'hr';
	$buttons[] = 'sup';
	$buttons[] = 'styleselect';

	return $buttons;
}
add_filter('mce_buttons_2', 'my_mce_buttons_2');
// Callback function to filter the MCE settings
function my_mce_before_init_insert_formats( $init_array ) {  
	// Define the style_formats array
	$style_formats = array(  
		// Each array child is a format with it's own settings
		array(  
			'title' => '.blocks',  
			'block' => 'p',  
			'classes' => 'p',
			'wrapper' => true,
			
		),  
		array(  
			'title' => '⇠.rtl',  
			'block' => 'blockquote',  
			'classes' => 'rtl',
			'wrapper' => true,
		),
		array(  
			'title' => '.ltr⇢',  
			'block' => 'blockquote',  
			'classes' => 'ltr',
			'wrapper' => true,
		),
	);  
	// Insert the array, JSON ENCODED, into 'style_formats'
	$init_array['style_formats'] = json_encode( $style_formats );  
	
	return $init_array;  
  
} 
// Attach callback to 'tiny_mce_before_init' 
add_filter( 'tiny_mce_before_init', 'my_mce_before_init_insert_formats' );
add_theme_support( 'post-thumbnails' ); 
?>