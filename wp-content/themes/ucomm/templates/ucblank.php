<?php

/* Template Name: UC Full */
// Provides simply an unmodified <main> container

?>

<?php get_header(); ?>

<main class="spine-blank-template">

	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<?php get_template_part('parts/headers'); ?>
		<section class="row single">
			<div id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="column one">
				<article style="margin-bottom:0; padding-bottom:0;"><header class="article-header">
					<h1 class="article-title"><?php the_title(); ?></h1>
				</header></article></div>
				<?php the_content(); ?>
			</div><!-- #post -->
	</section>
<?php endwhile; endif; ?>

</main>

<?php get_footer(); ?>