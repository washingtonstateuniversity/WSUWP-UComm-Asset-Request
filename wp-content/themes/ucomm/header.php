<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]><html class="lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]><html class="lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--><html <?php language_attributes(); ?>><!--<![endif]-->


<?php // CUSTOMIZATION
	$spine_options = get_option( 'spine_options' );
	$grid_style = $spine_options['grid_style'];
	$spine_color = $spine_options['spine_color'];
	$large_format = $spine_options['large_format'];
	?>

<head>

	<meta charset="<?php bloginfo( 'charset' ); ?>" />
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge"><![endif]--> 
	<title><?php wp_title( '|', true, 'right' ); ?> Washington State University</title>
	
	<!-- CONTACT -->
	<?php get_template_part('parts/head','contact'); ?> 
	
	<!-- FAVICON -->
	<link rel="shortcut icon" href="http://repo.wsu.edu/spine/1/favicon.ico" />
	
	<!-- RESPOND -->
	<meta name="viewport" content="width=device-width, user-scalable=yes">

	<!-- DOCS -->
	<link type="text/plain" rel="author" href="http://images.wsu.edu/spine/authors.txt" />
	<link type="text/html" rel="docs" href="http://identity.wsu.edu" />
	
	<?php wp_head(); ?>

	<!-- STYLESHEETS -->
	<!-- Your custom stylesheets here -->
	<link href="<?php echo get_stylesheet_directory_uri(); ?>/style.css" rel="stylesheet" type="text/css" />

	<!-- COMPATIBILITY -->
	<!--[if lt IE 9]><script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js">IE7_PNG_SUFFIX=".png";</script><![endif]-->
	<script>$ = jQuery;</script>

	<!-- ANALYTICS -->
	<!-- Your analytics code here -->

	<!-- SCRIPTS -->
	<!-- Your supplementary scripts here -->
</head>

<body <?php body_class(); ?>>

<div id="jacket" class="palette">
<!-- Accent banner to stretch across the page -->
<div class="acBanner"></div>
<div id="binder" class="<?php echo esc_attr( $grid_style ); echo esc_attr( $large_format ); ?>">