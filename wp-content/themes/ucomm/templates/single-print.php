<?php

/* Template Name: Single-Print */
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
			<section class="lib-work">
  	<?php 
  $thumbnails = get_posts(array('posts_per_page' => 5,'tag' => 'print'));
  foreach ($thumbnails as $thumbnail) {
    if ( has_post_thumbnail($thumbnail->ID)) {
      echo '<a href="' . get_permalink( $thumbnail->ID ) . '" title="' . esc_attr( $thumbnail->post_title ) . '">';
      echo get_the_post_thumbnail($thumbnail->ID, 'thumbnail');
      echo '</a>';
    }
  }
?>
 </section>
	</section>
	 	
<?php endwhile; endif; ?>

</main>

<?php get_footer(); ?>