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
	
	<!-- STYLESHEETS -->
	<!-- TARGET *LIVE* --><link href="http://repo.wsu.edu/spine/1/spine.min.css" rel="stylesheet" type="text/css" /><!-- -->
	<!-- TEMP *DEV* <link href="http://nbj.me/spine/1/0.5/styles/styles.css" rel="stylesheet" type="text/css" />  -->
	<!-- Your custom stylesheets here -->
	<link href="<?php echo get_stylesheet_directory_uri(); ?>/style.css" rel="stylesheet" type="text/css" />
	
	<!-- RESPOND -->
	<meta name="viewport" content="width=device-width, user-scalable=yes">
	
	<!-- SCRIPTS -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<!-- TARGET *LIVE* --><script src="http://repo.wsu.edu/spine/1/spine.min.js"></script><!-- -->
	<!-- TEMP *DEV* <script src="http://nbj.me/spine/1/0.5/scripts/spine.js"></script> -->
	<!-- Your supplementary scripts here -->
	<script src="webfonts/ss-pika.js"></script>
	<!-- COMPATIBILITY -->
	<!--[if lt IE 9]><script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js">IE7_PNG_SUFFIX=".png";</script><![endif]--> 
	
	<!-- DOCS -->
	<link type="text/plain" rel="author" href="http://images.wsu.edu/spine/authors.txt" />
	<link type="text/html" rel="docs" href="http://identity.wsu.edu" />
	
	<!-- ANALYTICS -->
	<!-- Your analytics code here -->
	
	<?php wp_head(); ?>
	<script>$ = jQuery;</script>

</head>

<body <?php body_class(); ?>>

<div id="jacket" class="palette">
<!-- Accent banner to stretch across the page -->
<div class="acBanner"></div>
<div id="binder" class="<?php echo $grid_style; echo $large_format; ?>">